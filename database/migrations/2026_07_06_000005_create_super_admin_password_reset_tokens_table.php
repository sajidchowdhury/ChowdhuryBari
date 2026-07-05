<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Central DB: password reset tokens for super admins.
 *
 * Required by the 'super_admins' password broker defined in config/auth.php.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('super_admin_password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('super_admin_password_reset_tokens');
    }
};
