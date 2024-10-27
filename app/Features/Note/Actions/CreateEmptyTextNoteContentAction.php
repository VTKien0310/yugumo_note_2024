<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Commands\CreateTextNoteContentCommand;
use App\Features\Note\Models\Note;
use App\Features\Note\Models\TextNoteContent;

readonly class CreateEmptyTextNoteContentAction
{
    public function __construct(
        private CreateTextNoteContentCommand $createTextNoteContentCommand
    ) {}

    public function handle(Note $note): TextNoteContent
    {
        return $this->createTextNoteContentCommand->handle([
            TextNoteContent::NOTE_ID => $note->id,
            TextNoteContent::CONTENT => '',
        ]);
    }
}
