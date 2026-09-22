<?php

use App\Extendables\Core\Http\Middleware\EnsureRequestWantJsonMiddleware;
use App\Extendables\Core\Http\Route\RouteInvoker;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'web',
    EnsureRequestWantJsonMiddleware::class,
    'throttle:60,1',
])
    ->prefix('bff')
    ->as('bff.')
    ->group(function () {
        RouteInvoker::invokeBffRoute('authentication');
    });
