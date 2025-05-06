<?php

namespace App\Features\Note\Commands;

use App\Features\Note\Models\Note;

class UpdateNoteCommand
{
    public function handle(Note $note, array $data, bool $silently = false): Note
    {
        $updateHandling = function () use ($note, $data) {
            $note->update($data);

            return $note->refresh();
        };

        if ($silently) {
            return Note::withoutTimestamps($updateHandling);
        }

        return $updateHandling();
    }
}
