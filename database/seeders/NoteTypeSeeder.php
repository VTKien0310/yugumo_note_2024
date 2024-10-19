<?php

namespace Database\Seeders;

use App\Features\NoteType\Models\NoteType;
use Illuminate\Database\Seeder;

class NoteTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // flush all note types before new data seeding
        NoteType::query()->forceDelete();

        $types = [
            [
                NoteType::NAME => 'Simple note',
                NoteType::DESCRIPTION => "A text-only note to write down what's on your mind",
                NoteType::ILLUSTRATION_PATH => 'resources/images/simple-note.svg',
            ],
            [
                NoteType::NAME => 'Checklist',
                NoteType::DESCRIPTION => 'A list with checkboxes to keep track of things you need',
                NoteType::ILLUSTRATION_PATH => 'resources/images/checklist.svg',
            ],
            [
                NoteType::NAME => 'Advanced note',
                NoteType::DESCRIPTION => 'A more advanced note with formatting and images',
                NoteType::ILLUSTRATION_PATH => 'resources/images/advanced-note.svg',
            ],
        ];

        foreach ($types as $type) {
            NoteType::create($type);
        }
    }
}
