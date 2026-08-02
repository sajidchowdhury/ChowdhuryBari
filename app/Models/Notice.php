<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $fillable = [
        'type',
        'headline',
        'description',
        'published_at',
        'active_till_date',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'published_at'     => 'datetime',
            'active_till_date' => 'date',
            'is_active'        => 'boolean',
            'sort_order'       => 'integer',
        ];
    }

    /**
     * Scope: only notices that are active AND not past their expiry date.
     * Used by the public site to show current notices only.
     */
    public function scopeCurrentlyActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('active_till_date')
                  ->orWhere('active_till_date', '>=', now()->toDateString());
            });
    }

    /**
     * Scope: order by most recently published first.
     */
    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('published_at');
    }

    /**
     * Check if this notice has expired.
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->active_till_date && $this->active_till_date->isPast();
    }
}
