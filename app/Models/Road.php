<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Road extends Model
{
    protected $fillable = [
        'name',
        'image_path',
        'description',
    ];

    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class);
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image_path
            ? Storage::disk('public')->url($this->image_path)
            : 'https://via.placeholder.com/1200x800?text=Road+Image';
    }
}
