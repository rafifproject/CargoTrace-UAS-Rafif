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
}

