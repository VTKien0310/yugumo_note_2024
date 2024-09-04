<?php

use App\Http\Note\NoteController;

Route::as('notes.')
    ->controller(NoteController::class)
    ->group(function () {
        Route::get('/', 'recent')->name('recent');

        Route::prefix('notes')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
        });
    });
