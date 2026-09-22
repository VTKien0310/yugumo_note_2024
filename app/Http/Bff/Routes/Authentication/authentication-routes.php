<?php

use App\Http\Bff\Routes\Authentication\AuthenticationController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')
    ->as('auth.')
    ->controller(AuthenticationController::class)
    ->group(function () {
        Route::post('login', 'login')->middleware('throttle:5,1')->name('login');

        Route::middleware('auth:web')->group(function () {
            Route::get('me', 'me')->name('me');
            Route::delete('logout', 'logout')->name('logout');
        });
    });
