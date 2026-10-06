<?php

namespace Database\Seeders;

use App\Features\Note\Actions\CreateNewNoteWithDefaultContentAction;
use App\Features\Note\Actions\UpdateNoteAction;
use App\Features\Note\Models\Note;
use App\Features\Note\Models\TextNoteContent;
use App\Features\NoteType\Enums\NoteTypeEnum;
use App\Features\NoteType\Models\NoteType;
use App\Features\User\Models\User;
use Illuminate\Database\Seeder;

class SimpleNoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(
        CreateNewNoteWithDefaultContentAction $createNewNoteWithDefaultContentAction,
        UpdateNoteAction $updateNoteAction
    ): void {
        $user = User::query()->where(User::EMAIL, 'trgkien.vu@gmail.com')->first();

        collect(range(1, 200))->each(
            function () use (
                $createNewNoteWithDefaultContentAction,
                $updateNoteAction,
                $user
            ): void {
                $note = $createNewNoteWithDefaultContentAction->handle(
                    user: $user,
                    noteType: NoteType::query()->where(NoteType::ID, NoteTypeEnum::SIMPLE->value)->first()
                );

                $updateNoteAction->handle($note, [
                    Note::TITLE => fake()->sentence(),
                ]);

                $note->textContent()->update([
                    TextNoteContent::CONTENT => fake()->paragraph(),
                ]);
            }
        );
    }
}
