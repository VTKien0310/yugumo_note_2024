<?php

namespace App\Features\Note\Commands;

use App\Features\Note\Models\ChecklistNoteContent;

class UpdateChecklistNoteContentByIdCommand
{
    public function handle(string $id, array $data): int
    {
        return ChecklistNoteContent::query()->where(ChecklistNoteContent::ID, $id)->update($data);
    }
}
