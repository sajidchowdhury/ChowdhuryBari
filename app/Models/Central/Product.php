<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Product — platform-wide catalog of products/services/subscriptions
 * sold to tenant societies.
 *
 * Examples:
 *  - CCTV camera (physical)
 *  - Monthly security guard service (subscription)
 *  - Community branding kit (physical)
 *  - One-time setup fee (service)
 */
class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'price',
        'currency',
        'image_path',
        'is_active',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
