<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Commands\CreateXmlNoteContentCommand;
use App\Features\Note\Models\Note;
use App\Features\Note\Models\XmlNoteContent;
use App\Features\Search\Actions\CreateSearchIndexForXmlNoteContentAction;

readonly class CreateEmptyXmlNoteContentAction
{
    public function __construct(
        private CreateXmlNoteContentCommand $createXmlNoteContentCommand,
        private CreateSearchIndexForXmlNoteContentAction $createSearchIndexForXmlNoteContentAction
    ) {}

    public function handle(Note $note): XmlNoteContent
    {
        $xmlNoteContent = $this->createXmlNoteContentCommand->handle([
            XmlNoteContent::NOTE_ID => $note->id,
            XmlNoteContent::CONTENT => '<content>Default</content>',
        ]);

        $this->createSearchIndexForXmlNoteContentAction->handle($xmlNoteContent);

        return $xmlNoteContent;
    }
}
