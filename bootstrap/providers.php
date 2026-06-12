<?php

use App\Extendables\Providers\ExtendableServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\TelescopeServiceProvider;
use App\Providers\VoltServiceProvider;

return [
    ExtendableServiceProvider::class,
    AppServiceProvider::class,
    TelescopeServiceProvider::class,
    VoltServiceProvider::class,
];
