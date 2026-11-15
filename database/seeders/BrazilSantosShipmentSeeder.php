<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Shipment;
use Illuminate\Database\Seeder;

class BrazilSantosShipmentSeeder extends Seeder
{
    /**
     * Run the database seeds for Brazil Santos Specialty Coffee Export Monitoring.
     */
    public function run(): void
    {
        $santosPort = Location::where('name', 'like', '%Santos%')->first() 
            ?? Location::where('country', 'like', '%Brasil%')->where('location_type', 'PORT')->first();
        
        $gruAirport = Location::where('name', 'like', '%Guarulhos%')->first()
            ?? Location::where('country', 'like', '%Brasil%')->where('location_type', 'AIRPORT')->first();

        if (! $santosPort || ! $gruAirport) {
            $this->command->warn('Santos Port or GRU Airport not found.');
            return;
        }

        $varieties = [
            'Kopi Brazil Santos Bourbon Specialty',
            'Brazil Santos Strictly Soft Fine Cup (SSFC)',
            'Brazil Santos Yellow Catuai Natural Micro-lot',
            'Brazil Santos Cerrado Mineiro Single-Origin',
            'Brazil Santos Decaf Swiss Water Process',
            'Brazil Santos Espresso Roast Master Blend',
        ];

        $grades = [
            'Specialty Grade 1 (SCA Score 84+)',
            'Strictly Soft Fine Cup NY 2/3',
            'Exclusive Micro-Lot Nano Batch',
            'Commercial Export Grade A',
        ];

        // Specific Last-Mile Ground Deliveries
        $lastMileTargets = [
            [
                'shipment_number' => 'CT-2026-001',
                'transport_mode' => 'LAND',
                'is_last_mile' => true,
                'coffee_variety' => 'Kopi Brazil Santos Bourbon Specialty',
                'coffee_grade' => 'Specialty Grade 1 (SCA Score 84+)',
                'delivery_country' => 'Indonesia',
                'delivery_city' => 'Jakarta Selatan',
                'delivery_district' => 'Kebayoran Baru (Senopati)',
                'delivery_address' => 'Jl. Senopati No. 42, Roastery Kopi Santos Jakarta',
                'delivery_lat' => -6.2297,
                'delivery_lng' => 106.8075,
                'courier_name' => 'Armada Kurir Mobil Kopi Santos Jakarta',
                'courier_vehicle' => 'Mobil Van Distribusi Kopi (B 9482 CT)',
                'notes' => 'Tiba di Pelabuhan Tanjung Priok. Saat ini mobil kurir sedang mengantar langsung karung biji kopi ke Roastery Senopati Jakarta Selatan.',
            ],
            [
                'shipment_number' => 'CT-2026-002',
                'transport_mode' => 'LAND',
                'is_last_mile' => true,
                'coffee_variety' => 'Brazil Santos Yellow Catuai Natural Micro-lot',
                'coffee_grade' => 'Exclusive Micro-Lot Nano Batch',
                'delivery_country' => 'United Kingdom',
                'delivery_city' => 'London',
                'delivery_district' => 'Westminster',
                'delivery_address' => '38 Upper Montagu Street, Westminster W1H 1LJ, United Kingdom',
                'delivery_lat' => 51.52016,
                'delivery_lng' => -0.1603064,
                'courier_name' => 'Santos Express London Courier Van',
                'courier_vehicle' => 'Electric Delivery Van (LDN-77-CT)',
                'notes' => 'Sampel micro-lot kopi kilat tiba di London Heathrow, kurir mobil listrik sedang mengantar ke 38 Upper Montagu Street, Westminster.',
            ],
            [
                'shipment_number' => 'CT-2026-003',
                'transport_mode' => 'AIR',
                'is_last_mile' => true,
                'coffee_variety' => 'Brazil Santos Strictly Soft Fine Cup (SSFC)',
                'coffee_grade' => 'Specialty Grade 1 (SCA Score 84+)',
                'delivery_country' => 'Japan',
                'delivery_city' => 'Tokyo',
                'delivery_district' => 'Shibuya-ku',
                'delivery_address' => 'Shibuya Specialty Roastery & Brew Bar, 1-22 Jinnan, Shibuya, Tokyo',
                'delivery_lat' => 35.6628,
                'delivery_lng' => 139.7013,
                'courier_name' => 'Yamato Santos Coffee Logistics',
                'courier_vehicle' => 'Mobil Truk Pendingin Khusus Biji Kopi (TK-2026)',
                'notes' => 'Kargo udara tiba di Haneda. Mobil kurir khusus dalam proses pengantaran langsung ke Shibuya Roastery.',
            ],
            [
                'shipment_number' => 'CT-2026-004',
                'transport_mode' => 'SEA',
                'is_last_mile' => true,
                'coffee_variety' => 'Brazil Santos Cerrado Mineiro Single-Origin',
                'coffee_grade' => 'Strictly Soft Fine Cup NY 2/3',
                'delivery_country' => 'Netherlands',
                'delivery_city' => 'Rotterdam',
                'delivery_district' => 'Centrum',
                'delivery_address' => 'Coolsingel 104, Rotterdam Specialty Coffee Roastery',
                'delivery_lat' => 51.9225,
                'delivery_lng' => 4.4791,
                'courier_name' => 'EuroCoffee Fast Van Delivery',
                'courier_vehicle' => 'Mobil Van Euro 6 (NL-CT-404)',
                'notes' => 'Kontainer dibongkar di Maasvlakte Rotterdam. Mobil kurir kargo mendistribusikan karung kopi ke Roastery Coolsingel.',
            ],
            [
                'shipment_number' => 'CT-2026-005',
                'transport_mode' => 'AIR',
                'is_last_mile' => true,
                'coffee_variety' => 'Brazil Santos Decaf Swiss Water Process',
                'coffee_grade' => 'Specialty Grade 1 (SCA Score 84+)',
                'delivery_country' => 'United States',
                'delivery_city' => 'New York',
                'delivery_district' => 'Manhattan (Meatpacking District)',
                'delivery_address' => '450 West 15th Street, Meatpacking Roastery, New York, NY 10014',
                'delivery_lat' => 40.7423,
                'delivery_lng' => -74.0076,
                'courier_name' => 'Santos NYC Metro Courier Van',
                'courier_vehicle' => 'Mobil Van Kargo Kopi NY (NYC-552)',
                'notes' => 'Penerbangan kargo kilat dari GRU tiba di JFK. Mobil kurir dalam perjalanan mengantar ke Manhattan Roastery.',
            ],
            [
                'shipment_number' => 'CT-2026-006',
                'transport_mode' => 'LAND',
                'is_last_mile' => true,
                'coffee_variety' => 'Brazil Santos Espresso Roast Master Blend',
                'coffee_grade' => 'Commercial Export Grade A',
                'delivery_country' => 'Indonesia',
                'delivery_city' => 'Surabaya',
                'delivery_district' => 'Gubeng',
                'delivery_address' => 'Jl. Raya Gubeng No. 88, Kafe & Roastery Kopi Santos Surabaya',
                'delivery_lat' => -7.2756,
                'delivery_lng' => 112.7541,
                'courier_name' => 'Armada Kurir Mobil Kopi Santos Jawa Timur',
                'courier_vehicle' => 'Mobil Truk Box Pendingin (L 8192 CT)',
                'notes' => 'Mobil kurir armada kargo darat mengantar 50 karung biji kopi espresso blend langsung ke Kafe Gubeng Surabaya.',
            ]
        ];

        $allShipments = Shipment::all();
        $targetMap = collect($lastMileTargets)->keyBy('shipment_number');

        foreach ($allShipments as $index => $shipment) {
            $num = $shipment->shipment_number;

            if (isset($targetMap[$num])) {
                $data = $targetMap[$num];
                $shipment->transport_mode = $data['transport_mode'];
                $shipment->is_last_mile = $data['is_last_mile'];
                $shipment->coffee_variety = $data['coffee_variety'];
                $shipment->coffee_grade = $data['coffee_grade'];
                $shipment->cargo_type = $data['coffee_variety'] . ' (' . $data['coffee_grade'] . ')';
                $shipment->delivery_country = $data['delivery_country'];
                $shipment->delivery_city = $data['delivery_city'];
                $shipment->delivery_district = $data['delivery_district'];
                $shipment->delivery_address = $data['delivery_address'];
                $shipment->delivery_lat = $data['delivery_lat'];
                $shipment->delivery_lng = $data['delivery_lng'];
                $shipment->courier_name = $data['courier_name'];
                $shipment->courier_vehicle = $data['courier_vehicle'];
                $shipment->notes = $data['notes'];

                // Origin is Brazil (Santos or GRU)
                if ($data['transport_mode'] === 'AIR') {
                    $shipment->origin_location_id = $gruAirport->id;
                } else {
                    $shipment->origin_location_id = $santosPort->id;
                }

                $shipment->save();
            } else {
                // Generalized Brazil Santos Coffee export shipment
                $variety = $varieties[$index % count($varieties)];
                $grade = $grades[$index % count($grades)];
                $isAir = ($shipment->transport_mode === 'AIR');

                $shipment->origin_location_id = $isAir ? $gruAirport->id : $santosPort->id;
                $shipment->coffee_variety = $variety;
                $shipment->coffee_grade = $grade;
                $shipment->cargo_type = 'Kopi Ekspor Brazil Santos: ' . $variety;
                $shipment->notes = 'Ekspor Resmi Kopi Brazil Santos — Santos Coffee Exporters S.A. Kemasan kantong hermetis GrainPro 60kg.';
                $shipment->save();
            }
        }
    }
}
