<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\TrackingEvent;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShipmentApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Shipment::with(['carrier', 'origin', 'destination', 'currentLocation', 'customer']);

        // Scope by customer if role is Customer
        if ($user->isCustomer()) {
            $query->where('customer_id', $user->id);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($carrierId = $request->input('carrier_id')) {
            $query->where('carrier_id', $carrierId);
        }

        $shipments = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $shipments,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'shipment_number' => ['required', 'string', 'unique:shipments,shipment_number'],
            'customer_id' => ['required', 'exists:users,id'],
            'carrier_id' => ['required', 'exists:carriers,id'],
            'origin_location_id' => ['required', 'exists:locations,id'],
            'destination_location_id' => ['required', 'exists:locations,id'],
            'cargo_type' => ['required', 'string', 'max:100'],
            'weight' => ['required', 'numeric', 'min:0.1'],
            'volume' => ['required', 'numeric', 'min:0.1'],
            'expected_departure' => ['required', 'date'],
            'expected_arrival' => ['required', 'date', 'after:expected_departure'],
            'estimated_time_arrival' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $shipment = new Shipment($validated);
        $shipment->current_location_id = $validated['origin_location_id'];
        $shipment->status = Shipment::STATUS_CREATED;
        $shipment->recalculateDelay();
        $shipment->save();

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'CREATE',
            'entity_type' => 'Shipment',
            'entity_id' => (string) $shipment->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => "Shipment {$shipment->shipment_number} dibuat melalui API.",
            'new_values' => $validated,
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shipment berhasil dibuat.',
            'data' => $shipment->load(['carrier', 'origin', 'destination', 'customer']),
        ], 201);
    }

    public function show(Request $request, Shipment $shipment): JsonResponse
    {
        $user = $request->user();
        if ($user->isCustomer() && $shipment->customer_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $shipment->load([
            'carrier',
            'origin',
            'destination',
            'currentLocation',
            'customer',
            'items',
            'trackingEvents.location',
            'exceptions',
        ]);

        return response()->json([
            'success' => true,
            'data' => $shipment,
        ]);
    }

    public function update(Request $request, Shipment $shipment): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', 'in:CREATED,BOOKED,PICKED_UP,IN_TRANSIT,DELIVERED'],
            'current_location_id' => ['nullable', 'exists:locations,id'],
            'estimated_time_arrival' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $oldValues = $shipment->only(['status', 'current_location_id', 'estimated_time_arrival']);
        $shipment->fill($validated);
        $shipment->recalculateDelay();
        $shipment->save();

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'UPDATE',
            'entity_type' => 'Shipment',
            'entity_id' => (string) $shipment->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => "Shipment {$shipment->shipment_number} diperbarui via API.",
            'old_values' => $oldValues,
            'new_values' => $validated,
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shipment berhasil diperbarui.',
            'data' => $shipment,
        ]);
    }

    public function destroy(Request $request, Shipment $shipment): JsonResponse
    {
        $num = $shipment->shipment_number;
        $shipment->delete();

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'DELETE',
            'entity_type' => 'Shipment',
            'entity_id' => (string) $shipment->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => "Shipment {$num} dihapus via API.",
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shipment berhasil dihapus.',
        ]);
    }

    public function tracking(Request $request, Shipment $shipment): JsonResponse
    {
        $user = $request->user();
        if ($user->isCustomer() && $shipment->customer_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $events = $shipment->trackingEvents()->with(['location', 'creator'])->get();

        return response()->json([
            'success' => true,
            'data' => $events,
        ]);
    }

    public function addTracking(Request $request, Shipment $shipment): JsonResponse
    {
        $validated = $request->validate([
            'event_type' => ['required', 'in:STATUS_CHANGE,CHECKPOINT_REACHED,DEPARTURE,ARRIVAL,CUSTOMS_CLEARED,EXCEPTION_NOTED'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'location_name_custom' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'event_time' => ['required', 'date'],
            'shipment_status_at_event' => ['required', 'in:CREATED,BOOKED,PICKED_UP,IN_TRANSIT,DELIVERED'],
            'description' => ['required', 'string'],
        ]);

        $event = TrackingEvent::create([
            'shipment_id' => $shipment->id,
            'event_type' => $validated['event_type'],
            'location_id' => $validated['location_id'] ?? null,
            'location_name_custom' => $validated['location_name_custom'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'event_time' => $validated['event_time'],
            'shipment_status_at_event' => $validated['shipment_status_at_event'],
            'description' => $validated['description'],
            'created_by' => $request->user()->id,
        ]);

        // Synchronize shipment status & current location
        $shipment->status = $validated['shipment_status_at_event'];
        if (! empty($validated['location_id'])) {
            $shipment->current_location_id = $validated['location_id'];
        }
        $shipment->save();

        return response()->json([
            'success' => true,
            'message' => 'Tracking event berhasil ditambahkan (append-only).',
            'data' => $event,
        ], 201);
    }

    public function exceptions(Request $request, Shipment $shipment): JsonResponse
    {
        $exceptions = $shipment->exceptions()->with(['reporter', 'resolver'])->get();

        return response()->json([
            'success' => true,
            'data' => $exceptions,
        ]);
    }

    public function addException(Request $request, Shipment $shipment): JsonResponse
    {
        $validated = $request->validate([
            'exception_type' => ['required', 'in:CUSTOMS_HOLD,WEATHER_DELAY,PORT_CONGESTION,DAMAGED,MISROUTED,DOCUMENTATION_ISSUE'],
            'severity' => ['required', 'in:LOW,MEDIUM,HIGH,CRITICAL'],
            'description' => ['required', 'string'],
        ]);

        $exception = ShipmentException::create([
            'shipment_id' => $shipment->id,
            'exception_type' => $validated['exception_type'],
            'severity' => $validated['severity'],
            'status' => ShipmentException::STATUS_OPEN,
            'description' => $validated['description'],
            'reported_by' => $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Exception berhasil dilaporkan.',
            'data' => $exception,
        ], 201);
    }
}

