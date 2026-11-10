<?php

namespace App\Extendables\Core\Models\Queries\Sorts;

use App\Extendables\Core\Http\Request\States\QueryString\SortCondition;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;

abstract class Sort
{
    protected ?string $targetColumn = null;

    public function setTargetColumn(?string $targetColumn): Sort
    {
        $this->targetColumn = $targetColumn;

        return $this;
    }

    abstract public function handle(EloquentBuilder|Builder $builder, SortCondition $sortCondition): Builder|EloquentBuilder;
}
