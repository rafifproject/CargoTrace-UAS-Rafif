<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('invoice_number', 50)->nullable()->after('shipment_number')->index();
            $table->decimal('shipping_cost', 12, 2)->default(0.00)->after('volume');
            $table->enum('payment_status', ['UNPAID', 'PAID'])->default('UNPAID')->after('status')->index();
            $table->string('payment_method', 100)->nullable()->after('payment_status');
            $table->timestamp('paid_at')->nullable()->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['invoice_number', 'shipping_cost', 'payment_status', 'payment_method', 'paid_at']);
        });
    }
};
