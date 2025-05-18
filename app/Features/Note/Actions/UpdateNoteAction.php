<?php

namespace App\Features\Note\Actions;

use App\Extendables\Core\Utils\BoolIntValueEnum;
use App\Features\Note\Commands\UpdateNoteCommand;
use App\Features\Note\Models\Note;
use App\Features\NoteType\Enums\NoteTypeEnum;
use App\Features\Search\Actions\UpdateSearchIndexForNoteTitleAction;
use Illuminate\Support\Facades\DB;

readonly class UpdateNoteAction
{
    public function __construct(
        private UpdateNoteCommand $updateNoteCommand,
        private UpdateTextNoteContentAction $updateTextNoteContentAction,
        private UpdateSearchIndexForNoteTitleAction $updateSearchIndexForNoteTitleAction,
        private UpdateXmlNoteContentAction $updateXmlNoteContentAction,
        private CheckUserHasReachedMaximumAllowedBookmarkedNotesAction $checkUserHasReachedMaximumAllowedBookmarkedNotesAction
    ) {}

    public function handle(Note $note, array $data): Note
    {
        return DB::transaction(function () use ($note, $data): Note {
            $note = $this->hasNoteBookmarkStatusChange($note, $data)
                ? $this->updateWithNoteBookmarkStatusChange($note, $data)
                : $this->updateWithoutNoteBookmarkStatusChange($note, $data);

            $this->updateNoteContentBasedOnNoteType($note, $data);

            $this->updateSearchIndexForNoteTitleAction->handle($note);

            return $note;
        });
    }

    private function updateNoteContentBasedOnNoteType(Note $note, array $data): void
    {
        match ($note->type_id) {
            NoteTypeEnum::SIMPLE->value, NoteTypeEnum::ADVANCED->value => $this->updateTextNoteContent($note, $data),
            NoteTypeEnum::XML->value => $this->updateXmlNoteType($note, $data),
            default => null
        };
    }

    private function updateTextNoteContent(Note $note, array $data): void
    {
        $validTextContent = isset($data['text_content']) && is_string($data['text_content']);
        if (! $validTextContent) {
            return;
        }

        $this->updateTextNoteContentAction->handle($note->textContent, $data['text_content']);
    }

    private function updateXmlNoteType(Note $note, array $data): void
    {
        // update note's "description"
        $this->updateTextNoteContent($note, $data);

        $validXmlContent = isset($data['xml_content']) && is_string($data['xml_content']);
        if (! $validXmlContent) {
            return;
        }

        $this->updateXmlNoteContentAction->handle($note->xmlContent, $data['xml_content']);
    }

    private function hasNoteBookmarkStatusChange(Note $note, array $data): bool
    {
        if (! isset($data[Note::BOOKMARKED])) {
            return false;
        }

        return $data[Note::BOOKMARKED] !== $note->bookmarked;
    }

    private function updateWithoutNoteBookmarkStatusChange(Note $note, array $data): Note
    {
        return $this->updateNoteCommand->handle($note, $data);
    }

    private function updateWithNoteBookmarkStatusChange(Note $note, array $data): Note
    {
        $data = $this->preventBookmarkMoreThanMaximumAllowed($note, $data);

        return $this->updateNoteCommand->handle($note, $data);
    }

    private function preventBookmarkMoreThanMaximumAllowed(Note $note, array $data): array
    {
        if ($data[Note::BOOKMARKED] !== BoolIntValueEnum::TRUE) {
            return $data;
        }

        if (! $this->checkUserHasReachedMaximumAllowedBookmarkedNotesAction->handle($note->user)) {
            return $data;
        }

        unset($data[Note::BOOKMARKED]);

        return $data;
    }
}
