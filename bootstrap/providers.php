<?php

use App\Providers\AppServiceProvider;
use App\Providers\TenancyServiceProvider;

return [
    AppServiceProvider::class,
    TenancyServiceProvider::class,
    // SuperAdminPanelProvider::class — DISABLED for now (Filament auth issues).
    // Will re-enable after we stabilize the public site + old admin panel.
    // The provider class still exists at app/Filament/SuperAdmin/ for reference.
];
