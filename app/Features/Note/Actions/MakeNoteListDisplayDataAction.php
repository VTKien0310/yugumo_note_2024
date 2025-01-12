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
            title: $note->title,
            type: $note->type->name,
            shortenedContent: $this->makeNoteShortenedContent($note),
            createdAt: $note->created_at->toLocalizedString(),
            updatedAt: $note->updated_at->toLocalizedString(),
        );
    }

    private function makeNoteShortenedContent(Note $note): string
    {
        $representingContent = match ($note->type->id) {
            NoteTypeEnum::CHECKLIST->value => $this->makeRepresentingContentForChecklistNote($note),
            default => $this->makeRepresentingContentForTextNote($note),
        };
        $representingContent = trim($representingContent);

        // the note has no content yet
        if (empty($representingContent)) {
            return '';
        }

        $shortenedContent = substr($representingContent, 0, 100);

        // the representing content is short
        if ($shortenedContent === $representingContent) {
            return $shortenedContent;
        }

        // the representing content is long
        return trim($shortenedContent).'...';
    }

    private function makeRepresentingContentForTextNote(Note $note): string
    {
        return $note->textContent()->first()?->content ?? '';
    }

    private function makeRepresentingContentForChecklistNote(Note $note): string
    {
        return $note->checklistContent()->first()?->content ?? '';
    }
}
