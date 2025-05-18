<?php

namespace App\Features\Search\Actions;

use App\Extendables\Core\Utils\GetRawTextFromXmlContentAction;
use App\Features\Note\Models\XmlNoteContent;
use App\Features\Search\Commands\CreateSearchIndexCommand;
use App\Features\Search\Models\SearchIndex;

readonly class CreateSearchIndexForXmlNoteContentAction
{
    public function __construct(
        private CreateSearchIndexCommand $createSearchIndexCommand,
        private GetRawTextFromXmlContentAction $getRawTextFromXmlContentAction
    ) {}

    public function handle(XmlNoteContent $xmlNoteContent): SearchIndex
    {
        $rawTextContent = $this->getRawTextFromXmlContentAction->handle($xmlNoteContent->content);

        return $this->createSearchIndexCommand->handle($xmlNoteContent, [
            SearchIndex::NOTE_ID => $xmlNoteContent->note_id,
            SearchIndex::CONTENT => $rawTextContent,
        ]);
    }
}
