<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Gallery items — community photos shown in the "গ্যালারি" section.
 * Admin uploads an image + short caption (optional category).
 * Public site shows the 10 most recent uploads.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->string('image_path');                  // stored under public/uploads/gallery/
            $table->string('caption');                     // short description
            $table->string('category')->nullable();        // optional: ইভেন্ট, উন্নয়ন, এলাকা...
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_items');
    }
};
