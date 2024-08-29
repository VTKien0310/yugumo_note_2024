<?php

namespace App\Extendables\Core\Models\Queries\Sorts;

use App\Extendables\Core\Http\Request\States\QueryString\SortCondition;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;

abstract class Sort
{
    /**
     * @var string|null
     */
    protected ?string $targetColumn = null;

    /**
     * @param  string|null  $targetColumn
     * @return Sort
     */
    public function setTargetColumn(?string $targetColumn): Sort
    {
        $this->targetColumn = $targetColumn;
        return $this;
    }

    /**
     * @param  EloquentBuilder|Builder  $builder
     * @param  SortCondition  $sortCondition
     * @return Builder|EloquentBuilder
     */
    abstract function handle(EloquentBuilder|Builder $builder, SortCondition $sortCondition): Builder|EloquentBuilder;
}
