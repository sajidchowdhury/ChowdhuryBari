<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ServiceCharge extends Model
{
    /** Charge types — how the amount is applied to the building. */
    public const CHARGE_TYPES = [
        'per_family' => 'প্রতি পরিবার',
        'per_floor'  => 'প্রতি ফ্লোর',
        'fixed'      => 'মোট (বিল্ডিং ওয়াইজ)',
    ];

    protected $fillable = [
        'name',
        'building_category',
        'amount',
        'charge_type',
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

    /**
     * Bengali label for this charge's type.
     */
    public function getChargeTypeLabelAttribute(): string
    {
        return self::CHARGE_TYPES[$this->charge_type] ?? 'মোট';
    }

    /**
     * Calculate the total charges for a building, respecting charge types.
     *   per_family: amount × billing_family_count
     *   per_floor:  amount × floor_count
     *   fixed:      amount
     *
     * @param string $category  The building's category
     * @param int $familyCount  The billing family count
     * @param int $floorCount   The building's floor count
     * @return array  ['total' => int, 'breakdown' => Collection]
     */
    public static function calculateForBuilding(string $category, int $familyCount, int $floorCount): array
    {
        $charges = self::where('is_active', true)
            ->where('building_category', $category)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $total = 0;
        $breakdown = $charges->map(function ($charge) use ($familyCount, $floorCount, &$total) {
            $multiplier = match($charge->charge_type) {
                'per_family' => $familyCount,
                'per_floor'  => $floorCount,
                default      => 1,
            };
            $lineTotal = $charge->amount * $multiplier;
            $total += $lineTotal;

            return (object) [
                'id'          => $charge->id,
                'name'        => $charge->name,
                'amount'      => $charge->amount,
                'charge_type' => $charge->charge_type,
                'type_label'  => $charge->charge_type_label,
                'multiplier'  => $multiplier,
                'line_total'  => $lineTotal,
            ];
        });

        return ['total' => $total, 'breakdown' => $breakdown];
    }
}
