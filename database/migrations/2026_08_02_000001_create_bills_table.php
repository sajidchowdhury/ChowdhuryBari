<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->cascadeOnDelete();
            $table->string('billing_month', 7); // e.g. "2026-08"
            $table->unsignedInteger('field_data_family_count')->default(0)->comment('From field data collection');
            $table->unsignedInteger('actual_family_count')->default(0)->comment('Admin set — used for billing');
            $table->unsignedInteger('cleaning_rate_per_family')->default(100)->comment('Per family cleaning rate in taka');
            $table->unsignedInteger('cleaning_total')->default(0)->comment('cleaning_rate × actual_family_count');
            $table->unsignedInteger('security_guard_bill')->default(0)->comment('Security guard charge based on category');
            $table->unsignedInteger('total_bill')->default(0)->comment('cleaning_total + security_guard_bill');
            $table->enum('status', ['pending', 'paid', 'partial'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['building_id', 'billing_month']);
            $table->index('billing_month');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
