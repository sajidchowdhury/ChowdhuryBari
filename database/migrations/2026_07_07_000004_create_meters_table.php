<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Meters — electricity meters. Each flat has 1 (sometimes 2) meters.
 *
 * The meter_number is the key we use to check BPDB/DESCO recharges.
 * last_recharge_amount + last_recharge_at are denormalized from the
 * meter_readings table for quick lookups (updated by the sync command
 * or when a manual reading is recorded).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flat_id')->constrained()->cascadeOnDelete();
            $table->string('meter_number')->unique();       // e.g. "BPDB-123456789"
            $table->enum('provider', ['bpdb', 'desco', 'other'])->default('bpdb');
            $table->boolean('is_active')->default(true);
            $table->decimal('last_recharge_amount', 10, 2)->nullable();
            $table->timestamp('last_recharge_at')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();

            $table->index('flat_id');
            $table->index('last_recharge_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meters');
    }
};
