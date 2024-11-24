<?php

namespace App\Features\Search\Actions;

use App\Features\Note\Models\TextNoteContent;
use App\Features\Search\Commands\CreateSearchIndexCommand;
use App\Features\Search\Models\SearchIndex;
use DOMDocument;
use DOMXPath;

readonly class CreateSearchIndexForTextNoteContentAction
{
    public function __construct(private CreateSearchIndexCommand $createSearchIndexCommand)
    {
    }

    public function handle(TextNoteContent $textNoteContent): SearchIndex
    {
        $rawTextContent = $this->getRawTextFromWYSIWYGContent($textNoteContent->content);

        return $this->createSearchIndexCommand->handle($textNoteContent, [
            SearchIndex::NOTE_ID => $textNoteContent->note_id,
            SearchIndex::CONTENT => $rawTextContent,
        ]);
    }

    private function getRawTextFromWYSIWYGContent(string $wysiwygContent): string
    {
        // load WYSIWYG content into DOMDocument
        $dom = new DOMDocument;
        @$dom->loadHTML($wysiwygContent);

        // use DOMXPath to query all text nodes
        $xpath = new DOMXPath($dom);
        $textNodes = $xpath->query('//text()');

        $plainText = '';
        foreach ($textNodes as $textNode) {
            $plainText .= $textNode->nodeValue.' ';
        }

        // trim extra white spaces
        return trim($plainText);
    }
}
