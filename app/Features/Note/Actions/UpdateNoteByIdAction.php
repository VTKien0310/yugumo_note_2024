<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Commands\UpdateNoteCommand;
use App\Features\Note\Models\Note;

readonly class UpdateNoteByIdAction
{
    public function __construct(private UpdateNoteCommand $updateNoteCommand) {}

    public function handle(string $noteId, array $data): Note
    {
        $note = Note::findOrFail($noteId);

        return $this->updateNoteCommand->handle($note, $data);
    }
}
