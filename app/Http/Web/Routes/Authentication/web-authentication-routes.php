<?php

use App\Http\Web\Routes\Authentication\WebAuthenticationController;

Route::prefix('auth')
    ->as('auth.')
    ->controller(WebAuthenticationController::class)
    ->group(function () {
        Route::get('login', 'loginPage')->name('login');

        Route::middleware('auth:web')->group(function () {
            Route::delete('logout', 'logout')->name('logout');
        });
    });
