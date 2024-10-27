<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Commands\DeleteChecklistNoteContentCommand;
use App\Features\Note\Models\ChecklistNoteContent;

readonly class DeleteChecklistNoteContentAction
{
    public function __construct(private DeleteChecklistNoteContentCommand $deleteChecklistNoteContentCommand) {}

    public function handle(ChecklistNoteContent $checklistNoteContent): bool
    {
        return (bool) $this->deleteChecklistNoteContentCommand->handle($checklistNoteContent);
    }
}
