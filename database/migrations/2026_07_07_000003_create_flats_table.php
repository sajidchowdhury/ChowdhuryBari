<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Flats — each flat/unit in a building. One family lives in one flat.
 *
 * The is_active flag is the manual override (secretary marks vacated).
 * The actual active status is computed from meter recharge history
 * (see Building::getActiveFamilyCount()).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->cascadeOnDelete();
            $table->string('flat_number');           // e.g. "A-1", "2nd Floor Left"
            $table->unsignedInteger('floor_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('vacated_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['building_id', 'is_active']);
            $table->unique(['building_id', 'flat_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flats');
    }
};
