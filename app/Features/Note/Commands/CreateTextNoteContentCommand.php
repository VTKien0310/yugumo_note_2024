<?php

namespace App\Features\Note\Commands;

use App\Features\Note\Models\TextNoteContent;

class CreateTextNoteContentCommand
{
    public function handle(array $data): TextNoteContent
    {
        return TextNoteContent::create($data);
    }
}
