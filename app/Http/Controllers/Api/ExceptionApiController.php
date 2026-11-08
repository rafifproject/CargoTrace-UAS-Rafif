<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ShipmentException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExceptionApiController extends Controller
{
    public function update(Request $request, ShipmentException $exception): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:OPEN,INVESTIGATING,RESOLVED'],
            'resolution_notes' => ['nullable', 'string'],
        ]);

        $exception->status = $validated['status'];
        if ($validated['status'] === ShipmentException::STATUS_RESOLVED) {
            $exception->resolution_notes = $validated['resolution_notes'] ?? 'Diselesaikan melalui API.';
            $exception->resolved_by = $request->user()->id;
            $exception->resolved_at = now();
        }
        $exception->save();

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'EXCEPTION_RESOLVED',
            'entity_type' => 'ShipmentException',
            'entity_id' => (string) $exception->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => "Status exception #{$exception->id} diubah menjadi {$exception->status} via API.",
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status exception berhasil diperbarui.',
            'data' => $exception,
        ]);
    }
}

