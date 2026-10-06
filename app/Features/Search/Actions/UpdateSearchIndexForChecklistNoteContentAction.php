<?php

namespace App\Features\Search\Actions;

use App\Features\Note\Models\ChecklistNoteContent;
use App\Features\Search\Commands\UpdateSearchIndexCommand;
use App\Features\Search\Models\SearchIndex;

readonly class UpdateSearchIndexForChecklistNoteContentAction
{
    public function __construct(
        private UpdateSearchIndexCommand $updateSearchIndexCommand
    ) {}

    public function handle(ChecklistNoteContent $checklistNoteContent): SearchIndex
    {
        return $this->updateSearchIndexCommand->handle($checklistNoteContent->searchIndex, [
            SearchIndex::CONTENT => $checklistNoteContent->content,
        ]);
    }
}
