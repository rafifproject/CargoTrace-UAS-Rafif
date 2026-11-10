<?php

namespace Database\Seeders;

use App\Models\Carrier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CarrierSeeder extends Seeder
{
    /**
     * Run the database seeds for 205+ global national and international carriers.
     */
    public function run(): void
    {
        $jsonPath = database_path('seeders/carriers_205.json');
        if (!File::exists($jsonPath)) {
            $this->command?->error("File carriers_205.json tidak ditemukan!");
            return;
        }

        $carriers = json_decode(File::get($jsonPath), true);
        if (!is_array($carriers)) {
            $this->command?->error("Format JSON carriers tidak valid!");
            return;
        }

        foreach ($carriers as $data) {
            Carrier::updateOrCreate(
                ['code' => $data['code']],
                [
                    'name'          => $data['name'],
                    'country'       => $data['country'],
                    'contact_email' => $data['contact_email'],
                    'contact_phone' => $data['contact_phone'] ?? null,
                    'website'       => $data['website'] ?? null,
                    'status'        => $data['status'] ?? Carrier::STATUS_ACTIVE,
                ]
            );
        }

        $this->command?->info("Berhasil mengimpor " . count($carriers) . " ekspedisi global dari seluruh 195+ negara dunia.");
    }
}
