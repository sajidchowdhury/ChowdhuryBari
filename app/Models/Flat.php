<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Flat extends Model
{
    protected $fillable = [
        'building_id',
        'flat_number',
        'floor_number',
        'is_active',
        'vacated_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'floor_number' => 'integer',
            'vacated_at' => 'date',
        ];
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function meters(): HasMany
    {
        return $this->hasMany(Meter::class);
    }

    /**
     * Convenience: the flat's primary meter (first one).
     */
    public function getPrimaryMeterAttribute(): ?Meter
    {
        return $this->meters()->first();
    }

    /**
     * Is this flat's family considered active based on meter recharge?
     * (45-day threshold — matches Building::getActiveFamilyCount)
     */
    public function isFamilyActive(int $days = 45): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $meters = $this->meters;
        if ($meters->isEmpty()) {
            return true; // no meter → assume active
        }

        return $meters->contains(function ($meter) use ($days) {
            return $meter->last_recharge_at && $meter->last_recharge_at->gte(now()->subDays($days));
        });
    }

    /**
     * Status badge for UI: 'active' | 'vacated' | 'inactive_meter'
     */
    public function getStatusBadgeAttribute(): string
    {
        if (!$this->is_active) {
            return 'vacated';
        }
        return $this->isFamilyActive() ? 'active' : 'inactive_meter';
    }
}
