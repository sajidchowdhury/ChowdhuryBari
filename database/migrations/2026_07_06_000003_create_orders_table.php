<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Central DB: orders placed by tenants (or their members) for products.
 *
 * An order belongs to ONE tenant (society) and may have many line items.
 * When a tenant society purchases something (e.g. CCTV cameras for the
 * community), the order is recorded here so the super admin can see
 * platform-wide revenue.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id'); // FK to tenants.id (central)
            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->cascadeOnDelete();

            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2); // snapshot of product price at order time
            $table->decimal('total_amount', 10, 2);

            $table->enum('status', [
                'pending',      // created, awaiting payment
                'paid',         // payment confirmed
                'fulfilled',    // product delivered / service activated
                'cancelled',
                'refunded',
            ])->default('pending');

            $table->json('metadata')->nullable(); // shipping address, member_id if placed by a member, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
