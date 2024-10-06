<?php

use App\Extendables\Core\Http\Route\RouteInvoker;

RouteInvoker::invokeWebRoute('authentication');

Route::middleware('auth:web')->group(function () {
    RouteInvoker::invokeWebRoute('note');
});
