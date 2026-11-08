<?php

use App\Features\NoteType\Actions\GetAllNoteTypeAction;
use Database\Seeders\NoteTypeSeeder;

describe(GetAllNoteTypeAction::class, function () {
    it('retrieves all seeded note types', function () {
        $this->seed(NoteTypeSeeder::class);

        $noteTypes = app(GetAllNoteTypeAction::class)->handle();

        expect($noteTypes)->not->toBeEmpty()
            ->and($noteTypes->count())->toBeGreaterThanOrEqual(4);
    });
});
