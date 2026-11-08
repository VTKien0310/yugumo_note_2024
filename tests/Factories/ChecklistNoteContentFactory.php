<?php

namespace Tests\Factories;

use App\Extendables\Core\Utils\BoolIntValueEnum;
use App\Features\Note\Models\ChecklistNoteContent;
use App\Features\NoteType\Enums\NoteTypeEnum;
use Tests\TestFactory;

class ChecklistNoteContentFactory extends TestFactory
{
    /**
     * @param  array<string, mixed>  $extra
     */
    public function create(array $extra = []): ChecklistNoteContent
    {
        $noteId = $extra[ChecklistNoteContent::NOTE_ID] ?? NoteFactory::new()->create([
            'type_id' => NoteTypeEnum::CHECKLIST->value,
        ])->id;

        return ChecklistNoteContent::query()->create(array_merge([
            ChecklistNoteContent::NOTE_ID => $noteId,
            ChecklistNoteContent::CONTENT => 'Checklist item',
            ChecklistNoteContent::IS_COMPLETED => BoolIntValueEnum::FALSE,
        ], $extra));
    }
}
