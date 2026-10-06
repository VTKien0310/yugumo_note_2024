<?php

namespace App\Extendables\Core\Http\Request\States\QueryString;

interface SortQueryStringState
{
    /**
     * @return SortCondition[]
     */
    public function getSortConditions(): array;
}
