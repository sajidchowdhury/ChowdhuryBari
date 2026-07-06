<?php

use App\Filament\SuperAdmin\SuperAdminPanelProvider;
use App\Providers\AppServiceProvider;
use App\Providers\TenancyServiceProvider;

return [
    AppServiceProvider::class,
    TenancyServiceProvider::class,
    SuperAdminPanelProvider::class,
];
