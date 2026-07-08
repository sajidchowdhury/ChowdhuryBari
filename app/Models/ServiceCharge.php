<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ServiceCharge extends Model
{
    protected $fillable = [
        'name',
        'amount',
        'description',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'amount'     => 'integer',
            'is_active'  => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Scope: only active charges, ordered by sort_order then name.
     */
    public function scopeActiveOrdered(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    /**
     * Sum of all active charges (the total monthly due).
     */
    public static function totalActive(): int
    {
        return (int) self::where('is_active', true)->sum('amount');
    }
}
