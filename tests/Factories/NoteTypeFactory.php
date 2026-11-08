<?php

namespace Tests\Factories;

use App\Features\NoteType\Enums\NoteTypeEnum;
use App\Features\NoteType\Models\NoteType;
use Tests\TestFactory;

class NoteTypeFactory extends TestFactory
{
    /**
     * @param  array<string, mixed>  $extra
     */
    public function create(array $extra = []): NoteType
    {
        $id = $extra[NoteType::ID] ?? NoteTypeEnum::SIMPLE->value;

        return NoteType::query()->firstOrCreate(
            [NoteType::ID => $id],
            array_merge([
                NoteType::NAME => 'Simple Note',
                NoteType::DESCRIPTION => 'A simple note',
                NoteType::ILLUSTRATION_PATH => '/images/notes/simple.svg',
            ], $extra)
        );
    }
}
