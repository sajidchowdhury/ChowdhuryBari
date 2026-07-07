<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meter extends Model
{
    protected $fillable = [
        'flat_id',
        'meter_number',
        'provider',
        'is_active',
        'last_recharge_amount',
        'last_recharge_at',
        'last_checked_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active'            => 'boolean',
            'last_recharge_amount' => 'decimal:2',
            'last_recharge_at'     => 'datetime',
            'last_checked_at'      => 'datetime',
        ];
    }

    public function flat(): BelongsTo
    {
        return $this->belongsTo(Flat::class);
    }

    public function readings(): HasMany
    {
        return $this->hasMany(MeterReading::class);
    }

    /**
     * Record a monthly reading (manual entry or API sync).
     * Updates the denormalized last_recharge_* fields on this meter.
     */
    public function recordReading(float $amount, $rechargedAt = null, string $source = 'manual', ?string $notes = null): MeterReading
    {
        $readingDate = $rechargedAt ? \Carbon\Carbon::parse($rechargedAt)->startOfMonth() : now()->startOfMonth();

        $reading = MeterReading::updateOrCreate(
            ['meter_id' => $this->id, 'reading_date' => $readingDate->toDateString()],
            [
                'recharge_amount' => $amount,
                'recharged_at'    => $rechargedAt ?? now(),
                'source'          => $source,
                'notes'           => $notes,
            ]
        );

        // Update denormalized fields on the meter
        $this->update([
            'last_recharge_amount' => $amount,
            'last_recharge_at'     => $rechargedAt ?? now(),
            'last_checked_at'      => now(),
        ]);

        return $reading;
    }
}
