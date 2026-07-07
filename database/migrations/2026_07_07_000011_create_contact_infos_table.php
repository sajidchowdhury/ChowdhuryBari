<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Contact / Get In Touch — singleton content for the public "যোগাযোগ করুন" section.
 * Admin sets address/phone/email/whatsapp + the recipient email that contact-form
 * submissions are mailed to. Only one row is ever used (id = 1).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_infos', function (Blueprint $table) {
            $table->id();
            $table->string('address')->nullable();           // office address
            $table->string('phone')->nullable();             // hotline
            $table->string('email')->nullable();             // public email
            $table->string('whatsapp')->nullable();          // whatsapp number / link
            $table->string('office_hours')->nullable();      // e.g. সকাল ৮টা — রাত ১০টা
            $table->string('recipient_email')->nullable();   // where form submissions are sent
            $table->boolean('form_active')->default(true);   // toggle the contact form on/off
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_infos');
    }
};
