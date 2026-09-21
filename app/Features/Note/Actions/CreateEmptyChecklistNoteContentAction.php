<?php

namespace App\Features\Note\Actions;

use App\Extendables\Core\Utils\BoolIntValueEnum;
use App\Features\Note\Commands\CreateChecklistNoteContentCommand;
use App\Features\Note\Models\ChecklistNoteContent;
use App\Features\Note\Models\Note;
use App\Features\Search\Actions\CreateSearchIndexForChecklistNoteContentAction;
use Illuminate\Support\Facades\DB;

readonly class CreateEmptyChecklistNoteContentAction
{
    public function __construct(
        private CreateChecklistNoteContentCommand $createChecklistNoteContentCommand,
        private CreateSearchIndexForChecklistNoteContentAction $createSearchIndexForChecklistNoteContentAction
    ) {}

    public function handle(Note $note, bool $useTransaction = true): ChecklistNoteContent
    {
        $handling = function () use ($note): ChecklistNoteContent {
            $checklistNoteContent = $this->createChecklistNoteContentCommand->handle([
                ChecklistNoteContent::NOTE_ID => $note->id,
                ChecklistNoteContent::CONTENT => 'Untitled',
                ChecklistNoteContent::IS_COMPLETED => BoolIntValueEnum::FALSE,
            ]);

            $this->createSearchIndexForChecklistNoteContentAction->handle($checklistNoteContent);

            return $checklistNoteContent;
        };

        return $useTransaction ? DB::transaction($handling) : $handling();
    }
}
