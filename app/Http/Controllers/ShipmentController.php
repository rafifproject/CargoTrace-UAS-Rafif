<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ShipmentController extends Controller
{
    /**
     * Halaman 02: Semua Pengiriman (All Shipments)
     */
    public function index(Request $request): View|Response
    {
        $query = Shipment::with(['customer', 'carrier', 'origin', 'destination', 'currentLocation'])
            ->withCount('openExceptions');

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('shipment_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$search}%"))
                    ->orWhere('cargo_type', 'like', "%{$search}%");
            });
        }

        // Filter Status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter Carrier
        if ($carrierId = $request->input('carrier_id')) {
            $query->where('carrier_id', $carrierId);
        }

        // Filter Origin Location
        if ($originId = $request->input('origin_id')) {
            $query->where('origin_location_id', $originId);
        }

        // Filter Destination Location
        if ($destinationId = $request->input('destination_id')) {
            $query->where('destination_location_id', $destinationId);
        }

        // Filter Delay
        if ($delay = $request->input('delay')) {
            if ($delay === 'delayed') {
                $query->where('delay_minutes', '>', 0);
            } elseif ($delay === 'ontime') {
                $query->where('delay_minutes', '<=', 0);
            }
        }

        // Date Range
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('expected_departure', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('expected_departure', '<=', $dateTo);
        }

        // Export CSV Handler
        if ($request->input('export') === 'csv') {
            $shipmentsToExport = (clone $query)->orderBy('created_at', 'desc')->get();
            $csvHeaders = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="cargotrace_shipments_' . date('Ymd_His') . '.csv"',
            ];

            $callback = function () use ($shipmentsToExport) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['No. Pengiriman', 'Pelanggan', 'Ekspedisi', 'Asal', 'Tujuan', 'Status', 'ETA', 'Keterlambatan (menit)', 'Keparahan']);

                foreach ($shipmentsToExport as $s) {
                    fputcsv($handle, [
                        $s->shipment_number,
                        $s->customer->name ?? '-',
                        $s->carrier->name ?? '-',
                        $s->origin->name ?? '-',
                        $s->destination->name ?? '-',
                        $s->status,
                        $s->estimated_time_arrival->format('Y-m-d H:i'),
                        $s->delay_minutes,
                        $s->delay_severity,
                    ]);
                }
                fclose($handle);
            };

            return response()->stream($callback, 200, $csvHeaders);
        }

        // Sorting & Pagination
        $sortColumn = $request->input('sort', 'created_at');
        $sortDirection = $request->input('dir', 'desc');
        $shipments = $query->orderBy($sortColumn, $sortDirection)->paginate(10)->withQueryString();

        // Filter Dropdowns
        $carriers = Carrier::where('status', 'ACTIVE')->get();
        $locations = Location::where('status', 'ACTIVE')->get();

        return view('admin.shipments.index', compact('shipments', 'carriers', 'locations'));
    }

    /**
     * Halaman 03: Detail Pengiriman (Shipment Detail)
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

        return view('admin.shipments.show', compact('shipment'));
    }

    /**
     * Halaman 04: Riwayat Pelacakan (Tracking Timeline)
     */
    public function tracking(Request $request): View
    {
        $selectedShipment = null;
        if ($shipmentId = $request->input('shipment_id')) {
            $selectedShipment = Shipment::with(['origin', 'destination', 'carrier', 'customer'])->find($shipmentId);
        }

        $allShipments = Shipment::orderBy('shipment_number', 'asc')->get();

        $query = TrackingEvent::with(['shipment.carrier', 'shipment.customer', 'location', 'creator']);

        if ($selectedShipment) {
            $query->where('shipment_id', $selectedShipment->id);
        }

        $events = $query->orderBy('event_time', 'desc')->paginate(15)->withQueryString();

        return view('admin.shipments.tracking', compact('events', 'allShipments', 'selectedShipment'));
    }

    /**
     * Halaman 05: Peta Pengiriman (Shipment Map)
     */
    public function map(Request $request): View
    {
        $selectedShipmentId = $request->input('shipment_id');
        $allShipments = Shipment::with(['origin', 'destination', 'currentLocation', 'carrier'])->get();

        $mapShipments = Shipment::with(['origin', 'destination', 'currentLocation', 'carrier', 'trackingEvents.location'])
            ->when($selectedShipmentId, fn ($q) => $q->where('id', $selectedShipmentId))
            ->get();

        return view('admin.shipments.map', compact('mapShipments', 'allShipments', 'selectedShipmentId'));
    }
}

