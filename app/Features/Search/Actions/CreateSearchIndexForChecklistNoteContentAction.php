<?php

namespace App\Features\Search\Actions;

use App\Features\Note\Models\ChecklistNoteContent;
use App\Features\Search\Commands\CreateSearchIndexCommand;
use App\Features\Search\Models\SearchIndex;

readonly class CreateSearchIndexForChecklistNoteContentAction
{
    public function __construct(private CreateSearchIndexCommand $createSearchIndexCommand) {}

    public function handle(ChecklistNoteContent $checklistNoteContent): SearchIndex
    {
        return $this->createSearchIndexCommand->handle($checklistNoteContent, [
            SearchIndex::NOTE_ID => $checklistNoteContent->note_id,
            SearchIndex::CONTENT => $checklistNoteContent->content,
        ]);
    }
}
