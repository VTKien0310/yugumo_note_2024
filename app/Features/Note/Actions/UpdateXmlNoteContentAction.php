<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Commands\UpdateXmlNoteContentCommand;
use App\Features\Note\Models\XmlNoteContent;
use App\Features\Search\Actions\UpdateSearchIndexForXmlNoteContentAction;

readonly class UpdateXmlNoteContentAction
{
    public function __construct(
        private UpdateXmlNoteContentCommand $updateXmlNoteContentCommand,
        private UpdateSearchIndexForXmlNoteContentAction $updateSearchIndexForXmlNoteContentAction
    ) {}

    public function handle(XmlNoteContent $xmlNoteContent, string $content): XmlNoteContent
    {
        $xmlNoteContent = $this->updateXmlNoteContentCommand->handle($xmlNoteContent, [
            XmlNoteContent::CONTENT => $content,
        ]);

        $this->updateSearchIndexForXmlNoteContentAction->handle($xmlNoteContent);

        return $xmlNoteContent;
    }
}
