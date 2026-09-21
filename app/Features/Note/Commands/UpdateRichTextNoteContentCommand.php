<?php

namespace App\Features\Note\Commands;

use App\Features\Note\Models\RichTextNoteContent;

class UpdateRichTextNoteContentCommand
{
    public function handle(RichTextNoteContent $richTextNoteContent, array $data, bool $silently = false): RichTextNoteContent
    {
        $updateHandling = function () use ($richTextNoteContent, $data) {
            $richTextNoteContent->update($data);

            return $richTextNoteContent->refresh();
        };

        if ($silently) {
            return RichTextNoteContent::withoutTimestamps($updateHandling);
        }

        return $updateHandling();
    }
}
