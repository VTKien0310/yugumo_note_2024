<?php

namespace App\Extendables\Core\Utils;

use DOMDocument;
use DOMXPath;

class GetRawTextFromWYSIWYGContentAction
{
    public function handle(string $wysiwygContent): string
    {
        if (empty($wysiwygContent)) {
            return '';
        }

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
