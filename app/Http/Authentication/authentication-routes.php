<?php

use App\Views\Livewire\Authentication\Pages\LoginPage;

Route::prefix('auth')
    ->as('auth.')
    ->group(function () {
        Route::get('login', LoginPage::class)->name('login');
    });
