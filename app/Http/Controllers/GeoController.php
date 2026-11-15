<?php

namespace App\Http\Controllers;

use App\Services\GeoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GeoController extends Controller
{
    public function __construct(
        protected GeoService $geoService
    ) {}

    /**
     * Daftar negara untuk dropdown negara tujuan
     */
    public function countries(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $this->geoService->getCountries(),
        ]);
    }

    /**
     * Daftar kota berdasarkan negara yang dipilih
     */
    public function cities(Request $request): JsonResponse
    {
        $country = $request->query('country', '');
        $cities = $this->geoService->getCities($country);

        return response()->json([
            'success' => true,
            'country' => $country,
            'data'    => $cities,
        ]);
    }

    /**
     * Daftar daerah / kecamatan berdasarkan kota & negara
     */
    public function districts(Request $request): JsonResponse
    {
        $country  = $request->query('country', '');
        $city     = $request->query('city', '');
        $districts = $this->geoService->getDistricts($country, $city);

        return response()->json([
            'success'   => true,
            'country'   => $country,
            'city'      => $city,
            'data'      => $districts,
        ]);
    }

    /**
     * Geocode alamat spesifik menggunakan Geoapify API
     */
    public function geocode(Request $request): JsonResponse
    {
        $query    = $request->query('address', $request->query('text', ''));
        $country  = $request->query('country');
        $city     = $request->query('city');
        $district = $request->query('district');

        $result = $this->geoService->geocode($query, $country, $city, $district);

        return response()->json([
            'success' => $result !== null,
            'data'    => $result,
        ]);
    }
}

