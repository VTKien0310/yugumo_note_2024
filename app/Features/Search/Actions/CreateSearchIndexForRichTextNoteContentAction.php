<?php

namespace App\Features\Search\Actions;

use App\Features\Note\Actions\GetRawTextFromQuillDeltaAction;
use App\Features\Note\Models\RichTextNoteContent;
use App\Features\Search\Commands\CreateSearchIndexCommand;
use App\Features\Search\Models\SearchIndex;

readonly class CreateSearchIndexForRichTextNoteContentAction
{
    public function __construct(
        private CreateSearchIndexCommand $createSearchIndexCommand,
        private GetRawTextFromQuillDeltaAction $getRawTextFromQuillDeltaAction
    ) {}

    public function handle(RichTextNoteContent $richTextNoteContent): SearchIndex
    {
        $rawTextContent = $this->getRawTextFromQuillDeltaAction->handle($richTextNoteContent->content ?? []);

        return $this->createSearchIndexCommand->handle($richTextNoteContent, [
            SearchIndex::NOTE_ID => $richTextNoteContent->note_id,
            SearchIndex::CONTENT => $rawTextContent,
        ]);
    }
}
