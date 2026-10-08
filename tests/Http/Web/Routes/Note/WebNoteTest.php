<?php

use App\Features\NoteType\Enums\NoteTypeEnum;
use App\Http\Web\Routes\Note\WebNoteController;
use Database\Seeders\NoteTypeSeeder;
use Tests\Factories\UserFactory;

describe(WebNoteController::class, function () {
    beforeEach(function () {
        $this->seed(NoteTypeSeeder::class);
    });

    it('renders the notes list page for authenticated user', function () {
        $user = UserFactory::new()->create();

        $response = $this->actingAs($user)
            ->get(route('notes.index'));

        $response->assertOk();
    });

    it('renders the advanced note page with quill editor host', function () {
        $user = UserFactory::new()->create();
        $note = createNote(NoteTypeEnum::ADVANCED, $user);

        $response = $this->actingAs($user)
            ->get(route('notes.show', ['note' => $note->id]));

        $response->assertOk()
            ->assertSee('ql-editor-host');
    });

    it('renders the checklist note page with checklist editor', function () {
        $user = UserFactory::new()->create();
        $note = createNote(NoteTypeEnum::CHECKLIST, $user);

        $response = $this->actingAs($user)
            ->get(route('notes.show', ['note' => $note->id]));

        $response->assertOk()
            ->assertSee('checklistNoteEditor', escape: false)
            ->assertSee('checklist-items', escape: false);
    });

    it('renders the markdown note page with easymde editor host', function () {
        $user = UserFactory::new()->create();
        $note = createNote(NoteTypeEnum::MARKDOWN, $user);

        $response = $this->actingAs($user)
            ->get(route('notes.show', ['note' => $note->id]));

        $response->assertOk()
            ->assertSee('markdownNoteEditor', escape: false)
            ->assertSee('easymde-editor-host');
    });

    it('redirects unauthenticated user accessing note page to login', function () {
        $user = UserFactory::new()->create();
        $note = createNote(NoteTypeEnum::SIMPLE, $user);

        $response = $this->get(route('notes.show', ['note' => $note->id]));

        $response->assertRedirect(route('auth.login'));
    });
});
