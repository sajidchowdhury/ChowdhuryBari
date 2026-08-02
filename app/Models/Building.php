<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Building extends Model
{
    protected $fillable = [
        'road_id',
        'name',
        'owner_name',
        'owner_phone',
        'caretaker_name',
        'caretaker_phone',
        'structure_type',
        'usage_type',
        'building_category',
        'per_family_amount',
        'billing_family_count',
        'floor_count',
        'families_per_floor',
        'has_security',
        'has_cleaning',
        'google_lt',
        'google_ln',
        'extra_information',
        'image_path',
    ];

    /**
     * The 4 building categories used for service-charge grouping.
     */
    public const CATEGORIES = [
        'tin_shed'              => 'টিন শেড',
        'below_or_equal_4_floor' => '৪তলা বা নিচে',
        'above_4_floor'          => '৪তলার উপরে',
        'shop'                   => 'দোকান',
    ];

    /**
     * Bengali label for this building's category.
     */
    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->building_category] ?? '—';
    }

    /**
     * The effective family count used for billing.
     * If billing_family_count is set (admin-controlled), use it.
     * Otherwise fall back to the auto-calculated active flat count.
     */
    public function getEffectiveBillingFamilyCountAttribute(): int
    {
        if ($this->billing_family_count !== null) {
            return $this->billing_family_count;
        }
        return $this->getActiveFamilyCount();
    }

    /**
     * The expected (total possible) family count = floor_count × families_per_floor.
     */
    public function getExpectedFamilyCountAttribute(): int
    {
        return $this->floor_count * $this->families_per_floor;
    }

    /**
     * Monthly due = per_family_amount × effective_billing_family_count
     *               + sum of service charges (respecting charge_type:
     *                 per_family × family_count, per_floor × floor_count, fixed = amount).
     */
    public function monthlyDue(): int
    {
        $familyCount = $this->effective_billing_family_count;
        $familyTotal = $this->per_family_amount * $familyCount;

        $charges = \App\Models\ServiceCharge::calculateForBuilding(
            $this->building_category ?? '',
            $familyCount,
            $this->floor_count,
        );

        return $familyTotal + $charges['total'];
    }

    /**
     * Detailed charge breakdown for this building (for the dashboard display).
     * Returns ['base_family_charge' => int, 'charges' => Collection, 'total' => int]
     */
    public function chargeBreakdown(): array
    {
        $familyCount = $this->effective_billing_family_count;
        $baseFamilyCharge = $this->per_family_amount * $familyCount;

        $charges = \App\Models\ServiceCharge::calculateForBuilding(
            $this->building_category ?? '',
            $familyCount,
            $this->floor_count,
        );

        return [
            'base_per_family_amount' => $this->per_family_amount,
            'family_count'           => $familyCount,
            'base_family_charge'     => $baseFamilyCharge,
            'charges'                => $charges['breakdown'],
            'charges_total'          => $charges['total'],
            'total'                  => $baseFamilyCharge + $charges['total'],
        ];
    }

    protected function casts(): array
    {
        return [
            'has_security'        => 'boolean',
            'has_cleaning'        => 'boolean',
            'floor_count'         => 'integer',
            'families_per_floor'  => 'integer',
        ];
    }

    public function road(): BelongsTo
    {
        return $this->belongsTo(Road::class);
    }

    public function flats(): HasMany
    {
        return $this->hasMany(Flat::class)->orderBy('floor_number')->orderBy('flat_number');
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class)->orderBy('billing_month', 'desc');
    }

    public function familyCountHistories(): HasMany
    {
        return $this->hasMany(FamilyCountHistory::class)->orderBy('created_at', 'desc');
    }

    public function getImageUrlAttribute(): string
    {
        if (!$this->image_path) {
            return 'https://via.placeholder.com/800x600?text=Building+Image';
        }

        // New-style path: stored on the public disk (relative to storage/app/public/).
        // This covers both 'buildings/xxx.jpg' (uploaded via BuildingController)
        // and 'field-data/xxx.jpg' (uploaded via FieldDataController, then migrated).
        if (Storage::disk('public')->exists($this->image_path)) {
            return Storage::disk('public')->url($this->image_path);
        }

        // Legacy-style path: file lives in public/<path> (e.g. public/uploads/field-data/xxx.jpg).
        // This happens for buildings created by migrating old field-data records
        // whose images were stored via File::move to public_path('uploads/field-data').
        if (file_exists(public_path($this->image_path))) {
            return asset($this->image_path);
        }

        // Path doesn't resolve to a real file — return the public-disk URL anyway
        // so the browser gets a deterministic URL (better than a broken /storage/...
        // link that masks the underlying issue).
        return Storage::disk('public')->url($this->image_path);
    }

    /**
     * Flats grouped by floor number — used by the per-floor meter UI.
     * Returns [1 => [Flat, Flat], 2 => [Flat, Flat], ...]
     */
    public function getFlatsByFloorAttribute(): array
    {
        $grouped = [];
        foreach ($this->flats as $flat) {
            $floor = $flat->floor_number ?? 0;
            $grouped[$floor][] = $flat;
        }
        ksort($grouped);
        return $grouped;
    }

    public function getTotalFlatsAttribute(): int
    {
        return $this->flats()->count();
    }

    public function getActiveFlatsAttribute(): int
    {
        return $this->flats()->where('is_active', true)->count();
    }

    /**
     * Auto-generate flats based on floor_count × families_per_floor.
     * Names: "Floor N - Flat X" where X = A, B, C, ...
     * Idempotent — skips existing flat numbers.
     */
    public function generateFlats(): int
    {
        $created = 0;
        $letters = range('A', 'Z');

        for ($floor = 1; $floor <= $this->floor_count; $floor++) {
            for ($i = 0; $i < $this->families_per_floor; $i++) {
                $letter = $letters[$i] ?? ($i + 1);
                $flatNumber = "Floor {$floor} - Flat {$letter}";

                $exists = Flat::where('building_id', $this->id)
                    ->where('flat_number', $flatNumber)
                    ->exists();

                if (!$exists) {
                    Flat::create([
                        'building_id'  => $this->id,
                        'flat_number'  => $flatNumber,
                        'floor_number' => $floor,
                        'is_active'    => true,
                    ]);
                    $created++;
                }
            }
        }

        return $created;
    }

    /**
     * Active family count based on meter recharge history (45-day threshold).
     * Flats without meters are assumed active (can't prove vacated).
     */
    public function getActiveFamilyCount(int $days = 45): int
    {
        return $this->flats()
            ->where('is_active', true)
            ->where(function ($query) use ($days) {
                $query->whereHas('meters', function ($meterQuery) use ($days) {
                    $meterQuery->where('last_recharge_at', '>=', now()->subDays($days));
                })
                ->orWhereDoesntHave('meters');
            })
            ->count();
    }

    public function getServicesLabelAttribute(): string
    {
        $services = array_filter([
            $this->has_security ? 'Security Guard' : null,
            $this->has_cleaning ? 'Cleaning' : null,
        ]);
        return $services ? implode(' + ', $services) : 'No services';
    }
}
