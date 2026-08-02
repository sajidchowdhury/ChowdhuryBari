<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Site settings — singleton for navigation + footer customization.
 * Admin uploads a logo, sets navbar color, and configures footer
 * social links (WhatsApp / Facebook / YouTube) + address.
 * Only one row is ever used (id = 1).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('logo_path')->nullable();         // stored under public/uploads/site/
            $table->string('nav_color')->nullable();         // navbar background color (hex)
            $table->string('whatsapp_link')->nullable();     // footer whatsapp link
            $table->string('facebook_link')->nullable();     // footer facebook link
            $table->string('youtube_link')->nullable();      // footer youtube link
            $table->text('footer_address')->nullable();      // footer address block
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
