<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Commands\UpdateNoteCommand;
use App\Features\Note\Models\Note;
use App\Features\NoteType\Enums\NoteTypeEnum;
use Illuminate\Support\Facades\DB;

readonly class UpdateNoteAction
{
    public function __construct(
        private UpdateNoteCommand $updateNoteCommand,
        private UpdateTextNoteContentAction $updateTextNoteContentAction
    ) {}

    public function handle(Note $note, array $data): Note
    {
        return DB::transaction(function () use ($note, $data): Note {
            $note = $this->updateNoteCommand->handle($note, $data);

            $this->updateNoteContentBasedOnNoteType($note, $data);

            return $note;
        });
    }

    private function updateNoteContentBasedOnNoteType(Note $note, array $data): void
    {
        match ($note->type_id) {
            NoteTypeEnum::SIMPLE->value, NoteTypeEnum::ADVANCED->value => $this->updateTextNoteContentAction->handle(
                $note->textContent,
                $data['text_content']
            ),
            default => null
        };
    }
}
