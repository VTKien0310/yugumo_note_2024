<?php

namespace App\Extendables\Core\Models\Queries\Filters;

use App\Extendables\Core\Http\Exception\InvalidFilterQueryStringException;
use App\Extendables\Core\Http\Request\States\QueryString\FilterCondition;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;

/**
 * For a filter restricting a column to a range (`filter[x]=min,max`). The bounds are passed through as-is, so numbers
 * and dates both work.
 */
abstract class BetweenFilter extends Filter
{
    /**
     * Whether the bounds themselves match.
     */
    abstract protected function isInclusive(): bool;

    /**
     * {@inheritDoc}
     *
     * @throws InvalidFilterQueryStringException when the condition is not exactly two non-blank bounds
     */
    public function handle(
        EloquentBuilder|Builder $builder,
        FilterCondition $filterCondition
    ): Builder|EloquentBuilder {
        $bounds = $filterCondition->condition;

        if (
            ! is_array($bounds)
            || count($bounds) !== 2
            || array_filter($bounds, fn (mixed $bound): bool => ! is_string($bound) || trim($bound) === '') !== []
        ) {
            throw new InvalidFilterQueryStringException($filterCondition->field);
        }

        [$min, $max] = array_values($bounds);
        $column = $this->resolveColumn($builder, $filterCondition);

        return $this->isInclusive()
            ? $builder->whereBetween($column, [$min, $max])
            : $builder->where($column, '>', $min)->where($column, '<', $max);
    }
}
