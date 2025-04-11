<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Models\Note;
use App\Features\Note\ValueObjects\NoteListDisplayDataValueObject;
use App\Features\NoteType\Enums\NoteTypeEnum;

readonly class MakeNoteListDisplayDataAction
{
    public function handle(Note $note): NoteListDisplayDataValueObject
    {
        return new NoteListDisplayDataValueObject(
            id: $note->id,
            shortTitle: $this->makeNoteShortTitle($note),
            mediumTitle: $this->makeNoteMediumTitle($note),
            type: $note->type->name,
            shortContent: $this->makeNoteShortContent($note),
            mediumContent: $this->makeNoteMediumContent($note),
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
        return $note->textContent()->first()?->content ?? '';
    }

    private function makeRepresentingContentForChecklistNote(Note $note): string
    {
        return $note->checklistContent()->first()?->content ?? '';
    }

    private function shortenTextData(string $textData, int $maxLength): string
    {
        $textData = trim($textData);

        if (empty($textData)) {
            return '';
        }

        $shortenedData = substr($textData, 0, $maxLength);

        // the text data is short
        if ($shortenedData === $textData) {
            return $shortenedData;
        }

        // the text data is long
        return trim($shortenedData).'...';
    }
}
