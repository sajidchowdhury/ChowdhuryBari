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
        'has_security',
        'has_cleaning',
        'total_floor',
        'google_lt',
        'google_ln',
        'extra_information',
        'image_path',
    ];

    protected function casts(): array
    {
        return [
            'has_security' => 'boolean',
            'has_cleaning' => 'boolean',
            'total_floor'  => 'integer',
        ];
    }

    public function road(): BelongsTo
    {
        return $this->belongsTo(Road::class);
    }

    public function flats(): HasMany
    {
        return $this->hasMany(Flat::class);
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image_path
            ? Storage::disk('public')->url($this->image_path)
            : 'https://via.placeholder.com/800x600?text=Building+Image';
    }

    /**
     * Total flats in this building (active + vacated).
     */
    public function getTotalFlatsAttribute(): int
    {
        return $this->flats()->count();
    }

    /**
     * Number of flats currently marked active (manual override).
     * This is NOT the same as "families who paid this month" —
     * see getActiveFamilyCount() for the meter-based calculation.
     */
    public function getActiveFlatsAttribute(): int
    {
        return $this->flats()->where('is_active', true)->count();
    }

    /**
     * Active family count based on meter recharge history.
     *
     * A flat is considered "active" (family is living there) if:
     * 1. is_active = true (not manually marked vacated), AND
     * 2. The flat's meter was recharged in the last 45 days
     *    (BPDB prepaid meters need monthly recharge)
     *
     * Flats without meters are assumed active (we can't prove vacated).
     *
     * @param int $days Threshold in days (default 45)
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

    /**
     * Service label — human-readable list of services this building gets.
     */
    public function getServicesLabelAttribute(): string
    {
        $services = array_filter([
            $this->has_security ? 'Security Guard' : null,
            $this->has_cleaning ? 'Cleaning' : null,
        ]);
        return $services ? implode(' + ', $services) : 'No services';
    }
}
