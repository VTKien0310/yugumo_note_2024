<?php

namespace App\Extendables\Core\Models\Queries\Filters;

use App\Extendables\Core\Http\Request\States\QueryString\FilterCondition;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;

class ExactFilter extends Filter
{
    /**
     * @inheritDoc
     */
    function handle(
        EloquentBuilder|Builder $builder,
        FilterCondition $filterCondition
    ): Builder|EloquentBuilder {
        $filterColumn = $this->targetColumn;

        if (empty($filterColumn)) {
            $filterColumn = $filterCondition->field;
        }

        if ($builder instanceof EloquentBuilder) {
            $filterColumn = $builder->qualifyColumn($filterColumn);
        }

        if (is_array($filterCondition->condition)) {
            return $builder->whereIn($filterColumn, $filterCondition->condition);
        }

        return $builder->where($filterColumn, $filterCondition->condition);
    }
}
