<?php

use App\Providers\AppServiceProvider;
use App\Providers\AuthServiceProvider;

return [
    AppServiceProvider::class,
    AuthServiceProvider::class,
    // TenancyServiceProvider::class — DISABLED until stancl/tenancy is fully configured.
    // Enable after running `php artisan tenancy:install` and creating config/tenancy.php.
    // SuperAdminPanelProvider::class — DISABLED for now (Filament auth issues).
    // Will re-enable after we stabilize the public site + old admin panel.
    // The provider class still exists at app/Filament/SuperAdmin/ for reference.
];
