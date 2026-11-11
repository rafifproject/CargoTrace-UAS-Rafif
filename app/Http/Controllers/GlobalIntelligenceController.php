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
        $originLat = (float) $request->input('lat1', -7.195);
        $originLng = (float) $request->input('lng1', 112.729);
        $destLat = (float) $request->input('lat2', -33.856);
        $destLng = (float) $request->input('lng2', 151.209);

        // Jika ada filter country dari klik peta
        $targetCountry = $request->input('country');
        $allWeatherHubs = $this->intelligenceService->getGlobalWeatherHubs();
        $selectedHub = null;

        if ($targetCountry) {
            foreach ($allWeatherHubs as $hub) {
                if (strcasecmp($hub['country'], $targetCountry) === 0 || strcasecmp($hub['code'], $targetCountry) === 0) {
                    $selectedHub = $hub;
                    $originLat = $hub['lat'];
                    $originLng = $hub['lng'];
                    break;
                }
            }
        }

        $originWeather = $this->intelligenceService->getWeather($originLat, $originLng);
        $destWeather = $this->intelligenceService->getWeather($destLat, $destLng);

        $locations = Location::where('status', 'ACTIVE')->get();

        return view('admin.intelligence.weather', compact('originWeather', 'destWeather', 'locations', 'allWeatherHubs', 'selectedHub', 'targetCountry'));
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
        $allAirports = $this->intelligenceService->getGlobalAirports();

        if ($search = $request->input('search')) {
            $allAirports = array_filter($allAirports, function ($a) use ($search) {
                return str_contains(strtolower($a['name'] ?? ''), strtolower($search))
                    || str_contains(strtolower($a['city'] ?? ''), strtolower($search))
                    || str_contains(strtolower($a['country'] ?? ''), strtolower($search))
                    || str_contains(strtolower($a['code'] ?? ''), strtolower($search))
                    || str_contains(strtolower($a['icao'] ?? ''), strtolower($search));
            });
        }

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

