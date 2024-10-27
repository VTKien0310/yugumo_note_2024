<?php

namespace App\Features\Note\Actions;

use App\Extendables\Core\Utils\BoolIntValueEnum;
use App\Features\Note\Commands\CreateChecklistNoteContentCommand;
use App\Features\Note\Models\ChecklistNoteContent;
use App\Features\Note\Models\Note;

readonly class CreateEmptyChecklistNoteContentAction
{
    public function __construct(
        private CreateChecklistNoteContentCommand $createChecklistNoteContentCommand
    ) {}

    public function handle(Note $note): ChecklistNoteContent
    {
        return $this->createChecklistNoteContentCommand->handle([
            ChecklistNoteContent::NOTE_ID => $note->id,
            ChecklistNoteContent::CONTENT => 'Untitled',
            ChecklistNoteContent::IS_COMPLETED => BoolIntValueEnum::FALSE,
        ]);
    }
}
