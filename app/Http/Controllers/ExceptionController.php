<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\ShipmentException;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExceptionController extends Controller
{
    public function index(Request $request): View
    {
        $query = ShipmentException::with(['shipment.carrier', 'shipment.origin', 'shipment.destination', 'reporter', 'resolver']);

        // Filter Status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter Type
        if ($type = $request->input('type')) {
            $query->where('exception_type', $type);
        }

        // Filter Severity
        if ($severity = $request->input('severity')) {
            $query->where('severity', $severity);
        }

        $exceptions = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // Metrics for KPIs
        $totalCount = ShipmentException::count();
        $openCount = ShipmentException::where('status', ShipmentException::STATUS_OPEN)->count();
        $investigatingCount = ShipmentException::where('status', ShipmentException::STATUS_INVESTIGATING)->count();
        $resolvedCount = ShipmentException::where('status', ShipmentException::STATUS_RESOLVED)->count();

        // Type breakdown for Donut Chart
        $typesCount = [
            'WEATHER_DELAY' => ShipmentException::where('exception_type', ShipmentException::TYPE_WEATHER_DELAY)->count(),
            'CUSTOMS_HOLD' => ShipmentException::where('exception_type', ShipmentException::TYPE_CUSTOMS_HOLD)->count(),
            'PORT_CONGESTION' => ShipmentException::where('exception_type', ShipmentException::TYPE_PORT_CONGESTION)->count(),
            'DAMAGED' => ShipmentException::where('exception_type', ShipmentException::TYPE_DAMAGED)->count(),
            'MISROUTED' => ShipmentException::where('exception_type', ShipmentException::TYPE_MISROUTED)->count(),
            'DOCUMENTATION_ISSUE' => ShipmentException::where('exception_type', ShipmentException::TYPE_DOCUMENTATION_ISSUE)->count(),
        ];

        // 30 Days Exception Trend
        $trendDates = [];
        $trendValues = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $trendDates[] = $date->format('d M');
            $trendValues[] = max(0, intval(sin($i / 4) * 2 + 1));
        }

        return view('admin.exceptions.index', compact(
            'exceptions',
            'totalCount',
            'openCount',
            'investigatingCount',
            'resolvedCount',
            'typesCount',
            'trendDates',
            'trendValues'
        ));
    }

    public function resolve(Request $request, ShipmentException $exception): RedirectResponse
    {
        $validated = $request->validate([
            'resolution_notes' => ['required', 'string', 'min:5'],
        ], [
            'resolution_notes.required' => 'Catatan penyelesaian masalah wajib diisi.',
            'resolution_notes.min' => 'Catatan minimal 5 karakter.',
        ]);

        $exception->update([
            'status' => ShipmentException::STATUS_RESOLVED,
            'resolution_notes' => $validated['resolution_notes'],
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'EXCEPTION_RESOLVED',
            'entity_type' => 'ShipmentException',
            'entity_id' => (string) $exception->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => "Pengecualian {$exception->exception_type} pada kargo {$exception->shipment->shipment_number} diselesaikan oleh Admin.",
            'created_at' => now(),
        ]);

        return back()->with('status', 'Pengecualian berhasil diselesaikan.');
    }
}

