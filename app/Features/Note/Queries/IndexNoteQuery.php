<?php

namespace App\Features\Note\Queries;

use App\Extendables\Core\Models\Queries\Filters\ExactFilter;
use App\Extendables\Core\Models\Queries\IndexQuery;
use App\Extendables\Core\Models\Queries\Sorts\BasicSort;
use App\Features\Note\Queries\Filters\NoteBookmarkedFilter;
use App\Features\Note\Queries\Filters\NoteKeywordFilter;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;

readonly class IndexNoteQuery
{
    public function __construct(
        private IndexQuery $indexQuery,
        private BasicSort $basicSort,
        private ExactFilter $exactFilter,
        private NoteKeywordFilter $noteKeywordFilter,
        private NoteBookmarkedFilter $noteBookmarkedFilter,
    ) {}

    public function handle(EloquentBuilder|Builder $builder): EloquentBuilder|Builder
    {
        return $this->indexQuery->handle(
            $builder,
            allowedFilters: [
                NoteFilterParamEnum::TYPE_ID->value => $this->exactFilter,
                NoteFilterParamEnum::KEYWORD->value => $this->noteKeywordFilter,
                NoteFilterParamEnum::BOOKMARKED->value => $this->noteBookmarkedFilter,
            ],
            allowedSorts: [
                NoteSortFieldEnum::ID->value => $this->basicSort,
                NoteSortFieldEnum::CREATED_AT->value => $this->basicSort,
                NoteSortFieldEnum::UPDATED_AT->value => $this->basicSort,
                NoteSortFieldEnum::TYPE_ID->value => $this->basicSort,
                NoteSortFieldEnum::TITLE->value => $this->basicSort,
            ]
        );
    }
}
