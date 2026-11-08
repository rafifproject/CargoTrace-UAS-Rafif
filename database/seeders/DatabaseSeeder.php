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
    public function run(): void
    {
        // 1. System Settings
        SystemSetting::create(['key' => 'app_language', 'value' => 'id', 'group' => 'general', 'description' => 'Bahasa aplikasi default (id/en)']);
        SystemSetting::create(['key' => 'app_timezone', 'value' => 'Asia/Jakarta', 'group' => 'general', 'description' => 'Zona waktu sistem']);
        SystemSetting::create(['key' => 'date_format', 'value' => 'd M Y, H:i', 'group' => 'general', 'description' => 'Format tampilan tanggal']);
        SystemSetting::create(['key' => 'delay_threshold_hours', 'value' => '6', 'group' => 'threshold', 'description' => 'Ambang batas jam keterlambatan (jam)']);
        SystemSetting::create(['key' => 'weather_provider', 'value' => 'Open-Meteo', 'group' => 'integration', 'description' => 'Penyedia data cuaca']);
        SystemSetting::create(['key' => 'currency_provider', 'value' => 'ExchangeRate-API', 'group' => 'integration', 'description' => 'Penyedia nilai tukar kurs valuta']);

        // 2. Users
        $admin = User::create([
            'name' => 'Admin Sistem',
            'email' => 'admin@cargotrace.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMINISTRATOR,
            'status' => User::STATUS_ACTIVE,
            'company_name' => 'CargoTrace Global HQ',
            'phone' => '+62 811-2345-6789',
            'last_login_at' => Carbon::now()->subMinutes(15),
        ]);

        $operator = User::create([
            'name' => 'Budi Operator',
            'email' => 'operator@cargotrace.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_LOGISTICS_OPERATOR,
            'status' => User::STATUS_ACTIVE,
            'company_name' => 'CargoTrace Surabaya Hub',
            'phone' => '+62 812-3456-7890',
            'last_login_at' => Carbon::now()->subHours(2),
        ]);

        $customer1 = User::create([
            'name' => 'Global Mart Enterprise',
            'email' => 'customer@globalmart.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_CUSTOMER,
            'status' => User::STATUS_ACTIVE,
            'company_name' => 'Global Mart Ltd.',
            'phone' => '+62 813-9876-5432',
            'last_login_at' => Carbon::now()->subDays(1),
        ]);

        $customer2 = User::create([
            'name' => 'Nusantara Trade Corp',
            'email' => 'citra@client.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_CUSTOMER,
            'status' => User::STATUS_ACTIVE,
            'company_name' => 'PT Nusantara Trade',
            'phone' => '+62 815-5555-1234',
            'last_login_at' => Carbon::now()->subDays(2),
        ]);

        // 3. Carriers
        $dhl = Carrier::create([
            'code' => 'DHL',
            'name' => 'DHL Express',
            'country' => 'Jerman',
            'contact_email' => 'contact@dhl.com',
            'contact_phone' => '+49 228 1820',
            'website' => 'https://www.dhl.com',
            'status' => Carrier::STATUS_ACTIVE,
        ]);

        $fedex = Carrier::create([
            'code' => 'FDX',
            'name' => 'FedEx Express',
            'country' => 'Amerika Serikat',
            'contact_email' => 'support@fedex.com',
            'contact_phone' => '+1 800 463 3339',
            'website' => 'https://www.fedex.com',
            'status' => Carrier::STATUS_ACTIVE,
        ]);

        $maersk = Carrier::create([
            'code' => 'MSK',
            'name' => 'Maersk Line',
            'country' => 'Denmark',
            'contact_email' => 'info@maersk.com',
            'contact_phone' => '+45 33 63 33 63',
            'website' => 'https://www.maersk.com',
            'status' => Carrier::STATUS_ACTIVE,
        ]);

        $cma = Carrier::create([
            'code' => 'CMA',
            'name' => 'CMA CGM',
            'country' => 'Prancis',
            'contact_email' => 'contact@cmacgm.com',
            'contact_phone' => '+33 4 88 91 90 00',
            'website' => 'https://www.cma-cgm.com',
            'status' => Carrier::STATUS_ACTIVE,
        ]);

        $hapag = Carrier::create([
            'code' => 'HPL',
            'name' => 'Hapag-Lloyd',
            'country' => 'Jerman',
            'contact_email' => 'info@hlag.com',
            'contact_phone' => '+49 40 3001 0',
            'website' => 'https://www.hapag-lloyd.com',
            'status' => Carrier::STATUS_ACTIVE,
        ]);

        // 4. Locations (Realistic international ports & hubs)
        $locSub = Location::create([
            'name' => 'Port Tanjung Perak',
            'city' => 'Surabaya',
            'country' => 'Indonesia',
            'address' => 'Jl. Tanjung Perak Timur No. 620, Pabean Cantian',
            'location_type' => Location::TYPE_PORT,
            'latitude' => -7.1950000,
            'longitude' => 112.7290000,
            'status' => Location::STATUS_ACTIVE,
        ]);

        $locSin = Location::create([
            'name' => 'Port of Singapore',
            'city' => 'Singapore',
            'country' => 'Singapore',
            'address' => '7B Keppel Rd, Tanjong Pagar Complex',
            'location_type' => Location::TYPE_PORT,
            'latitude' => 1.2650000,
            'longitude' => 103.8190000,
            'status' => Location::STATUS_ACTIVE,
        ]);

        $locRot = Location::create([
            'name' => 'Port Rotterdam',
            'city' => 'Rotterdam',
            'country' => 'Belanda',
            'address' => 'Wilhelminakade 902, 3072 AP',
            'location_type' => Location::TYPE_PORT,
            'latitude' => 51.9490000,
            'longitude' => 4.1370000,
            'status' => Location::STATUS_ACTIVE,
        ]);

        $locCgk = Location::create([
            'name' => 'Bandara Soekarno-Hatta Cargo Terminal',
            'city' => 'Tangerang',
            'country' => 'Indonesia',
            'address' => 'Kawasan Kargo Bandara Soekarno-Hatta',
            'location_type' => Location::TYPE_AIRPORT,
            'latitude' => -6.1270000,
            'longitude' => 106.6550000,
            'status' => Location::STATUS_ACTIVE,
        ]);

        $locSyd = Location::create([
            'name' => 'Port of Sydney (Port Botany)',
            'city' => 'Sydney',
            'country' => 'Australia',
            'address' => 'Bumborah Point Rd, Port Botany NSW 2036',
            'location_type' => Location::TYPE_PORT,
            'latitude' => -33.8560000,
            'longitude' => 151.2090000,
            'status' => Location::STATUS_ACTIVE,
        ]);

        $locHam = Location::create([
            'name' => 'Port of Hamburg',
            'city' => 'Hamburg',
            'country' => 'Jerman',
            'address' => 'Am Sandtorkai 68, 20457 Hamburg',
            'location_type' => Location::TYPE_PORT,
            'latitude' => 53.5350000,
            'longitude' => 9.9870000,
            'status' => Location::STATUS_ACTIVE,
        ]);

        $locSha = Location::create([
            'name' => 'Port of Shanghai (Yangshan Deep-Water Port)',
            'city' => 'Shanghai',
            'country' => 'China',
            'address' => 'Yangshan Island, Hangzhou Bay',
            'location_type' => Location::TYPE_PORT,
            'latitude' => 31.2300000,
            'longitude' => 121.4730000,
            'status' => Location::STATUS_ACTIVE,
        ]);

        // 5. Shipments
        // Shipment 1: CT-2026-001 (Surabaya to Sydney, Delayed +2 days, in transit)
        $shipment1 = Shipment::create([
            'shipment_number' => 'CT-2026-001',
            'customer_id' => $customer1->id,
            'carrier_id' => $fedex->id,
            'origin_location_id' => $locSub->id,
            'destination_location_id' => $locSyd->id,
            'current_location_id' => $locSin->id,
            'cargo_type' => 'Electronic Parts & Solar Components',
            'weight' => 2500.00,
            'volume' => 15.20,
            'expected_departure' => Carbon::now()->subDays(6)->setTime(16, 0),
            'expected_arrival' => Carbon::now()->addDays(4)->setTime(14, 0),
            'actual_departure' => Carbon::now()->subDays(6)->setTime(16, 30),
            'actual_arrival' => null,
            'status' => Shipment::STATUS_IN_TRANSIT,
            'estimated_time_arrival' => Carbon::now()->addDays(6)->setTime(14, 0), // +2 days delayed
            'delay_minutes' => 2880,
            'delay_severity' => Shipment::DELAY_HIGH,
            'notes' => 'Prioritas penanganan tinggi. Komponen sensitif cuaca.',
        ]);

        ShipmentItem::create([
            'shipment_id' => $shipment1->id,
            'item_name' => 'Solar Inverter Modules',
            'hs_code' => '8504.40.90',
            'quantity' => 120,
            'weight' => 1500.00,
            'declared_value' => 75000.00,
            'currency' => 'USD',
        ]);
        ShipmentItem::create([
            'shipment_id' => $shipment1->id,
            'item_name' => 'Lithium Battery Storage Cell',
            'hs_code' => '8507.60.00',
            'quantity' => 50,
            'weight' => 1000.00,
            'declared_value' => 45000.00,
            'currency' => 'USD',
        ]);

        // Tracking timeline for Shipment 1
        TrackingEvent::create([
            'shipment_id' => $shipment1->id,
            'event_type' => TrackingEvent::TYPE_STATUS_CHANGE,
            'location_id' => $locSub->id,
            'latitude' => $locSub->latitude,
            'longitude' => $locSub->longitude,
            'event_time' => Carbon::now()->subDays(7)->setTime(10, 15),
            'shipment_status_at_event' => Shipment::STATUS_BOOKED,
            'description' => 'Pengiriman telah dipesan dan dijadwalkan bersama FedEx Express.',
            'created_by' => $operator->id,
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment1->id,
            'event_type' => TrackingEvent::TYPE_DEPARTURE,
            'location_id' => $locSub->id,
            'latitude' => $locSub->latitude,
            'longitude' => $locSub->longitude,
            'event_time' => Carbon::now()->subDays(6)->setTime(16, 30),
            'shipment_status_at_event' => Shipment::STATUS_IN_TRANSIT,
            'description' => 'Kargo berangkat dari Port Tanjung Perak Surabaya menuju Pelabuhan Transit Singapura.',
            'created_by' => $operator->id,
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment1->id,
            'event_type' => TrackingEvent::TYPE_ARRIVAL,
            'location_id' => $locSin->id,
            'latitude' => $locSin->latitude,
            'longitude' => $locSin->longitude,
            'event_time' => Carbon::now()->subDays(3)->setTime(8, 30),
            'shipment_status_at_event' => Shipment::STATUS_IN_TRANSIT,
            'description' => 'Kargo tiba di Port of Singapore dan siap untuk transfer kapal transshipment.',
            'created_by' => $operator->id,
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment1->id,
            'event_type' => TrackingEvent::TYPE_EXCEPTION_NOTED,
            'location_id' => $locSin->id,
            'latitude' => $locSin->latitude,
            'longitude' => $locSin->longitude,
            'event_time' => Carbon::now()->subHours(18)->setTime(14, 20),
            'shipment_status_at_event' => Shipment::STATUS_IN_TRANSIT,
            'description' => 'Keterlambatan transit akibat cuaca buruk dan badai di rute laut selatan Singapura.',
            'created_by' => $operator->id,
        ]);

        // Exception for Shipment 1
        ShipmentException::create([
            'shipment_id' => $shipment1->id,
            'exception_type' => ShipmentException::TYPE_WEATHER_DELAY,
            'severity' => ShipmentException::SEVERITY_HIGH,
            'status' => ShipmentException::STATUS_OPEN,
            'description' => 'Peringatan gelombang tinggi dan badai tropis di Laut Jawa & Selat Karimata menunda keberangkatan kapal feeder transshipment.',
            'reported_by' => $operator->id,
        ]);

        // Notification for Shipment 1
        Notification::create([
            'user_id' => $admin->id,
            'shipment_id' => $shipment1->id,
            'type' => Notification::TYPE_DELAY_ALERT,
            'title' => 'Peringatan Keterlambatan: CT-2026-001',
            'message' => 'Pengiriman CT-2026-001 mengalami keterlambatan +2 hari (48 jam) akibat cuaca buruk di rute transit.',
            'severity' => Notification::SEVERITY_DANGER,
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $admin->id,
            'shipment_id' => $shipment1->id,
            'type' => Notification::TYPE_EXCEPTION_ALERT,
            'title' => 'Pengecualian Baru: Cuaca Buruk',
            'message' => 'WEATHER_DELAY dilaporkan oleh operator pada rute Surabaya ke Sydney.',
            'severity' => Notification::SEVERITY_WARNING,
            'is_read' => false,
        ]);

        // Shipment 2: CT-2026-002 (Surabaya to Rotterdam, DHL, DELIVERED)
        $shipment2 = Shipment::create([
            'shipment_number' => 'CT-2026-002',
            'customer_id' => $customer2->id,
            'carrier_id' => $dhl->id,
            'origin_location_id' => $locSub->id,
            'destination_location_id' => $locRot->id,
            'current_location_id' => $locRot->id,
            'cargo_type' => 'Textiles & Garments',
            'weight' => 5200.00,
            'volume' => 32.50,
            'expected_departure' => Carbon::now()->subDays(25),
            'expected_arrival' => Carbon::now()->subDays(2),
            'actual_departure' => Carbon::now()->subDays(25),
            'actual_arrival' => Carbon::now()->subDays(2),
            'status' => Shipment::STATUS_DELIVERED,
            'estimated_time_arrival' => Carbon::now()->subDays(2),
            'delay_minutes' => 0,
            'delay_severity' => Shipment::DELAY_NONE,
            'notes' => 'Selesai tepat waktu tanpa kendala bea cukai.',
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment2->id,
            'event_type' => TrackingEvent::TYPE_ARRIVAL,
            'location_id' => $locRot->id,
            'latitude' => $locRot->latitude,
            'longitude' => $locRot->longitude,
            'event_time' => Carbon::now()->subDays(2),
            'shipment_status_at_event' => Shipment::STATUS_DELIVERED,
            'description' => 'Kargo telah diterima dengan baik di gudang Port Rotterdam Belanda.',
            'created_by' => $operator->id,
        ]);

        // Shipment 3: CT-2026-003 (Tangerang to Singapore, Maersk, IN_TRANSIT)
        $shipment3 = Shipment::create([
            'shipment_number' => 'CT-2026-003',
            'customer_id' => $customer1->id,
            'carrier_id' => $maersk->id,
            'origin_location_id' => $locCgk->id,
            'destination_location_id' => $locSin->id,
            'current_location_id' => $locCgk->id,
            'cargo_type' => 'Pharmaceutical Goods',
            'weight' => 850.00,
            'volume' => 4.20,
            'expected_departure' => Carbon::now()->subHours(12),
            'expected_arrival' => Carbon::now()->addHours(24),
            'actual_departure' => Carbon::now()->subHours(10),
            'actual_arrival' => null,
            'status' => Shipment::STATUS_IN_TRANSIT,
            'estimated_time_arrival' => Carbon::now()->addHours(24),
            'delay_minutes' => 0,
            'delay_severity' => Shipment::DELAY_NONE,
            'notes' => 'Kargo berpendingin (Cold Chain).',
        ]);

        // Shipment 4: CT-2026-004 (Shanghai to Surabaya, CMA CGM, PICKED_UP)
        $shipment4 = Shipment::create([
            'shipment_number' => 'CT-2026-004',
            'customer_id' => $customer2->id,
            'carrier_id' => $cma->id,
            'origin_location_id' => $locSha->id,
            'destination_location_id' => $locSub->id,
            'current_location_id' => $locSha->id,
            'cargo_type' => 'Machinery & Heavy Equipment',
            'weight' => 12500.00,
            'volume' => 48.00,
            'expected_departure' => Carbon::now()->addDays(1),
            'expected_arrival' => Carbon::now()->addDays(12),
            'status' => Shipment::STATUS_PICKED_UP,
            'estimated_time_arrival' => Carbon::now()->addDays(12),
            'delay_minutes' => 0,
            'delay_severity' => Shipment::DELAY_NONE,
            'notes' => 'Kargo telah dijemput dari manufaktur Shanghai.',
        ]);

        // Shipment 5: CT-2026-005 (Hamburg to Jakarta, Hapag-Lloyd, IN_TRANSIT, Customs Hold)
        $shipment5 = Shipment::create([
            'shipment_number' => 'CT-2026-005',
            'customer_id' => $customer1->id,
            'carrier_id' => $hapag->id,
            'origin_location_id' => $locHam->id,
            'destination_location_id' => $locCgk->id,
            'current_location_id' => $locHam->id,
            'cargo_type' => 'Automotive Spare Parts',
            'weight' => 4100.00,
            'volume' => 18.50,
            'expected_departure' => Carbon::now()->subDays(5),
            'expected_arrival' => Carbon::now()->addDays(15),
            'actual_departure' => Carbon::now()->subDays(5),
            'status' => Shipment::STATUS_IN_TRANSIT,
            'estimated_time_arrival' => Carbon::now()->addDays(17), // +2 days
            'delay_minutes' => 2880,
            'delay_severity' => Shipment::DELAY_HIGH,
            'notes' => 'Pemeriksaan dokumen bea cukai di pelabuhan muat Hamburg.',
        ]);

        ShipmentException::create([
            'shipment_id' => $shipment5->id,
            'exception_type' => ShipmentException::TYPE_CUSTOMS_HOLD,
            'severity' => ShipmentException::SEVERITY_MEDIUM,
            'status' => ShipmentException::STATUS_INVESTIGATING,
            'description' => 'Verifikasi nomor HS Code dan perizinan impor suku cadang khusus.',
            'reported_by' => $operator->id,
        ]);

        // Audit Logs (Realistic history)
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

        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'CONFIG_CHANGE',
            'entity_type' => 'SystemSetting',
            'entity_id' => 'delay_threshold_hours',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            'old_values' => ['value' => '4'],
            'new_values' => ['value' => '6'],
            'description' => 'Admin memperbarui konfigurasi ambang batas keterlambatan menjadi 6 jam.',
            'created_at' => Carbon::now()->subDays(1),
        ]);
    }
}
