<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds requested_flat_states to family_reduction_applications.
 *
 * Stores the COMPLETE requested flat state (which flats on/off + meter numbers)
 * as a JSON array. On approval, the admin applies these states to the actual
 * flats + updates billing_family_count.
 *
 * Before this migration, toggle saves immediately changed flat statuses.
 * Now they create a PENDING application — nothing changes until admin approves.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('family_reduction_applications', function (Blueprint $table) {
            $table->json('requested_flat_states')->nullable()->after('vacant_flat_ids');
        });
    }

    public function down(): void
    {
        Schema::table('family_reduction_applications', function (Blueprint $table) {
            $table->dropColumn('requested_flat_states');
        });
    }
};
