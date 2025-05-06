<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Commands\CreateTextNoteContentCommand;
use App\Features\Note\Models\Note;
use App\Features\Note\Models\TextNoteContent;
use App\Features\Search\Actions\CreateSearchIndexForTextNoteContentAction;

readonly class CreateEmptyTextNoteContentAction
{
    public function __construct(
        private CreateTextNoteContentCommand $createTextNoteContentCommand,
        private CreateSearchIndexForTextNoteContentAction $createSearchIndexForTextNoteContentAction
    ) {}

    public function handle(Note $note): TextNoteContent
    {
        $textNoteContent = $this->createTextNoteContentCommand->handle([
            TextNoteContent::NOTE_ID => $note->id,
            TextNoteContent::CONTENT => '',
        ]);

        $this->createSearchIndexForTextNoteContentAction->handle($textNoteContent);

        return $textNoteContent;
    }
}
