<?php

namespace App\Extendables\Core\Models\Queries\Filters;

use App\Extendables\Core\Http\Request\States\QueryString\FilterCondition;
use Illuminate\Database\Eloquent\Builder;

class CreatedFromFilter extends Filter
{
    /**
     * {@inheritDoc}
     */
    public function handle(
        Builder|\Illuminate\Database\Query\Builder $builder,
        FilterCondition $filterCondition
    ): \Illuminate\Database\Query\Builder|Builder {
        return $builder->where('created_at', '>=', $filterCondition->condition);
    }
}
