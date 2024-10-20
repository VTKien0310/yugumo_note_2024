<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Commands\UpdateNoteCommand;
use App\Features\Note\Models\Note;
use App\Features\NoteType\Enums\NoteTypeEnum;
use Illuminate\Support\Facades\DB;

readonly class UpdateNoteByIdAction
{
    public function __construct(
        private UpdateNoteCommand $updateNoteCommand,
        private UpdateTextNoteContentAction $updateTextNoteContentAction
    ) {}

    public function handle(string $noteId, array $data): Note
    {
        return DB::transaction(function () use ($noteId, $data): Note {
            $note = $this->updateNote($noteId, $data);

            $this->updateNoteContentBasedOnNoteType($note, $data);

            return $note;
        });
    }

    private function updateNote(string $noteId, array $data): Note
    {
        $note = Note::findOrFail($noteId);

        return $this->updateNoteCommand->handle($note, $data);
    }

    private function updateNoteContentBasedOnNoteType(Note $note, array $data): void
    {
        match ($note->type_id) {
            NoteTypeEnum::SIMPLE->value, NoteTypeEnum::ADVANCED->value => $this->updateTextNoteContentAction->handle(
                $note->textContent,
                $data['content']
            ),
            default => null
        };
    }
}
