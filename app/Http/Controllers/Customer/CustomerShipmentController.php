<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerShipmentController extends Controller
{
    /**
     * Mockup 04: Pengiriman Saya (Customer Shipments List)
     */
    public function index(Request $request): View
    {
        // Anti-IDOR: strictly scoped to authenticated customer
        $query = $request->user()->shipments()
            ->with(['carrier', 'origin', 'destination', 'currentLocation'])
            ->withCount('openExceptions');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('shipment_number', 'like', "%{$search}%")
                    ->orWhere('cargo_type', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($delay = $request->input('delay')) {
            if ($delay === 'delayed') {
                $query->where('delay_minutes', '>', 0);
            } elseif ($delay === 'ontime') {
                $query->where('delay_minutes', '<=', 0);
            }
        }

        $shipments = $query->orderBy('updated_at', 'desc')->paginate(10)->withQueryString();

        return view('customer.shipments.index', compact('shipments'));
    }

    /**
     * Mockup 05: Detail Pengiriman (Customer View with Stepper & Anti-IDOR)
     */
    public function show(Request $request, int|string $id): View
    {
        // Anti-IDOR: findOrFail on user's own shipments relation, aborts 404/403 if belonging to someone else
        $shipment = $request->user()->shipments()
            ->with([
                'carrier',
                'origin',
                'destination',
                'currentLocation',
                'items',
                'trackingEvents.location',
                'exceptions',
            ])
            ->where(function ($q) use ($id) {
                if (is_numeric($id)) {
                    $q->where('id', $id)->orWhere('shipment_number', $id);
                } else {
                    $q->where('shipment_number', $id);
                }
            })
            ->firstOrFail();

        return view('customer.shipments.show', compact('shipment'));
    }

    /**
     * Mockup 06: Riwayat Pelacakan (Tracking Timeline for Customer)
     */
    public function tracking(Request $request): View
    {
        $user = $request->user();
        $myShipments = $user->shipments()->orderBy('shipment_number')->get();

        $selectedShipment = null;
        if ($shipmentId = $request->input('shipment_id')) {
            $selectedShipment = $user->shipments()->find($shipmentId);
        } elseif ($myShipments->isNotEmpty()) {
            $selectedShipment = $myShipments->first();
        }

        $events = collect();
        if ($selectedShipment) {
            $events = TrackingEvent::with(['location'])
                ->where('shipment_id', $selectedShipment->id)
                ->orderBy('event_time', 'desc')
                ->get();
        }

        return view('customer.shipments.tracking', compact('myShipments', 'selectedShipment', 'events'));
    }

    /**
     * Mockup 07: Peta Pengiriman
     */
    public function map(Request $request): View
    {
        $user = $request->user();
        $myShipments = $user->shipments()->with(['origin', 'destination', 'currentLocation', 'carrier'])->get();
        $allShipments = $myShipments;

        $selectedShipmentId = $request->input('shipment_id');
        $mapShipments = $user->shipments()
            ->with(['origin', 'destination', 'currentLocation', 'carrier', 'trackingEvents.location'])
            ->when($selectedShipmentId, fn ($q) => $q->where('id', $selectedShipmentId))
            ->get();

        return view('customer.shipments.map', compact('mapShipments', 'myShipments', 'allShipments', 'selectedShipmentId'));
    }
}

