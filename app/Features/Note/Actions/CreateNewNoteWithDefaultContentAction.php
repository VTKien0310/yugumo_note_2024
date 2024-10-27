<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Commands\CreateNoteCommand;
use App\Features\Note\Models\Note;
use App\Features\NoteType\Enums\NoteTypeEnum;
use App\Features\NoteType\Models\NoteType;
use App\Features\User\Models\User;
use Illuminate\Support\Facades\DB;

readonly class CreateNewNoteWithDefaultContentAction
{
    public function __construct(
        private CreateNoteCommand $createNoteCommand,
        private CreateEmptyTextNoteContentAction $createEmptyTextNoteContentAction,
        private CreateEmptyChecklistNoteContentAction $createEmptyChecklistNoteContentAction
    ) {}

    public function handle(User $user, NoteType $noteType): Note
    {
        return DB::transaction(function () use ($user, $noteType): Note {
            $note = $this->createNewNoteWithDefaultTitle($user, $noteType);

            $this->createDefaultContentForNewlyCreatedNote($noteType, $note);

            return $note;
        });
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
            NoteTypeEnum::CHECKLIST->value => $this->createEmptyChecklistNoteContentAction->handle($note),
            default => $this->createEmptyTextNoteContentAction->handle($note)
        };
    }
}
