<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldDataCollection extends Model
{
    protected $table = 'field_data_collections';

    protected $fillable = [
        'road_id',
        'new_road_name',
        'building_name',
        'owner_name',
        'owner_phone',
        'caretaker_name',
        'caretaker_phone',
        'building_category',
        'structure_type',
        'usage_type',
        'floor_count',
        'families_per_floor',
        'has_security',
        'has_cleaning',
        'google_lt',
        'google_ln',
        'extra_information',
        'image_path',
        'flats_data',
        'status',
        'collected_by',
        'migrated_at',
        'migrated_building_id',
    ];

    protected function casts(): array
    {
        return [
            'flats_data'    => 'array',
            'has_security'  => 'boolean',
            'has_cleaning'  => 'boolean',
            'floor_count'   => 'integer',
            'families_per_floor' => 'integer',
            'migrated_at'   => 'datetime',
        ];
    }

    public function road(): BelongsTo
    {
        return $this->belongsTo(Road::class);
    }

    public function collector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    /**
     * The effective road name (existing road or new_road_name).
     */
    public function getRoadNameAttribute(): string
    {
        return $this->road?->name ?? $this->new_road_name ?? '—';
    }

    /**
     * Count of flats in the flats_data JSON.
     */
    public function getFlatCountAttribute(): int
    {
        return count($this->flats_data ?? []);
    }

    /**
     * Count of flats that have a meter number.
     */
    public function getMeterCountAttribute(): int
    {
        return collect($this->flats_data ?? [])
            ->filter(fn($f) => !empty($f['meter_number']))
            ->count();
    }

    /**
     * Public URL for the building photo.
     *
     * Uses the 'public' disk so the URL matches what Storage::disk('public')->url()
     * returns — this is critical because when this field-data is migrated to a
     * Building, the Building model's getImageUrlAttribute() also uses the
     * 'public' disk, and the image_path column is copied verbatim.
     *
     * Legacy paths (e.g. 'uploads/field-data/xxx.jpg' stored via File::move in
     * older versions) are handled as a fallback by serving them from public_path().
     */
    public function getImageUrlAttribute(): string
    {
        if (!$this->image_path) {
            return '';
        }

        // New-style path: stored on the public disk (relative to storage/app/public/)
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->image_path)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($this->image_path);
        }

        // Legacy-style path: file lives in public/<path> (e.g. public/uploads/field-data/xxx.jpg)
        if (file_exists(public_path($this->image_path))) {
            return asset($this->image_path);
        }

        // Path doesn't resolve to a real file — return the public-disk URL anyway
        // so the browser at least gets a deterministic URL (better than empty).
        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->image_path);
    }

    /**
     * The expected total families (floor_count × families_per_floor).
     */
    public function getExpectedFamiliesAttribute(): int
    {
        return $this->floor_count * $this->families_per_floor;
    }
}
