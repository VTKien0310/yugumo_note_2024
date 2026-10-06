<?php

namespace Tests\Feature;

use App\Features\Note\Actions\CreateNewNoteWithDefaultContentAction;
use App\Features\NoteType\Enums\NoteTypeEnum;
use App\Features\NoteType\Models\NoteType;
use App\Features\User\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdvancedNoteSyncTest extends TestCase
{
    use DatabaseTransactions;

    public function test_advanced_note_sync_preserves_formatted_content_and_line_breaks(): void
    {
        $user = User::create([
            'name' => 'Advanced note tester',
            'email' => 'advanced-note-test@example.com',
            'password' => 'password',
        ]);
        $noteType = NoteType::query()->firstOrCreate(
            ['id' => NoteTypeEnum::ADVANCED->value],
            [
                'name' => 'Advanced note',
                'description' => 'Rich text note',
                'illustration_path' => 'resources/images/advanced-note.svg',
            ]
        );
        $note = app(CreateNewNoteWithDefaultContentAction::class)->handle($user, $noteType);

        $this->actingAs($user)
            ->get(route('notes.show', ['note' => $note->id]))
            ->assertOk()
            ->assertSee('ql-editor-host');

        $content = [
            'ops' => [
                ['insert' => '  important  ', 'attributes' => ['bold' => true]],
                ['insert' => "\n", 'attributes' => ['header' => 2]],
            ],
        ];

        $this->actingAs($user)
            ->withSession(['_token' => 'advanced-note-test-token'])
            ->withHeader('X-CSRF-TOKEN', 'advanced-note-test-token')
            ->putJson(route('bff.notes.update', ['note' => $note->id]), [
                'title' => 'Updated title',
                'content' => $content,
            ])
            ->assertOk();

        $this->assertSame('Updated title', $note->fresh()->title);
        $this->assertSame($content, $note->richTextContent()->first()->content);

        $blankContent = ['ops' => [['insert' => "\n"]]];

        $this->actingAs($user)
            ->withSession(['_token' => 'advanced-note-test-token'])
            ->withHeader('X-CSRF-TOKEN', 'advanced-note-test-token')
            ->putJson(route('bff.notes.update', ['note' => $note->id]), [
                'title' => 'Updated title',
                'content' => $blankContent,
            ])
            ->assertOk();

        $this->assertSame($blankContent, $note->richTextContent()->first()->content);
    }
}
