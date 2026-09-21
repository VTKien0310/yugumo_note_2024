<?php

namespace App\Features\Search\Actions;

use App\Features\Note\Models\Note;
use App\Features\Search\Commands\CreateSearchIndexCommand;
use App\Features\Search\Models\SearchIndex;

readonly class CreateSearchIndexForNoteTitleAction
{
    public function __construct(private CreateSearchIndexCommand $createSearchIndexCommand) {}

    public function handle(Note $note): SearchIndex
    {
        return $this->createSearchIndexCommand->handle($note, [
            SearchIndex::NOTE_ID => $note->id,
            SearchIndex::CONTENT => $note->title,
        ]);
    }
}
