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
     * Dapatkan seluruh provinsi / state dari suatu negara (CountriesNow API + cache 24 jam)
     *
     * @return array<int, string>
     */
    public function getProvinces(string $country): array
    {
        $countryClean = trim($country);
        if ($countryClean === '') {
            return [];
        }

        $apiCountry = $this->normalizeCountryForApi($countryClean);
        $cacheKey = 'geo_provinces_v1_' . md5(strtolower($apiCountry));

        return Cache::remember($cacheKey, 86400, function () use ($apiCountry) {
            try {
                $response = Http::timeout(4)->post('https://countriesnow.space/api/v0.1/countries/states', [
                    'country' => $apiCountry,
                ]);

                if ($response->successful()) {
                    $states = $response->json('data.states') ?? [];
                    $names = [];
                    foreach ($states as $st) {
                        $name = trim((string) ($st['name'] ?? ''));
                        if ($name !== '') {
                            $names[] = $name;
                        }
                    }

                    if (! empty($names)) {
                        return array_values(array_unique($names));
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('CountriesNow states API unreachable: ' . $e->getMessage());
            }

            return [];
        });
    }

    /**
     * Dapatkan daftar kota berdasarkan negara dan provinsi yang dipilih
     *
     * @return array<int, string>
     */
    public function getCitiesByProvince(string $country, string $province): array
    {
        $countryClean = trim($country);
        $provinceClean = trim($province);
        if ($countryClean === '' || $provinceClean === '') {
            return [];
        }

        $apiCountry = $this->normalizeCountryForApi($countryClean);
        $cacheKey = 'geo_prov_cities_v1_' . md5(strtolower($apiCountry . '|' . $provinceClean));

        return Cache::remember($cacheKey, 86400, function () use ($apiCountry, $provinceClean) {
            try {
                $response = Http::timeout(4)->post('https://countriesnow.space/api/v0.1/countries/state/cities', [
                    'country' => $apiCountry,
                    'state'   => $provinceClean,
                ]);

                if ($response->successful()) {
                    $cities = $response->json('data') ?? [];
                    if (is_array($cities) && ! empty($cities)) {
                        return array_values(array_unique(array_filter(array_map('trim', $cities))));
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('CountriesNow state cities API unreachable: ' . $e->getMessage());
            }

            return [];
        });
    }

    private function normalizeCountryForApi(string $country): string
    {
        $map = [
            'indonesia' => 'Indonesia',
            'malaysia' => 'Malaysia',
            'singapura' => 'Singapore',
            'jepang' => 'Japan',
            'tiongkok' => 'China',
            'korea selatan' => 'South Korea',
            'filipina' => 'Philippines',
            'amerika serikat' => 'United States',
            'inggris raya' => 'United Kingdom',
            'inggris' => 'United Kingdom',
            'belanda' => 'Netherlands',
            'jerman' => 'Germany',
            'prancis' => 'France',
            'italia' => 'Italy',
            'spanyol' => 'Spain',
            'turki' => 'Turkey',
            'rusia' => 'Russia',
            'arab saudi' => 'Saudi Arabia',
            'uni emirat arab' => 'United Arab Emirates',
            'brasil' => 'Brazil',
            'kamboja' => 'Cambodia',
            'swiss' => 'Switzerland',
            'belgia' => 'Belgium',
        ];

        $lower = strtolower($country);
        return $map[$lower] ?? $country;
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

        $cacheKey = 'geo_cities_v2_' . md5(strtolower($countryClean));

        return Cache::remember($cacheKey, 86400, function () use ($countryClean) {
            $apiKey = config('services.geoapify.key', env('GEOAPIFY_API_KEY', '11121e44a86f40d593e90176dda45e95'));
            $baseUrl = rtrim(config('services.geoapify.url', env('GEOAPIFY_BASE_URL', 'https://api.geoapify.com/v1')), '/');

            $cities = [];
            $iso = $this->getCountryIso($countryClean);

            // 1. Ambil kota-kota secara live dari Geoapify Search API
            if (!empty($apiKey)) {
                try {
                    $params = [
                        'apiKey' => $apiKey,
                        'limit'  => 20,
                    ];

                    if ($iso) {
                        $params['text'] = 'town';
                        $params['filter'] = 'countrycode:' . strtolower($iso);
                    } else {
                        $params['text'] = $countryClean;
                        $params['type'] = 'city';
                    }

                    $response = Http::timeout(5)->retry(2, 100)->get("{$baseUrl}/geocode/search", $params);

                    if ($response->successful()) {
                        $features = $response->json()['features'] ?? [];
                        foreach ($features as $f) {
                            $prop = $f['properties'] ?? [];
                            $city = $prop['city'] ?? $prop['name'] ?? null;
                            if ($city && !in_array($city, $cities, true) && strcasecmp(trim($city), $countryClean) !== 0) {
                                $cities[] = $city;
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning("Geoapify getCities failed for {$countryClean}: " . $e->getMessage());
                }
            }

            // 2. Gabungkan dengan master curated kota riil per negara
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
     * Dapatkan kode ISO dari nama negara
     */
    public function getCountryIso(string $country): ?string
    {
        $countries = $this->getCountries();
        $clean = strtolower(trim($country));
        foreach ($countries as $c) {
            $cName = strtolower($c['name']);
            $cCode = strtolower($c['code']);
            if ($cName === $clean || $cCode === $clean || str_contains($cName, $clean) || str_contains($clean, $cName)) {
                return strtolower($c['iso'] ?? $c['code']);
            }
        }
        return null;
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
            // ASEAN / Asia Tenggara
            'indonesia'             => ['Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Semarang', 'Makassar', 'Denpasar (Bali)', 'Palembang', 'Yogyakarta', 'Balikpapan', 'Batam', 'Malang', 'Pekanbaru', 'Bandar Lampung', 'Banjarmasin', 'Pontianak', 'Manado', 'Cilegon', 'Samarinda'],
            'brunei'                => ['Bandar Seri Begawan', 'Kuala Belait', 'Seria', 'Tutong', 'Bangar', 'Muara', 'Jerudong', 'Gadong', 'Sengkurong', 'Berakas', 'Lumut', 'Sungai Liang'],
            'brunei darussalam'     => ['Bandar Seri Begawan', 'Kuala Belait', 'Seria', 'Tutong', 'Bangar', 'Muara', 'Jerudong', 'Gadong', 'Sengkurong', 'Berakas', 'Lumut', 'Sungai Liang'],
            'malaysia'              => ['Kuala Lumpur', 'George Town (Penang)', 'Johor Bahru', 'Port Klang', 'Ipoh', 'Kota Kinabalu', 'Kuching', 'Shah Alam', 'Melaka', 'Petaling Jaya', 'Alor Setar', 'Kuantan', 'Miri', 'Sandakan'],
            'singapura'             => ['Singapore', 'Jurong', 'Changi', 'Tampines', 'Woodlands', 'Marina Bay', 'Tuas', 'Bedok', 'Yishun', 'Ang Mo Kio'],
            'singapore'             => ['Singapore', 'Jurong', 'Changi', 'Tampines', 'Woodlands', 'Marina Bay', 'Tuas', 'Bedok', 'Yishun', 'Ang Mo Kio'],
            'thailand'              => ['Bangkok', 'Nonthaburi', 'Chiang Mai', 'Phuket', 'Pattaya', 'Chonburi', 'Hat Yai', 'Nakhon Ratchasima', 'Udon Thani', 'Laem Chabang', 'Samut Prakan'],
            'vietnam'               => ['Ho Chi Minh City', 'Hanoi', 'Da Nang', 'Hai Phong', 'Can Tho', 'Bien Hoa', 'Nha Trang', 'Vung Tau', 'Hue', 'Buon Ma Thuot'],
            'filipina'              => ['Manila', 'Quezon City', 'Davao City', 'Cebu City', 'Caloocan', 'Zamboanga', 'Taguig', 'Pasig', 'Cagayan de Oro', 'Makati'],
            'philippines'           => ['Manila', 'Quezon City', 'Davao City', 'Cebu City', 'Caloocan', 'Zamboanga', 'Taguig', 'Pasig', 'Cagayan de Oro', 'Makati'],
            'myanmar'               => ['Yangon', 'Mandalay', 'Naypyidaw', 'Mawlamyine', 'Bago', 'Pathein', 'Monywa', 'Sittwe', 'Taunggyi'],
            'kamboja'               => ['Phnom Penh', 'Siem Reap', 'Battambang', 'Sihanoukville', 'Kampong Cham', 'Poipet', 'Kampot'],
            'cambodia'              => ['Phnom Penh', 'Siem Reap', 'Battambang', 'Sihanoukville', 'Kampong Cham', 'Poipet', 'Kampot'],
            'laos'                  => ['Vientiane', 'Luang Prabang', 'Pakse', 'Savannakhet', 'Thakhek', 'Vang Vieng'],
            'timor leste'           => ['Dili', 'Baucau', 'Maliana', 'Suai', 'Ermera', 'Liquiçá', 'Lospalos', 'Ainaro', 'Oecusse'],

            // Asia Timur & Selatan
            'tiongkok'              => ['Shanghai', 'Beijing', 'Guangzhou', 'Shenzhen', 'Ningbo', 'Tianjin', 'Qingdao', 'Xiamen', 'Hong Kong', 'Chengdu', 'Hangzhou', 'Wuhan', 'Chongqing', 'Dalian'],
            'china'                 => ['Shanghai', 'Beijing', 'Guangzhou', 'Shenzhen', 'Ningbo', 'Tianjin', 'Qingdao', 'Xiamen', 'Hong Kong', 'Chengdu', 'Hangzhou', 'Wuhan', 'Chongqing', 'Dalian'],
            'jepang'                => ['Tokyo', 'Yokohama', 'Osaka', 'Nagoya', 'Sapporo', 'Fukuoka', 'Kobe', 'Kyoto', 'Kawasaki', 'Hiroshima', 'Sendai', 'Kitakyushu'],
            'japan'                 => ['Tokyo', 'Yokohama', 'Osaka', 'Nagoya', 'Sapporo', 'Fukuoka', 'Kobe', 'Kyoto', 'Kawasaki', 'Hiroshima', 'Sendai', 'Kitakyushu'],
            'korea selatan'         => ['Seoul', 'Busan', 'Incheon', 'Daegu', 'Daejeon', 'Gwangju', 'Ulsan', 'Suwon', 'Changwon', 'Seongnam', 'Goyang'],
            'south korea'           => ['Seoul', 'Busan', 'Incheon', 'Daegu', 'Daejeon', 'Gwangju', 'Ulsan', 'Suwon', 'Changwon', 'Seongnam', 'Goyang'],
            'taiwan'                => ['Taipei', 'Kaohsiung', 'Taichung', 'Tainan', 'Taoyuan', 'Hsinchu', 'Keelung'],
            'hong kong'             => ['Central', 'Kowloon', 'Tsuen Wan', 'Sha Tin', 'Tuen Mun', 'Kwun Tong', 'Wan Chai'],
            'makau'                 => ['Sé', 'Nossa Senhora de Fátima', 'Taipa', 'Coloane', 'Cotai'],
            'mongolia'              => ['Ulaanbaatar', 'Erdenet', 'Darkhan', 'Choibalsan', 'Moron'],
            'korea utara'           => ['Pyongyang', 'Hamhung', 'Chongjin', 'Nampo', 'Wonsan'],
            'india'                 => ['Mumbai', 'Delhi', 'Bengaluru', 'Chennai', 'Kolkata', 'Hyderabad', 'Ahmedabad', 'Pune', 'Surat', 'Kochi', 'Visakhapatnam'],
            'pakistan'              => ['Karachi', 'Lahore', 'Faisalabad', 'Rawalpindi', 'Gujranwala', 'Peshawar', 'Multan', 'Islamabad', 'Gwadar', 'Quetta'],
            'bangladesh'            => ['Dhaka', 'Chittagong', 'Khulna', 'Rajshahi', 'Sylhet', 'Gazipur', 'Barisal', 'Rangpur'],
            'sri lanka'             => ['Colombo', 'Kandy', 'Galle', 'Jaffna', 'Negombo', 'Hambantota', 'Trincomalee'],
            'nepal'                 => ['Kathmandu', 'Pokhara', 'Lalitpur', 'Bharatpur', 'Biratnagar', 'Birgunj'],
            'maladewa'              => ['Malé', 'Hulhumalé', 'Addu City', 'Fuvahmulah'],

            // Timur Tengah
            'arab saudi'            => ['Riyadh', 'Jeddah', 'Dammam', 'Mecca', 'Medina', 'Khobar', 'Jubail', 'Yanbu', 'Tabuk', 'Abha'],
            'saudi arabia'          => ['Riyadh', 'Jeddah', 'Dammam', 'Mecca', 'Medina', 'Khobar', 'Jubail', 'Yanbu', 'Tabuk', 'Abha'],
            'uni emirat arab'       => ['Dubai', 'Abu Dhabi', 'Sharjah', 'Jebel Ali', 'Ajman', 'Ras Al Khaimah', 'Fujairah', 'Al Ain'],
            'united arab emirates'  => ['Dubai', 'Abu Dhabi', 'Sharjah', 'Jebel Ali', 'Ajman', 'Ras Al Khaimah', 'Fujairah', 'Al Ain'],
            'qatar'                 => ['Doha', 'Al Rayyan', 'Al Wakrah', 'Al Khor', 'Mesaieed', 'Lusail'],
            'kuwait'                => ['Kuwait City', 'Al Ahmadi', 'Hawalli', 'Salmiya', 'Sabah Al Salem', 'Shuwaikh'],
            'oman'                  => ['Muscat', 'Salalah', 'Sohar', 'Seeb', 'Nizwa', 'Sur', 'Duqm'],
            'bahrain'               => ['Manama', 'Riffa', 'Muharraq', 'Hamad Town', 'Sitra'],
            'irak'                  => ['Baghdad', 'Basra', 'Erbil', 'Mosul', 'Sulaymaniyah', 'Najaf', 'Karbala', 'Umm Qasr'],
            'iran'                  => ['Tehran', 'Mashhad', 'Isfahan', 'Shiraz', 'Tabriz', 'Ahvaz', 'Bandar Abbas', 'Bushehr'],
            'turki'                 => ['Istanbul', 'Ankara', 'Izmir', 'Bursa', 'Antalya', 'Adana', 'Mersin', 'Gaziantep', 'Trabzon'],
            'turkey'                => ['Istanbul', 'Ankara', 'Izmir', 'Bursa', 'Antalya', 'Adana', 'Mersin', 'Gaziantep', 'Trabzon'],
            'yordania'              => ['Amman', 'Zarqa', 'Irbid', 'Aqaba'],
            'lebanon'               => ['Beirut', 'Tripoli', 'Sidon', 'Tyre'],
            'israel'                => ['Tel Aviv', 'Jerusalem', 'Haifa', 'Ashdod', 'Beersheba'],
            'palestina'             => ['Gaza City', 'Ramallah', 'Hebron', 'Nablus'],

            // Eropa
            'inggris raya'          => ['London', 'Manchester', 'Birmingham', 'Liverpool', 'Edinburgh', 'Glasgow', 'Bristol', 'Leeds', 'Southampton', 'Newcastle', 'Belfast', 'Cardiff'],
            'united kingdom'        => ['London', 'Manchester', 'Birmingham', 'Liverpool', 'Edinburgh', 'Glasgow', 'Bristol', 'Leeds', 'Southampton', 'Newcastle', 'Belfast', 'Cardiff'],
            'belanda'               => ['Rotterdam', 'Amsterdam', 'The Hague', 'Utrecht', 'Eindhoven', 'Groningen', 'Tilburg', 'Almere', 'Breda', 'Nijmegen', 'Maastricht'],
            'netherlands'           => ['Rotterdam', 'Amsterdam', 'The Hague', 'Utrecht', 'Eindhoven', 'Groningen', 'Tilburg', 'Almere', 'Breda', 'Nijmegen', 'Maastricht'],
            'jerman'                => ['Hamburg', 'Berlin', 'Munich', 'Frankfurt', 'Cologne', 'Stuttgart', 'Düsseldorf', 'Bremen', 'Hannover', 'Leipzig', 'Nuremberg', 'Dresden'],
            'germany'               => ['Hamburg', 'Berlin', 'Munich', 'Frankfurt', 'Cologne', 'Stuttgart', 'Düsseldorf', 'Bremen', 'Hannover', 'Leipzig', 'Nuremberg', 'Dresden'],
            'prancis'               => ['Paris', 'Marseille', 'Le Havre', 'Lyon', 'Toulouse', 'Nice', 'Nantes', 'Bordeaux', 'Lille', 'Strasbourg', 'Montpellier'],
            'france'                => ['Paris', 'Marseille', 'Le Havre', 'Lyon', 'Toulouse', 'Nice', 'Nantes', 'Bordeaux', 'Lille', 'Strasbourg', 'Montpellier'],
            'italia'                => ['Rome', 'Milan', 'Genoa', 'Naples', 'Turin', 'Palermo', 'Bologna', 'Florence', 'Trieste', 'Venice', 'Verona', 'Bari'],
            'italy'                 => ['Rome', 'Milan', 'Genoa', 'Naples', 'Turin', 'Palermo', 'Bologna', 'Florence', 'Trieste', 'Venice', 'Verona', 'Bari'],
            'spanyol'               => ['Madrid', 'Barcelona', 'Valencia', 'Seville', 'Zaragoza', 'Malaga', 'Bilbao', 'Alicante', 'Cadiz', 'Algeciras'],
            'spain'                 => ['Madrid', 'Barcelona', 'Valencia', 'Seville', 'Zaragoza', 'Malaga', 'Bilbao', 'Alicante', 'Cadiz', 'Algeciras'],
            'portugal'              => ['Lisbon', 'Porto', 'Sines', 'Braga', 'Coimbra', 'Setubal', 'Faro'],
            'belgia'                => ['Brussels', 'Antwerp', 'Ghent', 'Charleroi', 'Liège', 'Bruges'],
            'swiss'                 => ['Zurich', 'Geneva', 'Basel', 'Bern', 'Lausanne', 'Lucerne'],
            'austria'               => ['Vienna', 'Graz', 'Linz', 'Salzburg', 'Innsbruck'],
            'irlandia'              => ['Dublin', 'Cork', 'Limerick', 'Galway', 'Waterford'],
            'swedia'                => ['Stockholm', 'Gothenburg', 'Malmö', 'Uppsala', 'Helsingborg'],
            'norwegia'              => ['Oslo', 'Bergen', 'Trondheim', 'Stavanger', 'Drammen'],
            'denmark'               => ['Copenhagen', 'Aarhus', 'Odense', 'Aalborg'],
            'finlandia'             => ['Helsinki', 'Espoo', 'Tampere', 'Vantaa', 'Oulu', 'Turku'],
            'polandia'              => ['Warsaw', 'Krakow', 'Lodz', 'Wroclaw', 'Poznan', 'Gdansk'],
            'yunani'                => ['Athens', 'Thessaloniki', 'Patras', 'Heraklion', 'Piraeus'],
            'rusia'                 => ['Moskow', 'Saint Petersburg', 'Novosibirsk', 'Vladivostok', 'Krasnoyarsk', 'Yekaterinburg', 'Kazan', 'Nizhny Novgorod', 'Samara', 'Novorossiysk'],
            'russia'                => ['Moscow', 'Saint Petersburg', 'Novosibirsk', 'Vladivostok', 'Krasnoyarsk', 'Yekaterinburg', 'Kazan', 'Nizhny Novgorod', 'Samara', 'Novorossiysk'],

            // Benua Amerika & Australia
            'brazil'                => ['Santos', 'São Paulo', 'Rio de Janeiro', 'Campinas', 'Belo Horizonte', 'Curitiba', 'Porto Alegre', 'Salvador', 'Recife', 'Brasília', 'Vitória', 'Paranaguá', 'Fortaleza', 'Manaus', 'Belém'],
            'amerika serikat'       => ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Miami', 'Seattle', 'San Francisco', 'Boston', 'Atlanta', 'Dallas', 'Philadelphia', 'Washington D.C.', 'New Orleans'],
            'united states'         => ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Miami', 'Seattle', 'San Francisco', 'Boston', 'Atlanta', 'Dallas', 'Philadelphia', 'Washington D.C.', 'New Orleans'],
            'australia'             => ['Sydney', 'Melbourne', 'Brisbane', 'Perth', 'Adelaide', 'Gold Coast', 'Fremantle', 'Darwin', 'Hobart', 'Canberra', 'Newcastle'],
        ];

        foreach ($database as $key => $cities) {
            if ($c === $key || str_contains($c, $key) || str_contains($key, $c)) {
                return $cities;
            }
        }

        return [];
    }

    /**
     * Curated Master Daerah / Kecamatan per Kota Populer
     */
    protected function getCuratedDistrictsByCity(string $city): array
    {
        $c = strtolower(trim($city));

        $database = [
            // Brunei
            'bandar seri begawan'   => ['Kiulap', 'Gadong Commercial Area', 'Berakas', 'Kumbang Pasang', 'Mabohai', 'Bandar Central', 'Muara Port Basin', 'Kota Batu', 'Pangkalan Gadong', 'Tasek Lama'],
            'kuala belait'          => ['KB Town', 'Pandan', 'Mumong', 'Kuala Balai', 'Sungai Liang', 'Labi Industrial Hub'],
            'seria'                 => ['Pekan Seria', 'Lorong Tiga', 'Badas', 'Anduki Airfield', 'Panaga'],
            'tutong'                => ['Pekan Tutong', 'Telisai', 'Tanjong Maya', 'Keriam', 'Kiudang', 'Lamunin'],
            'muara'                 => ['Muara Town Terminal', 'Serasa Port Zone', 'Pelabuhan Muara', 'Kapok', 'Meragang'],
            'bangar'                => ['Pekan Bangar', 'Labu Estate', 'Batu Apoi', 'Amo', 'Bokok'],
            'jerudong'              => ['Jerudong Park Area', 'Sengkurong', 'Tanjong Bunut', 'Mulaut'],
            'gadong'                => ['Gadong Central', 'Menglait', 'Pangkalan Gadong', 'Kiulap Commercial Hub'],

            // Indonesia
            'jakarta'               => ['Kebayoran Baru (Senopati)', 'Menteng', 'SCBD Sudirman', 'Kelapa Gading', 'Pantai Indah Kapuk (PIK)', 'Tanjung Priok Port Area', 'Cilandak', 'Kemang', 'Pondok Indah', 'Tanah Abang', 'Setiabudi (Kuningan)'],
            'surabaya'              => ['Tanjung Perak Port Basin', 'Gubeng', 'Tegalsari', 'Rungkut Industri', 'Wonokromo', 'Kenjeran', 'Sambikerep', 'Sukolilo', 'Genteng'],
            'bandung'               => ['Dago (Coblong)', 'Braga (Sumur Bandung)', 'Riau (LLRE Martadinata)', 'Ciumbuleuit', 'Buahbatu', 'Sukajadi (PVJ)', 'Pasirkaliki', 'Gedebage Logistics Hub'],
            'medan'                 => ['Medan Kota', 'Medan Baru', 'Medan Petisah', 'Medan Barat', 'Medan Sunggal', 'Belawan Port Area'],
            'semarang'              => ['Semarang Tengah', 'Tanjung Emas Port', 'Semarang Barat', 'Semarang Selatan', 'Candisari', 'Banyumanik'],
            'denpasar (bali)'       => ['Sanur (Denpasar Selatan)', 'Kuta', 'Seminyak', 'Canggu', 'Ubud', 'Denpasar Barat', 'Denpasar Timur'],

            // Malaysia & Singapura
            'kuala lumpur'          => ['KLCC Bukit Bintang', 'Bangsar', 'Mont Kiara', 'Damansara', 'Cheras', 'Sentul', 'Port Klang Gateway'],
            'singapore'             => ['Tanjong Pagar', 'Orchard', 'Marina Bay', 'Changi Air Freight Center', 'Jurong Industrial Estate', 'Bugis', 'Tiong Bahru', 'Tuas Port Hub'],
            'bangkok'               => ['Sukhumvit', 'Silom', 'Sathorn', 'Chatuchak', 'Bang Na Logistics Hub', 'Khlong Toei Port Area'],

            // Brazil & Global
            'santos'                => ['Gonzaga', 'Ponta da Praia', 'Porto de Santos Terminal', 'Vila Mathias', 'Boqueirão', 'Embaré', 'Aparecida', 'Centro Histórico'],
            'são paulo'             => ['Jardins', 'Pinheiros', 'Itaim Bibi', 'Vila Madalena', 'Moema', 'Bela Vista', 'Morumbi', 'Santana', 'Tatuapé', 'Guarulhos Cargo Zone'],
            'london'                => ['Westminster (Upper Montagu)', 'City of London', 'Camden', 'Shoreditch', 'Kensington & Chelsea', 'Canary Wharf', 'Greenwich', 'Soho', 'Hackney'],
            'rotterdam'             => ['Centrum', 'Maasvlakte Logistics Hub', 'Kralingen', 'Delfshaven', 'Feijenoord', 'Kop van Zuid', 'Prins Alexander'],
            'amsterdam'             => ['Centrum', 'Zuid (Zuidas)', 'De Pijp', 'Jordaan', 'Westpoort Port Area', 'Oost', 'Noord'],
            'hamburg'               => ['Altona', 'HafenCity', 'Hamburg-Mitte', 'Eimsbüttel', 'Wandsbek', 'Harburg Port Zone', 'Bergedorf', 'St. Pauli'],
            'tokyo'                 => ['Shibuya Specialty District', 'Shinjuku', 'Ginza (Chuo)', 'Roppongi (Minato)', 'Ota (Haneda Air Logistics)', 'Akihabara', 'Koto Port Hub'],
            'new york'              => ['Manhattan (Midtown)', 'Brooklyn (DUMBO)', 'Queens (Long Island City)', 'SoHo', 'Tribeca', 'Williamsburg', 'Financial District'],
            'los angeles'           => ['Downtown LA (DTLA)', 'Santa Monica', 'San Pedro (Port of LA)', 'Long Beach Harbor', 'Beverly Hills', 'Hollywood', 'Culver City'],
            'dubai'                 => ['Downtown Dubai', 'Dubai Marina', 'Jebel Ali Free Zone (JAFZA)', 'Business Bay', 'Deira', 'Al Quoz Industrial Roasteries'],
            'moscow'                => ['Tverskoy Central', 'Arbat', 'Presnensky (Moscow City)', 'Khamovniki', 'Basmanny', 'Danilovsky Logistics Zone', 'Sheremetyevo Hub'],
            'moskow'                => ['Tverskoy Central', 'Arbat', 'Presnensky (Moscow City)', 'Khamovniki', 'Basmanny', 'Danilovsky Logistics Zone', 'Sheremetyevo Hub'],
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

