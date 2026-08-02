<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the buildings table with the FINAL column names (not the old ones).
 * This replaces the original migration that used total_floor/owner/etc.
 * The alter migration (2026_07_07_000002) is now a no-op since these
 * columns already exist.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('road_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('owner_name');
            $table->string('owner_phone');
            $table->string('caretaker_name')->nullable();
            $table->string('caretaker_phone')->nullable();
            $table->enum('structure_type', ['building', 'tin_shed', 'other'])->default('building');
            $table->enum('usage_type', ['residential', 'shop', 'mixed'])->default('residential');
            $table->unsignedInteger('floor_count')->default(1);
            $table->unsignedInteger('families_per_floor')->default(1);
            $table->boolean('has_security')->default(false);
            $table->boolean('has_cleaning')->default(false);
            $table->string('google_lt')->nullable();
            $table->string('google_ln')->nullable();
            $table->text('extra_information')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
