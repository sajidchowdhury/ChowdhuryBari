<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Field data collection — single-form data gathered on-site at each building.
 *
 * Workflow:
 *   1. Field worker visits a building, fills one form (road, building, owner,
 *      GPS, floors, flats + residents + meter numbers)
 *   2. Form saved as a draft here (NOT yet in the main buildings/flats/meters tables)
 *   3. At end of day, worker reviews collected data in the dashboard
 *   4. "Migrate All" pushes all draft records into the real buildings/flats/meters
 *      tables (auto-generates flats, attaches meters, links to road)
 *
 * The flats + meters are stored as JSON (flats_data) so one form = one row,
 * but on migration it creates many flats + meters rows.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('field_data_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('road_id')->nullable()->constrained()->nullOnDelete();
            $table->string('new_road_name')->nullable();        // if road doesn't exist yet

            // Building info
            $table->string('building_name');
            $table->string('owner_name');
            $table->string('owner_phone');
            $table->string('caretaker_name')->nullable();
            $table->string('caretaker_phone')->nullable();
            $table->string('building_category')->nullable();    // tin_shed / below_or_equal_4_floor / above_4_floor / shop
            $table->string('structure_type')->default('building');
            $table->string('usage_type')->default('residential');
            $table->unsignedInteger('floor_count')->default(1);
            $table->unsignedInteger('families_per_floor')->default(1);
            $table->boolean('has_security')->default(false);
            $table->boolean('has_cleaning')->default(false);
            $table->string('google_lt')->nullable();
            $table->string('google_ln')->nullable();
            $table->text('extra_information')->nullable();
            $table->string('image_path')->nullable();           // building photo

            // Flats + meters + residents as JSON:
            // [{ floor: 1, flat_number: 'Floor 1 - Flat A', resident_name: '...', resident_phone: '...', meter_number: '...', provider: 'bpdb' }, ...]
            $table->json('flats_data')->nullable();

            // Tracking
            $table->string('status')->default('draft');         // draft / migrated
            $table->foreignId('collected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('migrated_at')->nullable();
            $table->foreignId('migrated_building_id')->nullable(); // the building created after migration (no FK — building may be deleted later)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_data_collections');
    }
};
