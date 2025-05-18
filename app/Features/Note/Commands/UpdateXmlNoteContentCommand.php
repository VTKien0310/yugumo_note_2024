<?php

namespace App\Features\Note\Commands;

use App\Features\Note\Models\XmlNoteContent;

class UpdateXmlNoteContentCommand
{
    public function handle(XmlNoteContent $xmlNoteContent, array $data, bool $silently = false): XmlNoteContent
    {
        $updateHandling = function () use ($xmlNoteContent, $data) {
            $xmlNoteContent->update($data);

            return $xmlNoteContent->refresh();
        };

        if ($silently) {
            return XmlNoteContent::withoutTimestamps($updateHandling);
        }

        return $updateHandling();
    }
}
