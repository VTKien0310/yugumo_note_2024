<?php

namespace App\Features\Search\Actions;

use App\Extendables\Core\Utils\GetRawTextFromXmlContentAction;
use App\Features\Note\Models\XmlNoteContent;
use App\Features\Search\Commands\UpdateSearchIndexCommand;
use App\Features\Search\Models\SearchIndex;

readonly class UpdateSearchIndexForXmlNoteContentAction
{
    public function __construct(
        private UpdateSearchIndexCommand $updateSearchIndexCommand,
        private GetRawTextFromXmlContentAction $getRawTextFromXmlContentAction
    ) {}

    public function handle(XmlNoteContent $xmlNoteContent): SearchIndex
    {
        $rawTextContent = $this->getRawTextFromXmlContentAction->handle($xmlNoteContent->content);

        return $this->updateSearchIndexCommand->handle($xmlNoteContent->searchIndex, [
            SearchIndex::CONTENT => $rawTextContent,
        ]);
    }
}
