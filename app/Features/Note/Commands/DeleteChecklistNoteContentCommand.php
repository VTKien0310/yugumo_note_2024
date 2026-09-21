<?php

namespace App\Features\Note\Commands;

use App\Features\Note\Models\ChecklistNoteContent;

class DeleteChecklistNoteContentCommand
{
    public function handle(ChecklistNoteContent $checklistNoteContent): ?bool
    {
        return $checklistNoteContent->delete();
    }
}
