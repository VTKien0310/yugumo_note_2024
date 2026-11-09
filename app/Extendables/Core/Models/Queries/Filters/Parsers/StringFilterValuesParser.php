<?php

namespace App\Extendables\Core\Models\Queries\Filters\Parsers;

use App\Extendables\Core\Http\Request\States\QueryString\FilterCondition;

/**
 * For a filter taking one or several strings, e.g. codes (`filter[x]=A` or `filter[x]=A,B`).
 * `JsonApiFilterQueryStringState` hands a filter a string, a list of strings, a bool or null; blank values and the
 * bool / null literals are dropped.
 */
trait StringFilterValuesParser
{
    /**
     * The condition's non-blank strings, trimmed and without duplicates.
     *
     * @return array<int, string>
     */
    private function stringValues(FilterCondition $filterCondition): array
    {
        $condition = $filterCondition->condition;

        return collect(is_array($condition) ? $condition : [$condition])
            ->filter(fn (mixed $value): bool => is_string($value) && trim($value) !== '')
            ->map(fn (string $value): string => trim($value))
            ->unique()
            ->values()
            ->all();
    }
}
