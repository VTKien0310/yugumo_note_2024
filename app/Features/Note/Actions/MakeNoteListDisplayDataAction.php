<?php

namespace App\Features\Note\Actions;

use App\Extendables\Core\Utils\BoolIntValueEnum;
use App\Extendables\Core\Utils\GetRawTextFromWYSIWYGContentAction;
use App\Features\Note\Models\ChecklistNoteContent;
use App\Features\Note\Models\Note;
use App\Features\Note\ValueObjects\NoteListDisplayDataValueObject;
use App\Features\NoteType\Enums\NoteTypeEnum;
use Illuminate\Support\Str;

readonly class MakeNoteListDisplayDataAction
{
    public function __construct(
        private GetRawTextFromWYSIWYGContentAction $getRawTextFromWYSIWYGContentAction,
    ) {
    }

    public function handle(Note $note): NoteListDisplayDataValueObject
    {
        return new NoteListDisplayDataValueObject(
            id: $note->id,
            shortTitle: $this->makeNoteShortTitle($note),
            mediumTitle: $this->makeNoteMediumTitle($note),
            type: $note->type->name,
            shortContent: $this->makeNoteShortContent($note),
            mediumContent: $this->makeNoteMediumContent($note),
            bookmarked: (bool) $note->bookmarked->value,
            createdAt: $note->created_at->toLocalizedString(),
            updatedAt: $note->updated_at->toLocalizedString(),
        );
    }

    private function makeNoteShortContent(Note $note): string
    {
        $representingContent = $this->getNoteRepresentingContent($note);

        return $this->shortenTextData($representingContent, 50);
    }

    private function makeNoteMediumContent(Note $note): string
    {
        $representingContent = $this->getNoteRepresentingContent($note);

        return $this->shortenTextData($representingContent, 100);
    }

    private function getNoteRepresentingContent(Note $note): string
    {
        return match ($note->type->id) {
            NoteTypeEnum::CHECKLIST->value => $this->makeRepresentingContentForChecklistNote($note),
            NoteTypeEnum::ADVANCED->value => $this->makeRepresentingContentForAdvancedNote($note),
            default => $this->makeRepresentingContentForTextNote($note),
        };
    }

    private function makeNoteShortTitle(Note $note): string
    {
        return $this->shortenTextData($note->title, 25);
    }

    private function makeNoteMediumTitle(Note $note): string
    {
        return $this->shortenTextData($note->title, 50);
    }

    private function makeRepresentingContentForTextNote(Note $note): string
    {
        return $note->textContent()->latest()->first()?->content ?? '';
    }

    private function makeRepresentingContentForChecklistNote(Note $note): string
    {
        return $note->checklistContent()
            ->where(ChecklistNoteContent::IS_COMPLETED, BoolIntValueEnum::FALSE)
            ->latest()
            ->first()
            ?->content ?? '';
    }

    private function makeRepresentingContentForAdvancedNote(Note $note): string
    {
        return $this->getRawTextFromWYSIWYGContentAction->handle(
            $this->makeRepresentingContentForTextNote($note)
        );
    }

    private function shortenTextData(string $textData, int $maxLength): string
    {
        return Str::limit($textData, $maxLength, '...', true);
    }
}
