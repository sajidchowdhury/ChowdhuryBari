<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds charge_type to service_charges.
 *
 * Each service charge can be billed in one of three ways:
 *   - per_family: amount × billing_family_count  (e.g. moyla / garbage bill)
 *   - per_floor:  amount × floor_count           (e.g. elevator maintenance)
 *   - fixed:      amount (once per building)     (e.g. guard bill)
 *
 * Defaults to 'fixed' so existing charges remain backward-compatible.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_charges', function (Blueprint $table) {
            $table->string('charge_type')->default('fixed')->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('service_charges', function (Blueprint $table) {
            $table->dropColumn('charge_type');
        });
    }
};
