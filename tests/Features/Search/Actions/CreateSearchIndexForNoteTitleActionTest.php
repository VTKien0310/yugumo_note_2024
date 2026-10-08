<?php

use App\Features\NoteType\Enums\NoteTypeEnum;
use App\Features\Search\Actions\CreateSearchIndexForNoteTitleAction;
use App\Features\Search\Models\SearchIndex;
use Tests\Factories\UserFactory;

describe(CreateSearchIndexForNoteTitleAction::class, function () {
    it('creates search index record for note title', function () {
        $user = UserFactory::new()->create();
        $note = createNote(NoteTypeEnum::SIMPLE, $user);

        // Delete any automatically generated search index so we test the action directly
        $note->searchIndex()->delete();

        $searchIndex = app(CreateSearchIndexForNoteTitleAction::class)->handle($note);

        expect($searchIndex)->toBeInstanceOf(SearchIndex::class)
            ->and($searchIndex->content)->toBe($note->title)
            ->and($searchIndex->searchable_id)->toBe($note->id);
    });
});
