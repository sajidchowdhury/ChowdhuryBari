<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Notices — society announcements shown in the "নোটিশ ও ঘোষণা" section.
 * Each notice has a type, headline, description, publish time, and an
 * optional active_till_date for auto-expiry.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('type');                    // e.g. সাধারণ, নিরাপত্তা, পরিচ্ছন্নতা, আসন্ন, গুরুত্বপূর্ণ
            $table->string('headline');
            $table->text('description');
            $table->timestamp('published_at')->useCurrent();
            $table->date('active_till_date')->nullable();  // auto-expiry date (null = never)
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};
