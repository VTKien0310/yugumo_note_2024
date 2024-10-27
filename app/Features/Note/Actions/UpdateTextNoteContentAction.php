<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Commands\UpdateTextNoteContentCommand;
use App\Features\Note\Models\TextNoteContent;

readonly class UpdateTextNoteContentAction
{
    public function __construct(
        private UpdateTextNoteContentCommand $updateTextNoteContentCommand
    ) {}

    public function handle(TextNoteContent $textNoteContent, string $content): TextNoteContent
    {
        return $this->updateTextNoteContentCommand->handle($textNoteContent, [
            TextNoteContent::CONTENT => $content,
        ]);
    }
}
