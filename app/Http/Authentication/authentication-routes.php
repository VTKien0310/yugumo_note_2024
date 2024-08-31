<?php

use App\Http\Authentication\AuthenticationController;

Route::prefix('auth')
    ->as('auth.')
    ->controller(AuthenticationController::class)
    ->group(function () {
        Route::get('login', 'loginPage')->name('login');
    });
