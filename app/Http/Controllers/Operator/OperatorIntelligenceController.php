<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Services\GlobalIntelligenceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Thin proxy controller for Operator Global Intelligence pages.
 * Delegates to the shared GlobalIntelligenceService; renders operator-namespaced views.
 */
class OperatorIntelligenceController extends Controller
{
    public function __construct(
        protected GlobalIntelligenceService $intelligenceService
    ) {}

    /** Mockup 09: Pemantauan Cuaca */
    public function weather(Request $request): View
    {
        $originLat = (float) $request->input('lat1', -7.195);
        $originLng = (float) $request->input('lng1', 112.729);
        $destLat   = (float) $request->input('lat2', -33.856);
        $destLng   = (float) $request->input('lng2', 151.209);

        // Jika ada filter country dari klik peta atau direktori stasiun cuaca
        $targetCountry  = $request->input('country');
        $allWeatherHubs = $this->intelligenceService->getGlobalWeatherHubs();
        $selectedHub    = null;

        if ($targetCountry) {
            foreach ($allWeatherHubs as $hub) {
                if (strcasecmp($hub['country'], $targetCountry) === 0 || strcasecmp($hub['code'], $targetCountry) === 0) {
                    $selectedHub = $hub;
                    $originLat   = $hub['lat'];
                    $originLng   = $hub['lng'];
                    break;
                }
            }
        }

        $originWeather = $this->intelligenceService->getWeather($originLat, $originLng);
        $destWeather   = $this->intelligenceService->getWeather($destLat, $destLng);
        $locations     = Location::where('status', 'ACTIVE')->get();

        return view('operator.intelligence.weather', compact('originWeather', 'destWeather', 'locations', 'allWeatherHubs', 'selectedHub', 'targetCountry'));
    }

    /** Mockup 10: Informasi Mata Uang */
    public function currency(Request $request): View
    {
        $base         = strtoupper($request->input('base', 'USD'));
        $currencies   = $this->intelligenceService->getCurrencies($base);
        $currencyMeta = $this->intelligenceService->getCurrenciesMetadata();

        $dates = [];
        $rates = [];
        for ($i = 29; $i >= 0; $i--) {
            $dates[] = now()->subDays($i)->format('d M');
            $rates[] = 15750 + intval(sin($i / 3) * 120 + ($i * 3));
        }

        return view('operator.intelligence.currency', compact('currencies', 'base', 'dates', 'rates', 'currencyMeta'));
    }

    /** Halaman 11: Indikator Ekonomi */
    public function economy(Request $request): View
    {
        $country = strtoupper($request->input('country', 'ID'));
        $data    = $this->intelligenceService->getCountryEconomy($country);
        $countriesList = $this->intelligenceService->getCountriesMaster();

        $years  = ['2020', '2021', '2022', '2023', '2024', '2025'];
        $growth = [-2.07, 3.69, 5.31, 5.05, 5.03, 5.12];

        return view('operator.intelligence.economy', compact('data', 'country', 'years', 'growth', 'countriesList'));
    }

    /** Mockup 11: Berita Logistik Global */
    public function news(Request $request): View
    {
        $category = $request->input('category', 'all');
        $news     = $this->intelligenceService->getLogisticsNews($category);

        return view('operator.intelligence.news', compact('news', 'category'));
    }

    /** Mockup 13: Direktori Pelabuhan Global */
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

        return view('operator.intelligence.ports', compact('allPorts'));
    }

    /** Direktori Bandara Kargo Global */
    public function airports(Request $request): View
    {
        $search = $request->input('search');
        $allAirports = $this->intelligenceService->getGlobalAirports($search);

        return view('operator.intelligence.airports', compact('allAirports'));
    }

    /** Mockup 14: Perbandingan Negara */
    public function comparison(Request $request): View
    {
        $countryA = strtoupper($request->input('country_a', 'ID'));
        $countryB = strtoupper($request->input('country_b', 'SG'));

        $dataA = $this->intelligenceService->getCountryEconomy($countryA);
        $dataB = $this->intelligenceService->getCountryEconomy($countryB);
        $countriesList = $this->intelligenceService->getCountriesMaster();

        return view('operator.intelligence.comparison', compact('dataA', 'dataB', 'countryA', 'countryB', 'countriesList'));
    }
}

