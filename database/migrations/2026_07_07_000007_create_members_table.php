<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Team members — society committee members shown in the "আমাদের নেতৃত্ব" section.
 * Each member has: name, designation, started_from (year), phone, image, bio (for modal).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('designation');           // e.g. সভাপতি, সাধারণ সম্পাদক
            $table->string('started_from');           // e.g. ২০১৮ or 2018
            $table->string('phone')->nullable();
            $table->string('image_path')->nullable();
            $table->text('bio')->nullable();          // short article shown in modal
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
