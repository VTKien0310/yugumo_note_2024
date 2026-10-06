<?php

namespace App\Extendables\Core\Http\Request\States\QueryString;

interface PaginateQueryStringState
{
    public function getPageSize(): int;

    public function getPageNumber(): int;
}
