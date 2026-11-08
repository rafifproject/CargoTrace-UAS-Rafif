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
     * Mockup 09: Pemantauan Cuaca Rute Pengiriman Customer
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

        $originWeather = $this->intelligenceService->getWeather((float) $originLat, (float) $originLng);
        $destWeather   = $this->intelligenceService->getWeather((float) $destLat, (float) $destLng);

        return view('customer.intelligence.weather', compact('activeShipment', 'originWeather', 'destWeather'));
    }

    /**
     * Mockup 10: Berita Logistik Global
     */
    public function news(Request $request): View
    {
        $category = $request->input('category', 'all');
        $news = $this->intelligenceService->getLogisticsNews($category);

        return view('customer.intelligence.news', compact('news', 'category'));
    }

    /**
     * Mockup 11: Perbandingan Negara Mitra Dagang
     */
    public function comparison(Request $request): View
    {
        $countryA = strtoupper($request->input('country_a', 'ID'));
        $countryB = strtoupper($request->input('country_b', 'SG'));

        $dataA = $this->intelligenceService->getCountryEconomy($countryA);
        $dataB = $this->intelligenceService->getCountryEconomy($countryB);

        return view('customer.intelligence.comparison', compact('dataA', 'dataB', 'countryA', 'countryB'));
    }
}
