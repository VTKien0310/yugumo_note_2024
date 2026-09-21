<?php

namespace App\Features\Search\Actions;

use App\Extendables\Core\Utils\GetRawTextFromWYSIWYGContentAction;
use App\Features\Note\Models\TextNoteContent;
use App\Features\Search\Commands\UpdateSearchIndexCommand;
use App\Features\Search\Models\SearchIndex;

readonly class UpdateSearchIndexForTextNoteContentAction
{
    public function __construct(
        private UpdateSearchIndexCommand $updateSearchIndexCommand,
        private GetRawTextFromWYSIWYGContentAction $getRawTextFromWYSIWYGContentAction
    ) {}

    public function handle(TextNoteContent $textNoteContent): SearchIndex
    {
        $rawTextContent = $this->getRawTextFromWYSIWYGContentAction->handle($textNoteContent->content);

        return $this->updateSearchIndexCommand->handle($textNoteContent->searchIndex, [
            SearchIndex::CONTENT => $rawTextContent,
        ]);
    }
}
