<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Carrier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CarrierApiController extends Controller
{
    public function index(): JsonResponse
    {
        $carriers = Carrier::where('status', 'ACTIVE')->orderBy('name', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $carriers,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:carriers,code'],
            'name' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'contact_email' => ['required', 'email'],
            'contact_phone' => ['nullable', 'string'],
            'website' => ['nullable', 'url'],
            'status' => ['required', 'in:ACTIVE,INACTIVE'],
        ]);

        $carrier = Carrier::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Ekspedisi berhasil ditambahkan.',
            'data' => $carrier,
        ], 201);
    }
}

