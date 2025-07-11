<?php

namespace App\Features\Note\Queries\Filters;

use App\Extendables\Core\Http\Request\States\QueryString\FilterCondition;
use App\Extendables\Core\Models\Queries\Filters\Filter;
use App\Features\Note\Models\Note;
use App\Features\Search\Models\SearchIndex;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;

class NoteKeywordFilter extends Filter
{
    public function handle(EloquentBuilder|Builder $builder, FilterCondition $filterCondition): Builder|EloquentBuilder
    {
        $contentColumn = SearchIndex::qualifiedColumn(SearchIndex::CONTENT);
        $keywordCondition = '%'.$filterCondition->condition.'%';

        $matchContentUsingFullText = fn (EloquentBuilder $searchIndexQuery) => $searchIndexQuery->whereLike(
            $contentColumn,
            $keywordCondition
        );

        return $builder->whereHas(Note::RELATION_FULL_TEXT_SEARCHABLE_CONTENTS, $matchContentUsingFullText);
    }
}
