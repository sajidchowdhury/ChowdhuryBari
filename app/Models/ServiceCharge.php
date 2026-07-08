<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ServiceCharge extends Model
{
    protected $fillable = [
        'name',
        'building_category',
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
     * Scope: charges for a specific building category.
     */
    public function scopeForCategory(Builder $query, string $category): Builder
    {
        return $query->where('building_category', $category);
    }

    /**
     * Active charges for a specific building category, ordered.
     */
    public static function activeForCategory(string $category): \Illuminate\Support\Collection
    {
        return self::where('is_active', true)
            ->where('building_category', $category)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Total monthly due for a specific building category.
     */
    public static function totalForCategory(string $category): int
    {
        return (int) self::where('is_active', true)
            ->where('building_category', $category)
            ->sum('amount');
    }

    /**
     * Bengali label for this charge's building category.
     */
    public function getCategoryLabelAttribute(): string
    {
        return Building::CATEGORIES[$this->building_category] ?? '—';
    }
}
