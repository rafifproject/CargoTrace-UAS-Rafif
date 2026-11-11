<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Carrier;
use App\Services\GlobalIntelligenceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerCarrierController extends Controller
{
    /**
     * Tampilan Direktori Mitra Ekspedisi Global untuk Customer
     */
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

        $carriers = $query->where('status', Carrier::STATUS_ACTIVE)->orderBy('name', 'asc')->paginate(12)->withQueryString();

        // 206 Titik Ekspedisi Global untuk Peta Interaktif
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

        return view('customer.carriers.index', compact('carriers', 'mapCarriers'));
    }
}

