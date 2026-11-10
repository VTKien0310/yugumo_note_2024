<?php

namespace App\Extendables\Core\Models\Queries\Filters\Parsers;

use App\Extendables\Core\Http\Request\States\QueryString\FilterCondition;

/**
 * For a filter taking one or several integers (`filter[x]=1` or `filter[x]=1,3`). `JsonApiFilterQueryStringState`
 * hands a filter a string, a list of strings, a bool or null; anything that is not a non-negative integer is dropped.
 */
trait IntegerFilterValuesParser
{
    /**
     * The condition's non-negative integers, without duplicates and, when given, limited to `$allowedValues` (e.g.
     * `array_column(SomeEnum::cases(), 'value')`).
     *
     * @param  list<int>|null  $allowedValues
     * @return array<int, int>
     */
    private function integerValues(FilterCondition $filterCondition, ?array $allowedValues = null): array
    {
        $condition = $filterCondition->condition;

        return collect(is_array($condition) ? $condition : [$condition])
            ->filter(fn (mixed $value): bool => is_string($value) && ctype_digit($value))
            ->map(fn (string $value): int => (int) $value)
            ->filter(fn (int $value): bool => $allowedValues === null || in_array($value, $allowedValues, true))
            ->unique()
            ->values()
            ->all();
    }
}
