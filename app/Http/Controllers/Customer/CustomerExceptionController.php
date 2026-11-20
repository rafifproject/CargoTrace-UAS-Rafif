<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ShipmentException;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerExceptionController extends Controller
{
    /**
     * Mockup 08: Informasi Pengecualian (Read-Only Customer View)
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $myShipmentIds = $user->shipments()->pluck('id');

        $query = ShipmentException::with(['shipment'])
            ->whereIn('shipment_id', $myShipmentIds)
            ->orderBy('created_at', 'desc');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('exception_type', 'like', "%{$search}%")
                    ->orWhere('resolution_notes', 'like', "%{$search}%")
                    ->orWhereHas('shipment', fn ($sq) => $sq->where('shipment_number', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $exceptions = $query->paginate(10)->withQueryString();
        $activeCount = ShipmentException::whereIn('shipment_id', $myShipmentIds)
            ->whereIn('status', [ShipmentException::STATUS_OPEN, ShipmentException::STATUS_INVESTIGATING])
            ->count();

        return view('customer.exceptions.index', compact('exceptions', 'activeCount'));
    }
}

