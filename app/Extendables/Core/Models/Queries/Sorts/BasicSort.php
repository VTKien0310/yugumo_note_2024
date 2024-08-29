<?php

namespace App\Extendables\Core\Models\Queries\Sorts;

use App\Extendables\Core\Http\Request\States\QueryString\SortCondition;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;

class BasicSort extends Sort
{

    /**
     * @inheritDoc
     */
    function handle(EloquentBuilder|Builder $builder, SortCondition $sortCondition): Builder|EloquentBuilder
    {
        $sortColumn = $this->targetColumn;

        if (empty($sortColumn)) {
            $sortColumn = $sortCondition->field;
        }

        if ($builder instanceof EloquentBuilder) {
            $sortColumn = $builder->qualifyColumn($sortColumn);
        }

        return $builder->orderBy($sortColumn, $sortCondition->direction->value);
    }
}
