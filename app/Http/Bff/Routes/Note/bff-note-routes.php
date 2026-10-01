<?php

use App\Http\Bff\Routes\Note\BffNoteController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:web')->group(function () {
    Route::put('notes/{note}', [BffNoteController::class, 'update'])->name('notes.update');
});
