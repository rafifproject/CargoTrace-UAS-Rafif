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
     * Daftar provinsi / state berdasarkan negara yang dipilih
     */
    public function provinces(Request $request): JsonResponse
    {
        $country = (string) $request->query('country', '');
        $provinces = $this->geoService->getProvinces($country);

        return response()->json([
            'success' => true,
            'country' => $country,
            'data'    => $provinces,
        ]);
    }

    /**
     * Daftar kota berdasarkan negara dan provinsi yang dipilih
     */
    public function provinceCities(Request $request): JsonResponse
    {
        $country  = (string) $request->query('country', '');
        $province = (string) $request->query('province', '');
        $cities   = $this->geoService->getCitiesByProvince($country, $province);

        return response()->json([
            'success'  => true,
            'country'  => $country,
            'province' => $province,
            'data'     => $cities,
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

    /**
     * Ambil atau auto-registrasi bandara/pelabuhan internasional untuk negara tujuan manapun
     */
    public function countryHubs(Request $request): JsonResponse
    {
        $country = (string) $request->query('country', '');
        $mode    = (string) $request->query('mode', 'SEA');

        if (trim($country) === '') {
            return response()->json(['success' => false, 'data' => []]);
        }

        $hubs = $this->geoService->getOrCreateCountryHubs($country, $mode);

        return response()->json([
            'success' => !empty($hubs),
            'country' => $country,
            'mode'    => strtoupper($mode),
            'data'    => $hubs,
        ]);
    }
}

