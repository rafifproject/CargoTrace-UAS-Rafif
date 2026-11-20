<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Services\GlobalIntelligenceService;
use Illuminate\Database\Seeder;

class GlobalCountryHubSeeder extends Seeder
{
    public function run(): void
    {
        /** @var GlobalIntelligenceService $globalService */
        $globalService = app(GlobalIntelligenceService::class);
        $countries = $globalService->getCountriesMaster();

        foreach ($countries as $c) {
            $hubName = $c['hub'];
            $cleanHub = explode(' / ', $hubName)[0];
            $cityName = explode(' ', $cleanHub)[0] ?? $c['name'];

            Location::firstOrCreate(
                ['name' => $cleanHub],
                [
                    'city'          => $cityName,
                    'country'       => $c['name'],
                    'address'       => 'Terminal Internasional ' . $cleanHub . ', ' . $c['name'],
                    'location_type' => Location::TYPE_PORT,
                    'latitude'      => $c['lat'],
                    'longitude'     => $c['lng'],
                    'status'        => Location::STATUS_ACTIVE,
                ]
            );
        }
    }
}
