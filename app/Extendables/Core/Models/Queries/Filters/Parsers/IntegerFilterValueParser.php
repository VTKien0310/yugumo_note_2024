<?php

namespace App\Extendables\Core\Models\Queries\Filters\Parsers;

use App\Extendables\Core\Http\Request\States\QueryString\FilterCondition;

/**
 * For a filter taking one integer (`filter[x]=N`). `JsonApiFilterQueryStringState` hands a filter a string, a list of
 * strings (`a,b`), a bool or null; only a single non-negative integer is a usable value.
 */
trait IntegerFilterValueParser
{
    /**
     * The condition's value when it is a single non-negative integer, else null.
     */
    private function integerValue(FilterCondition $filterCondition): ?int
    {
        $value = $filterCondition->condition;

        return is_string($value) && ctype_digit($value) ? (int) $value : null;
    }
}
