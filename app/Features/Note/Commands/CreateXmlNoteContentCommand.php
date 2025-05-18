<?php

namespace App\Features\Note\Commands;

use App\Features\Note\Models\XmlNoteContent;

class CreateXmlNoteContentCommand
{
    public function handle(array $data): XmlNoteContent
    {
        return XmlNoteContent::create($data);
    }
}
