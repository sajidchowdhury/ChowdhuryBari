<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Alters buildings table to add the new fields (caretaker, structure_type,
 * usage_type, has_security, has_cleaning, floor_count, families_per_floor).
 *
 * FULLY IDEMPOTENT — every column is checked with Schema::hasColumn() before
 * being added or dropped. This migration is safe to run on:
 *   - Fresh DB (create_buildings_table already has all columns → this is a no-op)
 *   - Old DB (create_buildings_table had old columns → this renames + adds)
 *   - Mid-migration state (some columns exist, some don't → only missing ones added)
 */
return new class extends Migration
{
    public function up(): void
    {
        // Rename old columns if they exist
        if (Schema::hasColumn('buildings', 'owner') && !Schema::hasColumn('buildings', 'owner_name')) {
            Schema::table('buildings', fn (Blueprint $t) => $t->renameColumn('owner', 'owner_name'));
        }
        if (Schema::hasColumn('buildings', 'owner_number') && !Schema::hasColumn('buildings', 'owner_phone')) {
            Schema::table('buildings', fn (Blueprint $t) => $t->renameColumn('owner_number', 'owner_phone'));
        }

        // Add new columns only if they don't exist
        Schema::table('buildings', function (Blueprint $table) {
            if (!Schema::hasColumn('buildings', 'caretaker_name')) {
                $table->string('caretaker_name')->nullable()->after('owner_phone');
            }
            if (!Schema::hasColumn('buildings', 'caretaker_phone')) {
                $table->string('caretaker_phone')->nullable()->after('caretaker_name');
            }
            if (!Schema::hasColumn('buildings', 'structure_type')) {
                $table->enum('structure_type', ['building', 'tin_shed', 'other'])->default('building')->after('caretaker_phone');
            }
            if (!Schema::hasColumn('buildings', 'usage_type')) {
                $table->enum('usage_type', ['residential', 'shop', 'mixed'])->default('residential')->after('structure_type');
            }
            if (!Schema::hasColumn('buildings', 'floor_count')) {
                $table->unsignedInteger('floor_count')->default(1)->after('usage_type');
            }
            if (!Schema::hasColumn('buildings', 'families_per_floor')) {
                $table->unsignedInteger('families_per_floor')->default(1)->after('floor_count');
            }
            if (!Schema::hasColumn('buildings', 'has_security')) {
                $table->boolean('has_security')->default(false)->after('families_per_floor');
            }
            if (!Schema::hasColumn('buildings', 'has_cleaning')) {
                $table->boolean('has_cleaning')->default(false)->after('has_security');
            }
        });

        // Drop old columns that are no longer needed
        Schema::table('buildings', function (Blueprint $table) {
            foreach (['building_type', 'total_family', 'total_floor', 'service_taking'] as $column) {
                if (Schema::hasColumn('buildings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    public function down(): void
    {
        // Reverse is destructive — only run on fresh dev DBs
        Schema::table('buildings', function (Blueprint $table) {
            foreach (['caretaker_name', 'caretaker_phone', 'structure_type', 'usage_type', 'floor_count', 'families_per_floor', 'has_security', 'has_cleaning'] as $column) {
                if (Schema::hasColumn('buildings', $column)) {
                    $table->dropColumn($column);
                }
            }
            if (!Schema::hasColumn('buildings', 'building_type')) {
                $table->string('building_type')->nullable();
            }
            if (!Schema::hasColumn('buildings', 'total_floor')) {
                $table->integer('total_floor')->default(1);
            }
            if (!Schema::hasColumn('buildings', 'total_family')) {
                $table->integer('total_family')->default(0);
            }
            if (!Schema::hasColumn('buildings', 'service_taking')) {
                $table->json('service_taking')->nullable();
            }
        });

        if (Schema::hasColumn('buildings', 'owner_name') && !Schema::hasColumn('buildings', 'owner')) {
            Schema::table('buildings', fn (Blueprint $t) => $t->renameColumn('owner_name', 'owner'));
        }
        if (Schema::hasColumn('buildings', 'owner_phone') && !Schema::hasColumn('buildings', 'owner_number')) {
            Schema::table('buildings', fn (Blueprint $t) => $t->renameColumn('owner_phone', 'owner_number'));
        }
    }
};
