<?php

namespace App\Features\Search\Actions;

use App\Extendables\Core\Utils\GetRawTextFromWYSIWYGContentAction;
use App\Features\Note\Models\TextNoteContent;
use App\Features\Search\Commands\CreateSearchIndexCommand;
use App\Features\Search\Models\SearchIndex;

readonly class CreateSearchIndexForTextNoteContentAction
{
    public function __construct(
        private CreateSearchIndexCommand $createSearchIndexCommand,
        private GetRawTextFromWYSIWYGContentAction $getRawTextFromWYSIWYGContentAction
    ) {}

    public function handle(TextNoteContent $textNoteContent): SearchIndex
    {
        $rawTextContent = $this->getRawTextFromWYSIWYGContentAction->handle($textNoteContent->content);

        return $this->createSearchIndexCommand->handle($textNoteContent, [
            SearchIndex::NOTE_ID => $textNoteContent->note_id,
            SearchIndex::CONTENT => $rawTextContent,
        ]);
    }
}
