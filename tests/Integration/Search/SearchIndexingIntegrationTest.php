<?php

use App\Features\Note\Actions\UpdateNoteAction;
use App\Features\Note\Models\Note;
use App\Features\NoteType\Enums\NoteTypeEnum;
use Database\Seeders\NoteTypeSeeder;
use Tests\Factories\UserFactory;

describe('Search Index Integration', function () {
    beforeEach(function () {
        $this->seed(NoteTypeSeeder::class);
    });

    it('synchronizes search index when note title and text content are updated', function () {
        $user = UserFactory::new()->create();
        $note = createNote(NoteTypeEnum::MARKDOWN, $user);

        app(UpdateNoteAction::class)->handle($note, [
            Note::TITLE => 'Quarterly Retrospective',
            'text_content' => 'Team velocity increased by 20 percent.',
        ]);

        $titleIndex = $note->searchIndex()->first();
        expect($titleIndex->content)->toBe('Quarterly Retrospective');

        $contentIndex = $note->textContent()->first()->searchIndex()->first();
        expect($contentIndex->content)->toContain('Team velocity increased by 20 percent.');
    });
});
