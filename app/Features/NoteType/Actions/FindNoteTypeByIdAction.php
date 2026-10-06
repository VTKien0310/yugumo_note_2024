<?php

namespace App\Features\NoteType\Actions;

use App\Features\NoteType\Models\NoteType;

class FindNoteTypeByIdAction
{
    public function handle(int|string $id): ?NoteType
    {
        return NoteType::find($id);
    }
}
