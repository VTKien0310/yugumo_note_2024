<?php

use App\Extendables\Core\Utils\BoolIntValueEnum;
use App\Features\Note\Actions\UpdateNoteAction;
use App\Features\Note\Models\Note;
use App\Features\NoteType\Enums\NoteTypeEnum;
use Database\Seeders\NoteTypeSeeder;
use Tests\Factories\UserFactory;

describe(UpdateNoteAction::class, function () {
    beforeEach(function () {
        $this->seed(NoteTypeSeeder::class);
    });

    it('updates note title and updates search index', function () {
        $user = UserFactory::new()->create();
        $note = createNote(NoteTypeEnum::SIMPLE, $user);

        $updated = app(UpdateNoteAction::class)->handle($note, [
            Note::TITLE => 'New Custom Title',
        ]);

        expect($updated->refresh()->title)->toBe('New Custom Title')
            ->and($note->searchIndex()->first()->content)->toBe('New Custom Title');
    });

    it('updates bookmark status when under limit', function () {
        $user = UserFactory::new()->create();
        $note = createNote(NoteTypeEnum::SIMPLE, $user);

        $updated = app(UpdateNoteAction::class)->handle($note, [
            Note::BOOKMARKED => BoolIntValueEnum::TRUE,
        ]);

        expect($updated->refresh()->bookmarked)->toBe(BoolIntValueEnum::TRUE);
    });

    it('updates text content for markdown notes', function () {
        $user = UserFactory::new()->create();
        $note = createNote(NoteTypeEnum::MARKDOWN, $user);

        app(UpdateNoteAction::class)->handle($note, [
            'text_content' => '# Hello Markdown World',
        ]);

        expect($note->textContent()->first()->content)->toBe('# Hello Markdown World');
    });

    it('updates rich text content for advanced notes', function () {
        $user = UserFactory::new()->create();
        $note = createNote(NoteTypeEnum::ADVANCED, $user);

        $delta = ['ops' => [['insert' => "Rich Text Updated\n"]]];
        app(UpdateNoteAction::class)->handle($note, [
            'rich_text_content' => $delta,
        ]);

        expect($note->richTextContent()->first()->content)->toBe($delta);
    });
});
