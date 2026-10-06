<?php

namespace App\Features\Search\Actions;

use App\Features\Note\Actions\GetRawTextFromQuillDeltaAction;
use App\Features\Note\Models\RichTextNoteContent;
use App\Features\Search\Commands\UpdateSearchIndexCommand;
use App\Features\Search\Models\SearchIndex;

readonly class UpdateSearchIndexForRichTextNoteContentAction
{
    public function __construct(
        private UpdateSearchIndexCommand $updateSearchIndexCommand,
        private GetRawTextFromQuillDeltaAction $getRawTextFromQuillDeltaAction
    ) {}

    public function handle(RichTextNoteContent $richTextNoteContent): SearchIndex
    {
        $rawTextContent = $this->getRawTextFromQuillDeltaAction->handle($richTextNoteContent->content ?? []);

        return $this->updateSearchIndexCommand->handle($richTextNoteContent->searchIndex, [
            SearchIndex::CONTENT => $rawTextContent,
        ]);
    }
}
