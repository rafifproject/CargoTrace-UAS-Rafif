<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'transport_mode')) {
                $table->enum('transport_mode', ['SEA', 'AIR'])->default('SEA')->after('cargo_type')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            if (Schema::hasColumn('shipments', 'transport_mode')) {
                $table->dropColumn('transport_mode');
            }
        });
    }
};
