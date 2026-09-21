<?php

namespace App\Features\NoteType\Actions;

use App\Features\NoteType\Models\NoteType;
use App\Features\NoteType\ValueObjects\NoteTypeViewDataValueObject;
use Illuminate\Support\Facades\Vite;

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
            ->map(fn (NoteType $noteType) => new NoteTypeViewDataValueObject(
                id: $noteType->id,
                name: $noteType->name,
                description: $noteType->description,
                illustrationPath: Vite::asset($noteType->illustration_path),
                illustrationAlt: $noteType->name.' illustration',
            ))
            ->all();
    }
}
