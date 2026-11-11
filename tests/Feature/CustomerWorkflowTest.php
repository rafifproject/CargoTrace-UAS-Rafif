<?php

namespace Tests\Feature;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $customerA;
    protected User $customerB;
    protected User $admin;
    protected User $operator;
    protected Shipment $shipmentA;
    protected Shipment $shipmentB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        // Ambil Customer A dari database seeder
        $this->customerA = User::where('role', User::ROLE_CUSTOMER)->firstOrFail();

        // Buat Customer B baru
        $this->customerB = User::factory()->create([
            'name' => 'Customer B Logistics',
            'email' => 'customerB@example.com',
            'role' => User::ROLE_CUSTOMER,
            'status' => User::STATUS_ACTIVE,
        ]);

        $this->admin = User::where('role', User::ROLE_ADMINISTRATOR)->firstOrFail();
        $this->operator = User::where('role', User::ROLE_LOGISTICS_OPERATOR)->firstOrFail();

        // Pastikan ada shipment milik Customer A
        $this->shipmentA = Shipment::where('customer_id', $this->customerA->id)->firstOrFail();

        // Buat shipment milik Customer B
        $carrier = Carrier::first();
        $origin = Location::first();
        $dest = Location::skip(1)->first();

        $this->shipmentB = Shipment::create([
            'shipment_number' => 'CT-CUST-B-001',
            'customer_id' => $this->customerB->id,
            'carrier_id' => $carrier->id,
            'origin_location_id' => $origin->id,
            'destination_location_id' => $dest->id,
            'current_location_id' => $origin->id,
            'status' => Shipment::STATUS_IN_TRANSIT,
            'cargo_type' => 'Secret Electronics',
            'weight' => 200,
            'volume' => 2.5,
            'expected_departure' => now()->subDay(),
            'expected_arrival' => now()->addDays(2),
            'estimated_time_arrival' => now()->addDays(2),
        ]);
    }

    /**
     * Skenario 1: Customer A login dan membuka dashboard serta kargo miliknya (Harus Berhasil)
     */
    public function test_customer_can_access_own_dashboard_and_shipments(): void
    {
        $response = $this->actingAs($this->customerA)->get(route('customer.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Selamat Datang');

        $listResponse = $this->actingAs($this->customerA)->get(route('customer.shipments.index'));
        $listResponse->assertStatus(200);
        $listResponse->assertSee($this->shipmentA->shipment_number);
        $listResponse->assertDontSee($this->shipmentB->shipment_number);

        $detailResponse = $this->actingAs($this->customerA)->get(route('customer.shipments.show', $this->shipmentA->id));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee($this->shipmentA->shipment_number);
    }

    /**
     * Skenario 2: Anti-IDOR: Customer A membuka shipment milik Customer B (Harus Ditolak 404/403)
     */
    public function test_customer_a_cannot_view_shipment_belonging_to_customer_b(): void
    {
        $response = $this->actingAs($this->customerA)->get(route('customer.shipments.show', $this->shipmentB->id));
        $response->assertStatus(404);
    }

    /**
     * Skenario 3: Customer A mencoba mengakses modul Administrator atau Operator (Harus Ditolak 403)
     */
    public function test_customer_cannot_access_admin_or_operator_modules(): void
    {
        $adminResponse = $this->actingAs($this->customerA)->get(route('admin.dashboard'));
        $adminResponse->assertStatus(403);

        $operatorResponse = $this->actingAs($this->customerA)->get(route('operator.dashboard'));
        $operatorResponse->assertStatus(403);
    }

    /**
     * Skenario 4: Customer A dapat memperbarui profil mandiri
     */
    public function test_customer_can_update_own_profile(): void
    {
        $response = $this->actingAs($this->customerA)
            ->put(route('customer.profile.update'), [
                'name' => 'Customer A Updated',
                'company_name' => 'PT Global Mart Internasional',
                'phone' => '+62899887766',
            ]);

        $response->assertSessionHas('status');

        $this->customerA->refresh();
        $this->assertEquals('Customer A Updated', $this->customerA->name);
        $this->assertEquals('+62899887766', $this->customerA->phone);
    }

    /**
     * Skenario 5: Customer diarahkan ke Customer Dashboard saat login
     */
    public function test_customer_redirected_to_customer_dashboard_after_login(): void
    {
        $response = $this->post('/login', [
            'email' => $this->customerA->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('customer.dashboard'));
    }

    /**
     * Skenario 6: Customer dapat membuka formulir pemesanan pengiriman baru
     */
    public function test_customer_can_view_create_shipment_form(): void
    {
        $response = $this->actingAs($this->customerA)->get(route('customer.shipments.create'));
        $response->assertStatus(200);
        $response->assertSee('Pesan Pengiriman Kargo Baru');
        $response->assertSee('Nomor Booking');
    }

    /**
     * Skenario 7: Customer dapat melakukan booking pemesanan pengiriman kargo baru
     */
    public function test_customer_can_book_new_shipment_successfully(): void
    {
        $carrier = Carrier::firstOrFail();
        $origin = Location::firstOrFail();
        $dest = Location::skip(1)->firstOrFail();

        $payload = [
            'carrier_id' => $carrier->id,
            'origin_location_id' => $origin->id,
            'destination_location_id' => $dest->id,
            'cargo_type' => 'High Grade Arabica Coffee',
            'weight' => 5000,
            'volume' => 20,
            'expected_departure' => now()->addDays(2)->format('Y-m-d'),
            'expected_arrival' => now()->addDays(10)->format('Y-m-d'),
            'item_name' => 'Green Coffee Beans Specialty',
            'hs_code' => '0901.11.10',
            'item_qty' => 500,
            'item_value' => 45000,
            'notes' => 'Simpan di kontainer berpendingin',
        ];

        $response = $this->actingAs($this->customerA)->post(route('customer.shipments.store'), $payload);

        $response->assertSessionHas('status');

        $createdShipment = Shipment::where('customer_id', $this->customerA->id)
            ->where('cargo_type', 'High Grade Arabica Coffee')
            ->first();

        $this->assertNotNull($createdShipment);
        $this->assertEquals(Shipment::STATUS_BOOKED, $createdShipment->status);
        $this->assertEquals($origin->id, $createdShipment->current_location_id);
        $this->assertDatabaseHas('shipment_items', [
            'shipment_id' => $createdShipment->id,
            'item_name' => 'Green Coffee Beans Specialty',
        ]);
        $this->assertDatabaseHas('tracking_events', [
            'shipment_id' => $createdShipment->id,
            'shipment_status_at_event' => Shipment::STATUS_BOOKED,
        ]);

        $response->assertRedirect(route('customer.shipments.show', $createdShipment));
    }

    /**
     * Skenario 8: Customer dapat memesan pengiriman via Udara (AIR)
     */
    public function test_customer_can_book_air_cargo_shipment(): void
    {
        $carrier = Carrier::firstOrFail();
        $origin = Location::firstOrFail();
        $dest = Location::skip(1)->firstOrFail();

        $payload = [
            'carrier_id' => $carrier->id,
            'transport_mode' => 'AIR',
            'origin_location_id' => $origin->id,
            'destination_location_id' => $dest->id,
            'cargo_type' => 'Express Semiconductor Wafers',
            'weight' => 150,
            'volume' => 1.5,
            'expected_departure' => now()->addDay()->format('Y-m-d'),
            'expected_arrival' => now()->addDays(3)->format('Y-m-d'),
            'item_name' => 'Silicon Microchips',
            'hs_code' => '8542.31.00',
            'item_qty' => 1000,
            'item_value' => 85000,
        ];

        $response = $this->actingAs($this->customerA)->post(route('customer.shipments.store'), $payload);

        $response->assertSessionHas('status');

        $this->assertDatabaseHas('shipments', [
            'customer_id' => $this->customerA->id,
            'transport_mode' => 'AIR',
            'cargo_type' => 'Express Semiconductor Wafers',
            'status' => Shipment::STATUS_BOOKED,
        ]);
    }
}

