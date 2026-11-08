<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\ShipmentException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OperatorExceptionController extends Controller
{
    /**
     * Mockup 09: Detail Pengecualian
     */
    public function show(ShipmentException $exception): View
    {
        $exception->load(['shipment.origin', 'shipment.destination', 'shipment.carrier', 'reporter', 'resolver']);

        return view('operator.exceptions.show', compact('exception'));
    }

    /**
     * Mockup 08: Manajemen Pengecualian (Exception Management)
     */
    public function index(Request $request): View
    {
        $totalExceptions = ShipmentException::count();
        $openExceptions  = ShipmentException::where('status', ShipmentException::STATUS_OPEN)->count();
        $investigating   = ShipmentException::where('status', ShipmentException::STATUS_INVESTIGATING)->count();
        $resolved        = ShipmentException::where('status', ShipmentException::STATUS_RESOLVED)->count();

        $query = ShipmentException::with(['shipment', 'reporter', 'resolver'])
            ->orderByRaw("FIELD(status, 'OPEN', 'INVESTIGATING', 'RESOLVED')")
            ->orderBy('created_at', 'desc');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($severity = $request->input('severity')) {
            $query->where('severity', $severity);
        }

        if ($search = $request->input('search')) {
            $query->whereHas('shipment', fn ($q) => $q->where('shipment_number', 'like', "%{$search}%"));
        }

        $exceptions = $query->paginate(10)->withQueryString();
        $shipments  = Shipment::whereIn('status', [
            Shipment::STATUS_BOOKED,
            Shipment::STATUS_PICKED_UP,
            Shipment::STATUS_IN_TRANSIT,
        ])->orderBy('shipment_number')->get();

        return view('operator.exceptions.index', compact(
            'exceptions',
            'shipments',
            'totalExceptions',
            'openExceptions',
            'investigating',
            'resolved'
        ));
    }

    /**
     * Simpan Exception Baru (dilaporkan oleh Operator)
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'shipment_id'     => ['required', 'exists:shipments,id'],
            'exception_type'  => ['required', 'string'],
            'severity'        => ['required', 'in:LOW,MEDIUM,HIGH,CRITICAL'],
            'description'     => ['required', 'string', 'min:10'],
        ], [
            'shipment_id.required'    => 'Pilih nomor pengiriman terlebih dahulu.',
            'exception_type.required' => 'Pilih jenis pengecualian.',
            'severity.required'       => 'Pilih tingkat keparahan.',
            'description.required'    => 'Deskripsi masalah wajib diisi.',
            'description.min'         => 'Deskripsi minimal 10 karakter.',
        ]);

        $exception = ShipmentException::create([
            'shipment_id'    => $validated['shipment_id'],
            'exception_type' => $validated['exception_type'],
            'severity'       => $validated['severity'],
            'description'    => $validated['description'],
            'status'         => ShipmentException::STATUS_OPEN,
            'reported_by'    => $request->user()->id,
        ]);

        /** @var Shipment $shipment */
        $shipment = Shipment::find($validated['shipment_id']);

        // Create notification for visibility
        Notification::create([
            'shipment_id' => $shipment->id,
            'type'        => Notification::TYPE_EXCEPTION_ALERT,
            'title'       => "Pengecualian Baru: {$validated['exception_type']}",
            'message'     => "Pengecualian tingkat {$validated['severity']} dilaporkan pada pengiriman {$shipment->shipment_number}. {$validated['description']}",
            'severity'    => in_array($validated['severity'], ['CRITICAL', 'HIGH']) ? Notification::SEVERITY_DANGER : Notification::SEVERITY_WARNING,
            'is_read'     => false,
        ]);

        AuditLog::create([
            'user_id'     => $request->user()->id,
            'action'      => 'CREATE',
            'entity_type' => 'ShipmentException',
            'entity_id'   => (string) $exception->id,
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
            'description' => "Operator {$request->user()->name} melaporkan pengecualian '{$validated['exception_type']}' pada kargo {$shipment->shipment_number}.",
            'new_values'  => $validated,
            'created_at'  => now(),
        ]);

        return back()->with('status', "Pengecualian berhasil dilaporkan pada kargo {$shipment->shipment_number}.");
    }

    /**
     * Selesaikan Exception (resolve)
     */
    public function resolve(Request $request, ShipmentException $exception): RedirectResponse
    {
        $validated = $request->validate([
            'resolution_notes' => ['required', 'string', 'min:10'],
        ], [
            'resolution_notes.required' => 'Catatan penyelesaian wajib diisi.',
            'resolution_notes.min'      => 'Catatan penyelesaian minimal 10 karakter.',
        ]);

        $exception->update([
            'status'           => ShipmentException::STATUS_RESOLVED,
            'resolution_notes' => $validated['resolution_notes'],
            'resolved_by'      => $request->user()->id,
            'resolved_at'      => now(),
        ]);

        AuditLog::create([
            'user_id'     => $request->user()->id,
            'action'      => 'UPDATE',
            'entity_type' => 'ShipmentException',
            'entity_id'   => (string) $exception->id,
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
            'description' => "Operator {$request->user()->name} menyelesaikan pengecualian #{$exception->id} pada kargo {$exception->shipment->shipment_number}.",
            'new_values'  => ['status' => 'RESOLVED', 'resolution_notes' => $validated['resolution_notes']],
            'created_at'  => now(),
        ]);

        return back()->with('status', 'Pengecualian berhasil diselesaikan dan dicatat dalam sistem.');
    }
}

