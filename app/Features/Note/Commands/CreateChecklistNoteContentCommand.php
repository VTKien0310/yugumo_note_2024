<?php

namespace App\Features\Note\Commands;

use App\Features\Note\Models\ChecklistNoteContent;

class CreateChecklistNoteContentCommand
{
    public function handle(array $data): ChecklistNoteContent
    {
        return ChecklistNoteContent::create($data);
    }
}
