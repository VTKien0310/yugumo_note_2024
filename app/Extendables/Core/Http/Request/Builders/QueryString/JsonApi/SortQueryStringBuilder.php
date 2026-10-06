<?php

namespace App\Extendables\Core\Http\Request\Builders\QueryString\JsonApi;

use App\Extendables\Core\Http\Request\States\QueryString\SortCondition;
use App\Extendables\Core\Utils\SortDirectionEnum;
use Illuminate\Support\Collection;

class SortQueryStringBuilder
{
    /**
     * @param  SortCondition[]|Collection<int, SortCondition>  $sortConditions
     */
    public function handle(array|Collection $sortConditions): string
    {
        if (! $sortConditions instanceof Collection) {
            $sortConditions = collect($sortConditions);
        }

        return $sortConditions
            ->map(function (SortCondition $sortCondition): string {
                $sortDirection = $sortCondition->direction === SortDirectionEnum::ASC ? '' : '-';

                return $sortDirection.$sortCondition->field;
            })
            ->implode(',');
    }
}
