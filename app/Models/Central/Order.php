<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Stancl\Tenancy\Database\Models\Tenant as StanclTenant;

/**
 * Order — a tenant society's purchase of a product.
 *
 * Stored in the central DB so the super admin can see platform-wide
 * revenue without connecting to each tenant DB.
 */
class Order extends Model
{
    protected $fillable = [
        'tenant_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_amount',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'quantity' => 'integer',
            'metadata' => 'array',
        ];
    }

    /**
     * The tenant (society) that placed this order.
     * References the central `tenants` table maintained by stancl/tenancy.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(StanclTenant::class, 'tenant_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Polymorphic: a Payment may belong to an Order.
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }
}
