<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add building_category to both buildings and service_charges.
 *
 * Building categories (one per building):
 *   - tin_shed              (টিন শেড)
 *   - below_or_equal_4_floor (৪তলা বা নিচে)
 *   - above_4_floor          (৪তলার উপরে)
 *   - shop                   (দোকান)
 *
 * Each service charge is configured for ONE building category, so the
 * member dashboard shows only the charges that apply to their building type.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->string('building_category')->nullable()->after('usage_type');
        });

        Schema::table('service_charges', function (Blueprint $table) {
            $table->string('building_category')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('service_charges', function (Blueprint $table) {
            $table->dropColumn('building_category');
        });
        Schema::table('buildings', function (Blueprint $table) {
            $table->dropColumn('building_category');
        });
    }
};
