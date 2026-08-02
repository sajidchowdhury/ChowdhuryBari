<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeterReading extends Model
{
    protected $fillable = [
        'meter_id',
        'reading_date',
        'recharge_amount',
        'recharged_at',
        'source',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'reading_date'    => 'date',
            'recharge_amount' => 'decimal:2',
            'recharged_at'    => 'datetime',
        ];
    }

    public function meter(): BelongsTo
    {
        return $this->belongsTo(Meter::class);
    }
}
