<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Commands\CreateNoteCommand;
use App\Features\Note\Commands\CreateTextNoteContentCommand;
use App\Features\Note\Models\Note;
use App\Features\Note\Models\TextNoteContent;
use App\Features\NoteType\Enums\NoteTypeEnum;
use App\Features\NoteType\Models\NoteType;
use App\Features\User\Models\User;

readonly class CreateNoteAction
{
    public function __construct(
        private CreateNoteCommand $createNoteCommand,
        private CreateTextNoteContentCommand $createTextNoteContentCommand
    ) {}

    public function handle(User $user, NoteType $noteType): Note
    {
        $note = $this->createNewNoteWithDefaultTitle($user, $noteType);

        $this->createDefaultContentForNewlyCreatedNote($noteType, $note);

        return $note;
    }

    private function createNewNoteWithDefaultTitle(User $user, NoteType $noteType): Note
    {
        $noteDefaultTitle = 'Untitled';

        return $this->createNoteCommand->handle([
            Note::USER_ID => $user->id,
            Note::TYPE_ID => $noteType->id,
            Note::TITLE => $noteDefaultTitle,
        ]);
    }

    private function createDefaultContentForNewlyCreatedNote(NoteType $noteType, Note $note): void
    {
        match ($noteType->id) {
            NoteTypeEnum::SIMPLE->value, NoteTypeEnum::ADVANCED->value => $this->emptyTextNoteContent($note),
            default => null
        };
    }

    private function emptyTextNoteContent(Note $note): void
    {
        $this->createTextNoteContentCommand->handle([
            TextNoteContent::NOTE_ID => $note->id,
            TextNoteContent::CONTENT => '',
        ]);
    }
}
