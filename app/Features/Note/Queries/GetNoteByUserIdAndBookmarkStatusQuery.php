<?php

namespace App\Features\Note\Queries;

use App\Extendables\Core\Utils\BoolIntValueEnum;
use App\Features\Note\Models\Note;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;

class GetNoteByUserIdAndBookmarkStatusQuery
{
    public function handle(EloquentBuilder|Builder $builder, string $userId, bool $bookmarked): EloquentBuilder|Builder
    {
        return $builder
            ->where(Note::USER_ID, $userId)
            ->where(Note::BOOKMARKED, $bookmarked ? BoolIntValueEnum::TRUE : BoolIntValueEnum::FALSE);
    }
}
