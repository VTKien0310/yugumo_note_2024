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
        $content = SearchIndex::qualifiedColumn(SearchIndex::CONTENT);

        $matchContentUsingFullText = fn (EloquentBuilder $searchIndexQuery) => $searchIndexQuery->whereRaw(
            "$content &@~ ?",
            [(string) $filterCondition->condition]
        );

        return $builder->whereHas(Note::RELATION_SEARCH_INDEX, $matchContentUsingFullText);
    }
}
