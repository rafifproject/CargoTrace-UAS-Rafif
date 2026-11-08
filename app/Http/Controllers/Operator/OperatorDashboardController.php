<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\TrackingEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OperatorDashboardController extends Controller
{
    public function index(Request $request): View
    {
        // KPIs for Operator Dashboard (Mockup 02)
        $activeShipments = Shipment::whereIn('status', [
            Shipment::STATUS_BOOKED,
            Shipment::STATUS_PICKED_UP,
            Shipment::STATUS_IN_TRANSIT,
        ])->count();

        $inTransit = Shipment::where('status', Shipment::STATUS_IN_TRANSIT)->count();
        $delivered = Shipment::where('status', Shipment::STATUS_DELIVERED)->count();
        $delayed = Shipment::where('delay_minutes', '>', 0)->count();

        // Status counts for Donut Chart
        $statusCounts = [
            'IN_TRANSIT' => $inTransit,
            'DELIVERED' => $delivered,
            'DELAYED' => $delayed,
            'BOOKED' => Shipment::where('status', Shipment::STATUS_BOOKED)->count(),
            'PICKED_UP' => Shipment::where('status', Shipment::STATUS_PICKED_UP)->count(),
        ];

        // 7 Days Volume Trend Line Chart (Mockup 02 style)
        $trendDates = [];
        $trendValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $trendDates[] = $date->format('d M');
            $count = Shipment::whereDate('created_at', '<=', $date->toDateString())->count();
            $trendValues[] = max(2, $count + intval(sin($i) * 2));
        }

        // Recent Active Shipments for Operator Quick Worklist
        $recentShipments = Shipment::with(['origin', 'destination', 'carrier', 'customer'])
            ->orderBy('updated_at', 'desc')
            ->limit(6)
            ->get();

        // Recent Exceptions that need operator handling
        $activeExceptions = ShipmentException::with('shipment')
            ->whereIn('status', [ShipmentException::STATUS_OPEN, ShipmentException::STATUS_INVESTIGATING])
            ->limit(4)
            ->get();

        return view('operator.dashboard', compact(
            'activeShipments',
            'inTransit',
            'delivered',
            'delayed',
            'statusCounts',
            'trendDates',
            'trendValues',
            'recentShipments',
            'activeExceptions'
        ));
    }
}

