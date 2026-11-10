<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\TrackingEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OperatorShipmentController extends Controller
{
    /**
     * Mockup 03: Semua Pengiriman (Operator View)
     */
    public function index(Request $request): View
    {
        $query = Shipment::with(['customer', 'carrier', 'origin', 'destination', 'currentLocation'])
            ->withCount('openExceptions');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('shipment_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$search}%"))
                    ->orWhere('cargo_type', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($carrierId = $request->input('carrier_id')) {
            $query->where('carrier_id', $carrierId);
        }

        if ($delay = $request->input('delay')) {
            if ($delay === 'delayed') {
                $query->where('delay_minutes', '>', 0);
            } elseif ($delay === 'ontime') {
                $query->where('delay_minutes', '<=', 0);
            }
        }

        $shipments = $query->orderBy('updated_at', 'desc')->paginate(10)->withQueryString();
        $carriers = Carrier::where('status', 'ACTIVE')->get();

        return view('operator.shipments.index', compact('shipments', 'carriers'));
    }

    /**
     * Mockup 04: Buat Pengiriman Baru
     */
    public function create(): View
    {
        $customers = User::where('role', User::ROLE_CUSTOMER)->where('status', User::STATUS_ACTIVE)->orderBy('name', 'asc')->get();
        $carriers = Carrier::where('status', Carrier::STATUS_ACTIVE)->orderBy('name', 'asc')->get();
        $locations = Location::where('status', Location::STATUS_ACTIVE)->orderBy('name', 'asc')->get();

        // Auto-generate next shipment number CT-2026-XXX
        $count = Shipment::count() + 1;
        $suggestedNumber = 'CT-2026-' . str_pad((string) $count, 3, '0', STR_PAD_LEFT);

        return view('operator.shipments.create', compact('customers', 'carriers', 'locations', 'suggestedNumber'));
    }

    /**
     * Simpan Pengiriman Baru (Store)
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'shipment_number' => ['required', 'string', 'max:50', 'unique:shipments,shipment_number'],
            'customer_id' => ['required', 'exists:users,id'],
            'carrier_id' => ['required', 'exists:carriers,id'],
            'origin_location_id' => ['required', 'exists:locations,id'],
            'destination_location_id' => ['required', 'exists:locations,id', 'different:origin_location_id'],
            'cargo_type' => ['required', 'string', 'max:100'],
            'weight' => ['required', 'numeric', 'min:0.1'],
            'volume' => ['required', 'numeric', 'min:0.1'],
            'expected_departure' => ['required', 'date'],
            'expected_arrival' => ['required', 'date', 'after:expected_departure'],
            'estimated_time_arrival' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            // Optional first item
            'item_name' => ['nullable', 'string', 'max:150'],
            'hs_code' => ['nullable', 'string', 'max:30'],
            'item_qty' => ['nullable', 'integer', 'min:1'],
            'item_value' => ['nullable', 'numeric', 'min:0'],
        ], [
            'shipment_number.required' => 'Nomor pengiriman wajib diisi.',
            'shipment_number.unique' => 'Nomor pengiriman sudah terdaftar.',
            'customer_id.required' => 'Pilih pelanggan kargo.',
            'carrier_id.required' => 'Pilih perusahaan ekspedisi.',
            'origin_location_id.required' => 'Pilih lokasi pelabuhan/bandara asal.',
            'destination_location_id.required' => 'Pilih lokasi tujuan.',
            'destination_location_id.different' => 'Lokasi tujuan tidak boleh sama dengan lokasi asal.',
            'expected_arrival.after' => 'Waktu kedatangan harus setelah waktu keberangkatan.',
        ]);

        $shipment = new Shipment($validated);
        $shipment->current_location_id = $validated['origin_location_id'];
        $shipment->status = Shipment::STATUS_CREATED;
        $shipment->recalculateDelay();
        $shipment->save();

        // If item provided, save initial shipment item
        if (! empty($validated['item_name'])) {
            ShipmentItem::create([
                'shipment_id' => $shipment->id,
                'item_name' => $validated['item_name'],
                'hs_code' => $validated['hs_code'] ?? null,
                'quantity' => $validated['item_qty'] ?? 1,
                'weight' => $validated['weight'],
                'declared_value' => $validated['item_value'] ?? 0.00,
                'currency' => 'USD',
            ]);
        }

        // Auto-create initial tracking event (append-only)
        TrackingEvent::create([
            'shipment_id' => $shipment->id,
            'event_type' => TrackingEvent::TYPE_STATUS_CHANGE,
            'location_id' => $shipment->origin_location_id,
            'latitude' => $shipment->origin->latitude,
            'longitude' => $shipment->origin->longitude,
            'event_time' => now(),
            'shipment_status_at_event' => Shipment::STATUS_CREATED,
            'description' => "Pengiriman kargo dibuat oleh Operator Logistik ({$request->user()->name}). Menunggu konfirmasi penjemputan.",
            'created_by' => $request->user()->id,
        ]);

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'CREATE',
            'entity_type' => 'Shipment',
            'entity_id' => (string) $shipment->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => "Operator {$request->user()->name} menerbitkan pengiriman kargo baru: {$shipment->shipment_number}",
            'new_values' => $validated,
            'created_at' => now(),
        ]);

        return redirect()->route('operator.shipments.show', $shipment)->with('status', "Pengiriman {$shipment->shipment_number} berhasil dibuat!");
    }

    /**
     * Mockup 05: Detail Pengiriman (Operator View with Stepper)
     */
    public function show(Shipment $shipment): View
    {
        $shipment->load([
            'customer',
            'carrier',
            'origin',
            'destination',
            'currentLocation',
            'items',
            'trackingEvents.location',
            'trackingEvents.creator',
            'exceptions.reporter',
            'exceptions.resolver',
        ]);

        $locations = Location::where('status', Location::STATUS_ACTIVE)->get();

        return view('operator.shipments.show', compact('shipment', 'locations'));
    }

    /**
     * Edit Pengiriman Form
     */
    public function edit(Shipment $shipment): View
    {
        $customers = User::where('role', User::ROLE_CUSTOMER)->where('status', User::STATUS_ACTIVE)->get();
        $carriers = Carrier::where('status', Carrier::STATUS_ACTIVE)->get();
        $locations = Location::where('status', Location::STATUS_ACTIVE)->get();

        return view('operator.shipments.edit', compact('shipment', 'customers', 'carriers', 'locations'));
    }

    /**
     * Update Pengiriman
     */
    public function update(Request $request, Shipment $shipment): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:CREATED,BOOKED,PICKED_UP,IN_TRANSIT,DELIVERED'],
            'current_location_id' => ['required', 'exists:locations,id'],
            'actual_departure' => ['nullable', 'date'],
            'actual_arrival' => ['nullable', 'date'],
            'estimated_time_arrival' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $oldStatus = $shipment->status;
        $oldLocation = $shipment->current_location_id;

        $shipment->fill($validated);
        $shipment->recalculateDelay();
        $shipment->save();

        // If status changed or location changed, auto-record tracking event
        if ($oldStatus !== $validated['status'] || $oldLocation != $validated['current_location_id']) {
            $loc = Location::find($validated['current_location_id']);
            TrackingEvent::create([
                'shipment_id' => $shipment->id,
                'event_type' => TrackingEvent::TYPE_STATUS_CHANGE,
                'location_id' => $loc->id,
                'latitude' => $loc->latitude,
                'longitude' => $loc->longitude,
                'event_time' => now(),
                'shipment_status_at_event' => $validated['status'],
                'description' => "Status kargo diperbarui menjadi '{$validated['status']}' di lokasi {$loc->name}.",
                'created_by' => $request->user()->id,
            ]);
        }

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'UPDATE',
            'entity_type' => 'Shipment',
            'entity_id' => (string) $shipment->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => "Operator {$request->user()->name} memperbarui status kargo {$shipment->shipment_number} ({$oldStatus} -> {$validated['status']})",
            'new_values' => $validated,
            'created_at' => now(),
        ]);

        return redirect()->route('operator.shipments.show', $shipment)->with('status', 'Data pengiriman berhasil diperbarui.');
    }

    /**
     * Mockup 06: Riwayat Pelacakan (Tracking Timeline)
     */
    public function tracking(Request $request): View
    {
        $selectedShipment = null;
        if ($shipmentId = $request->input('shipment_id')) {
            $selectedShipment = Shipment::with(['origin', 'destination', 'carrier', 'customer'])->find($shipmentId);
        }

        $allShipments = Shipment::orderBy('shipment_number', 'asc')->get();
        $locations = Location::where('status', Location::STATUS_ACTIVE)->get();

        $query = TrackingEvent::with(['shipment.carrier', 'shipment.customer', 'location', 'creator']);

        if ($selectedShipment) {
            $query->where('shipment_id', $selectedShipment->id);
        }

        $events = $query->orderBy('event_time', 'desc')->paginate(15)->withQueryString();

        return view('operator.shipments.tracking', compact('events', 'allShipments', 'selectedShipment', 'locations'));
    }

    /**
     * Catat Event Pelacakan Baru (Append-Only)
     */
    public function addTrackingEvent(Request $request, Shipment $shipment): RedirectResponse
    {
        $validated = $request->validate([
            'event_type' => ['required', 'in:STATUS_CHANGE,CHECKPOINT_REACHED,DEPARTURE,ARRIVAL,CUSTOMS_CLEARED,EXCEPTION_NOTED'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'location_name_custom' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'event_time' => ['required', 'date'],
            'shipment_status_at_event' => ['required', 'in:CREATED,BOOKED,PICKED_UP,IN_TRANSIT,DELIVERED'],
            'description' => ['required', 'string', 'min:5'],
        ], [
            'description.required' => 'Deskripsi event checkpoint wajib diisi.',
            'event_time.required' => 'Waktu event wajib ditentukan.',
        ]);

        $loc = ! empty($validated['location_id']) ? Location::find($validated['location_id']) : null;

        TrackingEvent::create([
            'shipment_id' => $shipment->id,
            'event_type' => $validated['event_type'],
            'location_id' => $validated['location_id'] ?? null,
            'location_name_custom' => $validated['location_name_custom'] ?? null,
            'latitude' => $loc ? $loc->latitude : ($validated['latitude'] ?? null),
            'longitude' => $loc ? $loc->longitude : ($validated['longitude'] ?? null),
            'event_time' => Carbon::parse($validated['event_time']),
            'shipment_status_at_event' => $validated['shipment_status_at_event'],
            'description' => $validated['description'],
            'created_by' => $request->user()->id,
        ]);

        // Sync current status & current location on shipment
        $shipment->status = $validated['shipment_status_at_event'];
        if ($loc) {
            $shipment->current_location_id = $loc->id;
        }
        $shipment->save();

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'STATUS_CHANGE',
            'entity_type' => 'TrackingEvent',
            'entity_id' => (string) $shipment->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => "Operator {$request->user()->name} mencatat event '{$validated['event_type']}' pada kargo {$shipment->shipment_number}.",
            'created_at' => now(),
        ]);

        return back()->with('status', 'Event pelacakan baru berhasil dicatat ke timeline.');
    }

    /**
     * Mockup 07: Peta Pengiriman
     */
    public function map(Request $request): View
    {
        $selectedShipmentId = $request->input('shipment_id');
        $selectedCarrierId  = $request->input('carrier_id');
        $allShipments = Shipment::with(['origin', 'destination', 'currentLocation', 'carrier'])->get();
        $carriers = Carrier::where('status', Carrier::STATUS_ACTIVE)->orderBy('name', 'asc')->get();

        $mapShipments = Shipment::with(['origin', 'destination', 'currentLocation', 'carrier', 'trackingEvents.location'])
            ->when($selectedShipmentId, fn ($q) => $q->where('id', $selectedShipmentId))
            ->when($selectedCarrierId, fn ($q) => $q->where('carrier_id', $selectedCarrierId))
            ->get();

        return view('operator.shipments.map', compact('mapShipments', 'allShipments', 'carriers', 'selectedShipmentId', 'selectedCarrierId'));
    }
}

