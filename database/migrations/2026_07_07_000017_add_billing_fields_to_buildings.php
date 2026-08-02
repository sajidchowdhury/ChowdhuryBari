<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds billing fields to buildings:
 *   - per_family_amount: the per-family monthly charge (admin sets per building)
 *   - billing_family_count: the expected number of families to bill
 *     (admin-controlled; set via approved reduction applications or manual edit.
 *      NULL = fall back to auto-calc from active flats.)
 *
 * The total monthly due for a building owner =
 *   (per_family_amount × billing_family_count) + sum(flat service charges for category)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->unsignedInteger('per_family_amount')->default(0)->after('building_category');
            $table->unsignedInteger('billing_family_count')->nullable()->after('per_family_amount');
        });
    }

    public function down(): void
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->dropColumn(['per_family_amount', 'billing_family_count']);
        });
    }
};
