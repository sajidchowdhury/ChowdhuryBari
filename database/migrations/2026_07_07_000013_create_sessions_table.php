<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sessions table — required when SESSION_DRIVER=database (the default in
 * config/session.php). Without this table, sessions silently fail to persist,
 * which causes 419 "Page Expired" CSRF errors on multi-step POST flows
 * (e.g. the member login phone -> OTP flow).
 *
 * If you use SESSION_DRIVER=file (the default in .env.example), this table
 * is unused but harmless.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
