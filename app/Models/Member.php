<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Member extends Model
{
    protected $fillable = [
        'name',
        'designation',
        'started_from',
        'phone',
        'image_path',
        'bio',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image_path
            ? Storage::disk('public')->url($this->image_path)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&size=400&background=065F46&color=fff';
    }
}
