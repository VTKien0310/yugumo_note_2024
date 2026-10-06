<?php

use App\Http\Bff\Routes\Note\BffNoteChecklistController;
use App\Http\Bff\Routes\Note\BffNoteController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:web')->group(function () {
    Route::put('notes/{note}', [BffNoteController::class, 'update'])->name('notes.update');

    Route::post('notes/{note}/checklist-items', [BffNoteChecklistController::class, 'store'])
        ->name('notes.checklist-items.store');
    Route::put('notes/{note}/checklist-items/{checklistItem}', [BffNoteChecklistController::class, 'update'])
        ->name('notes.checklist-items.update');
    Route::delete('notes/{note}/checklist-items/{checklistItem}', [BffNoteChecklistController::class, 'destroy'])
        ->name('notes.checklist-items.destroy');
});
