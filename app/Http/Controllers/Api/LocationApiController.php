<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationApiController extends Controller
{
    public function index(): JsonResponse
    {
        $locations = Location::where('status', 'ACTIVE')->orderBy('name', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $locations,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'city' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'location_type' => ['required', 'in:PORT,AIRPORT,WAREHOUSE,CUSTOMS_OFFICE'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'status' => ['required', 'in:ACTIVE,INACTIVE'],
        ]);

        $location = Location::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Lokasi berhasil didaftarkan.',
            'data' => $location,
        ], 201);
    }
}

