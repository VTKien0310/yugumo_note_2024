<?php

namespace App\Extendables\Core\Http\Request\States\QueryString;

interface PaginateQueryStringState
{
    /**
     * @return int
     */
    function getPageSize(): int;

    /**
     * @return int
     */
    function getPageNumber(): int;
}
