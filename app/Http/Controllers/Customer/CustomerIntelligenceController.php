<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Services\GlobalIntelligenceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerIntelligenceController extends Controller
{
    public function __construct(
        protected GlobalIntelligenceService $intelligenceService
    ) {}

    /**
     * Mockup 09: Pemantauan Cuaca Rute Pengiriman Customer & Direktori Global 205 Negara
     */
    public function weather(Request $request): View
    {
        $user = $request->user();
        $activeShipment = $user->shipments()
            ->with(['origin', 'destination'])
            ->whereIn('status', [Shipment::STATUS_IN_TRANSIT, Shipment::STATUS_PICKED_UP, Shipment::STATUS_BOOKED])
            ->first() ?? $user->shipments()->with(['origin', 'destination'])->first();

        $originLat = $activeShipment?->origin?->latitude ?? -6.2088;
        $originLng = $activeShipment?->origin?->longitude ?? 106.8456;
        $destLat   = $activeShipment?->destination?->latitude ?? 1.3521;
        $destLng   = $activeShipment?->destination?->longitude ?? 103.8198;

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

        $originWeather = $this->intelligenceService->getWeather((float) $originLat, (float) $originLng);
        $destWeather   = $this->intelligenceService->getWeather((float) $destLat, (float) $destLng);

        return view('customer.intelligence.weather', compact('activeShipment', 'originWeather', 'destWeather', 'allWeatherHubs', 'selectedHub', 'targetCountry'));
    }

    /**
     * Mockup 10: Informasi Kurs & Nilai Tukar Valas Global
     */
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

        return view('customer.intelligence.currency', compact('currencies', 'base', 'dates', 'rates', 'currencyMeta'));
    }

    /**
     * Mockup 11: Indikator Ekonomi & Neraca Perdagangan 205 Negara
     */
    public function economy(Request $request): View
    {
        $country       = strtoupper($request->input('country', 'ID'));
        $data          = $this->intelligenceService->getCountryEconomy($country);
        $countriesList = $this->intelligenceService->getCountriesMaster();

        $years  = ['2020', '2021', '2022', '2023', '2024', '2025'];
        $growth = [-2.07, 3.69, 5.31, 5.05, 5.03, 5.12];

        return view('customer.intelligence.economy', compact('data', 'country', 'years', 'growth', 'countriesList'));
    }

    /**
     * Mockup 12: Direktori Pelabuhan & Hub Maritim Global 205 Negara
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

        return view('customer.intelligence.ports', compact('allPorts'));
    }

    /**
     * Mockup 13: Berita Logistik Global & Analisis Sentimen
     */
    public function news(Request $request): View
    {
        $category = $request->input('category', 'all');
        $news     = $this->intelligenceService->getLogisticsNews($category);

        return view('customer.intelligence.news', compact('news', 'category'));
    }

    /**
     * Mockup 14: Perbandingan Komparatif 205 Negara Mitra Dagang
     */
    public function comparison(Request $request): View
    {
        $countryA = strtoupper($request->input('country_a', 'ID'));
        $countryB = strtoupper($request->input('country_b', 'SG'));

        $dataA         = $this->intelligenceService->getCountryEconomy($countryA);
        $dataB         = $this->intelligenceService->getCountryEconomy($countryB);
        $countriesList = $this->intelligenceService->getCountriesMaster();

        return view('customer.intelligence.comparison', compact('dataA', 'dataB', 'countryA', 'countryB', 'countriesList'));
    }
}
