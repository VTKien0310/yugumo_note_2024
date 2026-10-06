<?php

namespace App\Features\Note\Commands;

use App\Features\Note\Models\Note;

class CreateNoteCommand
{
    public function handle(array $data): Note
    {
        return Note::create($data);
    }
}
