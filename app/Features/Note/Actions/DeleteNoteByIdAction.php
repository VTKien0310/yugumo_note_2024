<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Models\ChecklistNoteContent;
use App\Features\Note\Models\Note;
use App\Features\Note\Models\TextNoteContent;
use App\Features\Search\Models\SearchIndex;
use Illuminate\Support\Facades\DB;

class DeleteNoteByIdAction
{
    public function handle(string $id): void
    {
        DB::transaction(function () use ($id): void {
            Note::query()->where(Note::ID, $id)->delete();

            TextNoteContent::query()->where(TextNoteContent::NOTE_ID, $id)->delete();

            ChecklistNoteContent::query()->where(ChecklistNoteContent::NOTE_ID, $id)->delete();

            SearchIndex::query()->where(SearchIndex::NOTE_ID, $id)->delete();
        });
    }
}
