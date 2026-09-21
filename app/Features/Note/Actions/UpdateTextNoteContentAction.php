<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Commands\UpdateTextNoteContentCommand;
use App\Features\Note\Models\TextNoteContent;
use App\Features\Search\Actions\UpdateSearchIndexForTextNoteContentAction;

readonly class UpdateTextNoteContentAction
{
    public function __construct(
        private UpdateTextNoteContentCommand $updateTextNoteContentCommand,
        private UpdateSearchIndexForTextNoteContentAction $updateSearchIndexForTextNoteContentAction
    ) {}

    public function handle(TextNoteContent $textNoteContent, string $content): TextNoteContent
    {
        $textNoteContent = $this->updateTextNoteContentCommand->handle($textNoteContent, [
            TextNoteContent::CONTENT => $content,
        ]);

        $this->updateSearchIndexForTextNoteContentAction->handle($textNoteContent);

        return $textNoteContent;
    }
}
