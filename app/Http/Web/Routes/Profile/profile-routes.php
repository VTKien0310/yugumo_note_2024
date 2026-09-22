<?php

use App\Http\Web\Routes\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

Route::as('profile.')
    ->prefix('profile')
    ->controller(ProfileController::class)
    ->group(function () {
        Route::get('/', 'show')->name('show');
    });
