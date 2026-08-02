<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyCountHistory extends Model
{
    protected $fillable = [
        'building_id',
        'bill_id',
        'previous_count',
        'new_count',
        'changed_by',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'previous_count' => 'integer',
            'new_count'      => 'integer',
        ];
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function changer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Get a description of the change.
     */
    public function getDescriptionAttribute(): string
    {
        $reason = $this->reason ? " ({$this->reason})" : '';
        return "{$this->previous_count} → {$this->new_count} পরিবার{$reason}";
    }
}
