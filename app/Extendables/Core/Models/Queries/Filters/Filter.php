<?php

namespace App\Extendables\Core\Models\Queries\Filters;

use App\Extendables\Core\Http\Request\States\QueryString\FilterCondition;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;

abstract class Filter
{
    /**
     * No definite usage. Feel free to use it as you want to customize the filter behavior
     *
     * @var string|null
     */
    protected ?string $targetColumn = null;

    /**
     * @param  string|null  $targetColumn
     * @return Filter
     */
    public function setTargetColumn(?string $targetColumn): Filter
    {
        $this->targetColumn = $targetColumn;
        return $this;
    }

    /**
     * @param  EloquentBuilder|Builder  $builder
     * @param  FilterCondition  $filterCondition
     * @return Builder|EloquentBuilder
     */
    abstract function handle(
        EloquentBuilder|Builder $builder,
        FilterCondition $filterCondition
    ): Builder|EloquentBuilder;
}
