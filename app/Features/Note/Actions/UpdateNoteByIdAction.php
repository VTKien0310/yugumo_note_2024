<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Commands\UpdateNoteCommand;
use App\Features\Note\Commands\UpdateTextNoteContentCommand;
use App\Features\Note\Models\Note;

readonly class UpdateNoteByIdAction
{
    public function __construct(
        private UpdateNoteCommand $updateNoteCommand,
        private UpdateTextNoteContentCommand $updateTextNoteContentCommand
    ) {}

    public function handle(string $noteId, array $data): Note
    {
        $note = Note::findOrFail($noteId);

        $note = $this->updateNoteCommand->handle($note, $data);

        $this->updateTextNoteContentCommand->handle($note->textContent, $data);

        return $note;
    }
}
