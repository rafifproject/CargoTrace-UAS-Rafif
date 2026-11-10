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

        // Status Breakdown for Donut Chart
        $statusCounts = [
            'CREATED' => Shipment::where('status', Shipment::STATUS_CREATED)->count(),
            'BOOKED' => Shipment::where('status', Shipment::STATUS_BOOKED)->count(),
            'PICKED_UP' => Shipment::where('status', Shipment::STATUS_PICKED_UP)->count(),
            'IN_TRANSIT' => Shipment::where('status', Shipment::STATUS_IN_TRANSIT)->count(),
            'DELIVERED' => Shipment::where('status', Shipment::STATUS_DELIVERED)->count(),
        ];

        // 30 Days Trend Simulation from actual / generated dates
        $trendDates = [];
        $trendValues = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $trendDates[] = $date->format('d M');
            $count = Shipment::whereDate('created_at', '<=', $date->toDateString())->count();
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

        // Global Intelligence Data for 3-Mode Interactive Map
        $globalPorts    = $intelligenceService->getGlobalPorts();
        $weatherHubs    = $intelligenceService->getGlobalWeatherHubs();
        $countriesList  = $intelligenceService->getCountriesMaster();

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
            'globalPorts',
            'weatherHubs',
            'countriesList'
        ));
    }
}

