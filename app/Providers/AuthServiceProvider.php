<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // 'Model' => 'Policy',
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Admin Access Gate
        Gate::define('view-admin', function ($user) {
            return in_array($user->role, ['admin', 'moderator']);
        });

        // Manage Users Gate
        Gate::define('manage-users', function ($user) {
            return $user->role === 'admin';
        });

        // Edit Content Gate
        Gate::define('edit-content', function ($user) {
            return in_array($user->role, ['admin', 'moderator']);
        });

        // View Reports Gate
        Gate::define('view-reports', function ($user) {
            return in_array($user->role, ['admin', 'moderator']);
        });

        // System Settings Gate
        Gate::define('system-settings', function ($user) {
            return $user->role === 'admin';
        });

        // Check Permissions (for JSON stored permissions)
        Gate::define('check-permission', function ($user, $permission) {
            if ($user->role === 'admin') {
                return true;
            }

            $permissions = json_decode($user->permissions, true) ?? [];
            return in_array($permission, $permissions);
        });
    }
}
