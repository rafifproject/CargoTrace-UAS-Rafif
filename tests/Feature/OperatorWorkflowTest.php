<?php

namespace Tests\Feature;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\TrackingEvent;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperatorWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $operator;
    protected User $admin;
    protected User $customer;
    protected Carrier $carrier;
    protected Location $origin;
    protected Location $destination;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->operator = User::where('role', User::ROLE_LOGISTICS_OPERATOR)->firstOrFail();
        $this->admin = User::where('role', User::ROLE_ADMINISTRATOR)->firstOrFail();
        $this->customer = User::where('role', User::ROLE_CUSTOMER)->firstOrFail();
        $this->carrier = Carrier::where('status', Carrier::STATUS_ACTIVE)->firstOrFail();
        $locations = Location::where('status', Location::STATUS_ACTIVE)->get();
        $this->origin = $locations[0];
        $this->destination = $locations[1];
    }

    public function test_operator_can_access_operator_dashboard(): void
    {
        $response = $this->actingAs($this->operator)->get(route('operator.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Dasbor Operasional');
    }

    public function test_operator_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->operator)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_admin_cannot_access_operator_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('operator.dashboard'));
        $response->assertStatus(403);
    }

    public function test_operator_can_create_new_shipment(): void
    {
        $payload = [
            'shipment_number' => 'CT-OPR-999',
            'customer_id' => $this->customer->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $this->origin->id,
            'destination_location_id' => $this->destination->id,
            'cargo_type' => 'Medical Equipment',
            'weight' => 450.50,
            'volume' => 3.20,
            'expected_departure' => now()->addDay()->format('Y-m-d H:i:s'),
            'expected_arrival' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'estimated_time_arrival' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'notes' => 'Fragile cargo handle with care',
        ];

        $response = $this->actingAs($this->operator)->post(route('operator.shipments.store'), $payload);

        $this->assertDatabaseHas('shipments', [
            'shipment_number' => 'CT-OPR-999',
            'cargo_type' => 'Medical Equipment',
            'status' => Shipment::STATUS_CREATED,
        ]);

        // Auto-creates first append-only tracking event
        $this->assertDatabaseHas('tracking_events', [
            'shipment_status_at_event' => Shipment::STATUS_CREATED,
            'event_type' => TrackingEvent::TYPE_STATUS_CHANGE,
        ]);

        $shipment = Shipment::where('shipment_number', 'CT-OPR-999')->first();
        $response->assertRedirect(route('operator.shipments.show', $shipment));
    }

    public function test_operator_can_append_tracking_event(): void
    {
        $shipment = Shipment::where('status', '!=', Shipment::STATUS_DELIVERED)->firstOrFail();

        $payload = [
            'event_type' => 'CHECKPOINT_REACHED',
            'location_id' => $this->destination->id,
            'event_time' => now()->format('Y-m-d H:i:s'),
            'shipment_status_at_event' => Shipment::STATUS_IN_TRANSIT,
            'description' => 'Kontainer tiba di transit terminal 2.',
        ];

        $response = $this->actingAs($this->operator)
            ->post(route('operator.shipments.tracking.store', $shipment), $payload);

        $response->assertSessionHas('status');

        $this->assertDatabaseHas('tracking_events', [
            'shipment_id' => $shipment->id,
            'event_type' => 'CHECKPOINT_REACHED',
            'description' => 'Kontainer tiba di transit terminal 2.',
        ]);

        $shipment->refresh();
        $this->assertEquals(Shipment::STATUS_IN_TRANSIT, $shipment->status);
    }

    public function test_operator_can_create_and_resolve_exception(): void
    {
        $shipment = Shipment::firstOrFail();

        // 1. Report Exception
        $response = $this->actingAs($this->operator)->post(route('operator.exceptions.store'), [
            'shipment_id' => $shipment->id,
            'exception_type' => 'WEATHER_DELAY',
            'severity' => 'HIGH',
            'description' => 'Badai topan di Laut Cina Selatan menunda perjalanan kapal.',
        ]);

        $response->assertSessionHas('status');

        $this->assertDatabaseHas('shipment_exceptions', [
            'shipment_id' => $shipment->id,
            'exception_type' => 'WEATHER_DELAY',
            'severity' => 'HIGH',
            'status' => ShipmentException::STATUS_OPEN,
        ]);

        $exception = ShipmentException::where('shipment_id', $shipment->id)
            ->where('exception_type', 'WEATHER_DELAY')
            ->firstOrFail();

        // 2. Resolve Exception
        $resolveResponse = $this->actingAs($this->operator)
            ->patch(route('operator.exceptions.resolve', $exception), [
                'resolution_notes' => 'Rute dialihkan ke Selat Malaka barat, estimasi keterlambatan 4 jam.',
            ]);

        $resolveResponse->assertSessionHas('status');

        $exception->refresh();
        $this->assertEquals(ShipmentException::STATUS_RESOLVED, $exception->status);
        $this->assertEquals($this->operator->id, $exception->resolved_by);
    }

    public function test_operator_can_update_profile(): void
    {
        $response = $this->actingAs($this->operator)
            ->put(route('operator.profile.update'), [
                'name' => 'Operator Baru Terupdate',
                'phone' => '+62811223344',
                'company_name' => 'CargoTrace Hub Tanjung Perak',
            ]);

        $response->assertSessionHas('status');

        $this->operator->refresh();
        $this->assertEquals('Operator Baru Terupdate', $this->operator->name);
        $this->assertEquals('+62811223344', $this->operator->phone);
    }
}
