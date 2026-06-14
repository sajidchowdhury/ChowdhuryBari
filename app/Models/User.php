<?php

namespace App\Models;

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
}