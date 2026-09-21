<?php

namespace App\Features\Note\Commands;

use App\Features\Note\Models\ChecklistNoteContent;

class UpdateChecklistNoteContentCommand
{
    public function handle(ChecklistNoteContent $checklistNoteContent, array $data): ChecklistNoteContent
    {
        $checklistNoteContent->update($data);

        return $checklistNoteContent->refresh();
    }
}
