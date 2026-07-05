<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Payment — every payment flowing into the platform.
 *
 * Polymorphic: a payment can pay for an Order, a Subscription,
 * or a tenant's monthly SaaS subscription.
 *
 * The `gateway` column records which processor was used:
 *  - sslcommerz (aggregator — handles bKash, Nagad, cards, banking)
 *  - bkash      (direct bKash integration — lower fees)
 *  - nagad      (direct Nagad integration — lower fees)
 *  - manual     (super admin records a cash/bank transfer manually)
 */
class Payment extends Model
{
    protected $fillable = [
        'tenant_id',
        'payable_type',
        'payable_id',
        'amount',
        'currency',
        'gateway',
        'gateway_txn_id',
        'gateway_payment_id',
        'gateway_trx_ref',
        'status',
        'gateway_response',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'gateway_response' => 'array',
        ];
    }

    public function tenant(): BelongsTo
    {
        // Uses Stancl's Tenant model
        return $this->belongsTo(\Stancl\Tenancy\Database\Models\Tenant::class, 'tenant_id');
    }

    /**
     * The thing this payment pays for — Order, Subscription, DuesPayment, etc.
     */
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }
}
