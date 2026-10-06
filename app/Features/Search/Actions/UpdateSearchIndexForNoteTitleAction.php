<?php

namespace App\Features\Search\Actions;

use App\Features\Note\Models\Note;
use App\Features\Search\Commands\UpdateSearchIndexCommand;
use App\Features\Search\Models\SearchIndex;

readonly class UpdateSearchIndexForNoteTitleAction
{
    public function __construct(private UpdateSearchIndexCommand $updateSearchIndexCommand) {}

    public function handle(Note $note): SearchIndex
    {
        return $this->updateSearchIndexCommand->handle($note->searchIndex, [
            SearchIndex::CONTENT => $note->title,
        ]);
    }
}
