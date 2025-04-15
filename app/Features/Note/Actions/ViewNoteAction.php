<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Commands\UpdateNoteCommand;
use App\Features\Note\Models\Note;

readonly class ViewNoteAction
{
    public function __construct(
        private UpdateNoteCommand $updateNoteCommand,
    ) {}

    public function handle(Note $note): Note
    {
        $lastViewedAt = now();
        $views = $note->views + 1;

        return $this->updateNoteCommand->handle(
            note: $note,
            data: [
                Note::LAST_VIEWED_AT => $lastViewedAt,
                Note::VIEWS => $views,
            ],
            silently: true
        );
    }
}
