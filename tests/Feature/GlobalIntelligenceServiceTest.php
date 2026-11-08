<?php

namespace Tests\Feature;

use App\Services\GlobalIntelligenceService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GlobalIntelligenceServiceTest extends TestCase
{
    public function test_weather_service_handles_mocked_api_response(): void
    {
        Http::fake([
            'api.open-meteo.com/*' => Http::response([
                'current' => [
                    'temperature_2m' => 28.5,
                    'relative_humidity_2m' => 65,
                    'wind_speed_10m' => 15.0,
                    'precipitation' => 0.0,
                ],
            ], 200),
        ]);

        $service = new GlobalIntelligenceService();
        $weather = $service->getWeather(-7.195, 112.729);

        $this->assertArrayHasKey('current', $weather);
    }

    public function test_supply_chain_weighted_risk_model_calculation(): void
    {
        $service = new GlobalIntelligenceService();
        $risk = $service->calculateSupplyChainRisk();

        $this->assertArrayHasKey('overall_score', $risk);
        $this->assertArrayHasKey('breakdown', $risk);
        $this->assertGreaterThanOrEqual(0, $risk['overall_score']);
        $this->assertLessThanOrEqual(100, $risk['overall_score']);
    }
}

