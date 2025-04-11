<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Commands\DeleteChecklistNoteContentCommand;
use App\Features\Note\Models\ChecklistNoteContent;
use App\Features\Search\Actions\DeleteSearchIndexOfModelAction;
use Illuminate\Support\Facades\DB;

readonly class DeleteChecklistNoteContentAction
{
    public function __construct(
        private DeleteChecklistNoteContentCommand $deleteChecklistNoteContentCommand,
        private DeleteSearchIndexOfModelAction $deleteSearchIndexOfModelAction
    ) {}

    public function handle(ChecklistNoteContent $checklistNoteContent): bool
    {
        return DB::transaction(function () use ($checklistNoteContent): bool {
            $this->deleteSearchIndexOfModelAction->handle($checklistNoteContent);

            return (bool) $this->deleteChecklistNoteContentCommand->handle($checklistNoteContent);
        });
    }
}
