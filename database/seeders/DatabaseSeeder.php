<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\ShipmentItem;
use App\Models\SystemSetting;
use App\Models\TrackingEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with comprehensive global shipment visibility dataset.
     */
    public function run(): void
    {
        // 1. System Settings
        $settings = [
            ['key' => 'app_name', 'value' => 'CargoTrace Global', 'group' => 'general'],
            ['key' => 'app_locale', 'value' => 'id', 'group' => 'general'],
            ['key' => 'default_currency', 'value' => 'USD', 'group' => 'general'],
            ['key' => 'timezone', 'value' => 'Asia/Jakarta', 'group' => 'general'],
            ['key' => 'auto_track_interval', 'value' => '15', 'group' => 'tracking'],
            ['key' => 'delay_threshold_hours', 'value' => '6', 'group' => 'tracking'],
            ['key' => 'enable_weather_alerts', 'value' => '1', 'group' => 'integration'],
            ['key' => 'enable_currency_sync', 'value' => '1', 'group' => 'integration'],
            ['key' => 'risk_score_high_threshold', 'value' => '70', 'group' => 'risk'],
            ['key' => 'risk_score_moderate_threshold', 'value' => '40', 'group' => 'risk'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        // 2. Users & Roles (RBAC)
        $admin = User::firstOrCreate(['email' => 'admin@cargotrace.id'], [
            'name' => 'Administrator Global',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMINISTRATOR,
            'status' => User::STATUS_ACTIVE,
            'company_name' => 'CargoTrace Headquarters',
            'phone' => '+62 21 555-0199',
            'last_login_at' => Carbon::now()->subMinutes(10),
        ]);

        $operator = User::firstOrCreate(['email' => 'operator@cargotrace.id'], [
            'name' => 'Budi Operator Logistik',
            'password' => Hash::make('password'),
            'role' => User::ROLE_LOGISTICS_OPERATOR,
            'status' => User::STATUS_ACTIVE,
            'company_name' => 'CargoTrace Operations Center',
            'phone' => '+62 812-3456-7890',
            'last_login_at' => Carbon::now()->subHours(2),
        ]);

        $customer1 = User::firstOrCreate(['email' => 'customer@globalmart.com'], [
            'name' => 'Global Mart International',
            'password' => Hash::make('password'),
            'role' => User::ROLE_CUSTOMER,
            'status' => User::STATUS_ACTIVE,
            'company_name' => 'Global Mart Ltd.',
            'phone' => '+62 813-9876-5432',
            'last_login_at' => Carbon::now()->subDays(1),
        ]);

        $customer2 = User::firstOrCreate(['email' => 'citra@client.com'], [
            'name' => 'Nusantara Trade Corp',
            'password' => Hash::make('password'),
            'role' => User::ROLE_CUSTOMER,
            'status' => User::STATUS_ACTIVE,
            'company_name' => 'PT Nusantara Trade',
            'phone' => '+62 815-5555-1234',
            'last_login_at' => Carbon::now()->subDays(2),
        ]);

        // 3. Global Carriers (205+ Countries Dataset)
        $this->call(CarrierSeeder::class);
        $carriers = Carrier::all()->keyBy('code');

        // 4. World Port Index Global Dataset (35+ Major Ports & Cargo Hubs across All Continents)
        $locationsData = [
            // Asia Tenggara (Indonesia, SG, MY, TH, VN, PH)
            ['name' => 'Pelabuhan Tanjung Priok', 'city' => 'Jakarta', 'country' => 'Indonesia', 'location_type' => Location::TYPE_PORT, 'latitude' => -6.1020000, 'longitude' => 106.8820000],
            ['name' => 'Pelabuhan Tanjung Perak', 'city' => 'Surabaya', 'country' => 'Indonesia', 'location_type' => Location::TYPE_PORT, 'latitude' => -7.1950000, 'longitude' => 112.7290000],
            ['name' => 'Bandara Internasional Soekarno-Hatta (CGK)', 'city' => 'Tangerang', 'country' => 'Indonesia', 'location_type' => Location::TYPE_AIRPORT, 'latitude' => -6.1270000, 'longitude' => 106.6550000],
            ['name' => 'Bandara Internasional Juanda (SUB)', 'city' => 'Surabaya', 'country' => 'Indonesia', 'location_type' => Location::TYPE_AIRPORT, 'latitude' => -7.3798000, 'longitude' => 112.7874000],
            ['name' => 'Singapore Changi Cargo Mega Terminal (SIN)', 'city' => 'Singapore', 'country' => 'Singapura', 'location_type' => Location::TYPE_AIRPORT, 'latitude' => 1.3644000, 'longitude' => 103.9915000],
            ['name' => 'Kuala Lumpur International Airport Cargo (KUL)', 'city' => 'Sepang', 'country' => 'Malaysia', 'location_type' => Location::TYPE_AIRPORT, 'latitude' => 2.7456000, 'longitude' => 101.7072000],
            ['name' => 'Suvarnabhumi Airport Cargo Hub (BKK)', 'city' => 'Bangkok', 'country' => 'Thailand', 'location_type' => Location::TYPE_AIRPORT, 'latitude' => 13.6900000, 'longitude' => 100.7501000],
            ['name' => 'Hong Kong International Airport Cargo Hub (HKG)', 'city' => 'Hong Kong', 'country' => 'Hong Kong', 'location_type' => Location::TYPE_AIRPORT, 'latitude' => 22.3080000, 'longitude' => 113.9185000],
            ['name' => 'Shanghai Pudong International Airport Cargo (PVG)', 'city' => 'Shanghai', 'country' => 'Tiongkok', 'location_type' => Location::TYPE_AIRPORT, 'latitude' => 31.1443000, 'longitude' => 121.8083000],
            ['name' => 'Tokyo Narita Airport Cargo Terminal (NRT)', 'city' => 'Tokyo', 'country' => 'Jepang', 'location_type' => Location::TYPE_AIRPORT, 'latitude' => 35.7720000, 'longitude' => 140.3929000],
            ['name' => 'Incheon International Airport Cargo Terminal (ICN)', 'city' => 'Seoul/Incheon', 'country' => 'Korea Selatan', 'location_type' => Location::TYPE_AIRPORT, 'latitude' => 37.4602000, 'longitude' => 126.4407000],
            ['name' => 'Dubai International Airport Cargo Terminal (DXB)', 'city' => 'Dubai', 'country' => 'Uni Emirat Arab', 'location_type' => Location::TYPE_AIRPORT, 'latitude' => 25.2532000, 'longitude' => 55.3657000],
            ['name' => 'Frankfurt Airport CargoCity (FRA)', 'city' => 'Frankfurt', 'country' => 'Jerman', 'location_type' => Location::TYPE_AIRPORT, 'latitude' => 50.0379000, 'longitude' => 8.5622000],
            ['name' => 'Amsterdam Airport Schiphol Cargo (AMS)', 'city' => 'Amsterdam', 'country' => 'Belanda', 'location_type' => Location::TYPE_AIRPORT, 'latitude' => 52.3105000, 'longitude' => 4.7683000],
            ['name' => 'London Heathrow Cargo Terminal (LHR)', 'city' => 'London', 'country' => 'Inggris', 'location_type' => Location::TYPE_AIRPORT, 'latitude' => 51.4700000, 'longitude' => -0.4543000],
            ['name' => 'Los Angeles International Airport Cargo (LAX)', 'city' => 'Los Angeles', 'country' => 'Amerika Serikat', 'location_type' => Location::TYPE_AIRPORT, 'latitude' => 33.9416000, 'longitude' => -118.4085000],
            ['name' => 'John F. Kennedy International Airport Cargo (JFK)', 'city' => 'New York', 'country' => 'Amerika Serikat', 'location_type' => Location::TYPE_AIRPORT, 'latitude' => 40.6413000, 'longitude' => -73.7781000],
            ['name' => 'Chicago O\'Hare International Airport Cargo (ORD)', 'city' => 'Chicago', 'country' => 'Amerika Serikat', 'location_type' => Location::TYPE_AIRPORT, 'latitude' => 41.9742000, 'longitude' => -87.9073000],
            ['name' => 'Sydney Kingsford Smith Airport Cargo (SYD)', 'city' => 'Sydney', 'country' => 'Australia', 'location_type' => Location::TYPE_AIRPORT, 'latitude' => -33.9399000, 'longitude' => 151.1753000],
            ['name' => 'Pelabuhan Belawan', 'city' => 'Medan', 'country' => 'Indonesia', 'location_type' => Location::TYPE_PORT, 'latitude' => 3.7850000, 'longitude' => 98.6830000],
            ['name' => 'Pelabuhan Makassar (Soekarno-Hatta)', 'city' => 'Makassar', 'country' => 'Indonesia', 'location_type' => Location::TYPE_PORT, 'latitude' => -5.1200000, 'longitude' => 119.4120000],
            ['name' => 'Port of Singapore (PSA Tuas)', 'city' => 'Singapore', 'country' => 'Singapura', 'location_type' => Location::TYPE_PORT, 'latitude' => 1.2640000, 'longitude' => 103.8200000],
            ['name' => 'Port Klang', 'city' => 'Klang', 'country' => 'Malaysia', 'location_type' => Location::TYPE_PORT, 'latitude' => 2.9990000, 'longitude' => 101.3920000],
            ['name' => 'Port of Tanjung Pelepas', 'city' => 'Johor', 'country' => 'Malaysia', 'location_type' => Location::TYPE_PORT, 'latitude' => 1.3650000, 'longitude' => 103.5470000],
            ['name' => 'Laem Chabang Port', 'city' => 'Chonburi', 'country' => 'Thailand', 'location_type' => Location::TYPE_PORT, 'latitude' => 13.0820000, 'longitude' => 100.8830000],
            ['name' => 'Saigon Port (Cat Lai)', 'city' => 'Ho Chi Minh', 'country' => 'Vietnam', 'location_type' => Location::TYPE_PORT, 'latitude' => 10.7620000, 'longitude' => 106.7930000],
            ['name' => 'Port of Manila', 'city' => 'Manila', 'country' => 'Filipina', 'location_type' => Location::TYPE_PORT, 'latitude' => 14.5830000, 'longitude' => 120.9670000],

            // Asia Timur (Tiongkok, Jepang, Korea, Taiwan, Hong Kong)
            ['name' => 'Port of Shanghai (Yangshan)', 'city' => 'Shanghai', 'country' => 'Tiongkok', 'location_type' => Location::TYPE_PORT, 'latitude' => 31.2300000, 'longitude' => 121.4730000],
            ['name' => 'Ningbo-Zhoushan Port', 'city' => 'Ningbo', 'country' => 'Tiongkok', 'location_type' => Location::TYPE_PORT, 'latitude' => 29.8680000, 'longitude' => 121.5440000],
            ['name' => 'Port of Shenzhen (Yantian)', 'city' => 'Shenzhen', 'country' => 'Tiongkok', 'location_type' => Location::TYPE_PORT, 'latitude' => 22.5430000, 'longitude' => 114.0570000],
            ['name' => 'Port of Hong Kong (Kwai Tsing)', 'city' => 'Hong Kong', 'country' => 'Hong Kong', 'location_type' => Location::TYPE_PORT, 'latitude' => 22.3190000, 'longitude' => 114.1690000],
            ['name' => 'Port of Busan', 'city' => 'Busan', 'country' => 'Korea Selatan', 'location_type' => Location::TYPE_PORT, 'latitude' => 35.1020000, 'longitude' => 129.0400000],
            ['name' => 'Port of Tokyo', 'city' => 'Tokyo', 'country' => 'Jepang', 'location_type' => Location::TYPE_PORT, 'latitude' => 35.6190000, 'longitude' => 139.7890000],
            ['name' => 'Port of Yokohama', 'city' => 'Yokohama', 'country' => 'Jepang', 'location_type' => Location::TYPE_PORT, 'latitude' => 35.4430000, 'longitude' => 139.6380000],
            ['name' => 'Port of Kaohsiung', 'city' => 'Kaohsiung', 'country' => 'Taiwan', 'location_type' => Location::TYPE_PORT, 'latitude' => 22.6160000, 'longitude' => 120.2830000],

            // Eropa (Belanda, Belgia, Jerman, Spanyol, Inggris, Italia)
            ['name' => 'Port of Rotterdam (Maasvlakte)', 'city' => 'Rotterdam', 'country' => 'Belanda', 'location_type' => Location::TYPE_PORT, 'latitude' => 51.9490000, 'longitude' => 4.1370000],
            ['name' => 'Port of Antwerp-Bruges', 'city' => 'Antwerp', 'country' => 'Belgia', 'location_type' => Location::TYPE_PORT, 'latitude' => 51.2190000, 'longitude' => 4.4020000],
            ['name' => 'Port of Hamburg', 'city' => 'Hamburg', 'country' => 'Jerman', 'location_type' => Location::TYPE_PORT, 'latitude' => 53.5350000, 'longitude' => 9.9870000],
            ['name' => 'Port of Bremen/Bremerhaven', 'city' => 'Bremerhaven', 'country' => 'Jerman', 'location_type' => Location::TYPE_PORT, 'latitude' => 53.5500000, 'longitude' => 8.5800000],
            ['name' => 'Port of Valencia', 'city' => 'Valencia', 'country' => 'Spanyol', 'location_type' => Location::TYPE_PORT, 'latitude' => 39.4560000, 'longitude' => -0.3200000],
            ['name' => 'Port of Algeciras (Gibraltar)', 'city' => 'Algeciras', 'country' => 'Spanyol', 'location_type' => Location::TYPE_PORT, 'latitude' => 36.1270000, 'longitude' => -5.4450000],
            ['name' => 'Port of Felixstowe', 'city' => 'Felixstowe', 'country' => 'Inggris', 'location_type' => Location::TYPE_PORT, 'latitude' => 51.9610000, 'longitude' => 1.3510000],
            ['name' => 'Port of Genoa', 'city' => 'Genoa', 'country' => 'Italia', 'location_type' => Location::TYPE_PORT, 'latitude' => 44.4050000, 'longitude' => 8.9460000],

            // Timur Tengah, Asia Selatan, & Afrika (UEA, Oman, Sri Lanka, India, Mesir)
            ['name' => 'Jebel Ali Port (Dubai)', 'city' => 'Dubai', 'country' => 'Uni Emirat Arab', 'location_type' => Location::TYPE_PORT, 'latitude' => 24.9850000, 'longitude' => 55.0670000],
            ['name' => 'Port of Salalah', 'city' => 'Salalah', 'country' => 'Oman', 'location_type' => Location::TYPE_PORT, 'latitude' => 16.9410000, 'longitude' => 54.0080000],
            ['name' => 'Port of Colombo', 'city' => 'Colombo', 'country' => 'Sri Lanka', 'location_type' => Location::TYPE_PORT, 'latitude' => 6.9490000, 'longitude' => 79.8480000],
            ['name' => 'Jawaharlal Nehru Port (Nhava Sheva)', 'city' => 'Mumbai', 'country' => 'India', 'location_type' => Location::TYPE_PORT, 'latitude' => 18.9490000, 'longitude' => 72.9510000],
            ['name' => 'Port Said (Suez Canal Hub)', 'city' => 'Port Said', 'country' => 'Mesir', 'location_type' => Location::TYPE_PORT, 'latitude' => 31.2650000, 'longitude' => 32.3010000],

            // Amerika Utara & Selatan (USA, Kanada, Panama, Brasil)
            ['name' => 'Port of Los Angeles', 'city' => 'Los Angeles', 'country' => 'Amerika Serikat', 'location_type' => Location::TYPE_PORT, 'latitude' => 33.7430000, 'longitude' => -118.2670000],
            ['name' => 'Port of Long Beach', 'city' => 'Long Beach', 'country' => 'Amerika Serikat', 'location_type' => Location::TYPE_PORT, 'latitude' => 33.7540000, 'longitude' => -118.2160000],
            ['name' => 'Port of New York & New Jersey', 'city' => 'New York', 'country' => 'Amerika Serikat', 'location_type' => Location::TYPE_PORT, 'latitude' => 40.6690000, 'longitude' => -74.1480000],
            ['name' => 'Port of Savannah', 'city' => 'Savannah', 'country' => 'Amerika Serikat', 'location_type' => Location::TYPE_PORT, 'latitude' => 32.1280000, 'longitude' => -81.1410000],
            ['name' => 'Port of Vancouver', 'city' => 'Vancouver', 'country' => 'Kanada', 'location_type' => Location::TYPE_PORT, 'latitude' => 49.2820000, 'longitude' => -123.1200000],
            ['name' => 'Port of Balboa (Panama Canal)', 'city' => 'Panama City', 'country' => 'Panama', 'location_type' => Location::TYPE_PORT, 'latitude' => 8.9560000, 'longitude' => -79.5620000],
            ['name' => 'Port of Santos', 'city' => 'Santos', 'country' => 'Brasil', 'location_type' => Location::TYPE_PORT, 'latitude' => -23.9610000, 'longitude' => -46.3020000],

            // Oseania (Australia)
            ['name' => 'Port Botany (Sydney)', 'city' => 'Sydney', 'country' => 'Australia', 'location_type' => Location::TYPE_PORT, 'latitude' => -33.9740000, 'longitude' => 151.2180000],
            ['name' => 'Port of Melbourne', 'city' => 'Melbourne', 'country' => 'Australia', 'location_type' => Location::TYPE_PORT, 'latitude' => -37.8280000, 'longitude' => 144.9140000],

            // Titik Navigasi Maritim Tengah Laut (Kapal sedang berlayar di jalur utama)
            ['name' => 'Selat Malaka Navigasi Barat', 'city' => 'Malacca Strait', 'country' => 'Transit Laut', 'location_type' => Location::TYPE_WAREHOUSE, 'latitude' => 4.2100000, 'longitude' => 99.8500000],
            ['name' => 'Laut Cina Selatan Transit Zone', 'city' => 'South China Sea', 'country' => 'Transit Laut', 'location_type' => Location::TYPE_WAREHOUSE, 'latitude' => 12.5000000, 'longitude' => 114.2000000],
            ['name' => 'Laut Arab (Rute Teluk Oman)', 'city' => 'Arabian Sea', 'country' => 'Transit Laut', 'location_type' => Location::TYPE_WAREHOUSE, 'latitude' => 20.3000000, 'longitude' => 64.5000000],
            ['name' => 'Samudra Pasifik Utara Jalur Timur', 'city' => 'North Pacific Ocean', 'country' => 'Transit Laut', 'location_type' => Location::TYPE_WAREHOUSE, 'latitude' => 32.5000000, 'longitude' => -160.0000000],
            ['name' => 'Samudra Hindia Timur', 'city' => 'Indian Ocean', 'country' => 'Transit Laut', 'location_type' => Location::TYPE_WAREHOUSE, 'latitude' => -12.4000000, 'longitude' => 95.2000000],
        ];

        $locations = [];
        foreach ($locationsData as $loc) {
            $locations[$loc['name']] = Location::firstOrCreate(
                ['name' => $loc['name']],
                array_merge($loc, [
                    'address' => 'Terminal Internasional ' . $loc['name'] . ', ' . $loc['city'],
                    'status' => Location::STATUS_ACTIVE,
                ])
            );
        }

        // 5. Global Shipments (20 Realistic International Maritime & Air Shipments Across All Major Sea Lanes)
        $shipmentsSpecs = [
            [
                'number' => 'CT-2026-001',
                'customer' => $customer1,
                'carrier' => $carriers['FDX'],
                'origin' => $locations['Pelabuhan Tanjung Perak'],
                'dest' => $locations['Port Botany (Sydney)'],
                'current' => $locations['Port of Singapore (PSA Tuas)'],
                'cargo' => 'Electronic Parts & Solar Inverters',
                'weight' => 2500.00, 'volume' => 15.20,
                'status' => Shipment::STATUS_IN_TRANSIT,
                'delay' => 2880, 'sev' => Shipment::DELAY_HIGH,
                'dep_sub' => 6, 'arr_add' => 4, 'eta_add' => 6,
                'items' => [
                    ['name' => 'Solar Inverter Modules', 'hs' => '8504.40.90', 'qty' => 120, 'val' => 75000.00],
                    ['name' => 'Lithium Battery Storage Cell', 'hs' => '8507.60.00', 'qty' => 50, 'val' => 45000.00],
                ]
            ],
            [
                'number' => 'CT-2026-002',
                'customer' => $customer2,
                'carrier' => $carriers['DHL'],
                'origin' => $locations['Pelabuhan Tanjung Perak'],
                'dest' => $locations['Port of Rotterdam (Maasvlakte)'],
                'current' => $locations['Port of Rotterdam (Maasvlakte)'],
                'cargo' => 'Textiles & Garments',
                'weight' => 5200.00, 'volume' => 32.50,
                'status' => Shipment::STATUS_DELIVERED,
                'delay' => 0, 'sev' => Shipment::DELAY_NONE,
                'dep_sub' => 25, 'arr_add' => -2, 'eta_add' => -2,
                'items' => [
                    ['name' => 'Cotton Batik Apparel', 'hs' => '6205.20.00', 'qty' => 1400, 'val' => 88000.00],
                ]
            ],
            [
                'number' => 'CT-2026-003',
                'customer' => $customer1,
                'carrier' => $carriers['MSK'],
                'origin' => $locations['Bandara Internasional Soekarno-Hatta (CGK)'],
                'dest' => $locations['Port of Singapore (PSA Tuas)'],
                'current' => $locations['Selat Malaka Navigasi Barat'],
                'cargo' => 'Pharmaceutical Goods & Vaccines',
                'weight' => 850.00, 'volume' => 4.20,
                'status' => Shipment::STATUS_IN_TRANSIT,
                'delay' => 0, 'sev' => Shipment::DELAY_NONE,
                'dep_sub' => 1, 'arr_add' => 1, 'eta_add' => 1,
                'items' => [
                    ['name' => 'Temperature-Controlled Vaccines', 'hs' => '3002.20.00', 'qty' => 500, 'val' => 125000.00],
                ]
            ],
            [
                'number' => 'CT-2026-004',
                'customer' => $customer2,
                'carrier' => $carriers['CMA'],
                'origin' => $locations['Port of Shanghai (Yangshan)'],
                'dest' => $locations['Pelabuhan Tanjung Perak'],
                'current' => $locations['Laut Cina Selatan Transit Zone'],
                'cargo' => 'Machinery & Heavy Equipment',
                'weight' => 12500.00, 'volume' => 48.00,
                'status' => Shipment::STATUS_IN_TRANSIT,
                'delay' => 0, 'sev' => Shipment::DELAY_NONE,
                'dep_sub' => 3, 'arr_add' => 9, 'eta_add' => 9,
                'items' => [
                    ['name' => 'CNC Milling Machine Units', 'hs' => '8457.10.00', 'qty' => 4, 'val' => 240000.00],
                ]
            ],
            [
                'number' => 'CT-2026-005',
                'customer' => $customer1,
                'carrier' => $carriers['HPL'],
                'origin' => $locations['Port of Hamburg'],
                'dest' => $locations['Pelabuhan Tanjung Priok'],
                'current' => $locations['Port of Hamburg'],
                'cargo' => 'Automotive Spare Parts',
                'weight' => 4100.00, 'volume' => 18.50,
                'status' => Shipment::STATUS_BOOKED,
                'delay' => 1440, 'sev' => Shipment::DELAY_MEDIUM,
                'dep_sub' => 0, 'arr_add' => 18, 'eta_add' => 19,
                'items' => [
                    ['name' => 'Precision Gearbox Assemblies', 'hs' => '8708.40.11', 'qty' => 80, 'val' => 110000.00],
                ]
            ],
            // Additional Real-World Trans-Continental Shipments
            [
                'number' => 'CT-2026-006',
                'customer' => $customer1,
                'carrier' => $carriers['MSC'],
                'origin' => $locations['Pelabuhan Tanjung Priok'],
                'dest' => $locations['Port of Los Angeles'],
                'current' => $locations['Samudra Pasifik Utara Jalur Timur'],
                'cargo' => 'Consumer Electronics & Smart Devices',
                'weight' => 8200.00, 'volume' => 36.00,
                'status' => Shipment::STATUS_IN_TRANSIT,
                'delay' => 0, 'sev' => Shipment::DELAY_NONE,
                'dep_sub' => 8, 'arr_add' => 10, 'eta_add' => 10,
                'items' => [
                    ['name' => 'Smart Home Hub Devices', 'hs' => '8517.62.00', 'qty' => 3000, 'val' => 180000.00],
                ]
            ],
            [
                'number' => 'CT-2026-007',
                'customer' => $customer2,
                'carrier' => $carriers['COS'],
                'origin' => $locations['Ningbo-Zhoushan Port'],
                'dest' => $locations['Port of Antwerp-Bruges'],
                'current' => $locations['Port Said (Suez Canal Hub)'],
                'cargo' => 'Industrial Solar Panels & Hardware',
                'weight' => 16400.00, 'volume' => 54.00,
                'status' => Shipment::STATUS_IN_TRANSIT,
                'delay' => 2160, 'sev' => Shipment::DELAY_HIGH,
                'dep_sub' => 14, 'arr_add' => 12, 'eta_add' => 14,
                'items' => [
                    ['name' => 'Monocrystalline Solar Panels', 'hs' => '8541.43.00', 'qty' => 2200, 'val' => 210000.00],
                ]
            ],
            [
                'number' => 'CT-2026-008',
                'customer' => $customer1,
                'carrier' => $carriers['ONE'],
                'origin' => $locations['Port of Tokyo'],
                'dest' => $locations['Pelabuhan Tanjung Priok'],
                'current' => $locations['Port of Kaohsiung'],
                'cargo' => 'Semiconductor Wafers & Sensors',
                'weight' => 1200.00, 'volume' => 8.40,
                'status' => Shipment::STATUS_IN_TRANSIT,
                'delay' => 0, 'sev' => Shipment::DELAY_NONE,
                'dep_sub' => 4, 'arr_add' => 5, 'eta_add' => 5,
                'items' => [
                    ['name' => 'Integrated Microcontroller Chips', 'hs' => '8542.31.00', 'qty' => 10000, 'val' => 320000.00],
                ]
            ],
            [
                'number' => 'CT-2026-009',
                'customer' => $customer2,
                'carrier' => $carriers['EVG'],
                'origin' => $locations['Jebel Ali Port (Dubai)'],
                'dest' => $locations['Pelabuhan Belawan'],
                'current' => $locations['Laut Arab (Rute Teluk Oman)'],
                'cargo' => 'Petrochemicals & Polymers',
                'weight' => 22000.00, 'volume' => 60.00,
                'status' => Shipment::STATUS_IN_TRANSIT,
                'delay' => 0, 'sev' => Shipment::DELAY_NONE,
                'dep_sub' => 5, 'arr_add' => 7, 'eta_add' => 7,
                'items' => [
                    ['name' => 'Polyethylene Granules Resin', 'hs' => '3901.10.00', 'qty' => 880, 'val' => 95000.00],
                ]
            ],
            [
                'number' => 'CT-2026-010',
                'customer' => $customer1,
                'carrier' => $carriers['MSK'],
                'origin' => $locations['Port of Busan'],
                'dest' => $locations['Port of Melbourne'],
                'current' => $locations['Port of Busan'],
                'cargo' => 'Automotive Lithium Battery Packs',
                'weight' => 9800.00, 'volume' => 32.00,
                'status' => Shipment::STATUS_PICKED_UP,
                'delay' => 0, 'sev' => Shipment::DELAY_NONE,
                'dep_sub' => 0, 'arr_add' => 16, 'eta_add' => 16,
                'items' => [
                    ['name' => 'EV Battery Modules 75kWh', 'hs' => '8507.60.00', 'qty' => 60, 'val' => 270000.00],
                ]
            ],
            [
                'number' => 'CT-2026-011',
                'customer' => $customer2,
                'carrier' => $carriers['CMA'],
                'origin' => $locations['Pelabuhan Makassar (Soekarno-Hatta)'],
                'dest' => $locations['Port of Shanghai (Yangshan)'],
                'current' => $locations['Port of Manila'],
                'cargo' => 'Agricultural Commodities & Coffee Beans',
                'weight' => 18500.00, 'volume' => 55.00,
                'status' => Shipment::STATUS_IN_TRANSIT,
                'delay' => 720, 'sev' => Shipment::DELAY_LOW,
                'dep_sub' => 7, 'arr_add' => 5, 'eta_add' => 6,
                'items' => [
                    ['name' => 'Specialty Toraja Arabica Green Coffee', 'hs' => '0901.11.10', 'qty' => 300, 'val' => 140000.00],
                ]
            ],
            [
                'number' => 'CT-2026-012',
                'customer' => $customer1,
                'carrier' => $carriers['HPL'],
                'origin' => $locations['Port of New York & New Jersey'],
                'dest' => $locations['Port of Rotterdam (Maasvlakte)'],
                'current' => $locations['Port of Felixstowe'],
                'cargo' => 'Aerospace Components & Avionics',
                'weight' => 3200.00, 'volume' => 14.00,
                'status' => Shipment::STATUS_IN_TRANSIT,
                'delay' => 0, 'sev' => Shipment::DELAY_NONE,
                'dep_sub' => 6, 'arr_add' => 3, 'eta_add' => 3,
                'items' => [
                    ['name' => 'Flight Deck Display Avionics', 'hs' => '8803.30.00', 'qty' => 15, 'val' => 350000.00],
                ]
            ],
            [
                'number' => 'CT-2026-013',
                'customer' => $customer2,
                'carrier' => $carriers['FDX'],
                'origin' => $locations['Laem Chabang Port'],
                'dest' => $locations['Pelabuhan Tanjung Priok'],
                'current' => $locations['Port Klang'],
                'cargo' => 'Automotive Assembly Parts',
                'weight' => 6400.00, 'volume' => 24.00,
                'status' => Shipment::STATUS_IN_TRANSIT,
                'delay' => 0, 'sev' => Shipment::DELAY_NONE,
                'dep_sub' => 3, 'arr_add' => 2, 'eta_add' => 2,
                'items' => [
                    ['name' => 'Engine Cylinder Heads', 'hs' => '8409.91.00', 'qty' => 120, 'val' => 68000.00],
                ]
            ],
            [
                'number' => 'CT-2026-014',
                'customer' => $customer1,
                'carrier' => $carriers['MSC'],
                'origin' => $locations['Port of Santos'],
                'dest' => $locations['Port of Rotterdam (Maasvlakte)'],
                'current' => $locations['Port of Algeciras (Gibraltar)'],
                'cargo' => 'Raw Coffee & Frozen Fruit Concentrate',
                'weight' => 24000.00, 'volume' => 65.00,
                'status' => Shipment::STATUS_IN_TRANSIT,
                'delay' => 0, 'sev' => Shipment::DELAY_NONE,
                'dep_sub' => 16, 'arr_add' => 4, 'eta_add' => 4,
                'items' => [
                    ['name' => 'Frozen Orange Juice Concentrate', 'hs' => '2009.11.00', 'qty' => 800, 'val' => 135000.00],
                ]
            ],
            [
                'number' => 'CT-2026-015',
                'customer' => $customer2,
                'carrier' => $carriers['COS'],
                'origin' => $locations['Port of Colombo'],
                'dest' => $locations['Port Klang'],
                'current' => $locations['Samudra Hindia Timur'],
                'cargo' => 'Ceylon Tea & Rubber Commodities',
                'weight' => 7600.00, 'volume' => 28.00,
                'status' => Shipment::STATUS_IN_TRANSIT,
                'delay' => 1440, 'sev' => Shipment::DELAY_MEDIUM,
                'dep_sub' => 4, 'arr_add' => 2, 'eta_add' => 3,
                'items' => [
                    ['name' => 'Pure Ceylon Black Tea Bags', 'hs' => '0902.30.00', 'qty' => 1500, 'val' => 72000.00],
                ]
            ],
            // ── Comprehensive Real-World Global Cargo Shipments (CT-2026-016 s/d CT-2026-035) ──
            [
                'number' => 'CT-2026-016',
                'customer' => $customer1,
                'carrier' => $carriers['MSK'],
                'origin' => $locations['Pelabuhan Tanjung Priok'],
                'dest' => $locations['Port of Los Angeles'],
                'current' => $locations['Samudra Pasifik Utara Jalur Timur'],
                'cargo' => 'Footwear & Consumer Sportswear',
                'weight' => 8400.00, 'volume' => 38.00,
                'status' => Shipment::STATUS_IN_TRANSIT,
                'delay' => 0, 'sev' => Shipment::DELAY_NONE,
                'dep_sub' => 8, 'arr_add' => 6, 'eta_add' => 6,
                'items' => [
                    ['name' => 'Running Shoes & Athletic Apparel', 'hs' => '6404.11.00', 'qty' => 4200, 'val' => 185000.00],
                ]
            ],
            [
                'number' => 'CT-2026-017',
                'customer' => $customer2,
                'carrier' => $carriers['ONE'],
                'origin' => $locations['Port of Tokyo'],
                'dest' => $locations['Pelabuhan Tanjung Priok'],
                'current' => $locations['Laut Cina Selatan Transit Zone'],
                'cargo' => 'Robotics & Precision Factory Automation',
                'weight' => 5600.00, 'volume' => 22.00,
                'status' => Shipment::STATUS_IN_TRANSIT,
                'delay' => 0, 'sev' => Shipment::DELAY_NONE,
                'dep_sub' => 4, 'arr_add' => 5, 'eta_add' => 5,
                'items' => [
                    ['name' => 'Industrial Robotic Arms & Controllers', 'hs' => '8479.50.00', 'qty' => 8, 'val' => 320000.00],
                ]
            ],
            [
                'number' => 'CT-2026-018',
                'customer' => $customer1,
                'carrier' => $carriers['CMA'],
                'origin' => $locations['Port of Antwerp-Bruges'],
                'dest' => $locations['Pelabuhan Tanjung Perak'],
                'current' => $locations['Port Said (Suez Canal Hub)'],
                'cargo' => 'Pharmaceutical Fine Chemicals & API',
                'weight' => 3100.00, 'volume' => 12.50,
                'status' => Shipment::STATUS_IN_TRANSIT,
                'delay' => 0, 'sev' => Shipment::DELAY_NONE,
                'dep_sub' => 11, 'arr_add' => 7, 'eta_add' => 7,
                'items' => [
                    ['name' => 'Active Pharmaceutical Ingredients (API)', 'hs' => '2942.00.00', 'qty' => 120, 'val' => 210000.00],
                ]
            ],
            [
                'number' => 'CT-2026-019',
                'customer' => $customer2,
                'carrier' => $carriers['MSC'],
                'origin' => $locations['Pelabuhan Belawan'],
                'dest' => $locations['Port of Rotterdam (Maasvlakte)'],
                'current' => $locations['Pelabuhan Belawan'],
                'cargo' => 'Certified Sustainable Palm Oil (RSPO)',
                'weight' => 32000.00, 'volume' => 85.00,
                'status' => Shipment::STATUS_BOOKED,
                'delay' => 0, 'sev' => Shipment::DELAY_NONE,
                'dep_sub' => 0, 'arr_add' => 24, 'eta_add' => 24,
                'items' => [
                    ['name' => 'Refined Bleached Deodorized Palm Olein', 'hs' => '1511.90.00', 'qty' => 1200, 'val' => 165000.00],
                ]
            ],
            [
                'number' => 'CT-2026-020',
                'customer' => $customer1,
                'carrier' => $carriers['COS'],
                'origin' => $locations['Ningbo-Zhoushan Port'],
                'dest' => $locations['Pelabuhan Tanjung Priok'],
                'current' => $locations['Selat Malaka Navigasi Barat'],
                'cargo' => 'EV Batteries & Energy Storage Units',
                'weight' => 14200.00, 'volume' => 45.00,
                'status' => Shipment::STATUS_IN_TRANSIT,
                'delay' => 0, 'sev' => Shipment::DELAY_NONE,
                'dep_sub' => 5, 'arr_add' => 3, 'eta_add' => 3,
                'items' => [
                    ['name' => 'Lithium Iron Phosphate Battery Packs', 'hs' => '8507.60.00', 'qty' => 400, 'val' => 290000.00],
                ]
            ],
            [
                'number' => 'CT-2026-021',
                'customer' => $customer2,
                'carrier' => $carriers['EVG'],
                'origin' => $locations['Port of Kaohsiung'],
                'dest' => $locations['Port of Singapore (PSA Tuas)'],
                'current' => $locations['Port of Singapore (PSA Tuas)'],
                'cargo' => 'Semiconductors & Integrated Circuit Wafers',
                'weight' => 1100.00, 'volume' => 5.20,
                'status' => Shipment::STATUS_DELIVERED,
                'delay' => 0, 'sev' => Shipment::DELAY_NONE,
                'dep_sub' => 9, 'arr_add' => -1, 'eta_add' => -1,
                'items' => [
                    ['name' => 'Advanced Microprocessor Silicon Wafers', 'hs' => '8542.31.00', 'qty' => 2500, 'val' => 580000.00],
                ]
            ],
            [
                'number' => 'CT-2026-022',
                'customer' => $customer1,
                'carrier' => $carriers['DHL'],
                'origin' => $locations['Bandara Internasional Soekarno-Hatta (CGK)'],
                'dest' => $locations['Port of Hamburg'],
                'current' => $locations['Bandara Internasional Soekarno-Hatta (CGK)'],
                'cargo' => 'Handcrafted Furniture & Home Decor',
                'weight' => 1800.00, 'volume' => 16.00,
                'status' => Shipment::STATUS_BOOKED,
                'delay' => 0, 'sev' => Shipment::DELAY_NONE,
                'dep_sub' => 0, 'arr_add' => 12, 'eta_add' => 12,
                'items' => [
                    ['name' => 'Teak Wood Dining Sets & Rattan Crafts', 'hs' => '9403.60.00', 'qty' => 60, 'val' => 48000.00],
                ]
            ],
            [
                'number' => 'CT-2026-023',
                'customer' => $customer2,
                'carrier' => $carriers['HPL'],
                'origin' => $locations['Port of Genoa'],
                'dest' => $locations['Pelabuhan Tanjung Priok'],
                'current' => $locations['Laut Arab (Rute Teluk Oman)'],
                'cargo' => 'Italian Leather Goods & Footwear',
                'weight' => 2400.00, 'volume' => 11.00,
                'status' => Shipment::STATUS_IN_TRANSIT,
                'delay' => 720, 'sev' => Shipment::DELAY_MEDIUM,
                'dep_sub' => 14, 'arr_add' => 4, 'eta_add' => 5,
                'items' => [
                    ['name' => 'Full Grain Calfskin Leather Goods', 'hs' => '4202.21.00', 'qty' => 650, 'val' => 175000.00],
                ]
            ],
            [
                'number' => 'CT-2026-024',
                'customer' => $customer1,
                'carrier' => $carriers['MSK'],
                'origin' => $locations['Port of Melbourne'],
                'dest' => $locations['Pelabuhan Tanjung Perak'],
                'current' => $locations['Port of Melbourne'],
                'cargo' => 'Chilled Dairy Products & Milk Powder',
                'weight' => 16000.00, 'volume' => 42.00,
                'status' => Shipment::STATUS_BOOKED,
                'delay' => 0, 'sev' => Shipment::DELAY_NONE,
                'dep_sub' => 0, 'arr_add' => 15, 'eta_add' => 15,
                'items' => [
                    ['name' => 'Skim Milk Powder & Cheddar Cheese Blocks', 'hs' => '0402.10.00', 'qty' => 800, 'val' => 92000.00],
                ]
            ],
            [
                'number' => 'CT-2026-025',
                'customer' => $customer2,
                'carrier' => $carriers['FDX'],
                'origin' => $locations['Port of Shenzhen (Yantian)'],
                'dest' => $locations['Port of Los Angeles'],
                'current' => $locations['Samudra Pasifik Utara Jalur Timur'],
                'cargo' => 'Consumer Electronics & 5G Smartphones',
                'weight' => 4800.00, 'volume' => 20.00,
                'status' => Shipment::STATUS_IN_TRANSIT,
                'delay' => 0, 'sev' => Shipment::DELAY_NONE,
                'dep_sub' => 7, 'arr_add' => 6, 'eta_add' => 6,
                'items' => [
                    ['name' => '5G Smartphones & OLED Tablets', 'hs' => '8517.13.00', 'qty' => 3000, 'val' => 650000.00],
                ]
            ],
            [
                'number' => 'CT-2026-026',
                'customer' => $customer1,
                'carrier' => $carriers['MSC'],
                'origin' => $locations['Pelabuhan Tanjung Priok'],
                'dest' => $locations['Jebel Ali Port (Dubai)'],
                'current' => $locations['Jebel Ali Port (Dubai)'],
                'cargo' => 'Organic Spices, Nutmeg & Cloves',
                'weight' => 12500.00, 'volume' => 35.00,
                'status' => Shipment::STATUS_DELIVERED,
                'delay' => 0, 'sev' => Shipment::DELAY_NONE,
                'dep_sub' => 18, 'arr_add' => -2, 'eta_add' => -2,
                'items' => [
                    ['name' => 'Whole Dried Cloves & Nutmeg Seeds', 'hs' => '0907.10.00', 'qty' => 600, 'val' => 115000.00],
                ]
            ],
            [
                'number' => 'CT-2026-027',
                'customer' => $customer2,
                'carrier' => $carriers['CMA'],
                'origin' => $locations['Port of Valencia'],
                'dest' => $locations['Port of Santos'],
                'current' => $locations['Port of Valencia'],
                'cargo' => 'Ceramic Tiles & Building Materials',
                'weight' => 28000.00, 'volume' => 70.00,
                'status' => Shipment::STATUS_BOOKED,
                'delay' => 0, 'sev' => Shipment::DELAY_NONE,
                'dep_sub' => 0, 'arr_add' => 22, 'eta_add' => 22,
                'items' => [
                    ['name' => 'Glazed Porcelain Floor Tiles', 'hs' => '6907.21.00', 'qty' => 1800, 'val' => 85000.00],
                ]
            ],
            [
                'number' => 'CT-2026-028',
                'customer' => $customer1,
                'carrier' => $carriers['ONE'],
                'origin' => $locations['Port of Busan'],
                'dest' => $locations['Pelabuhan Tanjung Priok'],
                'current' => $locations['Pelabuhan Tanjung Priok'],
                'cargo' => 'Automotive Steel Coils & Stamping Sheets',
                'weight' => 45000.00, 'volume' => 50.00,
                'status' => Shipment::STATUS_DELIVERED,
                'delay' => 0, 'sev' => Shipment::DELAY_NONE,
                'dep_sub' => 12, 'arr_add' => -1, 'eta_add' => -1,
                'items' => [
                    ['name' => 'Cold-Rolled High-Strength Steel Coils', 'hs' => '7209.16.00', 'qty' => 20, 'val' => 195000.00],
                ]
            ],
            [
                'number' => 'CT-2026-029',
                'customer' => $customer2,
                'carrier' => $carriers['COS'],
                'origin' => $locations['Jawaharlal Nehru Port (Nhava Sheva)'],
                'dest' => $locations['Port of Singapore (PSA Tuas)'],
                'current' => $locations['Selat Malaka Navigasi Barat'],
                'cargo' => 'Cotton Yarn & Industrial Fabrics',
                'weight' => 9500.00, 'volume' => 36.00,
                'status' => Shipment::STATUS_IN_TRANSIT,
                'delay' => 0, 'sev' => Shipment::DELAY_NONE,
                'dep_sub' => 4, 'arr_add' => 3, 'eta_add' => 3,
                'items' => [
                    ['name' => 'Combed Cotton Ring-Spun Yarn', 'hs' => '5205.23.00', 'qty' => 450, 'val' => 78000.00],
                ]
            ],
            [
                'number' => 'CT-2026-030',
                'customer' => $customer1,
                'carrier' => $carriers['EVG'],
                'origin' => $locations['Port of Hong Kong (Kwai Tsing)'],
                'dest' => $locations['Pelabuhan Makassar (Soekarno-Hatta)'],
                'current' => $locations['Port of Hong Kong (Kwai Tsing)'],
                'cargo' => 'Solar PV Glass Panels & Aluminum Frames',
                'weight' => 15000.00, 'volume' => 40.00,
                'status' => Shipment::STATUS_BOOKED,
                'delay' => 0, 'sev' => Shipment::DELAY_NONE,
                'dep_sub' => 0, 'arr_add' => 9, 'eta_add' => 9,
                'items' => [
                    ['name' => 'Anti-Reflective Solar Photovoltaic Glass', 'hs' => '7007.19.00', 'qty' => 900, 'val' => 110000.00],
                ]
            ],
            [
                'number' => 'CT-2026-031',
                'customer' => $customer2,
                'carrier' => $carriers['HPL'],
                'origin' => $locations['Port of Hamburg'],
                'dest' => $locations['Port of Singapore (PSA Tuas)'],
                'current' => $locations['Port Said (Suez Canal Hub)'],
                'cargo' => 'German Industrial Pumps & Valves',
                'weight' => 7800.00, 'volume' => 26.00,
                'status' => Shipment::STATUS_IN_TRANSIT,
                'delay' => 1440, 'sev' => Shipment::DELAY_MEDIUM,
                'dep_sub' => 10, 'arr_add' => 6, 'eta_add' => 7,
                'items' => [
                    ['name' => 'High-Pressure Centrifugal Water Pumps', 'hs' => '8413.70.00', 'qty' => 35, 'val' => 225000.00],
                ]
            ],
            [
                'number' => 'CT-2026-032',
                'customer' => $customer1,
                'carrier' => $carriers['DHL'],
                'origin' => $locations['Port of Vancouver'],
                'dest' => $locations['Port of Tokyo'],
                'current' => $locations['Samudra Pasifik Utara Jalur Timur'],
                'cargo' => 'Softwood Lumber & Paper Pulp',
                'weight' => 34000.00, 'volume' => 90.00,
                'status' => Shipment::STATUS_IN_TRANSIT,
                'delay' => 0, 'sev' => Shipment::DELAY_NONE,
                'dep_sub' => 9, 'arr_add' => 5, 'eta_add' => 5,
                'items' => [
                    ['name' => 'Kiln-Dried Douglas Fir Timber', 'hs' => '4407.11.00', 'qty' => 150, 'val' => 88000.00],
                ]
            ],
            [
                'number' => 'CT-2026-033',
                'customer' => $customer2,
                'carrier' => $carriers['MSK'],
                'origin' => $locations['Pelabuhan Tanjung Priok'],
                'dest' => $locations['Port of Felixstowe'],
                'current' => $locations['Pelabuhan Tanjung Priok'],
                'cargo' => 'Natural Rubber Blocks & Latex Gloves',
                'weight' => 21000.00, 'volume' => 60.00,
                'status' => Shipment::STATUS_BOOKED,
                'delay' => 0, 'sev' => Shipment::DELAY_NONE,
                'dep_sub' => 0, 'arr_add' => 26, 'eta_add' => 26,
                'items' => [
                    ['name' => 'Standard Indonesian Rubber (SIR-20)', 'hs' => '4001.22.00', 'qty' => 800, 'val' => 145000.00],
                ]
            ],
            [
                'number' => 'CT-2026-034',
                'customer' => $customer1,
                'carrier' => $carriers['MSC'],
                'origin' => $locations['Port of Balboa (Panama Canal)'],
                'dest' => $locations['Port of Rotterdam (Maasvlakte)'],
                'current' => $locations['Port of Rotterdam (Maasvlakte)'],
                'cargo' => 'Fresh Cavendish Bananas & Fruit Puree',
                'weight' => 19500.00, 'volume' => 52.00,
                'status' => Shipment::STATUS_DELIVERED,
                'delay' => 0, 'sev' => Shipment::DELAY_NONE,
                'dep_sub' => 17, 'arr_add' => -1, 'eta_add' => -1,
                'items' => [
                    ['name' => 'Fresh Cavendish Bananas in Controlled Atmosphere', 'hs' => '0803.90.00', 'qty' => 1200, 'val' => 64000.00],
                ]
            ],
            [
                'number' => 'CT-2026-035',
                'customer' => $customer2,
                'carrier' => $carriers['ONE'],
                'origin' => $locations['Port of Singapore (PSA Tuas)'],
                'dest' => $locations['Pelabuhan Tanjung Perak'],
                'current' => $locations['Pelabuhan Tanjung Perak'],
                'cargo' => 'Polymer Resin Pellets (HDPE)',
                'weight' => 38000.00, 'volume' => 75.00,
                'status' => Shipment::STATUS_DELIVERED,
                'delay' => 0, 'sev' => Shipment::DELAY_NONE,
                'dep_sub' => 8, 'arr_add' => -2, 'eta_add' => -2,
                'items' => [
                    ['name' => 'High-Density Polyethylene (HDPE) Pellets', 'hs' => '3901.20.00', 'qty' => 1500, 'val' => 128000.00],
                ]
            ],
        ];

        foreach ($shipmentsSpecs as $spec) {
            $dep = Carbon::now()->subDays($spec['dep_sub']);
            $arr = Carbon::now()->addDays($spec['arr_add']);
            $eta = Carbon::now()->addDays($spec['eta_add']);

            $shipment = Shipment::firstOrCreate(
                ['shipment_number' => $spec['number']],
                [
                    'customer_id' => $spec['customer']->id,
                    'carrier_id' => $spec['carrier']->id,
                    'origin_location_id' => $spec['origin']->id,
                    'destination_location_id' => $spec['dest']->id,
                    'current_location_id' => $spec['current']->id,
                    'cargo_type' => $spec['cargo'],
                    'weight' => $spec['weight'],
                    'volume' => $spec['volume'],
                    'expected_departure' => $dep,
                    'expected_arrival' => $arr,
                    'actual_departure' => $spec['dep_sub'] > 0 ? $dep : null,
                    'actual_arrival' => $spec['status'] === Shipment::STATUS_DELIVERED ? $arr : null,
                    'status' => $spec['status'],
                    'estimated_time_arrival' => $eta,
                    'delay_minutes' => $spec['delay'],
                    'delay_severity' => $spec['sev'],
                    'notes' => 'Shipment kargo rute internasional prioritas operasional.',
                ]
            );

            // Seed Shipment Items
            foreach ($spec['items'] as $item) {
                ShipmentItem::firstOrCreate(
                    ['shipment_id' => $shipment->id, 'item_name' => $item['name']],
                    [
                        'hs_code' => $item['hs'],
                        'quantity' => $item['qty'],
                        'weight' => $spec['weight'] / count($spec['items']),
                        'declared_value' => $item['val'],
                        'currency' => 'USD',
                    ]
                );
            }

            // Seed Baseline Tracking Event (Booking Placed by Customer)
            TrackingEvent::firstOrCreate(
                ['shipment_id' => $shipment->id, 'event_type' => TrackingEvent::TYPE_STATUS_CHANGE, 'shipment_status_at_event' => Shipment::STATUS_BOOKED],
                [
                    'location_id' => $spec['origin']->id,
                    'latitude' => $spec['origin']->latitude,
                    'longitude' => $spec['origin']->longitude,
                    'event_time' => $dep->copy()->subDays(2),
                    'description' => "Pemesanan pengiriman (#{$spec['number']}) diajukan oleh Pelanggan {$spec['customer']->name} dan dikonfirmasi oleh ekspedisi {$spec['carrier']->name}.",
                    'created_by' => $spec['customer']->id,
                ]
            );

            if ($spec['status'] === Shipment::STATUS_IN_TRANSIT) {
                // Customs Cleared Origin
                TrackingEvent::firstOrCreate(
                    ['shipment_id' => $shipment->id, 'event_type' => TrackingEvent::TYPE_CUSTOMS_CLEARED],
                    [
                        'location_id' => $spec['origin']->id,
                        'latitude' => $spec['origin']->latitude,
                        'longitude' => $spec['origin']->longitude,
                        'event_time' => $dep->copy()->subHours(12),
                        'shipment_status_at_event' => Shipment::STATUS_PICKED_UP,
                        'description' => "Dokumen manifest dan izin kepabeanan ekspor disetujui di {$spec['origin']->name}.",
                        'created_by' => $operator->id,
                    ]
                );

                // Vessel Departure
                TrackingEvent::firstOrCreate(
                    ['shipment_id' => $shipment->id, 'event_type' => TrackingEvent::TYPE_DEPARTURE],
                    [
                        'location_id' => $spec['origin']->id,
                        'latitude' => $spec['origin']->latitude,
                        'longitude' => $spec['origin']->longitude,
                        'event_time' => $dep,
                        'shipment_status_at_event' => Shipment::STATUS_IN_TRANSIT,
                        'description' => "Armada kapal kargo bertolak meninggalkan {$spec['origin']->name} berlayar menuju {$spec['dest']->name}.",
                        'created_by' => $operator->id,
                    ]
                );

                if ($spec['current']->id !== $spec['origin']->id) {
                    TrackingEvent::firstOrCreate(
                        ['shipment_id' => $shipment->id, 'event_type' => TrackingEvent::TYPE_CHECKPOINT_REACHED],
                        [
                            'location_id' => $spec['current']->id,
                            'latitude' => $spec['current']->latitude,
                            'longitude' => $spec['current']->longitude,
                            'event_time' => Carbon::now()->subHours(6),
                            'shipment_status_at_event' => Shipment::STATUS_IN_TRANSIT,
                            'description' => "Kapal kargo melewati koordinat navigasi maritim di {$spec['current']->name}.",
                            'created_by' => $operator->id,
                        ]
                    );
                }
            } elseif ($spec['status'] === Shipment::STATUS_DELIVERED) {
                // Customs Cleared Origin
                TrackingEvent::firstOrCreate(
                    ['shipment_id' => $shipment->id, 'event_type' => TrackingEvent::TYPE_CUSTOMS_CLEARED],
                    [
                        'location_id' => $spec['origin']->id,
                        'latitude' => $spec['origin']->latitude,
                        'longitude' => $spec['origin']->longitude,
                        'event_time' => $dep->copy()->subHours(12),
                        'shipment_status_at_event' => Shipment::STATUS_PICKED_UP,
                        'description' => "Dokumen ekspor disetujui di {$spec['origin']->name}.",
                        'created_by' => $operator->id,
                    ]
                );

                // Departure
                TrackingEvent::firstOrCreate(
                    ['shipment_id' => $shipment->id, 'event_type' => TrackingEvent::TYPE_DEPARTURE],
                    [
                        'location_id' => $spec['origin']->id,
                        'latitude' => $spec['origin']->latitude,
                        'longitude' => $spec['origin']->longitude,
                        'event_time' => $dep,
                        'shipment_status_at_event' => Shipment::STATUS_IN_TRANSIT,
                        'description' => "Kapal bertolak dari {$spec['origin']->name}.",
                        'created_by' => $operator->id,
                    ]
                );

                // Arrival at Destination Port
                TrackingEvent::firstOrCreate(
                    ['shipment_id' => $shipment->id, 'event_type' => TrackingEvent::TYPE_ARRIVAL],
                    [
                        'location_id' => $spec['dest']->id,
                        'latitude' => $spec['dest']->latitude,
                        'longitude' => $spec['dest']->longitude,
                        'event_time' => $arr->copy()->subHours(18),
                        'shipment_status_at_event' => Shipment::STATUS_IN_TRANSIT,
                        'description' => "Kapal kargo sandar dan proses bongkar muat kontainer dimulai di {$spec['dest']->name}.",
                        'created_by' => $operator->id,
                    ]
                );

                // Final Delivered
                TrackingEvent::firstOrCreate(
                    ['shipment_id' => $shipment->id, 'event_type' => TrackingEvent::TYPE_STATUS_CHANGE, 'shipment_status_at_event' => Shipment::STATUS_DELIVERED],
                    [
                        'location_id' => $spec['dest']->id,
                        'latitude' => $spec['dest']->latitude,
                        'longitude' => $spec['dest']->longitude,
                        'event_time' => $arr,
                        'description' => "Kargo berhasil diterima secara penuh oleh penerima (Consignee) di {$spec['dest']->name}.",
                        'created_by' => $operator->id,
                    ]
                );
            }

            // Seed Exceptions for delayed shipments
            if ($spec['delay'] > 0) {
                ShipmentException::firstOrCreate(
                    ['shipment_id' => $shipment->id],
                    [
                        'exception_type' => ShipmentException::TYPE_WEATHER_DELAY,
                        'severity' => $spec['sev'] === Shipment::DELAY_HIGH ? ShipmentException::SEVERITY_HIGH : ShipmentException::SEVERITY_MEDIUM,
                        'status' => ShipmentException::STATUS_OPEN,
                        'description' => "Perubahan cuaca laut dan gelombang tinggi memperlambat kecepatan kapal pada rute {$spec['origin']->city} ke {$spec['dest']->city}.",
                        'reported_by' => $operator->id,
                    ]
                );

                Notification::firstOrCreate(
                    ['shipment_id' => $shipment->id, 'type' => Notification::TYPE_DELAY_ALERT],
                    [
                        'user_id' => $spec['customer']->id,
                        'title' => "Peringatan Keterlambatan: {$spec['number']}",
                        'message' => "Pengiriman {$spec['number']} diperkirakan terlambat +" . round($spec['delay'] / 60) . " jam akibat kondisi maritim.",
                        'severity' => Notification::SEVERITY_DANGER,
                        'is_read' => false,
                    ]
                );
            }
        }

        // 6. Realistic Audit Logs
        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'LOGIN',
            'entity_type' => 'User',
            'entity_id' => (string) $admin->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            'description' => 'Administrator login ke sistem CargoTrace Platform.',
            'created_at' => Carbon::now()->subMinutes(15),
        ]);

        AuditLog::create([
            'user_id' => $operator->id,
            'action' => 'STATUS_CHANGE',
            'entity_type' => 'Shipment',
            'entity_id' => 'CT-2026-001',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            'new_values' => ['status' => 'IN_TRANSIT', 'delay_minutes' => 2880],
            'description' => 'Operator memperbarui status perjalanan kargo CT-2026-001 dan mencatat keterlambatan cuaca.',
            'created_at' => Carbon::now()->subHours(18),
        ]);
    }
}
