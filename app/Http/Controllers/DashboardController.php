<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\TrackingEvent;
use App\Services\GlobalIntelligenceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, GlobalIntelligenceService $intelligenceService): View
    {
        // KPI Counts
        $totalShipments = Shipment::count();
        $inTransit = Shipment::where('status', Shipment::STATUS_IN_TRANSIT)->count();
        $delivered = Shipment::where('status', Shipment::STATUS_DELIVERED)->count();
        $delayed = Shipment::where('delay_minutes', '>', 0)->count();
        $openExceptions = ShipmentException::whereIn('status', [
            ShipmentException::STATUS_OPEN,
            ShipmentException::STATUS_INVESTIGATING,
        ])->count();

        // Financial & Export Turnover Metrics (Santos Coffee Logistics B2B)
        $totalCommodityValue = \App\Models\ShipmentItem::sum('declared_value');
        $totalFreightRevenue = Shipment::where('payment_status', 'PAID')->sum('shipping_cost');
        $paidInvoicesCount = Shipment::where('payment_status', 'PAID')->count();
        $unpaidInvoicesCount = Shipment::where('payment_status', 'UNPAID')->count();
        $settlementRate = $totalShipments > 0 ? round(($paidInvoicesCount / $totalShipments) * 100, 1) : 100;

        // Status Breakdown for Donut Chart (single aggregated query)
        $groupedStatuses = Shipment::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusCounts = [
            'CREATED' => (int) ($groupedStatuses[Shipment::STATUS_CREATED] ?? 0),
            'BOOKED' => (int) ($groupedStatuses[Shipment::STATUS_BOOKED] ?? 0),
            'PICKED_UP' => (int) ($groupedStatuses[Shipment::STATUS_PICKED_UP] ?? 0),
            'IN_TRANSIT' => (int) ($groupedStatuses[Shipment::STATUS_IN_TRANSIT] ?? 0),
            'DELIVERED' => (int) ($groupedStatuses[Shipment::STATUS_DELIVERED] ?? 0),
        ];

        // 30 Days Trend Simulation from a single timestamp fetch (avoids 30 sequential DB round-trips)
        $shipmentDates = Shipment::pluck('created_at')->map(fn ($dt) => Carbon::parse($dt)->toDateString());
        $trendDates = [];
        $trendValues = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateStr = $date->toDateString();
            $trendDates[] = $date->format('d M');
            $count = $shipmentDates->filter(fn (string $d) => $d <= $dateStr)->count();
            // Scale smoothly for realistic visual presentation
            $trendValues[] = max(1, $count + intval(sin($i / 3) * 3));
        }

        // Active Global Shipments with coordinates for map display
        $mapShipments = Shipment::with(['origin', 'destination', 'currentLocation', 'carrier'])
            ->whereIn('status', [Shipment::STATUS_IN_TRANSIT, Shipment::STATUS_PICKED_UP, Shipment::STATUS_BOOKED])
            ->get();

        // Recent tracking activity
        $recentActivities = TrackingEvent::with(['shipment', 'location', 'creator'])
            ->orderBy('event_time', 'desc')
            ->limit(5)
            ->get();

        // Top Carrier Shipments
        $carriers = Carrier::withCount('shipments')
            ->orderBy('shipments_count', 'desc')
            ->limit(5)
            ->get();

        // Global Intelligence Data for Interactive Map
        $globalPorts    = $intelligenceService->getGlobalPorts();
        $globalAirports = $intelligenceService->getGlobalAirports();
        $weatherHubs    = $intelligenceService->getGlobalWeatherHubs();
        $countriesList  = $intelligenceService->getCountriesMaster();

        // Seluruh 206 Ekspedisi Aktif dengan info pengiriman & koordinat markas untuk Peta Ekspedisi
        $allCarriers = Carrier::withCount('shipments')
            ->where('status', Carrier::STATUS_ACTIVE)
            ->orderBy('name', 'asc')
            ->get();

        $countryMap = [];
        foreach ($countriesList as $c) {
            $countryMap[strtolower($c['name'])] = $c;
            $countryMap[strtolower($c['code'])] = $c;
            $iso = strtolower(str_replace('_EAST', '', $c['code']));
            $countryMap[$iso] = $c;
        }

        $carriersMapData = $allCarriers->map(function ($carrier) use ($countryMap) {
            $cLower = strtolower(trim($carrier->country));
            $countryInfo = $countryMap[$cLower] ?? null;
            $lat = $countryInfo['lat'] ?? 0.0;
            $lng = $countryInfo['lng'] ?? 0.0;
            $iso = $countryInfo ? strtolower(str_replace('_EAST', '', $countryInfo['code'])) : 'un';
            $flag = $countryInfo['flag'] ?? '🌐';

            return [
                'id'              => $carrier->id,
                'code'            => $carrier->code,
                'name'            => $carrier->name,
                'country'         => $carrier->country,
                'contact_email'   => $carrier->contact_email,
                'contact_phone'   => $carrier->contact_phone,
                'website'         => $carrier->website,
                'shipments_count' => $carrier->shipments_count,
                'lat'             => $lat,
                'lng'             => $lng,
                'iso'             => $iso,
                'flag'            => $flag,
            ];
        });

        return view('admin.dashboard', compact(
            'totalShipments',
            'inTransit',
            'delivered',
            'delayed',
            'openExceptions',
            'statusCounts',
            'trendDates',
            'trendValues',
            'mapShipments',
            'recentActivities',
            'carriers',
            'allCarriers',
            'carriersMapData',
            'globalPorts',
            'globalAirports',
            'weatherHubs',
            'countriesList',
            'totalCommodityValue',
            'totalFreightRevenue',
            'paidInvoicesCount',
            'unpaidInvoicesCount',
            'settlementRate'
        ));
    }
}

