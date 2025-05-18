<?php

namespace App\Extendables\Core\Utils;

class GetRawTextFromXmlContentAction
{
    public function handle(string $xmlContent): string
    {
        if (empty($xmlContent)) {
            return '';
        }

        // Load the XML content
        $doc = new \DOMDocument;
        $doc->loadXML($xmlContent);

        // Grab all text under the root
        $rawText = $doc->documentElement->textContent;

        // Normalize whitespace
        return preg_replace('/\s+/', ' ', trim($rawText));
    }
}
