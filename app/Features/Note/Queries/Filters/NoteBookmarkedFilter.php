<?php

namespace App\Features\Note\Queries\Filters;

use App\Extendables\Core\Http\Request\States\QueryString\FilterCondition;
use App\Extendables\Core\Models\Queries\Filters\Filter;
use App\Extendables\Core\Utils\BoolIntValueEnum;
use App\Features\Note\Models\Note;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;

class NoteBookmarkedFilter extends Filter
{
    public function handle(EloquentBuilder|Builder $builder, FilterCondition $filterCondition): Builder|EloquentBuilder
    {
        return $builder->where(Note::BOOKMARKED, BoolIntValueEnum::TRUE);
    }
}
