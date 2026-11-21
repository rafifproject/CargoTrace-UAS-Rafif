<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\TrackingEvent;
use Illuminate\Http\RedirectResponse;
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
                    ->orWhere('cargo_type', 'like', "%{$search}%")
                    ->orWhere('coffee_variety', 'like', "%{$search}%")
                    ->orWhere('delivery_city', 'like', "%{$search}%")
                    ->orWhere('delivery_address', 'like', "%{$search}%")
                    ->orWhere('courier_name', 'like', "%{$search}%")
                    ->orWhere('courier_vehicle', 'like', "%{$search}%")
                    ->orWhereHas('carrier', function ($c) use ($search) {
                        $c->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('origin', function ($o) use ($search) {
                        $o->where('name', 'like', "%{$search}%")
                            ->orWhere('city', 'like', "%{$search}%")
                            ->orWhere('country', 'like', "%{$search}%");
                    })
                    ->orWhereHas('destination', function ($d) use ($search) {
                        $d->where('name', 'like', "%{$search}%")
                            ->orWhere('city', 'like', "%{$search}%")
                            ->orWhere('country', 'like', "%{$search}%");
                    });
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($transportMode = $request->input('transport_mode')) {
            $query->where('transport_mode', $transportMode);
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
     * Formulir Pemesanan Pengiriman Kargo Baru oleh Pelanggan
     */
    public function create(): View
    {
        $carriers = Carrier::where('status', Carrier::STATUS_ACTIVE)->orderBy('name', 'asc')->get();
        $locations = Location::where('status', Location::STATUS_ACTIVE)->orderBy('name', 'asc')->get();
        $portLocations = Location::where('status', Location::STATUS_ACTIVE)->where('location_type', Location::TYPE_PORT)->orderBy('name', 'asc')->get();
        $airportLocations = Location::where('status', Location::STATUS_ACTIVE)->where('location_type', Location::TYPE_AIRPORT)->orderBy('name', 'asc')->get();
        $countries = app(\App\Services\GeoService::class)->getCountries();

        $count = Shipment::count() + 1;
        $suggestedNumber = 'CT-2026-' . str_pad((string) $count, 3, '0', STR_PAD_LEFT);

        return view('customer.shipments.create', compact('carriers', 'locations', 'portLocations', 'airportLocations', 'countries', 'suggestedNumber'));
    }

    /**
     * Simpan Pemesanan Pengiriman Kargo oleh Pelanggan
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'carrier_id' => ['required', 'exists:carriers,id'],
            'transport_mode' => ['nullable', 'in:SEA,AIR,LAND'],
            'origin_location_id' => ['required', 'exists:locations,id'],
            'destination_location_id' => ['required', 'exists:locations,id', 'different:origin_location_id'],
            'delivery_country' => ['nullable', 'string', 'max:100'],
            'delivery_city' => ['nullable', 'string', 'max:100'],
            'delivery_district' => ['nullable', 'string', 'max:150'],
            'delivery_address' => ['nullable', 'string', 'max:500'],
            'delivery_lat' => ['nullable', 'numeric'],
            'delivery_lng' => ['nullable', 'numeric'],
            'coffee_variety' => ['nullable', 'string', 'max:100'],
            'coffee_grade' => ['nullable', 'string', 'max:100'],
            'is_last_mile' => ['nullable', 'boolean'],
            'cargo_type' => ['required', 'string', 'max:100'],
            'weight' => ['required', 'numeric', 'min:0.1'],
            'volume' => ['required', 'numeric', 'min:0.1'],
            'expected_departure' => ['required', 'date'],
            'expected_arrival' => ['required', 'date', 'after:expected_departure'],
            'notes' => ['nullable', 'string', 'max:500'],
            'item_name' => ['nullable', 'string', 'max:150'],
            'hs_code' => ['nullable', 'string', 'max:30'],
            'item_qty' => ['nullable', 'integer', 'min:1'],
            'item_value' => ['nullable', 'numeric', 'min:0'],
        ], [
            'carrier_id.required' => 'Pilih perusahaan ekspedisi mitra.',
            'transport_mode.required' => 'Pilih jenis pengiriman (Kapal Laut, Pesawat Udara, atau Mobil Darat).',
            'origin_location_id.required' => 'Pilih titik asal kargo.',
            'destination_location_id.required' => 'Pilih titik tujuan kargo.',
            'destination_location_id.different' => 'Lokasi tujuan tidak boleh sama dengan lokasi asal.',
            'expected_arrival.after' => 'Estimasi kedatangan harus setelah waktu keberangkatan.',
        ]);

        $count = Shipment::count() + 1;
        $shipmentNumber = 'CT-2026-' . str_pad((string) $count, 3, '0', STR_PAD_LEFT);
        while (Shipment::where('shipment_number', $shipmentNumber)->exists()) {
            $count++;
            $shipmentNumber = 'CT-2026-' . str_pad((string) $count, 3, '0', STR_PAD_LEFT);
        }

        $shipment = new Shipment();
        $shipment->shipment_number = $shipmentNumber;
        $shipment->customer_id = $request->user()->id;
        $shipment->carrier_id = $validated['carrier_id'];
        $shipment->transport_mode = $validated['transport_mode'] ?? Shipment::TRANSPORT_SEA;
        $shipment->origin_location_id = $validated['origin_location_id'];
        $shipment->destination_location_id = $validated['destination_location_id'];
        $shipment->current_location_id = $validated['origin_location_id'];

        $shipment->delivery_country = $validated['delivery_country'] ?? null;
        $shipment->delivery_city = $validated['delivery_city'] ?? null;
        $shipment->delivery_district = $validated['delivery_district'] ?? null;
        $shipment->delivery_address = $validated['delivery_address'] ?? null;
        $shipment->delivery_lat = !empty($validated['delivery_lat']) ? (float)$validated['delivery_lat'] : null;
        $shipment->delivery_lng = !empty($validated['delivery_lng']) ? (float)$validated['delivery_lng'] : null;
        $shipment->coffee_variety = $validated['coffee_variety'] ?? 'Kopi Brazil Santos Bourbon Specialty';
        $shipment->coffee_grade = $validated['coffee_grade'] ?? 'Specialty Grade 1 (SCA 84+)';

        $isLastMile = ($validated['transport_mode'] ?? '') === Shipment::TRANSPORT_LAND || $request->boolean('is_last_mile');
        $shipment->is_last_mile = $isLastMile;
        if ($isLastMile) {
            $shipment->courier_name = 'Armada Kurir Mobil Kopi Santos';
            $shipment->courier_vehicle = 'Mobil Van Distribusi Kopi (B 9482 CT)';
        }

        // Resolusi otomatis koordinat alamat jika belum terisi via Geoapify
        if ((empty($shipment->delivery_lat) || empty($shipment->delivery_lng)) && (!empty($shipment->delivery_address) || !empty($shipment->delivery_city))) {
            $geoResult = app(\App\Services\GeoService::class)->geocode(
                $shipment->delivery_address ?? '',
                $shipment->delivery_country,
                $shipment->delivery_city,
                $shipment->delivery_district
            );
            if ($geoResult && !empty($geoResult['lat'])) {
                $shipment->delivery_lat = $geoResult['lat'];
                $shipment->delivery_lng = $geoResult['lng'];
            }
        }

        $shipment->cargo_type = $validated['cargo_type'];
        $shipment->weight = $validated['weight'];
        $shipment->volume = $validated['volume'];

        // Hitung estimasi biaya pengiriman/freight transparan berdasarkan moda & berat
        $transMode = $validated['transport_mode'] ?? Shipment::TRANSPORT_SEA;
        $weightVal = (float) $validated['weight'];
        if ($transMode === Shipment::TRANSPORT_AIR) {
            $ratePerKg = 2.80;
            $handlingFee = 250.00;
        } elseif ($transMode === Shipment::TRANSPORT_LAND) {
            $ratePerKg = 0.85;
            $handlingFee = 150.00;
        } else {
            $ratePerKg = 0.35;
            $handlingFee = 250.00;
        }
        $shipment->shipping_cost = round(($weightVal * $ratePerKg) + $handlingFee, 2);
        $shipment->invoice_number = 'INV-' . date('Y') . '-' . strtoupper(\Illuminate\Support\Str::random(6));
        $shipment->payment_status = 'UNPAID';

        $shipment->expected_departure = $validated['expected_departure'];
        $shipment->expected_arrival = $validated['expected_arrival'];
        $shipment->estimated_time_arrival = $validated['expected_arrival'];
        $shipment->status = Shipment::STATUS_BOOKED;
        $shipment->notes = $validated['notes'] ?? null;
        $shipment->delay_minutes = 0;
        $shipment->delay_severity = Shipment::DELAY_NONE;
        $shipment->save();

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

        $origin = Location::find($shipment->origin_location_id);
        $dest = Location::find($shipment->destination_location_id);

        TrackingEvent::create([
            'shipment_id' => $shipment->id,
            'event_type' => TrackingEvent::TYPE_STATUS_CHANGE,
            'location_id' => $shipment->origin_location_id,
            'latitude' => $origin ? $origin->latitude : null,
            'longitude' => $origin ? $origin->longitude : null,
            'event_time' => now(),
            'shipment_status_at_event' => Shipment::STATUS_BOOKED,
            'description' => "Pemesanan pengiriman baru (#{$shipment->shipment_number}) berhasil diajukan oleh Pelanggan {$request->user()->name}. Rute: {$origin->name} menuju {$dest->name}. Menunggu jadwal pemuatan kapal.",
            'created_by' => $request->user()->id,
        ]);

        Notification::create([
            'user_id' => $request->user()->id,
            'shipment_id' => $shipment->id,
            'type' => Notification::TYPE_STATUS_UPDATE,
            'severity' => Notification::SEVERITY_SUCCESS,
            'title' => "Pesanan Kargo Berhasil Dibuat: {$shipment->shipment_number}",
            'message' => "Pemesanan pengiriman Anda rute {$origin->name} -> {$dest->name} telah terdaftar dan menunggu proses pemuatan armada.",
            'is_read' => false,
        ]);

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'CREATE',
            'entity_type' => 'Shipment',
            'entity_id' => (string) $shipment->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => "Pelanggan {$request->user()->name} membuat pesanan pengiriman baru: {$shipment->shipment_number}",
            'new_values' => $validated,
            'created_at' => now(),
        ]);

        return redirect()->route('customer.shipments.show', $shipment)
            ->with('status', "Pesanan pengiriman {$shipment->shipment_number} berhasil dibuat dan telah terdaftar!");
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
            $selectedShipment = $user->shipments()
                ->where(function ($q) use ($shipmentId) {
                    if (is_numeric($shipmentId)) {
                        $q->where('id', $shipmentId)->orWhere('shipment_number', $shipmentId);
                    } else {
                        $q->where('shipment_number', $shipmentId);
                    }
                })->first();
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
        $selectedCarrierId  = $request->input('carrier_id');
        $selectedTransportMode = $request->input('transport_mode');

        $carrierIds = $myShipments->pluck('carrier_id')->filter()->unique();
        $carriers = Carrier::whereIn('id', $carrierIds)->orderBy('name', 'asc')->get();

        $mapShipments = $user->shipments()
            ->with(['origin', 'destination', 'currentLocation', 'carrier', 'trackingEvents.location'])
            ->when($selectedShipmentId, fn ($q) => $q->where('id', $selectedShipmentId))
            ->when($selectedCarrierId, fn ($q) => $q->where('carrier_id', $selectedCarrierId))
            ->when($selectedTransportMode, fn ($q) => $q->where('transport_mode', $selectedTransportMode))
            ->get();

        $routeHubs = Location::where('status', Location::STATUS_ACTIVE)->orderBy('name', 'asc')->get();
        $countriesList = app(\App\Services\GlobalIntelligenceService::class)->getCountriesMaster();

        return view('customer.shipments.map', compact('mapShipments', 'myShipments', 'allShipments', 'carriers', 'selectedShipmentId', 'selectedCarrierId', 'selectedTransportMode', 'routeHubs', 'countriesList'));
    }

    /**
     * Simulasi Pembayaran Proforma Invoice Ekspor B2B via Bank Wire Transfer (T/T SWIFT)
     */
    public function pay(Request $request, int|string $id): RedirectResponse
    {
        $shipment = $request->user()->shipments()
            ->where(function ($q) use ($id) {
                if (is_numeric($id)) {
                    $q->where('id', $id)->orWhere('shipment_number', $id);
                } else {
                    $q->where('shipment_number', $id);
                }
            })
            ->firstOrFail();

        if ($shipment->payment_status === 'PAID') {
            return back()->with('info', "Proforma Invoice #{$shipment->invoice_number} sudah berstatus LUNAS.");
        }

        $shipment->payment_status = 'PAID';
        $shipment->payment_method = 'T/T Bank Wire (SWIFT Transfer)';
        $shipment->paid_at = now();
        $shipment->save();

        $origin = Location::find($shipment->origin_location_id);

        TrackingEvent::create([
            'shipment_id' => $shipment->id,
            'event_type' => TrackingEvent::TYPE_STATUS_CHANGE,
            'location_id' => $shipment->origin_location_id,
            'latitude' => $origin ? $origin->latitude : null,
            'longitude' => $origin ? $origin->longitude : null,
            'event_time' => now(),
            'shipment_status_at_event' => $shipment->status,
            'description' => "Pembayaran Proforma Invoice #{$shipment->invoice_number} terverifikasi via Bank Wire Transfer (T/T via SWIFT). Dana freight & komoditas settled. Kargo siap diproses oleh operator.",
            'created_by' => $request->user()->id,
        ]);

        Notification::create([
            'user_id' => $request->user()->id,
            'shipment_id' => $shipment->id,
            'type' => Notification::TYPE_STATUS_UPDATE,
            'severity' => Notification::SEVERITY_SUCCESS,
            'title' => "Pembayaran Invoice Terverifikasi: {$shipment->invoice_number}",
            'message' => "Proforma Invoice kargo #{$shipment->shipment_number} berhasil diselesaikan. Operator logistik sedang menyiapkan proses pemuatan kargo.",
            'is_read' => false,
        ]);

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'PAYMENT_SETTLED',
            'entity_type' => 'Shipment',
            'entity_id' => (string) $shipment->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => "Pelanggan {$request->user()->name} menyelesaikan pembayaran Proforma Invoice #{$shipment->invoice_number} via T/T Bank Wire",
            'new_values' => [
                'payment_status' => 'PAID',
                'payment_method' => 'T/T Bank Wire (SWIFT Transfer)',
                'paid_at' => now()->toIso8601String(),
                'total_amount' => $shipment->total_invoice_amount,
            ],
            'created_at' => now(),
        ]);

        return redirect()->route('customer.shipments.show', $shipment->id)
            ->with('status', "Pembayaran Proforma Invoice #{$shipment->invoice_number} berhasil diverifikasi! Dana Freight telah diselesaikan (Settled).");
    }
}
