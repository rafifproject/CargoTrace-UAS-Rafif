<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Shipment;
use App\Services\GlobalIntelligenceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GlobalIntelligenceController extends Controller
{
    public function __construct(
        protected GlobalIntelligenceService $intelligenceService
    ) {}

    /**
     * 13. Monitoring Cuaca (Weather Monitoring)
     */
    public function weather(Request $request): View
    {
        $allWeatherHubs = $this->intelligenceService->getGlobalWeatherHubs();

        // 1. Determine Origin Hub (Titik 1)
        $originCode = $request->input('origin');
        $originHub = null;
        if ($request->has('lat1') && $request->has('lng1')) {
            $originLat = (float) $request->input('lat1');
            $originLng = (float) $request->input('lng1');
            $originHub = collect($allWeatherHubs)->first(fn ($h) => abs($h['lat'] - $originLat) < 1.5 && abs($h['lng'] - $originLng) < 1.5);
        } elseif ($originCode) {
            $originHub = collect($allWeatherHubs)->first(fn ($h) => strcasecmp($h['code'], $originCode) === 0 || strcasecmp($h['country'], $originCode) === 0);
            $originLat = (float) ($originHub['lat'] ?? -7.195);
            $originLng = (float) ($originHub['lng'] ?? 112.729);
        } else {
            $originHub = collect($allWeatherHubs)->firstWhere('code', 'ID') ?? $allWeatherHubs[0];
            $originLat = (float) ($originHub['lat'] ?? -7.195);
            $originLng = (float) ($originHub['lng'] ?? 112.729);
        }
        if (!$originHub) {
            $originHub = ['country' => 'Indonesia', 'city' => 'Port Tanjung Perak / Surabaya', 'code' => 'ID', 'iso' => 'id', 'lat' => $originLat, 'lng' => $originLng, 'risk' => 'Rendah', 'condition' => 'Cerah Berawan', 'icon' => 'sun', 'temp' => 30.2, 'humidity' => 72, 'wind' => 15.4];
        }

        // 2. Determine Destination Hub (Titik 2)
        $destCode = $request->input('dest');
        $destHub = null;
        if ($request->has('lat2') && $request->has('lng2')) {
            $destLat = (float) $request->input('lat2');
            $destLng = (float) $request->input('lng2');
            $destHub = collect($allWeatherHubs)->first(fn ($h) => abs($h['lat'] - $destLat) < 1.5 && abs($h['lng'] - $destLng) < 1.5);
        } elseif ($destCode) {
            $destHub = collect($allWeatherHubs)->first(fn ($h) => strcasecmp($h['code'], $destCode) === 0 || strcasecmp($h['country'], $destCode) === 0);
            $destLat = (float) ($destHub['lat'] ?? -33.856);
            $destLng = (float) ($destHub['lng'] ?? 151.209);
        } else {
            $destHub = collect($allWeatherHubs)->firstWhere('code', 'AU') ?? $allWeatherHubs[1];
            $destLat = (float) ($destHub['lat'] ?? -33.856);
            $destLng = (float) ($destHub['lng'] ?? 151.209);
        }
        if (!$destHub) {
            $destHub = ['country' => 'Australia', 'city' => 'Port Botany / Sydney', 'code' => 'AU', 'iso' => 'au', 'lat' => $destLat, 'lng' => $destLng, 'risk' => 'Waspada', 'condition' => 'Hujan Ringan & Berangin', 'icon' => 'cloud-rain', 'temp' => 23.4, 'humidity' => 70, 'wind' => 12.1];
        }

        // Jika ada filter country dari klik peta
        $targetCountry = $request->input('country');
        $selectedHub = null;
        if ($targetCountry) {
            $selectedHub = $this->intelligenceService->findWeatherHub($targetCountry);
            if ($selectedHub) {
                $originLat = (float) $selectedHub['lat'];
                $originLng = (float) $selectedHub['lng'];
                $originHub = $selectedHub;
            }
        }

        $originWeather = $this->intelligenceService->getWeather($originLat, $originLng);
        $destWeather = $this->intelligenceService->getWeather($destLat, $destLng);

        $locations = Location::where('status', 'ACTIVE')->get();

        return view('admin.intelligence.weather', compact(
            'originWeather',
            'destWeather',
            'originHub',
            'destHub',
            'originLat',
            'originLng',
            'destLat',
            'destLng',
            'locations',
            'allWeatherHubs',
            'selectedHub',
            'targetCountry'
        ));
    }

    /**
     * 14. Kurs & Nilai Tukar (Currency & Exchange)
     */
    public function currency(Request $request): View
    {
        $base = strtoupper($request->input('base', 'USD'));
        $currencies = $this->intelligenceService->getCurrencies($base);
        $currencyMeta = $this->intelligenceService->getCurrencyMeta();

        // Pastikan setiap kode mata uang dari live feed memiliki metadata lengkap
        foreach (array_keys($currencies['rates'] ?? []) as $code) {
            if (!isset($currencyMeta[$code])) {
                $currencyMeta[$code] = $this->intelligenceService->getCurrencyMetaFor($code);
            }
        }

        // 30 days trend simulation for USD/IDR
        $dates = [];
        $rates = [];
        for ($i = 29; $i >= 0; $i--) {
            $dates[] = now()->subDays($i)->format('d M');
            $rates[] = 15750 + intval(sin($i / 3) * 120 + ($i * 3));
        }

        return view('admin.intelligence.currency', compact('currencies', 'base', 'currencyMeta', 'dates', 'rates'));
    }

    /**
     * 15. Indikator Ekonomi (Economic Indicators)
     */
    public function economy(Request $request): View
    {
        $country = strtoupper($request->input('country', 'ID'));
        $data = $this->intelligenceService->getCountryEconomy($country);
        $countriesList = $this->intelligenceService->getCountriesMaster();

        // Historical GDP Growth trend for chart
        $years = ['2020', '2021', '2022', '2023', '2024', '2025'];
        $growth = [-2.07, 3.69, 5.31, 5.05, 5.03, 5.12];

        return view('admin.intelligence.economy', compact('data', 'country', 'years', 'growth', 'countriesList'));
    }

    /**
     * 16. Berita Global (Global Logistics News)
     */
    public function news(Request $request): View
    {
        $category = $request->input('category', 'all');
        $news = $this->intelligenceService->getLogisticsNews($category);

        return view('admin.intelligence.news', compact('news', 'category'));
    }

    /**
     * 17. Direktori Pelabuhan Global (Global Port Directory)
     */
    public function ports(Request $request): View
    {
        $allPorts = $this->intelligenceService->getGlobalPorts();

        if ($search = $request->input('search')) {
            $allPorts = array_filter($allPorts, function ($p) use ($search) {
                return str_contains(strtolower($p['name']), strtolower($search))
                    || str_contains(strtolower($p['city']), strtolower($search))
                    || str_contains(strtolower($p['country']), strtolower($search))
                    || str_contains(strtolower($p['code']), strtolower($search));
            });
        }

        return view('admin.intelligence.ports', compact('allPorts'));
    }

    /**
     * 17b. Direktori Bandara Kargo Global (Global Cargo Airport Directory)
     */
    public function airports(Request $request): View
    {
        $search = $request->input('search');
        $allAirports = $this->intelligenceService->getGlobalAirports($search);

        return view('admin.intelligence.airports', compact('allAirports'));
    }

    /**
     * 18. Perbandingan Negara (Country Comparison)
     */
    public function comparison(Request $request): View
    {
        $countryA = strtoupper($request->input('country_a', 'ID'));
        $countryB = strtoupper($request->input('country_b', 'AU'));

        $dataA = $this->intelligenceService->getCountryEconomy($countryA);
        $dataB = $this->intelligenceService->getCountryEconomy($countryB);
        $countriesList = $this->intelligenceService->getCountriesMaster();

        return view('admin.intelligence.comparison', compact('dataA', 'dataB', 'countryA', 'countryB', 'countriesList'));
    }
}

