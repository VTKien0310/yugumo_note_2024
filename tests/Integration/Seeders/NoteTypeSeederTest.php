<?php

use App\Features\NoteType\Enums\NoteTypeEnum;
use App\Features\NoteType\Models\NoteType;
use Database\Seeders\NoteTypeSeeder;

describe(NoteTypeSeeder::class, function () {
    it('seeds all note types declared in NoteTypeEnum', function () {
        $this->seed(NoteTypeSeeder::class);

        foreach (NoteTypeEnum::cases() as $case) {
            $type = NoteType::query()->find($case->value);

            expect($type)->not->toBeNull()
                ->and($type->name)->not->toBeEmpty()
                ->and($type->description)->not->toBeEmpty();
        }
    });
});
