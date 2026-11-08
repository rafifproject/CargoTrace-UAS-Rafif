<?php

namespace Tests\Feature;

use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CargoTraceApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_api_login_returns_valid_sanctum_token(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'admin@cargotrace.id',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user',
                    'token',
                ]
            ]);
    }

    public function test_authenticated_user_can_fetch_shipments_via_api(): void
    {
        $admin = User::where('email', 'admin@cargotrace.id')->firstOrFail();

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/shipments');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_append_only_tracking_event_via_api(): void
    {
        $admin = User::where('email', 'admin@cargotrace.id')->firstOrFail();
        $shipment = Shipment::where('shipment_number', 'CT-2026-001')->firstOrFail();

        $response = $this->actingAs($admin, 'sanctum')->postJson("/api/shipments/{$shipment->id}/tracking", [
            'event_type' => 'CHECKPOINT_REACHED',
            'event_time' => now()->toDateTimeString(),
            'shipment_status_at_event' => 'IN_TRANSIT',
            'description' => 'Kontainer melewati gerbang pemeriksaan terminal otomatis.',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('tracking_events', [
            'shipment_id' => $shipment->id,
            'description' => 'Kontainer melewati gerbang pemeriksaan terminal otomatis.',
        ]);
    }

    public function test_exception_resolution_via_api(): void
    {
        $admin = User::where('email', 'admin@cargotrace.id')->firstOrFail();
        $exception = ShipmentException::where('status', 'OPEN')->firstOrFail();

        $response = $this->actingAs($admin, 'sanctum')->patchJson("/api/exceptions/{$exception->id}", [
            'status' => 'RESOLVED',
            'resolution_notes' => 'Masalah telah diverifikasi dan diselesaikan.',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('shipment_exceptions', [
            'id' => $exception->id,
            'status' => 'RESOLVED',
            'resolution_notes' => 'Masalah telah diverifikasi dan diselesaikan.',
        ]);
    }
}

