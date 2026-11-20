<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeoService
{
    /**
     * Master daftar negara global
     */
    public function getCountries(): array
    {
        /** @var GlobalIntelligenceService $globalService */
        $globalService = app(GlobalIntelligenceService::class);
        $master = $globalService->getCountriesMaster();

        return array_map(function ($c) {
            return [
                'code' => $c['code'],
                'name' => $c['name'],
                'flag' => $c['flag'],
                'iso'  => strtolower(str_replace('_EAST', '', $c['code'])),
            ];
        }, $master);
    }

    /**
     * Dapatkan daftar kota dari suatu negara (Geoapify live query + Curated Global Fallback)
     */
    public function getCities(string $country): array
    {
        $countryClean = trim($country);
        if (empty($countryClean)) {
            return [];
        }

        $cacheKey = 'geo_cities_' . md5(strtolower($countryClean));

        return Cache::remember($cacheKey, 86400, function () use ($countryClean) {
            $apiKey = config('services.geoapify.key', env('GEOAPIFY_API_KEY', '11121e44a86f40d593e90176dda45e95'));
            $baseUrl = rtrim(config('services.geoapify.url', env('GEOAPIFY_BASE_URL', 'https://api.geoapify.com/v1')), '/');

            $cities = [];

            // 1. Coba ambil kota-kota utama secara live dari Geoapify
            if (!empty($apiKey)) {
                try {
                    $response = Http::timeout(5)->retry(2, 100)->get("{$baseUrl}/geocode/search", [
                        'text' => $countryClean,
                        'type' => 'city',
                        'limit' => 20,
                        'apiKey' => $apiKey,
                    ]);

                    if ($response->successful()) {
                        $features = $response->json()['features'] ?? [];
                        foreach ($features as $f) {
                            $prop = $f['properties'] ?? [];
                            $city = $prop['city'] ?? $prop['name'] ?? null;
                            if ($city && !in_array($city, $cities, true)) {
                                $cities[] = $city;
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning("Geoapify getCities failed for {$countryClean}: " . $e->getMessage());
                }
            }

            // 2. Gabungkan dengan master curated kota global (menjamin 100% ketersediaan instan)
            $fallbackCities = $this->getCuratedCitiesByCountry($countryClean);
            foreach ($fallbackCities as $fc) {
                if (!in_array($fc, $cities, true)) {
                    $cities[] = $fc;
                }
            }

            // Filter out country name itself or empty strings
            $cities = array_values(array_filter($cities, function ($city) use ($countryClean) {
                return !empty($city) && strcasecmp(trim($city), $countryClean) !== 0;
            }));

            sort($cities);
            return $cities;
        });
    }

    /**
     * Dapatkan daftar daerah / kecamatan / suburb dari suatu kota
     */
    public function getDistricts(string $country, string $city): array
    {
        $countryClean = trim($country);
        $cityClean = trim($city);

        if (empty($cityClean)) {
            return [];
        }

        $cacheKey = 'geo_districts_' . md5(strtolower("{$countryClean}_{$cityClean}"));

        return Cache::remember($cacheKey, 86400, function () use ($countryClean, $cityClean) {
            $apiKey = config('services.geoapify.key', env('GEOAPIFY_API_KEY', '11121e44a86f40d593e90176dda45e95'));
            $baseUrl = rtrim(config('services.geoapify.url', env('GEOAPIFY_BASE_URL', 'https://api.geoapify.com/v1')), '/');

            $districts = [];

            // 1. Coba ambil distrik / suburb / daerah dari Geoapify
            if (!empty($apiKey)) {
                try {
                    $response = Http::timeout(5)->retry(2, 100)->get("{$baseUrl}/geocode/search", [
                        'text' => "{$cityClean}, {$countryClean}",
                        'type' => 'amenity',
                        'limit' => 25,
                        'apiKey' => $apiKey,
                    ]);

                    if ($response->successful()) {
                        $features = $response->json()['features'] ?? [];
                        foreach ($features as $f) {
                            $prop = $f['properties'] ?? [];
                            $district = $prop['district'] ?? $prop['suburb'] ?? $prop['neighbourhood'] ?? $prop['county'] ?? null;
                            if ($district && !in_array($district, $districts, true) && strcasecmp($district, $cityClean) !== 0) {
                                $districts[] = $district;
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning("Geoapify getDistricts failed for {$cityClean}: " . $e->getMessage());
                }
            }

            // 2. Gabungkan dengan curated fallback distrik kota-kota populer
            $fallbackDistricts = $this->getCuratedDistrictsByCity($cityClean);
            foreach ($fallbackDistricts as $fd) {
                if (!in_array($fd, $districts, true)) {
                    $districts[] = $fd;
                }
            }

            if (empty($districts)) {
                $districts = [
                    "{$cityClean} Pusat / Central Business District",
                    "{$cityClean} Utara (North District)",
                    "{$cityClean} Selatan (South District)",
                    "{$cityClean} Timur (East Industrial Park)",
                    "{$cityClean} Barat (West Commercial Zone)",
                    "Kawasan Logistik & Roastery {$cityClean}",
                ];
            }

            sort($districts);
            return $districts;
        });
    }

    /**
     * Geocode alamat lengkap via Geoapify API
     */
    public function geocode(string $query, ?string $country = null, ?string $city = null, ?string $district = null): ?array
    {
        $searchParts = array_filter([$query, $district, $city, $country]);
        $searchText = implode(', ', $searchParts);

        if (empty($searchText)) {
            return null;
        }

        $cacheKey = 'geo_geocode_' . md5(strtolower($searchText));

        return Cache::remember($cacheKey, 86400, function () use ($searchText, $country, $city, $district) {
            $apiKey = config('services.geoapify.key', env('GEOAPIFY_API_KEY', '11121e44a86f40d593e90176dda45e95'));
            $baseUrl = rtrim(config('services.geoapify.url', env('GEOAPIFY_BASE_URL', 'https://api.geoapify.com/v1')), '/');

            if (!empty($apiKey)) {
                try {
                    $response = Http::timeout(6)->retry(2, 100)->get("{$baseUrl}/geocode/search", [
                        'text' => $searchText,
                        'limit' => 1,
                        'apiKey' => $apiKey,
                    ]);

                    if ($response->successful()) {
                        $features = $response->json()['features'] ?? [];
                        if (!empty($features[0]['properties'])) {
                            $p = $features[0]['properties'];
                            return [
                                'formatted' => $p['formatted'] ?? $searchText,
                                'country'   => $p['country'] ?? $country,
                                'city'      => $p['city'] ?? $city,
                                'district'  => $p['district'] ?? $p['suburb'] ?? $district,
                                'lat'       => (float) ($p['lat'] ?? 0),
                                'lng'       => (float) ($p['lon'] ?? 0),
                                'postcode'  => $p['postcode'] ?? null,
                                'source'    => 'Geoapify Live API',
                            ];
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning("Geoapify Geocoding failed: " . $e->getMessage());
                }
            }

            // Fallback koordinat kota jika geocoding eksternal gagal
            $cityCoords = $this->getCityCoordinates($city ?? '', $country ?? '');

            return [
                'formatted' => $searchText,
                'country'   => $country,
                'city'      => $city,
                'district'  => $district,
                'lat'       => $cityCoords['lat'],
                'lng'       => $cityCoords['lng'],
                'postcode'  => null,
                'source'    => 'Curated City Coordinates',
            ];
        });
    }

    /**
     * Curated Master Kota per Negara
     */
    protected function getCuratedCitiesByCountry(string $country): array
    {
        $c = strtolower(trim($country));

        $database = [
            'brazil' => ['Santos', 'São Paulo', 'Rio de Janeiro', 'Campinas', 'Belo Horizonte', 'Curitiba', 'Porto Alegre', 'Salvador', 'Recife', 'Brasília', 'Vitória', 'Paranaguá'],
            'indonesia' => ['Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Semarang', 'Makassar', 'Denpasar (Bali)', 'Yogyakarta', 'Palembang', 'Balikpapan', 'Batam', 'Malang'],
            'united kingdom' => ['London', 'Manchester', 'Birmingham', 'Liverpool', 'Edinburgh', 'Glasgow', 'Bristol', 'Leeds', 'Southampton', 'Newcastle'],
            'inggris' => ['London', 'Manchester', 'Birmingham', 'Liverpool', 'Edinburgh', 'Glasgow', 'Bristol', 'Leeds', 'Southampton', 'Newcastle'],
            'united states' => ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Miami', 'Seattle', 'San Francisco', 'Boston', 'Atlanta', 'Dallas'],
            'amerika serikat' => ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Miami', 'Seattle', 'San Francisco', 'Boston', 'Atlanta', 'Dallas'],
            'germany' => ['Hamburg', 'Berlin', 'Munich', 'Frankfurt', 'Cologne', 'Stuttgart', 'Düsseldorf', 'Bremen', 'Hannover', 'Leipzig'],
            'jerman' => ['Hamburg', 'Berlin', 'Munich', 'Frankfurt', 'Cologne', 'Stuttgart', 'Düsseldorf', 'Bremen', 'Hannover', 'Leipzig'],
            'netherlands' => ['Rotterdam', 'Amsterdam', 'The Hague', 'Utrecht', 'Eindhoven', 'Groningen', 'Tilburg', 'Almere', 'Breda', 'Nijmegen'],
            'belanda' => ['Rotterdam', 'Amsterdam', 'The Hague', 'Utrecht', 'Eindhoven', 'Groningen', 'Tilburg', 'Almere', 'Breda', 'Nijmegen'],
            'japan' => ['Tokyo', 'Yokohama', 'Osaka', 'Nagoya', 'Sapporo', 'Fukuoka', 'Kobe', 'Kyoto', 'Kawasaki', 'Hiroshima'],
            'jepang' => ['Tokyo', 'Yokohama', 'Osaka', 'Nagoya', 'Sapporo', 'Fukuoka', 'Kobe', 'Kyoto', 'Kawasaki', 'Hiroshima'],
            'china' => ['Shanghai', 'Beijing', 'Guangzhou', 'Shenzhen', 'Ningbo', 'Tianjin', 'Qingdao', 'Xiamen', 'Hong Kong', 'Chengdu'],
            'tiongkok' => ['Shanghai', 'Beijing', 'Guangzhou', 'Shenzhen', 'Ningbo', 'Tianjin', 'Qingdao', 'Xiamen', 'Hong Kong', 'Chengdu'],
            'singapore' => ['Singapore', 'Jurong', 'Changi', 'Tampines', 'Woodlands', 'Marina Bay', 'Tuas'],
            'singapura' => ['Singapore', 'Jurong', 'Changi', 'Tampines', 'Woodlands', 'Marina Bay', 'Tuas'],
            'malaysia' => ['Kuala Lumpur', 'George Town (Penang)', 'Johor Bahru', 'Port Klang', 'Kota Kinabalu', 'Kuching', 'Ipoh', 'Melaka'],
            'australia' => ['Sydney', 'Melbourne', 'Brisbane', 'Perth', 'Adelaide', 'Gold Coast', 'Fremantle', 'Darwin', 'Hobart', 'Canberra'],
            'russia' => ['Moscow', 'Saint Petersburg', 'Novosibirsk', 'Vladivostok', 'Krasnoyarsk', 'Yekaterinburg', 'Kazan', 'Nizhny Novgorod', 'Samara', 'Novorossiysk'],
            'rusia' => ['Moskow', 'Saint Petersburg', 'Novosibirsk', 'Vladivostok', 'Krasnoyarsk', 'Yekaterinburg', 'Kazan', 'Nizhny Novgorod', 'Samara', 'Novorossiysk'],
            'france' => ['Paris', 'Marseille', 'Le Havre', 'Lyon', 'Toulouse', 'Nice', 'Nantes', 'Bordeaux', 'Lille', 'Strasbourg'],
            'prancis' => ['Paris', 'Marseille', 'Le Havre', 'Lyon', 'Toulouse', 'Nice', 'Nantes', 'Bordeaux', 'Lille', 'Strasbourg'],
            'italy' => ['Rome', 'Milan', 'Genoa', 'Naples', 'Turin', 'Palermo', 'Bologna', 'Florence', 'Trieste', 'Venice'],
            'italia' => ['Roma', 'Milan', 'Genoa', 'Napoli', 'Turin', 'Palermo', 'Bologna', 'Florence', 'Trieste', 'Venesia'],
            'united arab emirates' => ['Dubai', 'Abu Dhabi', 'Sharjah', 'Jebel Ali', 'Ajman', 'Ras Al Khaimah', 'Fujairah'],
            'uni emirat arab' => ['Dubai', 'Abu Dhabi', 'Sharjah', 'Jebel Ali', 'Ajman', 'Ras Al Khaimah', 'Fujairah'],
            'south korea' => ['Seoul', 'Busan', 'Incheon', 'Daegu', 'Daejeon', 'Gwangju', 'Ulsan', 'Suwon'],
            'korea selatan' => ['Seoul', 'Busan', 'Incheon', 'Daegu', 'Daejeon', 'Gwangju', 'Ulsan', 'Suwon'],
        ];

        foreach ($database as $key => $cities) {
            if ($c === $key || str_contains($c, $key) || str_contains($key, $c)) {
                return $cities;
            }
        }

        // Default kota metropolitan umum jika negara baru
        return [
            ucwords($country) . ' Capital City',
            ucwords($country) . ' Port Hub',
            ucwords($country) . ' Commercial Center',
            ucwords($country) . ' International Logistics Zone',
        ];
    }

    /**
     * Curated Master Daerah / Kecamatan per Kota Populer
     */
    protected function getCuratedDistrictsByCity(string $city): array
    {
        $c = strtolower(trim($city));

        $database = [
            'santos' => ['Gonzaga', 'Ponta da Praia', 'Porto de Santos Terminal', 'Vila Mathias', 'Boqueirão', 'Embaré', 'Aparecida', 'Centro Histórico'],
            'são paulo' => ['Jardins', 'Pinheiros', 'Itaim Bibi', 'Vila Madalena', 'Moema', 'Bela Vista', 'Morumbi', 'Santana', 'Tatuapé', 'Guarulhos Cargo Zone'],
            'jakarta' => ['Kebayoran Baru (Senopati)', 'Menteng', 'SCBD Sudirman', 'Kelapa Gading', 'Pantai Indah Kapuk (PIK)', 'Tanjung Priok Port Area', 'Cilandak', 'Kemang', 'Pondok Indah', 'Tanah Abang'],
            'surabaya' => ['Tanjung Perak Port Basin', 'Gubeng', 'Tegalsari', 'Rungkut Industri', 'Wonokromo', 'Kenjeran', 'Sambikerep', 'Sukolilo'],
            'bandung' => ['Dago', 'Riau (LLRE Martadinata)', 'Ciumbuleuit', 'Buahbatu', 'Sukajadi', 'Pasirkaliki', 'Gedebage Logistics Hub'],
            'london' => ['Westminster (Upper Montagu)', 'City of London', 'Camden', 'Shoreditch', 'Kensington & Chelsea', 'Canary Wharf', 'Greenwich', 'Soho', 'Hackney'],
            'rotterdam' => ['Centrum', 'Maasvlakte Logistics Hub', 'Kralingen', 'Delfshaven', 'Feijenoord', 'Kop van Zuid', 'Prins Alexander'],
            'amsterdam' => ['Centrum', 'Zuid (Zuidas)', 'De Pijp', 'Jordaan', 'Westpoort Port Area', 'Oost', 'Noord'],
            'hamburg' => ['Altona', 'HafenCity', 'Hamburg-Mitte', 'Eimsbüttel', 'Wandsbek', 'Harburg Port Zone', 'Bergedorf', 'St. Pauli'],
            'tokyo' => ['Shibuya Specialty District', 'Shinjuku', 'Ginza (Chuo)', 'Roppongi (Minato)', 'Ota (Haneda Air Logistics)', 'Akihabara', 'Koto Port Hub'],
            'new york' => ['Manhattan (Midtown)', 'Brooklyn (DUMBO)', 'Queens (Long Island City)', 'SoHo', 'Tribeca', 'Williamsburg', 'Financial District'],
            'los angeles' => ['Downtown LA (DTLA)', 'Santa Monica', 'San Pedro (Port of LA)', 'Long Beach Harbor', 'Beverly Hills', 'Hollywood', 'Culver City'],
            'singapore' => ['Tanjong Pagar', 'Orchard', 'Marina Bay', 'Changi Air Freight Center', 'Jurong Industrial Estate', 'Bugis', 'Tiong Bahru'],
            'dubai' => ['Downtown Dubai', 'Dubai Marina', 'Jebel Ali Free Zone (JAFZA)', 'Business Bay', 'Deira', 'Al Quoz Industrial Roasteries'],
            'moscow' => ['Tverskoy Central', 'Arbat', 'Presnensky (Moscow City)', 'Khamovniki', 'Basmanny', 'Danilovsky Logistics Zone', 'Sheremetyevo Hub'],
            'moskow' => ['Tverskoy Central', 'Arbat', 'Presnensky (Moscow City)', 'Khamovniki', 'Basmanny', 'Danilovsky Logistics Zone', 'Sheremetyevo Hub'],
        ];

        foreach ($database as $key => $districts) {
            if ($c === $key || str_contains($c, $key) || str_contains($key, $c)) {
                return $districts;
            }
        }

        return [];
    }

    /**
     * Koordinat fallback kota jika geocoding API tidak terjangkau
     */
    protected function getCityCoordinates(string $city, string $country): array
    {
        $c = strtolower(trim($city));

        $coords = [
            'santos'        => ['lat' => -23.9618, 'lng' => -46.3322],
            'são paulo'     => ['lat' => -23.5505, 'lng' => -46.6333],
            'jakarta'       => ['lat' => -6.2088,  'lng' => 106.8456],
            'surabaya'      => ['lat' => -7.2575,  'lng' => 112.7521],
            'bandung'       => ['lat' => -6.9175,  'lng' => 107.6191],
            'london'        => ['lat' => 51.5074,  'lng' => -0.1278],
            'rotterdam'     => ['lat' => 51.9244,  'lng' => 4.4777],
            'amsterdam'     => ['lat' => 52.3676,  'lng' => 4.9041],
            'hamburg'       => ['lat' => 53.5511,  'lng' => 9.9937],
            'tokyo'         => ['lat' => 35.6762,  'lng' => 139.6503],
            'new york'      => ['lat' => 40.7128,  'lng' => -74.0060],
            'los angeles'   => ['lat' => 34.0522,  'lng' => -118.2437],
            'singapore'     => ['lat' => 1.3521,   'lng' => 103.8198],
            'dubai'         => ['lat' => 25.2048,  'lng' => 55.2708],
            'moscow'        => ['lat' => 55.7558,  'lng' => 37.6173],
            'moskow'        => ['lat' => 55.7558,  'lng' => 37.6173],
        ];

        if (isset($coords[$c])) {
            return $coords[$c];
        }

        /** @var GlobalIntelligenceService $globalService */
        $globalService = app(GlobalIntelligenceService::class);
        $countries = $globalService->getCountriesMaster();
        foreach ($countries as $ctry) {
            if (strcasecmp($ctry['name'], $country) === 0 || strcasecmp($ctry['code'], $country) === 0) {
                return ['lat' => (float) $ctry['lat'], 'lng' => (float) $ctry['lng']];
            }
        }

        return ['lat' => -23.9618, 'lng' => -46.3322]; // Default: Santos, Brazil
    }
}

