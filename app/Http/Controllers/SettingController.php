<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = SystemSetting::all()->pluck('value', 'key')->toArray();

        $apiStatuses = [
            'open_meteo' => ['name' => 'Open-Meteo Weather API', 'status' => 'CONNECTED', 'type' => 'Cuaca & Prakiraan Global'],
            'exchange_rate' => ['name' => 'ExchangeRate-API', 'status' => 'CONNECTED', 'type' => 'Nilai Tukar Valuta & Kurs'],
            'world_bank' => ['name' => 'World Bank Indicator API', 'status' => 'CONNECTED', 'type' => 'Indikator Makro Ekonomi'],
            'rest_countries' => ['name' => 'REST Countries API', 'status' => 'CONNECTED', 'type' => 'Profil & Referensi Negara'],
            'nominatim' => ['name' => 'OpenStreetMap Nominatim', 'status' => 'CONNECTED', 'type' => 'Geocoding & Koordinat Pelabuhan'],
            'gnews' => ['name' => 'GNews Global Logistics News', 'status' => 'CONNECTED', 'type' => 'Warta Logistik & Rantai Pasok'],
        ];

        return view('admin.settings.index', compact('settings', 'apiStatuses'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'app_language' => ['required', 'in:id,en'],
            'app_timezone' => ['required', 'string'],
            'date_format' => ['required', 'string'],
            'delay_threshold_hours' => ['required', 'numeric', 'min:1'],
        ]);

        foreach ($validated as $key => $val) {
            SystemSetting::set($key, $val, 'general');
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'CONFIG_CHANGE',
            'entity_type' => 'SystemSetting',
            'entity_id' => 'system_preferences',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => 'Administrator memperbarui preferensi sistem dan ambang batas keterlambatan.',
            'new_values' => $validated,
            'created_at' => now(),
        ]);

        return back()->with('status', 'Pengaturan sistem berhasil disimpan.');
    }
}

