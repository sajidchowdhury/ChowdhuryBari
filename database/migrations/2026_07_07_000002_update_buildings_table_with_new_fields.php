<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Updates the buildings table to match the new spec:
 *   - Renames/keeps: owner → owner_name, owner_number → owner_phone
 *   - Adds: caretaker_name, caretaker_phone
 *   - Adds: structure_type (building/tin_shed/other)
 *   - Adds: usage_type (residential/shop/mixed)
 *   - Removes: total_family (now derived from flats table)
 *   - Removes: service_taking (moved to building-level boolean flags: has_security, has_cleaning)
 *   - Removes: building_type (replaced by structure_type + usage_type)
 *
 * This is a destructive migration — it drops building_type, total_family,
 * and service_taking columns. That's OK because we have no real data yet
 * (Phase 1, fresh setup). In production we'd write a data-migration step.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buildings', function (Blueprint $table) {
            // Rename existing columns for clarity
            if (Schema::hasColumn('buildings', 'owner')) {
                $table->renameColumn('owner', 'owner_name');
            }
            if (Schema::hasColumn('buildings', 'owner_number')) {
                $table->renameColumn('owner_number', 'owner_phone');
            }

            // Add new columns
            $table->string('caretaker_name')->nullable()->after('owner_phone');
            $table->string('caretaker_phone')->nullable()->after('caretaker_name');
            $table->enum('structure_type', ['building', 'tin_shed', 'other'])->default('building')->after('caretaker_phone');
            $table->enum('usage_type', ['residential', 'shop', 'mixed'])->default('residential')->after('structure_type');
            $table->boolean('has_security')->default(false)->after('usage_type');
            $table->boolean('has_cleaning')->default(false)->after('has_security');
        });

        // Drop columns that are no longer needed
        Schema::table('buildings', function (Blueprint $table) {
            foreach (['building_type', 'total_family', 'service_taking'] as $column) {
                if (Schema::hasColumn('buildings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->string('building_type')->nullable();
            $table->integer('total_family')->default(0);
            $table->json('service_taking')->nullable();
        });

        Schema::table('buildings', function (Blueprint $table) {
            $table->dropColumn(['caretaker_name', 'caretaker_phone', 'structure_type', 'usage_type', 'has_security', 'has_cleaning']);
        });

        Schema::table('buildings', function (Blueprint $table) {
            if (Schema::hasColumn('buildings', 'owner_name')) {
                $table->renameColumn('owner_name', 'owner');
            }
            if (Schema::hasColumn('buildings', 'owner_phone')) {
                $table->renameColumn('owner_phone', 'owner_number');
            }
        });
    }
};
