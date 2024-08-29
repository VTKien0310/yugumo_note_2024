<?php

namespace App\Extendables\Core\Http\Request\States\QueryString;

interface FilterQueryStringState
{
    /**
     * @return FilterCondition[]
     */
    function getFilterConditions(): array;
}
