<?php

namespace Tests\Feature;

use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShipmentModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_database_has_seeded_data(): void
    {
        $this->assertDatabaseHas('users', ['email' => 'admin@cargotrace.id', 'role' => User::ROLE_ADMINISTRATOR]);
        $this->assertDatabaseHas('carriers', ['code' => 'FDX']);
        $this->assertDatabaseHas('shipments', ['shipment_number' => 'CT-2026-001']);
    }

    public function test_delay_calculation_logic(): void
    {
        $shipment = Shipment::where('shipment_number', 'CT-2026-001')->firstOrFail();
        
        $this->assertTrue($shipment->isDelayed());
        $this->assertEquals(Shipment::DELAY_HIGH, $shipment->delay_severity);
        $this->assertStringContainsString('+2 hari', $shipment->formatted_delay);
    }

    public function test_tracking_events_are_chronological(): void
    {
        $shipment = Shipment::where('shipment_number', 'CT-2026-001')->firstOrFail();
        $events = $shipment->trackingEvents;

        $this->assertNotEmpty($events);
        $this->assertInstanceOf(TrackingEvent::class, $events->first());
    }
}
