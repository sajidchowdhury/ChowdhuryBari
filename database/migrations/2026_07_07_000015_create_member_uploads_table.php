<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Member uploads — monthly yard photos submitted by members for the
 * cleanliness social-value ranking.
 *
 * - Each member can upload up to 4 images per month (month_key = YYYY-MM).
 * - The upload counter resets each month (monthly rotation).
 * - Admin rates each image 1-10 stars (anonymous — admin can't see who sent it).
 * - Member's monthly social value = avg(star_rating of rated images) × 10 → 1-100.
 * - Previous months' rows are kept for ranking-history comparison.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');                       // stored under public/uploads/member/
            $table->string('caption')->nullable();
            $table->string('month_key', 7)->index();            // e.g. 2026-01
            $table->unsignedTinyInteger('star_rating')->nullable(); // 1-10, null = unrated
            $table->timestamp('rated_at')->nullable();
            $table->foreignId('rated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'month_key']);
            $table->index(['month_key', 'star_rating']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_uploads');
    }
};
