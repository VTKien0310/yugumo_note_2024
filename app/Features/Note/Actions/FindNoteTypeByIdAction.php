<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Models\NoteType;

class FindNoteTypeByIdAction
{
    public function handle(int|string $id): ?NoteType
    {
        return NoteType::find($id);
    }
}
