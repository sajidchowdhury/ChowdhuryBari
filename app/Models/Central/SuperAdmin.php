<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

/**
 * Super Admin — the platform owner account.
 *
 * Lives in the CENTRAL database (NOT tenant DBs).
 * Logs in at the central domain (e.g. app.com/super-admin) to manage
 * all tenants, products, orders, and payments.
 */
class SuperAdmin extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'avatar_url',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Simplified: just check is_active. The panel ID check was causing
     * redirect loops in some Filament v3 setups.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active;
    }
}
