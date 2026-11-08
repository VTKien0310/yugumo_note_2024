<?php

use App\Extendables\Providers\ExtendableServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\TelescopeServiceProvider;

return [
    ExtendableServiceProvider::class,
    AppServiceProvider::class,
    TelescopeServiceProvider::class,
];
