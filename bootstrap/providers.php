<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\PimpinanPanelProvider;
use App\Providers\Filament\PortalPanelProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    PimpinanPanelProvider::class,
    PortalPanelProvider::class,
];
