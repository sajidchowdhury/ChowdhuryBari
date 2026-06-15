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
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('road_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('owner');
            $table->integer('total_floor');
            $table->integer('total_family');
            $table->string('building_type');
            $table->string('owner_number');
            $table->string('google_ln')->nullable();
            $table->string('google_lt')->nullable();
            $table->text('extra_information')->nullable();
            $table->json('service_taking')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
