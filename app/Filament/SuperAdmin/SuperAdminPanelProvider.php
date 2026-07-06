<?php

namespace App\Filament\SuperAdmin;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Models\Central\SuperAdmin;

/**
 * Panel #4 — Super Admin (the platform owner's control room).
 *
 * This panel lives on the CENTRAL domain (e.g. app.com/super-admin),
 * NOT on tenant subdomains. It uses the SuperAdmin model from the
 * central DB and gives the platform owner visibility into all tenants,
 * products, orders, and payments.
 *
 * Login URL: /super-admin/login
 * Dashboard: /super-admin
 *
 * NOTE: This file may be replaced/renamed by `php artisan filament:install --panels`
 * if that command runs. After running it, copy the auth model + path settings from
 * this file into the generated panel provider.
 */
class SuperAdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('super-admin')
            ->path('super-admin')
            ->login()
            ->authGuard('super_admin')
            ->colors([
                'primary' => Color::Emerald,
                'danger'  => Color::Rose,
                'gray'    => Color::Slate,
                'success' => Color::Green,
                'warning' => Color::Amber,
            ])
            ->discoverResources(
                in: app_path('Filament/SuperAdmin/Resources'),
                for: 'App\\Filament\\SuperAdmin\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/SuperAdmin/Pages'),
                for: 'App\\Filament\\SuperAdmin\\Pages'
            )
            ->discoverWidgets(
                in: app_path('Filament/SuperAdmin/Widgets'),
                for: 'App\\Filament\\SuperAdmin\\Widgets'
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
