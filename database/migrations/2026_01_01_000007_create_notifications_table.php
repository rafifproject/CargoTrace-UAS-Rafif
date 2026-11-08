<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('shipment_id')->nullable()->constrained('shipments')->cascadeOnDelete();
            $table->enum('type', ['DELAY_ALERT', 'EXCEPTION_ALERT', 'STATUS_UPDATE', 'SYSTEM_NOTICE'])->index();
            $table->string('title', 150);
            $table->text('message');
            $table->enum('severity', ['INFO', 'WARNING', 'DANGER', 'SUCCESS'])->default('INFO')->index();
            $table->boolean('is_read')->default(false)->index();
            $table->dateTime('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

