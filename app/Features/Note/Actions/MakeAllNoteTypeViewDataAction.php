<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Models\NoteType;
use App\Features\Note\ValueObjects\NoteTypeViewDataValueObject;

readonly class MakeAllNoteTypeViewDataAction
{
    public function __construct(private GetAllNoteTypeAction $getAllNoteTypeAction) {}

    /**
     * @return NoteTypeViewDataValueObject[]
     */
    public function handle(): array
    {
        return $this->getAllNoteTypeAction
            ->handle()
            ->map(fn (NoteType $noteType) => new NoteTypeViewDataValueObject($noteType))
            ->all();
    }
}
