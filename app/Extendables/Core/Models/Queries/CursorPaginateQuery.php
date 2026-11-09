<?php

namespace App\Extendables\Core\Models\Queries;

use App\Extendables\Core\Http\Request\States\QueryString\PaginateQueryStringState;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\Cursor;

class CursorPaginateQuery
{
    const int MAX_PAGE_SIZE = 2000;

    public function __construct(
        private readonly PaginateQueryStringState $paginateQueryStringState
    ) {}

    public function handle(
        EloquentBuilder|Builder $builder,
        ?int $pageSize = null,
        Cursor|string|null $cursor = null
    ): CursorPaginator {
        if (empty($pageSize)) {
            $pageSize = $this->paginateQueryStringState->getPageSize();
        }
        $pageSize = min($pageSize, self::MAX_PAGE_SIZE);

        if (empty($cursor)) {
            $cursor = $this->paginateQueryStringState->getCursor() ?: null;
        }

        return $builder->cursorPaginate(
            perPage: $pageSize,
            cursorName: 'page[cursor]',
            cursor: $cursor
        );
    }
}
