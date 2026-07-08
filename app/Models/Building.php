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

    public function getImageUrlAttribute(): string
    {
        return $this->image_path
            ? Storage::disk('public')->url($this->image_path)
            : 'https://via.placeholder.com/800x600?text=Building+Image';
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
