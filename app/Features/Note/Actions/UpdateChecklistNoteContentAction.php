<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Commands\UpdateChecklistNoteContentCommand;
use App\Features\Note\Models\ChecklistNoteContent;
use App\Features\Search\Actions\UpdateSearchIndexForChecklistNoteContentAction;
use Illuminate\Support\Facades\DB;

readonly class UpdateChecklistNoteContentAction
{
    public function __construct(
        private UpdateChecklistNoteContentCommand $updateChecklistNoteContentByIdCommand,
        private UpdateSearchIndexForChecklistNoteContentAction $updateSearchIndexForChecklistNoteContentAction
    ) {}

    public function handle(ChecklistNoteContent $checklistNoteContent, array $data): ChecklistNoteContent
    {
        return DB::transaction(function () use ($checklistNoteContent, $data): ChecklistNoteContent {
            $checklistNoteContent = $this->updateChecklistNoteContentByIdCommand->handle($checklistNoteContent, $data);

            $this->updateSearchIndexForChecklistNoteContentAction->handle($checklistNoteContent);

            return $checklistNoteContent;
        });
    }
}
