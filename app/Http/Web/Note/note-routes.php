<?php

use App\Http\Web\Note\NoteController;
use Illuminate\Support\Facades\Route;

Route::as('notes.')
    ->controller(NoteController::class)
    ->group(function () {
        Route::get('/', 'home')->name('home');

        Route::prefix('notes')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::get('/{note}', 'show')->name('show');
            Route::put('/{note}/remove-bookmark', 'removeBookmark')->name('remove-bookmark');
        });
    });

Route::post('/note-types/{noteType}/notes', [NoteController::class, 'store'])->name('note-types.notes.store');
