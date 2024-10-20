<?php

namespace App\Features\Note\Actions;

use App\Extendables\Core\Utils\SortDirectionEnum;
use App\Features\Note\Models\ChecklistNoteContent;
use App\Features\Note\Models\Note;
use Illuminate\Database\Eloquent\Collection;

class FindChecklistContentOfNoteForDisplayAction
{
    public function handle(Note $note): Collection
    {
        return $note
            ->checklistContent()
            ->orderBy(ChecklistNoteContent::IS_COMPLETED, SortDirectionEnum::ASC->value)
            ->orderBy(ChecklistNoteContent::ID, SortDirectionEnum::DESC->value)
            ->get();
    }
}
