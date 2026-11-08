<?php

namespace Tests\Factories;

use App\Extendables\Core\Utils\BoolIntValueEnum;
use App\Features\Note\Models\Note;
use App\Features\NoteType\Enums\NoteTypeEnum;
use Tests\TestFactory;

class NoteFactory extends TestFactory
{
    /**
     * @param  array<string, mixed>  $extra
     */
    public function create(array $extra = []): Note
    {
        $userId = $extra[Note::USER_ID] ?? UserFactory::new()->create()->id;
        $typeId = $extra[Note::TYPE_ID] ?? NoteTypeFactory::new()->create([NoteTypeEnum::SIMPLE->value])->id;

        return Note::query()->create(array_merge([
            Note::USER_ID => $userId,
            Note::TYPE_ID => $typeId,
            Note::TITLE => 'Test Note',
            Note::BOOKMARKED => BoolIntValueEnum::FALSE,
            Note::VIEWS => 0,
        ], $extra));
    }
}
