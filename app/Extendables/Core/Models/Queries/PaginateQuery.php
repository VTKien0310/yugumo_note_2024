<?php

namespace App\Extendables\Core\Models\Queries;

use App\Extendables\Core\Http\Request\States\QueryString\PaginateQueryStringState;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;

class PaginateQuery
{
    const MAX_PAGE_SIZE = 2000;

    public function __construct(
        private readonly PaginateQueryStringState $paginateQueryStringState
    ) {}

    public function handle(
        EloquentBuilder|Builder $builder,
        ?int $pageSize = null,
        ?int $pageNumber = null
    ): LengthAwarePaginator {
        if (empty($pageSize)) {
            $pageSize = $this->paginateQueryStringState->getPageSize();
        }
        $pageSize = min($pageSize, self::MAX_PAGE_SIZE);

        if (empty($pageNumber)) {
            $pageNumber = $this->paginateQueryStringState->getPageNumber();
        }

        return $builder->paginate(
            perPage: $pageSize,
            pageName: 'x-secret-page',      // needed to override Laravel default pagination behavior
            page: $pageNumber,
        );
    }
}
