<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Central DB: catalog of products you sell across all societies.
 *
 * Examples: CCTV cameras, branded ID cards, cleaning supplies,
 * monthly security-guard subscription, etc.
 *
 * Products are platform-wide (not per-tenant) so you can sell the
 * same product to multiple societies. Orders store which tenant
 * purchased which product.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['physical', 'service', 'subscription']);
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('BDT');
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable(); // e.g. {"sku":"CCTV-001","stock":50}
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
