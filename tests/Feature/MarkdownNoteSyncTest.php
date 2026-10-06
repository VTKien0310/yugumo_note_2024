<?php

namespace Tests\Feature;

use App\Features\Note\Actions\CreateNewNoteWithDefaultContentAction;
use App\Features\Note\Models\Note;
use App\Features\NoteType\Enums\NoteTypeEnum;
use App\Features\NoteType\Models\NoteType;
use App\Features\User\Models\User;
use Database\Seeders\NoteTypeSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class MarkdownNoteSyncTest extends TestCase
{
    use DatabaseTransactions;

    private const string CSRF_TOKEN = 'markdown-note-test-token';

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
            'name' => 'Markdown note tester',
            'email' => 'markdown-note-test@example.com',
            'password' => 'password',
        ]);
        $this->note = app(CreateNewNoteWithDefaultContentAction::class)
            ->handle($this->user, NoteType::findOrFail(NoteTypeEnum::MARKDOWN->value));
    }

    public function test_markdown_note_is_created_with_text_note_content(): void
    {
        $this->assertNotNull($this->note->textContent);
        $this->assertSame('', $this->note->textContent->content);
    }

    public function test_markdown_note_page_renders_the_bff_backed_editor(): void
    {
        $this->actingAs($this->user)
            ->get(route('notes.show', ['note' => $this->note->id]))
            ->assertOk()
            ->assertSee('markdownNoteEditor', escape: false)
            ->assertSee('easymde-editor-host');
    }

    public function test_markdown_note_sync_preserves_markdown_content(): void
    {
        // Note: Laravel's TrimStrings middleware strips leading/trailing
        // whitespace from request input, so the stored content is the trimmed
        // version of what the client sends.
        $content = "# Shopping list\n\n- milk\n- **eggs**\n\n> don't forget";

        $this->putJsonAsUser(route('bff.notes.update', ['note' => $this->note->id]), [
            'title' => 'Updated markdown title',
            'content' => $content,
        ])
            ->assertOk();

        $this->assertSame('Updated markdown title', $this->note->fresh()->title);
        $this->assertSame($content, $this->note->textContent()->first()->content);
    }

    public function test_markdown_note_sync_accepts_empty_content(): void
    {
        $this->putJsonAsUser(route('bff.notes.update', ['note' => $this->note->id]), [
            'title' => 'Empty note',
            'content' => '',
        ])
            ->assertOk();

        $this->assertSame('', $this->note->textContent()->first()->content);
    }

    public function test_markdown_note_sync_indexes_content_for_search(): void
    {
        $content = "# Project plan\n\nShip the **release** on Friday";

        $this->putJsonAsUser(route('bff.notes.update', ['note' => $this->note->id]), [
            'title' => 'Plan',
            'content' => $content,
        ])
            ->assertOk();

        $searchIndexContent = $this->note->textContent()->first()->searchIndex()->first()->content;

        $this->assertStringContainsString('# Project plan', $searchIndexContent);
        $this->assertStringContainsString('Ship the **release** on Friday', $searchIndexContent);
    }

    private function putJsonAsUser(string $url, array $data): TestResponse
    {
        return $this->actingAs($this->user)
            ->withSession(['_token' => self::CSRF_TOKEN])
            ->withHeader('X-CSRF-TOKEN', self::CSRF_TOKEN)
            ->putJson($url, $data);
    }
}
