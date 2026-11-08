<?php

namespace Tests\Factories;

use App\Features\Note\Models\Note;
use App\Features\Search\Models\SearchIndex;
use Tests\TestFactory;

class SearchIndexFactory extends TestFactory
{
    /**
     * @param  array<string, mixed>  $extra
     */
    public function create(array $extra = []): SearchIndex
    {
        $note = isset($extra[SearchIndex::NOTE_ID])
            ? Note::query()->findOrFail($extra[SearchIndex::NOTE_ID])
            : NoteFactory::new()->create();

        return SearchIndex::query()->create(array_merge([
            SearchIndex::NOTE_ID => $note->id,
            SearchIndex::CONTENT => 'Searchable content',
            SearchIndex::SEARCHABLE_ID => $note->id,
            SearchIndex::SEARCHABLE_TYPE => Note::morphType(),
        ], $extra));
    }
}
