<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            // Ubah transport_mode ke string agar fleksibel menampung SEA, AIR, dan LAND (Mobil / Truk)
            $table->string('transport_mode', 20)->default('SEA')->change();

            if (!Schema::hasColumn('shipments', 'delivery_country')) {
                $table->string('delivery_country', 100)->nullable()->after('destination_location_id');
            }
            if (!Schema::hasColumn('shipments', 'delivery_city')) {
                $table->string('delivery_city', 100)->nullable()->after('delivery_country');
            }
            if (!Schema::hasColumn('shipments', 'delivery_district')) {
                $table->string('delivery_district', 150)->nullable()->after('delivery_city');
            }
            if (!Schema::hasColumn('shipments', 'delivery_address')) {
                $table->text('delivery_address')->nullable()->after('delivery_district');
            }
            if (!Schema::hasColumn('shipments', 'delivery_lat')) {
                $table->decimal('delivery_lat', 10, 7)->nullable()->after('delivery_address');
            }
            if (!Schema::hasColumn('shipments', 'delivery_lng')) {
                $table->decimal('delivery_lng', 10, 7)->nullable()->after('delivery_lat');
            }
            if (!Schema::hasColumn('shipments', 'is_last_mile')) {
                $table->boolean('is_last_mile')->default(false)->after('delivery_lng')->index();
            }
            if (!Schema::hasColumn('shipments', 'courier_name')) {
                $table->string('courier_name', 100)->nullable()->after('is_last_mile');
            }
            if (!Schema::hasColumn('shipments', 'courier_vehicle')) {
                $table->string('courier_vehicle', 100)->nullable()->after('courier_name');
            }
            if (!Schema::hasColumn('shipments', 'coffee_variety')) {
                $table->string('coffee_variety', 100)->nullable()->after('cargo_type');
            }
            if (!Schema::hasColumn('shipments', 'coffee_grade')) {
                $table->string('coffee_grade', 100)->nullable()->after('coffee_variety');
            }
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $columns = [
                'delivery_country',
                'delivery_city',
                'delivery_district',
                'delivery_address',
                'delivery_lat',
                'delivery_lng',
                'is_last_mile',
                'courier_name',
                'courier_vehicle',
                'coffee_variety',
                'coffee_grade',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('shipments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
