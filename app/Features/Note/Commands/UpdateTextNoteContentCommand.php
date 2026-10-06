<?php

namespace App\Features\Note\Commands;

use App\Features\Note\Models\TextNoteContent;

class UpdateTextNoteContentCommand
{
    public function handle(TextNoteContent $textNoteContent, array $data, bool $silently = false): TextNoteContent
    {
        $updateHandling = function () use ($textNoteContent, $data) {
            $textNoteContent->update($data);

            return $textNoteContent->refresh();
        };

        if ($silently) {
            return TextNoteContent::withoutTimestamps($updateHandling);
        }

        return $updateHandling();
    }
}
