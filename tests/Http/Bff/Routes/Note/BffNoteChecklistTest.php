<?php

use App\Extendables\Core\Utils\BoolIntValueEnum;
use App\Features\Note\Models\ChecklistNoteContent;
use App\Features\NoteType\Enums\NoteTypeEnum;
use App\Http\Bff\Routes\Note\BffNoteChecklistController;
use Database\Seeders\NoteTypeSeeder;
use Tests\Factories\UserFactory;

describe(BffNoteChecklistController::class, function () {
    beforeEach(function () {
        $this->seed(NoteTypeSeeder::class);
    });

    it('creates a new checklist item', function () {
        $user = UserFactory::new()->create();
        $note = createNote(NoteTypeEnum::CHECKLIST, $user);

        $response = actingAsUser($user)
            ->postJson(route('bff.notes.checklist-items.store', ['note' => $note->id]));

        $response->assertOk()
            ->assertJsonStructure([
                'data' => ['id', 'content', 'is_completed'],
            ]);

        $item = ChecklistNoteContent::query()->findOrFail($response->json('data.id'));

        expect($item->note_id)->toBe($note->id)
            ->and($item->content)->toBe('Untitled')
            ->and($item->is_completed)->toBe(BoolIntValueEnum::FALSE);
    });

    it('updates checklist item content and completion status', function () {
        $user = UserFactory::new()->create();
        $note = createNote(NoteTypeEnum::CHECKLIST, $user);
        $item = $note->checklistContent()->firstOrFail();

        actingAsUser($user)
            ->putJson(route('bff.notes.checklist-items.update', ['note' => $note->id, 'checklistItem' => $item->id]), [
                'content' => 'Buy milk',
                'is_completed' => true,
            ])
            ->assertOk();

        $item->refresh();
        expect($item->content)->toBe('Buy milk')
            ->and($item->is_completed)->toBe(BoolIntValueEnum::TRUE);
    });

    it('persists emptied checklist item content as empty string', function () {
        $user = UserFactory::new()->create();
        $note = createNote(NoteTypeEnum::CHECKLIST, $user);
        $item = $note->checklistContent()->firstOrFail();

        actingAsUser($user)
            ->putJson(route('bff.notes.checklist-items.update', ['note' => $note->id, 'checklistItem' => $item->id]), [
                'content' => '',
                'is_completed' => false,
            ])
            ->assertOk();

        expect($item->refresh()->content)->toBe('');
    });

    it('validates checklist item update payload', function () {
        $user = UserFactory::new()->create();
        $note = createNote(NoteTypeEnum::CHECKLIST, $user);
        $item = $note->checklistContent()->firstOrFail();

        actingAsUser($user)
            ->putJson(route('bff.notes.checklist-items.update', ['note' => $note->id, 'checklistItem' => $item->id]), [
                'content' => 'Missing completion flag',
            ])
            ->assertUnprocessable();

        actingAsUser($user)
            ->putJson(route('bff.notes.checklist-items.update', ['note' => $note->id, 'checklistItem' => $item->id]), [
                'content' => str_repeat('a', 256),
                'is_completed' => false,
            ])
            ->assertUnprocessable();
    });

    it('soft deletes checklist items', function () {
        $user = UserFactory::new()->create();
        $note = createNote(NoteTypeEnum::CHECKLIST, $user);
        $item = $note->checklistContent()->firstOrFail();

        actingAsUser($user)
            ->deleteJson(route('bff.notes.checklist-items.destroy', ['note' => $note->id, 'checklistItem' => $item->id]))
            ->assertOk();

        $this->assertSoftDeleted($item);
    });

    it('rejects items belonging to another note with 404', function () {
        $user = UserFactory::new()->create();
        $noteA = createNote(NoteTypeEnum::CHECKLIST, $user);
        $noteB = createNote(NoteTypeEnum::CHECKLIST, $user);
        $itemB = $noteB->checklistContent()->firstOrFail();

        actingAsUser($user)
            ->putJson(
                route('bff.notes.checklist-items.update', ['note' => $noteA->id, 'checklistItem' => $itemB->id]),
                ['content' => 'Cross-note edit', 'is_completed' => false]
            )
            ->assertNotFound();

        actingAsUser($user)
            ->deleteJson(
                route('bff.notes.checklist-items.destroy', ['note' => $noteA->id, 'checklistItem' => $itemB->id])
            )
            ->assertNotFound();
    });

    it('rejects checklist item operations on non-checklist notes', function () {
        $user = UserFactory::new()->create();
        $advancedNote = createNote(NoteTypeEnum::ADVANCED, $user);

        actingAsUser($user)
            ->postJson(route('bff.notes.checklist-items.store', ['note' => $advancedNote->id]))
            ->assertUnprocessable();
    });

    it('requires authentication for checklist item endpoints', function () {
        $user = UserFactory::new()->create();
        $note = createNote(NoteTypeEnum::CHECKLIST, $user);
        $item = $note->checklistContent()->firstOrFail();

        $this->postJson(route('bff.notes.checklist-items.store', ['note' => $note->id]))
            ->assertUnauthorized();

        $this->putJson(route('bff.notes.checklist-items.update', ['note' => $note->id, 'checklistItem' => $item->id]), [
            'content' => 'Anon',
            'is_completed' => false,
        ])->assertUnauthorized();

        $this->deleteJson(route('bff.notes.checklist-items.destroy', ['note' => $note->id, 'checklistItem' => $item->id]))
            ->assertUnauthorized();
    });

    it('rejects users who do not own the note with 403', function () {
        $owner = UserFactory::new()->create();
        $stranger = UserFactory::new()->create();
        $note = createNote(NoteTypeEnum::CHECKLIST, $owner);
        $item = $note->checklistContent()->firstOrFail();

        actingAsUser($stranger)
            ->postJson(route('bff.notes.checklist-items.store', ['note' => $note->id]))
            ->assertForbidden();

        actingAsUser($stranger)
            ->putJson(route('bff.notes.checklist-items.update', ['note' => $note->id, 'checklistItem' => $item->id]), [
                'content' => 'Stolen',
                'is_completed' => true,
            ])
            ->assertForbidden();

        actingAsUser($stranger)
            ->deleteJson(route('bff.notes.checklist-items.destroy', ['note' => $note->id, 'checklistItem' => $item->id]))
            ->assertForbidden();
    });
});
