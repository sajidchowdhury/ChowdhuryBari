<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * About Us — singleton content for the public "আমাদের সম্পর্কে" section.
 * Admin edits headline + image + short description; public site renders it.
 * Only one row is ever used (id = 1).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_infos', function (Blueprint $table) {
            $table->id();
            $table->string('headline');                 // e.g. আমরা কারা?
            $table->text('description')->nullable();    // short description
            $table->string('image_path')->nullable();   // stored under public/uploads/about/
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_infos');
    }
};
