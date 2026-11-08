<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_administrator_can_access_dashboard_and_settings(): void
    {
        $admin = User::where('email', 'admin@cargotrace.id')->firstOrFail();

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);

        $responseSettings = $this->actingAs($admin)->get('/admin/settings');
        $responseSettings->assertStatus(200);
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $customer = User::where('email', 'customer@globalmart.com')->firstOrFail();

        $response = $this->actingAs($customer)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_operator_cannot_access_admin_dashboard(): void
    {
        $operator = User::where('email', 'operator@cargotrace.id')->firstOrFail();

        $response = $this->actingAs($operator)->get('/admin/dashboard');
        $response->assertStatus(403);
    }
}

