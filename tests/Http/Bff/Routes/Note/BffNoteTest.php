<?php

use App\Features\NoteType\Enums\NoteTypeEnum;
use App\Http\Bff\Routes\Note\BffNoteController;
use Database\Seeders\NoteTypeSeeder;
use Tests\Factories\UserFactory;

describe(BffNoteController::class, function () {
    beforeEach(function () {
        $this->seed(NoteTypeSeeder::class);
    });

    it('syncs checklist note title through the note update endpoint', function () {
        $user = UserFactory::new()->create();
        $note = createNote(NoteTypeEnum::CHECKLIST, $user);

        actingAsUser($user)
            ->putJson(route('bff.notes.update', ['note' => $note->id]), [
                'title' => 'Groceries',
            ])
            ->assertOk()
            ->assertJsonPath('data.saved_at', $note->refresh()->updated_at->toIso8601String());

        expect($note->fresh()->title)->toBe('Groceries');
    });

    it('rejects invalid checklist note title', function () {
        $user = UserFactory::new()->create();
        $note = createNote(NoteTypeEnum::CHECKLIST, $user);

        actingAsUser($user)
            ->putJson(route('bff.notes.update', ['note' => $note->id]), [
                'title' => str_repeat('a', 256),
            ])
            ->assertUnprocessable();

        actingAsUser($user)
            ->putJson(route('bff.notes.update', ['note' => $note->id]), [])
            ->assertUnprocessable();
    });

    it('rejects title sync for unsupported note types', function () {
        $user = UserFactory::new()->create();
        $simpleNote = createNote(NoteTypeEnum::SIMPLE, $user);

        actingAsUser($user)
            ->putJson(route('bff.notes.update', ['note' => $simpleNote->id]), [
                'title' => 'Nope',
            ])
            ->assertUnprocessable();
    });

    it('syncs advanced note content and preserves formatting and line breaks', function () {
        $user = UserFactory::new()->create();
        $note = createNote(NoteTypeEnum::ADVANCED, $user);

        $content = [
            'ops' => [
                ['insert' => '  important  ', 'attributes' => ['bold' => true]],
                ['insert' => "\n", 'attributes' => ['header' => 2]],
            ],
        ];

        actingAsUser($user)
            ->putJson(route('bff.notes.update', ['note' => $note->id]), [
                'title' => 'Updated title',
                'content' => $content,
            ])
            ->assertOk();

        expect($note->fresh()->title)->toBe('Updated title')
            ->and($note->richTextContent()->first()->content)->toBe($content);
    });

    it('syncs advanced note with blank content', function () {
        $user = UserFactory::new()->create();
        $note = createNote(NoteTypeEnum::ADVANCED, $user);

        $blankContent = ['ops' => [['insert' => "\n"]]];

        actingAsUser($user)
            ->putJson(route('bff.notes.update', ['note' => $note->id]), [
                'title' => 'Updated title',
                'content' => $blankContent,
            ])
            ->assertOk();

        expect($note->richTextContent()->first()->content)->toBe($blankContent);
    });

    it('syncs markdown note content', function () {
        $user = UserFactory::new()->create();
        $note = createNote(NoteTypeEnum::MARKDOWN, $user);

        $content = "# Shopping list\n\n- milk\n- **eggs**\n\n> don't forget";

        actingAsUser($user)
            ->putJson(route('bff.notes.update', ['note' => $note->id]), [
                'title' => 'Updated markdown title',
                'content' => $content,
            ])
            ->assertOk();

        expect($note->fresh()->title)->toBe('Updated markdown title')
            ->and($note->textContent()->first()->content)->toBe($content);
    });

    it('accepts empty content for markdown notes', function () {
        $user = UserFactory::new()->create();
        $note = createNote(NoteTypeEnum::MARKDOWN, $user);

        actingAsUser($user)
            ->putJson(route('bff.notes.update', ['note' => $note->id]), [
                'title' => 'Empty note',
                'content' => '',
            ])
            ->assertOk();

        expect($note->textContent()->first()->content)->toBe('');
    });

    it('indexes markdown note content for search', function () {
        $user = UserFactory::new()->create();
        $note = createNote(NoteTypeEnum::MARKDOWN, $user);

        $content = "# Project plan\n\nShip the **release** on Friday";

        actingAsUser($user)
            ->putJson(route('bff.notes.update', ['note' => $note->id]), [
                'title' => 'Plan',
                'content' => $content,
            ])
            ->assertOk();

        $searchIndexContent = $note->textContent()->first()->searchIndex()->first()->content;

        expect($searchIndexContent)->toContain('# Project plan')
            ->and($searchIndexContent)->toContain('Ship the **release** on Friday');
    });

    it('rejects unauthenticated users from updating notes', function () {
        $user = UserFactory::new()->create();
        $note = createNote(NoteTypeEnum::CHECKLIST, $user);

        $this->putJson(route('bff.notes.update', ['note' => $note->id]), ['title' => 'Anon'])
            ->assertUnauthorized();
    });

    it('rejects users who do not own the note', function () {
        $owner = UserFactory::new()->create();
        $stranger = UserFactory::new()->create();
        $note = createNote(NoteTypeEnum::CHECKLIST, $owner);

        actingAsUser($stranger)
            ->putJson(route('bff.notes.update', ['note' => $note->id]), ['title' => 'Stolen'])
            ->assertForbidden();
    });
});
