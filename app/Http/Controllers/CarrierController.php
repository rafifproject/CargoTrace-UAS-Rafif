<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Carrier;
use App\Services\GlobalIntelligenceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarrierController extends Controller
{
    public function index(Request $request, GlobalIntelligenceService $intelligenceService): View
    {
        $query = Carrier::withCount('shipments');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $carriers = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();

        // Titik-titik lokasi kantor pusat 206 ekspedisi global untuk Peta Ekspedisi
        $countriesList = $intelligenceService->getCountriesMaster();
        $countryMap = [];
        foreach ($countriesList as $c) {
            $countryMap[strtolower($c['name'])] = $c;
            $countryMap[strtolower($c['code'])] = $c;
            $iso = strtolower(str_replace('_EAST', '', $c['code']));
            $countryMap[$iso] = $c;
        }

        $allCarriers = Carrier::withCount('shipments')->where('status', Carrier::STATUS_ACTIVE)->get();
        $mapCarriers = $allCarriers->map(function ($c) use ($countryMap) {
            $cLower = strtolower(trim($c->country));
            $countryInfo = $countryMap[$cLower] ?? null;
            $lat = $countryInfo['lat'] ?? 0.0;
            $lng = $countryInfo['lng'] ?? 0.0;
            $iso = $countryInfo ? strtolower(str_replace('_EAST', '', $countryInfo['code'])) : 'un';
            $flag = $countryInfo['flag'] ?? '🌐';

            return [
                'id'              => $c->id,
                'code'            => $c->code,
                'name'            => $c->name,
                'country'         => $c->country,
                'contact_email'   => $c->contact_email,
                'contact_phone'   => $c->contact_phone,
                'website'         => $c->website,
                'shipments_count' => $c->shipments_count,
                'lat'             => $lat,
                'lng'             => $lng,
                'iso'             => $iso,
                'flag'            => $flag,
            ];
        });

        return view('admin.carriers.index', compact('carriers', 'mapCarriers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:carriers,code'],
            'name' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'url', 'max:255'],
            'status' => ['required', 'in:ACTIVE,INACTIVE'],
        ], [
            'code.required' => 'Kode ekspedisi wajib diisi.',
            'code.unique' => 'Kode ekspedisi sudah ada.',
            'name.required' => 'Nama perusahaan ekspedisi wajib diisi.',
            'contact_email.required' => 'Email kontak wajib diisi.',
        ]);

        $carrier = Carrier::create($validated);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'CREATE',
            'entity_type' => 'Carrier',
            'entity_id' => (string) $carrier->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => "Ekspedisi baru ditambahkan: {$carrier->name} ({$carrier->code})",
            'new_values' => $validated,
            'created_at' => now(),
        ]);

        return back()->with('status', 'Ekspedisi berhasil didaftarkan.');
    }

    public function update(Request $request, Carrier $carrier): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:carriers,code,' . $carrier->id],
            'name' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'url', 'max:255'],
            'status' => ['required', 'in:ACTIVE,INACTIVE'],
        ]);

        $oldValues = $carrier->only(['code', 'name', 'country', 'contact_email', 'status']);
        $carrier->update($validated);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'UPDATE',
            'entity_type' => 'Carrier',
            'entity_id' => (string) $carrier->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => "Data ekspedisi {$carrier->name} diperbarui oleh Admin.",
            'old_values' => $oldValues,
            'new_values' => $validated,
            'created_at' => now(),
        ]);

        return back()->with('status', 'Data ekspedisi berhasil diperbarui.');
    }

    public function destroy(Request $request, Carrier $carrier): RedirectResponse
    {
        if ($carrier->shipments()->count() > 0) {
            // Sesuai prinsip integritas relasi di master prompt: tidak boleh delete sembarangan jika ada relasi shipment
            $carrier->update(['status' => Carrier::STATUS_INACTIVE]);
            return back()->with('status', "Ekspedisi memiliki relasi pengiriman aktif sehingga statusnya dinonaktifkan (Deactivated) bukan dihapus permanen.");
        }

        $carrierName = $carrier->name;
        $carrier->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'DELETE',
            'entity_type' => 'Carrier',
            'entity_id' => (string) $carrier->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => "Ekspedisi {$carrierName} dihapus secara permanen.",
            'created_at' => now(),
        ]);

        return back()->with('status', 'Ekspedisi berhasil dihapus.');
    }
}

