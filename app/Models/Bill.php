<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bill extends Model
{
    protected $fillable = [
        'building_id',
        'billing_month',
        'field_data_family_count',
        'actual_family_count',
        'cleaning_rate_per_family',
        'cleaning_total',
        'security_guard_bill',
        'total_bill',
        'status',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'field_data_family_count'  => 'integer',
            'actual_family_count'      => 'integer',
            'cleaning_rate_per_family' => 'integer',
            'cleaning_total'           => 'integer',
            'security_guard_bill'      => 'integer',
            'total_bill'               => 'integer',
        ];
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function familyCountHistories(): HasMany
    {
        return $this->hasMany(FamilyCountHistory::class);
    }

    /**
     * Get the billing month label in Bengali format.
     * e.g. "2026-08" → "আগস্ট ২০২৬"
     */
    public function getBillingMonthLabelAttribute(): string
    {
        $months = [
            '01' => 'জানুয়ারি', '02' => 'ফেব্রুয়ারি', '03' => 'মার্চ',
            '04' => 'এপ্রিল', '05' => 'মে', '06' => 'জুন',
            '07' => 'জুলাই', '08' => 'আগস্ট', '09' => 'সেপ্টেম্বর',
            '10' => 'অক্টোবর', '11' => 'নভেম্বর', '12' => 'ডিসেম্বর',
        ];

        $parts = explode('-', $this->billing_month);
        $year = $parts[0] ?? '';
        $month = $parts[1] ?? '';

        return ($months[$month] ?? $month) . ' ' . $this->toBengaliDigits($year);
    }

    /**
     * Get the status label in Bengali.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'বকেয়া',
            'paid'    => 'পরিশোধিত',
            'partial' => 'আংশিক পরিশোধিত',
            default   => $this->status,
        };
    }

    /**
     * Get the status badge CSS classes.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-rose-50 text-rose-600',
            'paid'    => 'bg-emerald-50 text-emerald-700',
            'partial' => 'bg-amber-50 text-amber-700',
            default   => 'bg-slate-50 text-slate-600',
        };
    }

    /**
     * Calculate bill totals from actual_family_count and rates.
     */
    public function recalculate(): self
    {
        $this->cleaning_total = $this->cleaning_rate_per_family * $this->actual_family_count;
        $this->total_bill = $this->cleaning_total + $this->security_guard_bill;
        return $this;
    }

    /**
     * Get the current billing month key (e.g. "2026-08").
     */
    public static function currentMonthKey(): string
    {
        return now()->format('Y-m');
    }

    /**
     * Get the previous billing month key.
     */
    public static function previousMonthKey(): string
    {
        return now()->subMonth()->format('Y-m');
    }

    /**
     * Convert English digits to Bengali digits.
     */
    private function toBengaliDigits(string $text): string
    {
        $en = range(0, 9);
        $bn = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        return str_replace($en, $bn, $text);
    }
}
