<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Central DB: payment records for all money flowing into the platform.
 *
 * One payment can be for:
 * - A product order (payable_type = Order, payable_id = order.id)
 * - A society's monthly SaaS subscription (payable_type = Subscription)
 * - A member's monthly dues (payable_type = DuesPayment, payable_id stored in tenant DB)
 *
 * The `gateway` column records which payment processor was used.
 * `gateway_txn_id` is the gateway's transaction ID (from bKash/Nagad/SSL).
 *
 * Webhook/IPN handlers update this table when a payment status changes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            // UUID FK to tenants.id — nullable (null for platform-level payments).
            // FK constraint only added if the tenants table exists (see orders migration).
            $table->uuid('tenant_id')->nullable();
            if (Schema::hasTable('tenants')) {
                $table->foreign('tenant_id')
                    ->references('id')
                    ->on('tenants')
                    ->nullOnDelete();
            }

            $table->morphs('payable'); // payable_type + payable_id (Order, Subscription, DuesPayment)

            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('BDT');

            $table->enum('gateway', ['sslcommerz', 'bkash', 'nagad', 'manual']);
            $table->string('gateway_txn_id')->nullable()->unique();
            $table->string('gateway_payment_id')->nullable(); // e.g. bKash payment ID
            $table->string('gateway_trx_ref')->nullable();    // e.g. SSL Commerz bank_txn_id

            $table->enum('status', [
                'initiated',    // payment URL generated, user redirected
                'pending',      // user completed gateway side, awaiting webhook/IPN
                'successful',
                'failed',
                'cancelled',
                'refunded',
            ])->default('initiated');

            $table->json('gateway_response')->nullable(); // full webhook payload for audit
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['gateway', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
