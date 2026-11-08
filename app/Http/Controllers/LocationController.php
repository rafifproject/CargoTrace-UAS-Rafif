<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(Request $request): View
    {
        $query = Location::withCount(['shipmentsOrigin', 'shipmentsDestination']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%");
            });
        }

        if ($type = $request->input('location_type')) {
            $query->where('location_type', $type);
        }

        $locations = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();

        return view('admin.locations.index', compact('locations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'city' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'location_type' => ['required', 'in:PORT,AIRPORT,WAREHOUSE,CUSTOMS_OFFICE'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'status' => ['required', 'in:ACTIVE,INACTIVE'],
        ], [
            'name.required' => 'Nama lokasi wajib diisi.',
            'city.required' => 'Kota wajib diisi.',
            'country.required' => 'Negara wajib diisi.',
            'latitude.required' => 'Koordinat Latitude wajib diisi.',
            'longitude.required' => 'Koordinat Longitude wajib diisi.',
        ]);

        $location = Location::create($validated);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'CREATE',
            'entity_type' => 'Location',
            'entity_id' => (string) $location->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => "Lokasi baru ditambahkan: {$location->name} ({$location->city}, {$location->country})",
            'new_values' => $validated,
            'created_at' => now(),
        ]);

        return back()->with('status', 'Lokasi berhasil ditambahkan ke direktori sistem.');
    }

    public function update(Request $request, Location $location): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'city' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'location_type' => ['required', 'in:PORT,AIRPORT,WAREHOUSE,CUSTOMS_OFFICE'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'status' => ['required', 'in:ACTIVE,INACTIVE'],
        ]);

        $oldValues = $location->toArray();
        $location->update($validated);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'UPDATE',
            'entity_type' => 'Location',
            'entity_id' => (string) $location->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => "Data lokasi {$location->name} diperbarui oleh Admin.",
            'old_values' => $oldValues,
            'new_values' => $validated,
            'created_at' => now(),
        ]);

        return back()->with('status', 'Data lokasi berhasil diperbarui.');
    }

    public function destroy(Request $request, Location $location): RedirectResponse
    {
        $hasRelations = ($location->shipmentsOrigin()->count() + $location->shipmentsDestination()->count() + $location->trackingEvents()->count()) > 0;

        if ($hasRelations) {
            $location->update(['status' => Location::STATUS_INACTIVE]);
            return back()->with('status', 'Lokasi memiliki riwayat pengiriman/tracking aktif sehingga dinonaktifkan (Deactivated) demi integritas data audit.');
        }

        $name = $location->name;
        $location->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'DELETE',
            'entity_type' => 'Location',
            'entity_id' => (string) $location->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => "Lokasi {$name} dihapus dari direktori.",
            'created_at' => now(),
        ]);

        return back()->with('status', 'Lokasi berhasil dihapus.');
    }

    /**
     * Integrasi API Geocoding Nominatim OpenStreetMap
     */
    public function geocode(Request $request): JsonResponse
    {
        $query = $request->query('q');
        if (! $query) {
            return response()->json(['error' => 'Parameter pencarian kosong.'], 422);
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'CargoTrace-Platform/1.0 (academic-project; admin@cargotrace.id)'
            ])->timeout(5)->get('https://nominatim.openstreetmap.org/search', [
                'q' => $query,
                'format' => 'json',
                'limit' => 5,
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json(['error' => 'Gagal mengambil data koordinat.'], 502);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Timeout layanan Geocoding: ' . $e->getMessage()], 504);
        }
    }
}

