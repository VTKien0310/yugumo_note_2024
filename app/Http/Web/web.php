<?php

use App\Extendables\Core\Http\Route\RouteInvoker;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    RouteInvoker::invokeWebRoute('authentication');

    Route::middleware('auth:web')->group(function () {
        RouteInvoker::invokeWebRoute('note');
        RouteInvoker::invokeWebRoute('profile');
    });
});
