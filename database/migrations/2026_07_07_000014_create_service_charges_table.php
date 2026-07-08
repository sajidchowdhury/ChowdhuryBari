<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Service charges — the monthly fee breakdown shown to members
 * in the "সেবা চার্জের বিবরণ" card on their dashboard.
 * Admin adds each service (e.g. মাসিক সদস্য ফি, নিরাপত্তা চার্জ) with its amount;
 * the member dashboard sums all active ones to show the total monthly due.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_charges', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // e.g. মাসিক সদস্য ফি
            $table->unsignedInteger('amount');         // amount in BDT (integer taka)
            $table->text('description')->nullable();   // optional note
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_charges');
    }
};
