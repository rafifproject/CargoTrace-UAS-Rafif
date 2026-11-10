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

            // Seed Baseline Tracking Event
            TrackingEvent::firstOrCreate(
                ['shipment_id' => $shipment->id, 'event_type' => TrackingEvent::TYPE_STATUS_CHANGE],
                [
                    'location_id' => $spec['origin']->id,
                    'latitude' => $spec['origin']->latitude,
                    'longitude' => $spec['origin']->longitude,
                    'event_time' => $dep->subDay(),
                    'shipment_status_at_event' => Shipment::STATUS_BOOKED,
                    'description' => "Pengiriman dijadwalkan bersama {$spec['carrier']->name}.",
                    'created_by' => $operator->id,
                ]
            );

            if ($spec['status'] === Shipment::STATUS_IN_TRANSIT) {
                TrackingEvent::firstOrCreate(
                    ['shipment_id' => $shipment->id, 'event_type' => TrackingEvent::TYPE_DEPARTURE],
                    [
                        'location_id' => $spec['origin']->id,
                        'latitude' => $spec['origin']->latitude,
                        'longitude' => $spec['origin']->longitude,
                        'event_time' => $dep,
                        'shipment_status_at_event' => Shipment::STATUS_IN_TRANSIT,
                        'description' => "Kargo bertolak dari {$spec['origin']->name} menuju {$spec['dest']->name}.",
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
                            'description' => "Kapal kargo melewati koordinat navigasi di {$spec['current']->name}.",
                            'created_by' => $operator->id,
                        ]
                    );
                }
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
