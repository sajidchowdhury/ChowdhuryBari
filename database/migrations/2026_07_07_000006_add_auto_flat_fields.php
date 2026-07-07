<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds floor_count + families_per_floor to buildings (used for auto-flat generation),
 * and resident_name + resident_phone to flats (extra info, editable per slot).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buildings', function (Blueprint $table) {
            // Renamed from total_floor for clarity — drives auto-flat generation
            if (!Schema::hasColumn('buildings', 'floor_count')) {
                $table->unsignedInteger('floor_count')->default(1)->after('usage_type');
            }
            // How many families/flats per floor (default 1)
            if (!Schema::hasColumn('buildings', 'families_per_floor')) {
                $table->unsignedInteger('families_per_floor')->default(1)->after('floor_count');
            }
            // Drop the old total_floor column if it exists
            if (Schema::hasColumn('buildings', 'total_floor')) {
                $table->dropColumn('total_floor');
            }
        });

        Schema::table('flats', function (Blueprint $table) {
            if (!Schema::hasColumn('flats', 'resident_name')) {
                $table->string('resident_name')->nullable()->after('flat_number');
            }
            if (!Schema::hasColumn('flats', 'resident_phone')) {
                $table->string('resident_phone')->nullable()->after('resident_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('flats', function (Blueprint $table) {
            $table->dropColumn(['resident_name', 'resident_phone']);
        });

        Schema::table('buildings', function (Blueprint $table) {
            $table->integer('total_floor')->default(1);
            $table->dropColumn(['floor_count', 'families_per_floor']);
        });
    }
};
