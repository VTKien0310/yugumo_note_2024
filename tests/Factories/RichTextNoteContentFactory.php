<?php

namespace Tests\Factories;

use App\Features\Note\Models\RichTextNoteContent;
use App\Features\NoteType\Enums\NoteTypeEnum;
use Tests\TestFactory;

class RichTextNoteContentFactory extends TestFactory
{
    /**
     * @param  array<string, mixed>  $extra
     */
    public function create(array $extra = []): RichTextNoteContent
    {
        $noteId = $extra[RichTextNoteContent::NOTE_ID] ?? NoteFactory::new()->create([
            'type_id' => NoteTypeEnum::ADVANCED->value,
        ])->id;

        return RichTextNoteContent::query()->create(array_merge([
            RichTextNoteContent::NOTE_ID => $noteId,
            RichTextNoteContent::CONTENT => [
                'ops' => [
                    ['insert' => "Test note content\n"],
                ],
            ],
        ], $extra));
    }
}
