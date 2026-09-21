<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Commands\CreateRichTextNoteContentCommand;
use App\Features\Note\Models\Note;
use App\Features\Note\Models\RichTextNoteContent;
use App\Features\Search\Actions\CreateSearchIndexForRichTextNoteContentAction;

readonly class CreateEmptyRichTextNoteContentAction
{
    public function __construct(
        private CreateRichTextNoteContentCommand $createRichTextNoteContentCommand,
        private CreateSearchIndexForRichTextNoteContentAction $createSearchIndexForRichTextNoteContentAction
    ) {}

    public function handle(Note $note): RichTextNoteContent
    {
        $richTextNoteContent = $this->createRichTextNoteContentCommand->handle([
            RichTextNoteContent::NOTE_ID => $note->id,
            RichTextNoteContent::CONTENT => [
                'ops' => [
                    ['insert' => "\n"],
                ],
            ],
        ]);

        $this->createSearchIndexForRichTextNoteContentAction->handle($richTextNoteContent);

        return $richTextNoteContent;
    }
}
