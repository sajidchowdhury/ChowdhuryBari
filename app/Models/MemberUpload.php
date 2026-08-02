<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\File;

class MemberUpload extends Model
{
    protected $fillable = [
        'user_id',
        'image_path',
        'caption',
        'month_key',
        'star_rating',
        'rated_at',
        'rated_by',
    ];

    protected function casts(): array
    {
        return [
            'star_rating' => 'integer',
            'rated_at'    => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rated_by');
    }

    /**
     * Current month key, e.g. "2026-01".
     */
    public static function currentMonthKey(): string
    {
        return now()->format('Y-m');
    }

    /**
     * Previous month key, e.g. "2025-12".
     */
    public static function previousMonthKey(): string
    {
        return now()->subMonth()->format('Y-m');
    }

    public function scopeForMonth(Builder $query, string $monthKey): Builder
    {
        return $query->where('month_key', $monthKey);
    }

    public function scopeRated(Builder $query): Builder
    {
        return $query->whereNotNull('star_rating');
    }

    public function scopeUnrated(Builder $query): Builder
    {
        return $query->whereNull('star_rating');
    }

    /**
     * Public web URL for the stored image.
     */
    public function getImageUrlAttribute(): string
    {
        return asset($this->image_path);
    }

    /**
     * Whether this image has been rated by the admin.
     */
    public function getIsRatedAttribute(): bool
    {
        return $this->star_rating !== null;
    }

    // ============ SOCIAL VALUE CALCULATIONS ============

    /**
     * A member's social value for a given month = avg(rated image stars) × 10.
     * Returns null if the member has no rated images that month.
     *
     * Example: 4 images rated 8,7,9,10 → avg=8.5 → social value=85 (out of 100)
     */
    public static function socialValueFor(int $userId, string $monthKey): ?float
    {
        $avg = self::where('user_id', $userId)
            ->where('month_key', $monthKey)
            ->rated()
            ->avg('star_rating');

        return $avg !== null ? round($avg * 10) : null;
    }

    /**
     * The image with the highest star rating for a user in a month
     * (used for the top-10 website display).
     */
    public static function bestImageFor(int $userId, string $monthKey): ?self
    {
        return self::where('user_id', $userId)
            ->where('month_key', $monthKey)
            ->rated()
            ->orderByDesc('star_rating')
            ->orderBy('created_at')
            ->first();
    }
}
