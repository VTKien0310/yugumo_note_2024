<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Commands\UpdateChecklistNoteContentCommand;
use App\Features\Note\Models\ChecklistNoteContent;

readonly class UpdateChecklistNoteContentAction
{
    public function __construct(private UpdateChecklistNoteContentCommand $updateChecklistNoteContentByIdCommand) {}

    public function handle(ChecklistNoteContent $checklistNoteContent, array $data): ChecklistNoteContent
    {
        return $this->updateChecklistNoteContentByIdCommand->handle($checklistNoteContent, $data);
    }
}
