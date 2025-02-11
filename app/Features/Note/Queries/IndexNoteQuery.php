<?php

namespace App\Features\Note\Queries;

use App\Extendables\Core\Models\Queries\Filters\ExactFilter;
use App\Extendables\Core\Models\Queries\IndexQuery;
use App\Extendables\Core\Models\Queries\Sorts\BasicSort;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;

readonly class IndexNoteQuery
{
    public function __construct(
        private IndexQuery $indexQuery,
        private BasicSort $basicSort,
        private ExactFilter $exactFilter,
    ) {}

    public function handle(EloquentBuilder|Builder $builder): EloquentBuilder|Builder
    {
        return $this->indexQuery->handle(
            $builder,
            allowedFilters: [
                'type' => $this->exactFilter,
            ],
            allowedSorts: [
                'id' => $this->basicSort,
                'created_at' => $this->basicSort,
                'updated_at' => $this->basicSort,
            ]
        );
    }
}
