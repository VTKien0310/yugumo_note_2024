<?php

namespace App\Features\Note\Commands;

use App\Features\Note\Models\RichTextNoteContent;

class CreateRichTextNoteContentCommand
{
    public function handle(array $data): RichTextNoteContent
    {
        return RichTextNoteContent::create($data);
    }
}
