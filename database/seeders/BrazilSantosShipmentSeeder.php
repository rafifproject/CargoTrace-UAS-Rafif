<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Shipment;
use Carbon\Carbon;
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

        // Destination ports and airports
        $tanjungPriok = Location::where('name', 'like', '%Tanjung Priok%')->first();
        $tanjungPerak = Location::where('name', 'like', '%Tanjung Perak%')->first();
        $heathrow = Location::where('name', 'like', '%Heathrow%')->first();
        $rotterdam = Location::where('name', 'like', '%Rotterdam%')->first();
        $jfk = Location::where('name', 'like', '%Kennedy%')->first() ?? Location::where('name', 'like', '%JFK%')->first();
        $haneda = Location::where('name', 'like', '%Haneda%')->first() ?? Location::where('name', 'like', '%Tokyo%')->first();
        $busanPort = Location::where('name', 'like', '%Busan%')->first() 
            ?? Location::where('country', 'like', '%Korea%')->where('location_type', 'PORT')->first();

        if (! $busanPort) {
            $busanPort = Location::firstOrCreate(
                ['name' => 'Port of Busan'],
                [
                    'city' => 'Busan',
                    'country' => 'Korea Selatan',
                    'address' => 'Busan Port International Container Terminal, South Korea',
                    'location_type' => Location::TYPE_PORT,
                    'latitude' => 35.102,
                    'longitude' => 129.040,
                    'status' => Location::STATUS_ACTIVE,
                ]
            );
        }

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

        // Specific Last-Mile Multimodal Deliveries (Ocean/Air from Brazil -> Local Courier Van to address)
        $lastMileTargets = [
            [
                'shipment_number' => 'CT-2026-001',
                'transport_mode' => 'SEA',
                'origin_id' => $santosPort->id,
                'dest_id' => $tanjungPriok ? $tanjungPriok->id : 1,
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
                'courier_vehicle' => 'Mobil Van Paket Kopi (B 9482 CT)',
                'notes' => 'Berangkat via Kapal Laut dari Pelabuhan Santos ke Pelabuhan Tanjung Priok. Saat ini kontainer telah dibongkar dan di-oper ke mobil paket kurir lokal menuju Roastery Senopati Jakarta Selatan.',
                'dep_days_ago' => 18,
                'arr_hours_ahead' => 3,
            ],
            [
                'shipment_number' => 'CT-2026-002',
                'transport_mode' => 'AIR',
                'origin_id' => $gruAirport->id,
                'dest_id' => $heathrow ? $heathrow->id : 58,
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
                'courier_vehicle' => 'Mobil Van Paket Listrik (LDN-77-CT)',
                'notes' => 'Berangkat via Pesawat Kargo Udara Kilat dari Bandara GRU São Paulo ke London Heathrow. Setelah mendarat, kargo di-oper ke mobil van paket kurir menuju 38 Upper Montagu Street, Westminster.',
                'dep_days_ago' => 2,
                'arr_hours_ahead' => 2,
            ],
            [
                'shipment_number' => 'CT-2026-003',
                'transport_mode' => 'AIR',
                'origin_id' => $gruAirport->id,
                'dest_id' => $haneda ? $haneda->id : 3,
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
                'courier_vehicle' => 'Mobil Van Paket Pendingin (TK-2026)',
                'notes' => 'Pesawat kargo mendarat di Tokyo Haneda dari São Paulo GRU. Biji kopi di-oper ke mobil paket kurir pendingin menuju Shibuya Roastery.',
                'dep_days_ago' => 2,
                'arr_hours_ahead' => 4,
            ],
            [
                'shipment_number' => 'CT-2026-004',
                'transport_mode' => 'SEA',
                'origin_id' => $santosPort->id,
                'dest_id' => $rotterdam ? $rotterdam->id : 20,
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
                'courier_vehicle' => 'Mobil Van Paket Euro 6 (NL-CT-404)',
                'notes' => 'Kapal kontainer tiba di Pelabuhan Rotterdam dari Santos Brazil. Kontainer dibongkar lalu di-oper ke mobil paket kurir menuju Roastery Coolsingel.',
                'dep_days_ago' => 16,
                'arr_hours_ahead' => 3,
            ],
            [
                'shipment_number' => 'CT-2026-005',
                'transport_mode' => 'AIR',
                'origin_id' => $gruAirport->id,
                'dest_id' => $jfk ? $jfk->id : 60,
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
                'courier_vehicle' => 'Mobil Van Paket Kargo NY (NYC-552)',
                'notes' => 'Penerbangan kargo kilat dari GRU tiba di Bandara JFK New York. Biji kopi di-oper ke mobil paket kurir menuju Manhattan Roastery.',
                'dep_days_ago' => 1,
                'arr_hours_ahead' => 2,
            ],
            [
                'shipment_number' => 'CT-2026-006',
                'transport_mode' => 'SEA',
                'origin_id' => $santosPort->id,
                'dest_id' => $tanjungPerak ? $tanjungPerak->id : 2,
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
                'courier_vehicle' => 'Mobil Van Paket Box (L 8192 CT)',
                'notes' => 'Kapal Kontainer Samudera berlayar dari Pelabuhan Santos (Brazil) ke Pelabuhan Tanjung Priok (Jakarta). Setibanya di Tanjung Priok, kargo dilanjutkan via Pesawat Kargo Domestik (Jakarta ➔ Bandara Juanda Surabaya), lalu diantar oleh Mobil Van Kurir ke Kafe Gubeng Surabaya.',
                'dep_days_ago' => 21,
                'arr_hours_ahead' => 1,
            ],
            [
                'shipment_number' => 'CT-2026-007',
                'transport_mode' => 'SEA',
                'origin_id' => $santosPort->id,
                'dest_id' => $busanPort->id,
                'is_last_mile' => true,
                'coffee_variety' => 'Brazil Santos Yellow Catuai Natural Micro-lot',
                'coffee_grade' => 'Specialty Grade 1 (SCA Score 84+)',
                'delivery_country' => 'Korea Selatan',
                'delivery_city' => 'Suwon',
                'delivery_district' => 'Gwonseon-gu',
                'delivery_address' => 'Gwonseon-ro 52, Roastery Kopi Santos Suwon, Gyeonggi-do',
                'delivery_lat' => 37.2575,
                'delivery_lng' => 127.0145,
                'courier_name' => 'Santos Express Korea Logistics',
                'courier_vehicle' => 'Mobil Van Paket Kargo (KR-7821-CT)',
                'notes' => 'Kapal Kontainer Samudera tiba di Port of Busan dari Pelabuhan Santos (Brazil). Kargo dilanjutkan via Pesawat Kargo Domestik (Busan ➔ Bandara Gimpo/Suwon), lalu diantar oleh Mobil Van Kurir ke Gwonseon-gu Suwon.',
                'dep_days_ago' => 22,
                'arr_hours_ahead' => 2,
            ]
        ];

        $allShipments = Shipment::all();
        $targetMap = collect($lastMileTargets)->keyBy('shipment_number');

        foreach ($allShipments as $index => $shipment) {
            $num = $shipment->shipment_number;

            if (isset($targetMap[$num])) {
                $data = $targetMap[$num];
                $shipment->transport_mode = $data['transport_mode'];
                $shipment->origin_location_id = $data['origin_id'];
                $shipment->destination_location_id = $data['dest_id'];
                $shipment->current_location_id = null;
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

                // Realistic departure and arrival times
                $dep = Carbon::now()->subDays($data['dep_days_ago']);
                $arr = Carbon::now()->addHours($data['arr_hours_ahead']);
                $shipment->expected_departure = $dep;
                $shipment->actual_departure = $dep;
                $shipment->expected_arrival = $arr;
                $shipment->estimated_time_arrival = $arr;

                // B2B Proforma Invoice & Settlement Data
                $shipment->invoice_number = 'INV-2026-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);
                $modeVal = $shipment->transport_mode ?? 'SEA';
                $weightVal = (float) ($shipment->weight > 0 ? $shipment->weight : 1500);
                $rateVal = ($modeVal === 'AIR') ? 2.80 : (($modeVal === 'LAND') ? 0.85 : 0.35);
                $feeVal = ($modeVal === 'LAND') ? 150.00 : 250.00;
                $shipment->shipping_cost = round(($weightVal * $rateVal) + $feeVal, 2);
                $shipment->payment_status = 'PAID';
                $shipment->payment_method = 'T/T Bank Wire (SWIFT Transfer)';
                $shipment->paid_at = $dep->copy()->subDay();

                $shipment->save();
            } else {
                // Generalized Brazil Santos Coffee export shipment
                $variety = $varieties[$index % count($varieties)];
                $grade = $grades[$index % count($grades)];

                // Seluruh kargo ekspor biji kopi berangkat via Kapal Kontainer Laut Samudera dari Santos (Brazil)
                $shipment->transport_mode = 'SEA';
                $shipment->origin_location_id = $santosPort->id;
                $shipment->current_location_id = null;
                $shipment->is_last_mile = false;
                $shipment->coffee_variety = $variety;
                $shipment->coffee_grade = $grade;
                $shipment->cargo_type = 'Kopi Ekspor Brazil Santos: ' . $variety;
                $shipment->notes = 'Ekspor Resmi Kopi Brazil Santos via Kapal Kontainer Laut Samudera — Santos Coffee Exporters S.A. Kemasan kantong hermetis GrainPro 60kg.';

                // Staggered departure and arrival schedule so no two shipments share the same spot
                $depDays = 3 + (($index * 3) % 18);
                $arrDays = 4 + (($index * 2) % 16);
                $dep = Carbon::now()->subDays($depDays);
                $arr = Carbon::now()->addDays($arrDays);

                $shipment->expected_departure = $dep;
                $shipment->actual_departure = $dep;
                $shipment->expected_arrival = $arr;
                $shipment->estimated_time_arrival = $arr;

                // B2B Proforma Invoice & Settlement Data
                $shipment->invoice_number = 'INV-2026-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);
                $weightVal = (float) ($shipment->weight > 0 ? $shipment->weight : 2000);
                $shipment->shipping_cost = round(($weightVal * 0.35) + 250.00, 2);
                $shipment->payment_status = 'PAID';
                $shipment->payment_method = 'T/T Bank Wire (SWIFT Transfer)';
                $shipment->paid_at = $dep->copy()->subDay();

                $shipment->save();
            }
        }
    }
}
