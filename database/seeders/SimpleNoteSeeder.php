<?php

namespace Database\Seeders;

use App\Features\Note\Models\Note;
use App\Features\Note\Models\TextNoteContent;
use App\Features\NoteType\Enums\NoteTypeEnum;
use App\Features\User\Models\User;
use Illuminate\Database\Seeder;

class SimpleNoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->where(User::EMAIL, 'trgkien.vu@gmail.com')->first();

        collect(range(1, 200))->each(function () use ($user) {
            $note = $user->notes()->create([
                Note::TYPE_ID => NoteTypeEnum::SIMPLE->value,
                Note::TITLE => fake()->sentence(),
            ]);

            $note->textContent()->create([
                TextNoteContent::CONTENT => fake()->paragraph(),
            ]);
        });
    }
}
