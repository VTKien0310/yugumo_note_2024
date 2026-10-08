<?php

namespace Tests\Factories;

use App\Features\Note\Models\TextNoteContent;
use App\Features\NoteType\Enums\NoteTypeEnum;
use Tests\TestFactory;

class TextNoteContentFactory extends TestFactory
{
    /**
     * @param  array<string, mixed>  $extra
     */
    public function create(array $extra = []): TextNoteContent
    {
        $noteId = $extra[TextNoteContent::NOTE_ID] ?? NoteFactory::new()->create([
            'type_id' => NoteTypeEnum::MARKDOWN->value,
        ])->id;

        return TextNoteContent::query()->create(array_merge([
            TextNoteContent::NOTE_ID => $noteId,
            TextNoteContent::CONTENT => '# Test Markdown Note',
        ], $extra));
    }
}
