<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\ShipmentException;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerDashboardController extends Controller
{
    /**
     * Mockup 03: Beranda (Dashboard Customer)
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Scope to own shipments only (Strict Anti-IDOR)
        $shipmentQuery = $user->shipments();

        $totalShipments = (clone $shipmentQuery)->count();
        $inTransit      = (clone $shipmentQuery)->where('status', Shipment::STATUS_IN_TRANSIT)->count();
        $delivered      = (clone $shipmentQuery)->where('status', Shipment::STATUS_DELIVERED)->count();
        $delayed        = (clone $shipmentQuery)->where('delay_minutes', '>', 0)->count();

        // Status counts for Donut Chart
        $statusCounts = [
            'IN_TRANSIT' => $inTransit,
            'DELIVERED'  => $delivered,
            'DELAYED'    => $delayed,
            'BOOKED'     => (clone $shipmentQuery)->where('status', Shipment::STATUS_BOOKED)->count(),
            'PICKED_UP'  => (clone $shipmentQuery)->where('status', Shipment::STATUS_PICKED_UP)->count(),
        ];

        // 30 Days Trend Line Chart (Mockup 03 style)
        $trendDates = [];
        $trendValues = [];
        for ($i = 29; $i >= 0; $i -= 4) {
            $date = Carbon::now()->subDays($i);
            $trendDates[] = $date->format('d M');
            $count = (clone $shipmentQuery)->whereDate('created_at', '<=', $date->toDateString())->count();
            $trendValues[] = $count;
        }

        // Recent shipments belonging to this customer
        $recentShipments = (clone $shipmentQuery)
            ->with(['carrier', 'origin', 'destination', 'currentLocation'])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        // Active exceptions on customer's shipments
        $customerShipmentIds = (clone $shipmentQuery)->pluck('id');
        $activeExceptions = ShipmentException::with('shipment')
            ->whereIn('shipment_id', $customerShipmentIds)
            ->whereIn('status', [ShipmentException::STATUS_OPEN, ShipmentException::STATUS_INVESTIGATING])
            ->limit(3)
            ->get();

        return view('customer.dashboard', compact(
            'user',
            'totalShipments',
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

