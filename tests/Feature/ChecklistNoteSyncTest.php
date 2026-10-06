<?php

namespace Tests\Feature;

use App\Extendables\Core\Utils\BoolIntValueEnum;
use App\Features\Note\Actions\CreateNewNoteWithDefaultContentAction;
use App\Features\Note\Models\ChecklistNoteContent;
use App\Features\Note\Models\Note;
use App\Features\NoteType\Enums\NoteTypeEnum;
use App\Features\NoteType\Models\NoteType;
use App\Features\User\Models\User;
use Database\Seeders\NoteTypeSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ChecklistNoteSyncTest extends TestCase
{
    use DatabaseTransactions;

    private const string CSRF_TOKEN = 'checklist-note-test-token';

    private User $user;

    private Note $note;

    protected function setUp(): void
    {
        parent::setUp();

        // The page dispatch and the BFF type guards compare note->type_id
        // against the enum values, so the note_types rows must carry those
        // exact ids - which is what the production seeder inserts.
        $this->seed(NoteTypeSeeder::class);

        $this->user = User::create([
            'name' => 'Checklist note tester',
            'email' => 'checklist-note-test@example.com',
            'password' => 'password',
        ]);
        $this->note = app(CreateNewNoteWithDefaultContentAction::class)
            ->handle($this->user, NoteType::findOrFail(NoteTypeEnum::CHECKLIST->value));
    }

    public function test_checklist_note_page_renders_the_bff_backed_editor(): void
    {
        $this->actingAs($this->user)
            ->get(route('notes.show', ['note' => $this->note->id]))
            ->assertOk()
            ->assertSee('checklistNoteEditor', escape: false)
            ->assertSee('checklist-items', escape: false)
            // the old Livewire per-item component is gone
            ->assertDontSee('checklist-item-form-livewire');
    }

    public function test_checklist_note_title_syncs_through_the_note_update_endpoint(): void
    {
        $this->putJsonAsUser(route('bff.notes.update', ['note' => $this->note->id]), [
            'title' => 'Groceries',
        ])
            ->assertOk()
            ->assertJsonPath('data.saved_at', $this->note->refresh()->updated_at->toIso8601String());

        $this->assertSame('Groceries', $this->note->fresh()->title);
    }

    public function test_checklist_note_title_sync_rejects_invalid_title(): void
    {
        $this->putJsonAsUser(route('bff.notes.update', ['note' => $this->note->id]), [
            'title' => str_repeat('a', 256),
        ])->assertUnprocessable();

        $this->putJsonAsUser(route('bff.notes.update', ['note' => $this->note->id]), [])
            ->assertUnprocessable();
    }

    public function test_checklist_note_title_sync_rejects_other_note_types(): void
    {
        $simpleNote = $this->createNoteOfType(NoteTypeEnum::SIMPLE);

        $this->putJsonAsUser(route('bff.notes.update', ['note' => $simpleNote->id]), [
            'title' => 'Nope',
        ])->assertUnprocessable();
    }

    public function test_checklist_item_creation_returns_the_new_item(): void
    {
        $response = $this->postJsonAsUser(route('bff.notes.checklist-items.store', ['note' => $this->note->id]));

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => ['id', 'content', 'is_completed'],
            ]);

        $item = ChecklistNoteContent::query()->findOrFail($response->json('data.id'));

        $this->assertSame($this->note->id, $item->note_id);
        $this->assertSame('Untitled', $item->content);
        $this->assertSame(BoolIntValueEnum::FALSE, $item->is_completed);
    }

    public function test_checklist_item_update_edits_content_and_completion(): void
    {
        $item = $this->note->checklistContent()->firstOrFail();

        $this->putJsonAsUser($this->itemRoute('bff.notes.checklist-items.update', $item), [
            'content' => 'Buy milk',
            'is_completed' => true,
        ])->assertOk();

        $item->refresh();
        $this->assertSame('Buy milk', $item->content);
        $this->assertSame(BoolIntValueEnum::TRUE, $item->is_completed);
    }

    public function test_checklist_item_update_persists_emptied_content_as_empty_string(): void
    {
        $item = $this->note->checklistContent()->firstOrFail();

        $this->putJsonAsUser($this->itemRoute('bff.notes.checklist-items.update', $item), [
            'content' => '',
            'is_completed' => false,
        ])->assertOk();

        $this->assertSame('', $item->refresh()->content);
    }

    public function test_checklist_item_update_validates_the_payload(): void
    {
        $item = $this->note->checklistContent()->firstOrFail();

        $this->putJsonAsUser($this->itemRoute('bff.notes.checklist-items.update', $item), [
            'content' => 'Missing completion flag',
        ])->assertUnprocessable();

        $this->putJsonAsUser($this->itemRoute('bff.notes.checklist-items.update', $item), [
            'content' => str_repeat('a', 256),
            'is_completed' => false,
        ])->assertUnprocessable();
    }

    public function test_checklist_item_deletion_soft_deletes_the_item(): void
    {
        $item = $this->note->checklistContent()->firstOrFail();

        $this->deleteJsonAsUser($this->itemRoute('bff.notes.checklist-items.destroy', $item))
            ->assertOk();

        $this->assertSoftDeleted($item);
    }

    public function test_checklist_endpoints_reject_users_who_do_not_own_the_note(): void
    {
        $stranger = User::create([
            'name' => 'Stranger',
            'email' => 'checklist-note-stranger@example.com',
            'password' => 'password',
        ]);
        $item = $this->note->checklistContent()->firstOrFail();

        $this->actingAs($stranger)
            ->withSession(['_token' => self::CSRF_TOKEN])
            ->withHeader('X-CSRF-TOKEN', self::CSRF_TOKEN)
            ->putJson(route('bff.notes.update', ['note' => $this->note->id]), ['title' => 'Stolen'])
            ->assertForbidden();

        $this->actingAs($stranger)
            ->withSession(['_token' => self::CSRF_TOKEN])
            ->withHeader('X-CSRF-TOKEN', self::CSRF_TOKEN)
            ->postJson(route('bff.notes.checklist-items.store', ['note' => $this->note->id]))
            ->assertForbidden();

        $this->actingAs($stranger)
            ->withSession(['_token' => self::CSRF_TOKEN])
            ->withHeader('X-CSRF-TOKEN', self::CSRF_TOKEN)
            ->putJson($this->itemRoute('bff.notes.checklist-items.update', $item), [
                'content' => 'Stolen',
                'is_completed' => true,
            ])
            ->assertForbidden();

        $this->actingAs($stranger)
            ->withSession(['_token' => self::CSRF_TOKEN])
            ->withHeader('X-CSRF-TOKEN', self::CSRF_TOKEN)
            ->deleteJson($this->itemRoute('bff.notes.checklist-items.destroy', $item))
            ->assertForbidden();
    }

    public function test_checklist_item_endpoints_reject_items_of_another_note(): void
    {
        $otherNote = $this->createNoteOfType(NoteTypeEnum::CHECKLIST);
        $foreignItem = $otherNote->checklistContent()->firstOrFail();

        // the item does not belong to $this->note, so it must look like it
        // does not exist under this note's URL at all
        $this->putJsonAsUser(
            route('bff.notes.checklist-items.update', [
                'note' => $this->note->id,
                'checklistItem' => $foreignItem->id,
            ]),
            ['content' => 'Cross-note edit', 'is_completed' => false]
        )->assertNotFound();

        $this->deleteJsonAsUser(
            route('bff.notes.checklist-items.destroy', [
                'note' => $this->note->id,
                'checklistItem' => $foreignItem->id,
            ])
        )->assertNotFound();
    }

    public function test_checklist_item_endpoints_reject_non_checklist_notes(): void
    {
        $advancedNote = $this->createNoteOfType(NoteTypeEnum::ADVANCED);

        $this->postJsonAsUser(route('bff.notes.checklist-items.store', ['note' => $advancedNote->id]))
            ->assertUnprocessable();
    }

    public function test_checklist_endpoints_require_authentication(): void
    {
        $item = $this->note->checklistContent()->firstOrFail();

        $this->putJson(route('bff.notes.update', ['note' => $this->note->id]), ['title' => 'Anon'])
            ->assertUnauthorized();

        $this->postJson(route('bff.notes.checklist-items.store', ['note' => $this->note->id]))
            ->assertUnauthorized();

        $this->putJson($this->itemRoute('bff.notes.checklist-items.update', $item), [
            'content' => 'Anon',
            'is_completed' => false,
        ])->assertUnauthorized();

        $this->deleteJson($this->itemRoute('bff.notes.checklist-items.destroy', $item))
            ->assertUnauthorized();
    }

    private function createNoteOfType(NoteTypeEnum $noteType): Note
    {
        return app(CreateNewNoteWithDefaultContentAction::class)
            ->handle($this->user, NoteType::findOrFail($noteType->value));
    }

    private function itemRoute(string $name, ChecklistNoteContent $item): string
    {
        return route($name, ['note' => $this->note->id, 'checklistItem' => $item->id]);
    }

    private function putJsonAsUser(string $url, array $data)
    {
        return $this->actingAs($this->user)
            ->withSession(['_token' => self::CSRF_TOKEN])
            ->withHeader('X-CSRF-TOKEN', self::CSRF_TOKEN)
            ->putJson($url, $data);
    }

    private function postJsonAsUser(string $url, array $data = [])
    {
        return $this->actingAs($this->user)
            ->withSession(['_token' => self::CSRF_TOKEN])
            ->withHeader('X-CSRF-TOKEN', self::CSRF_TOKEN)
            ->postJson($url, $data);
    }

    private function deleteJsonAsUser(string $url)
    {
        return $this->actingAs($this->user)
            ->withSession(['_token' => self::CSRF_TOKEN])
            ->withHeader('X-CSRF-TOKEN', self::CSRF_TOKEN)
            ->deleteJson($url);
    }
}
