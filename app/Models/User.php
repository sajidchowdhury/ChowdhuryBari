<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'role',           // ← যোগ করো
        'is_active',      // ← যোগ করো
        'permissions'     // ← যোগ করো
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'permissions' => 'array',   // JSON কে অ্যারে হিসেবে ব্যবহার করবে
    ];

    // Helper method (সুবিধার জন্য)
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * The member's building, matched by phone = owner_phone.
     * Used for the top-10 ranking display (building name + owner name).
     * Returns Building or null if no building has this phone.
     */
    public function getBuildingAttribute(): ?Building
    {
        return Building::where('owner_phone', $this->phone)->first();
    }

    /**
     * The member's monthly yard-photo uploads.
     */
    public function uploads(): HasMany
    {
        return $this->hasMany(MemberUpload::class)->latest();
    }
}