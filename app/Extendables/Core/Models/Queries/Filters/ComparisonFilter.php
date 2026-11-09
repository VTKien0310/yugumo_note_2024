<?php

namespace App\Extendables\Core\Models\Queries\Filters;

use App\Extendables\Core\Http\Exception\InvalidFilterQueryStringException;
use App\Extendables\Core\Http\Request\States\QueryString\FilterCondition;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;

/**
 * For a filter comparing a column with one value (`filter[x]=N`). The value is passed through as-is, so numbers and
 * dates both work.
 */
abstract class ComparisonFilter extends Filter
{
    /**
     * The SQL comparison operator. Must be a hard-coded constant, never user input.
     */
    abstract protected function operator(): string;

    /**
     * {@inheritDoc}
     *
     * @throws InvalidFilterQueryStringException when the condition is not a single non-blank value
     */
    public function handle(
        EloquentBuilder|Builder $builder,
        FilterCondition $filterCondition
    ): Builder|EloquentBuilder {
        $value = $filterCondition->condition;

        if (! is_string($value) || trim($value) === '') {
            throw new InvalidFilterQueryStringException($filterCondition->field);
        }

        return $builder->where($this->resolveColumn($builder, $filterCondition), $this->operator(), $value);
    }
}
