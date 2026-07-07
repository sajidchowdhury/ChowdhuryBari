<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Meter readings — monthly snapshot of each meter's recharge status.
 *
 * One row per meter per month. If recharge_amount is null, the meter
 * was not recharged that month (flat likely vacated).
 *
 * 'source' field tracks how the data was obtained:
 *   - 'manual'  : secretary entered it by hand
 *   - 'bpdb_api': (future) scraped from BPDB/DESCO
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meter_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meter_id')->constrained()->cascadeOnDelete();
            $table->date('reading_date');                  // the month this snapshot is for
            $table->decimal('recharge_amount', 10, 2)->nullable();  // null = no recharge that month
            $table->timestamp('recharged_at')->nullable();
            $table->enum('source', ['manual', 'bpdb_api'])->default('manual');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['meter_id', 'reading_date']);
            $table->index('reading_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meter_readings');
    }
};
