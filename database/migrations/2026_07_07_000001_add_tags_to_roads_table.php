<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds a JSON 'tags' column to the roads table.
 *
 * Tags are short labels shown on road cards (e.g. "Main Road", "CCTV Covered",
 * "Cleanest 2024", "Newly Renovated"). Stored as a JSON array so a road can
 * have multiple tags without a separate tags table.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roads', function (Blueprint $table) {
            $table->json('tags')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('roads', function (Blueprint $table) {
            $table->dropColumn('tags');
        });
    }
};
