<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Commands\UpdateRichTextNoteContentCommand;
use App\Features\Note\Models\RichTextNoteContent;
use App\Features\Search\Actions\UpdateSearchIndexForRichTextNoteContentAction;

readonly class UpdateRichTextNoteContentAction
{
    public function __construct(
        private UpdateRichTextNoteContentCommand $updateRichTextNoteContentCommand,
        private UpdateSearchIndexForRichTextNoteContentAction $updateSearchIndexForRichTextNoteContentAction
    ) {}

    public function handle(RichTextNoteContent $richTextNoteContent, array $content): RichTextNoteContent
    {
        $richTextNoteContent = $this->updateRichTextNoteContentCommand->handle($richTextNoteContent, [
            RichTextNoteContent::CONTENT => $content,
        ]);

        $this->updateSearchIndexForRichTextNoteContentAction->handle($richTextNoteContent);

        return $richTextNoteContent;
    }
}
