<?php

namespace App\Extendables\Core\Http\Request\States\QueryString;

interface FilterQueryStringState
{
    /**
     * @return FilterCondition[]
     */
    public function getFilterConditions(): array;
}
