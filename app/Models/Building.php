<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Building extends Model
{
    protected $fillable = [
        'road_id',
        'name',
        'owner',
        'total_floor',
        'total_family',
        'building_type',
        'owner_number',
        'google_ln',
        'google_lt',
        'extra_information',
        'service_taking',
        'image_path',
    ];

    protected $casts = [
        'service_taking' => 'array',
    ];

    public function road(): BelongsTo
    {
        return $this->belongsTo(Road::class);
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image_path
            ? Storage::disk('public')->url($this->image_path)
            : 'https://via.placeholder.com/800x600?text=Building+Image';
    }
}
