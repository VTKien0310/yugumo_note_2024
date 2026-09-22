<?php

use App\Http\Web\Routes\Profile\WebProfileController;
use Illuminate\Support\Facades\Route;

Route::as('profile.')
    ->prefix('profile')
    ->controller(WebProfileController::class)
    ->group(function () {
        Route::get('/', 'show')->name('show');
    });
