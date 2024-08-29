<?php

namespace App\Extendables\Core\Models\Queries;

use App\Extendables\Core\Http\Enums\HttpRequestParamEnum;
use App\Extendables\Core\Http\Request\States\QueryString\FilterQueryStringState;
use App\Extendables\Core\Http\Request\States\QueryString\OnlyQueryStringState;
use App\Extendables\Core\Http\Request\States\QueryString\RelationQueryStringState;
use App\Extendables\Core\Http\Request\States\QueryString\SortQueryStringState;
use App\Extendables\Core\Models\Queries\Filters\Filter;
use App\Extendables\Core\Models\Queries\Sorts\Sort;
use App\Extendables\Core\Models\Queries\ValueObjects\SelectByOnlyQueryStringValueObject;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class IndexQuery
{
    public function __construct(
        private readonly FilterQueryStringState $filterQueryStringState,
        private readonly SortQueryStringState $sortQueryStringState,
        private readonly OnlyQueryStringState $onlyQueryStringState,
        private readonly RelationQueryStringState $relationQueryStringState
    ) {
    }

    /**
     * @param  Filter[]  $allowedFilters
     * @param  Sort[]  $allowedSorts
     */
    public function handle(
        Builder|EloquentBuilder $builder,
        array $allowedFilters = [],
        array $allowedSorts = [],
        SelectByOnlyQueryStringValueObject $selectByOnlyQueryStringValueObject = null,
        array $eagerLoadableCounts = [],
        array $eagerLoadableRelations = []
    ): Builder|EloquentBuilder {
        $builder = $this->applyFilter($builder, $allowedFilters);
        $builder = $this->applySortPriority($builder);
        $builder = $this->applySort($builder, $allowedSorts);

        if ($selectByOnlyQueryStringValueObject) {
            $builder = $this->applyScopedSelect($builder, $selectByOnlyQueryStringValueObject);
        }

        if ($builder instanceof EloquentBuilder) {
            $builder = $this->eagerLoadCounts($builder, $eagerLoadableCounts);
            $builder = $this->eagerLoadRelations($builder, $eagerLoadableRelations);
        }

        return $builder;
    }

    private function applyFilter(Builder|EloquentBuilder $builder, array $allowedFilters): EloquentBuilder|Builder
    {
        $filterConditions = $this->filterQueryStringState->getFilterConditions();
        foreach ($filterConditions as $condition) {
            if (array_key_exists($condition->field, $allowedFilters)) {
                $filter = $allowedFilters[$condition->field];
                $builder = $filter->handle($builder, $condition);
            }
        }

        return $builder;
    }

    private function applySort(Builder|EloquentBuilder $builder, array $allowedSorts): EloquentBuilder|Builder
    {
        $sortConditions = $this->sortQueryStringState->getSortConditions();
        foreach ($sortConditions as $condition) {
            if (array_key_exists($condition->field, $allowedSorts)) {
                $sort = $allowedSorts[$condition->field];
                $builder = $sort->handle($builder, $condition);
            }
        }

        return $builder;
    }

    private function applySortPriority(Builder|EloquentBuilder $builder): EloquentBuilder|Builder
    {
        $sortPriority = request()->query(HttpRequestParamEnum::SORT_PRIORITY->value);
        if (
            ! empty($sortPriority)
            && (is_numeric($sortPriority) || Str::isUuid($sortPriority) || Str::isUlid($sortPriority))
        ) {
            $builder = $builder->orderByRaw('id = ? DESC', [$sortPriority]);
        }

        return $builder;
    }

    private function applyScopedSelect(
        Builder|EloquentBuilder $builder,
        SelectByOnlyQueryStringValueObject $selectByOnlyQueryStringValueObject
    ): EloquentBuilder|Builder {
        $onlySelect = $this->onlyQueryStringState->getOnlyOfResource($selectByOnlyQueryStringValueObject->resourceName);

        $selectColumns = empty($onlySelect)
            ? $selectByOnlyQueryStringValueObject->defaultSelect
            : $this->handleScopedSelect($selectByOnlyQueryStringValueObject, $onlySelect);

        $selectColumns = $this->handleForcedSelect($selectByOnlyQueryStringValueObject, $selectColumns);

        $selectColumns = $this->isSelectAllColumns($selectColumns)
            ? $selectColumns
            : $this->filterInvalidSelects($selectColumns, $selectByOnlyQueryStringValueObject);

        $selectColumns = collect($selectColumns)
            ->unique()
            ->map(fn ($column): string => "$selectByOnlyQueryStringValueObject->table.$column")
            ->toArray();

        return $builder->select($selectColumns);
    }

    private function filterInvalidSelects(
        array $selects,
        SelectByOnlyQueryStringValueObject $selectByOnlyQueryStringValueObject
    ): array {
        $validSelects = Schema::getColumnListing($selectByOnlyQueryStringValueObject->table);

        return array_intersect($selects, $validSelects);
    }

    private function isSelectAllColumns(array $selects): bool
    {
        return in_array('*', $selects);
    }

    private function handleScopedSelect(
        SelectByOnlyQueryStringValueObject $selectByOnlyQueryStringValueObject,
        array $onlySelect
    ): array {
        if ($selectByOnlyQueryStringValueObject->isDefaultSelectAll()) {
            return $onlySelect;
        }

        return array_intersect($selectByOnlyQueryStringValueObject->defaultSelect, $onlySelect);
    }

    private function handleForcedSelect(
        SelectByOnlyQueryStringValueObject $selectByOnlyQueryStringValueObject,
        array $currentSelect
    ): array {
        if ($this->isSelectAllColumns($currentSelect)) {
            return ['*'];
        }

        return array_merge($currentSelect, $selectByOnlyQueryStringValueObject->forcedSelect);
    }

    private function eagerLoadCounts(EloquentBuilder $builder, array $eagerLoadableCounts): EloquentBuilder
    {
        $relations = $this->relationQueryStringState->getRelations();

        $eagerLoadedCount = [];
        foreach ($relations as $relation) {
            if (! array_key_exists($relation, $eagerLoadableCounts)) {
                continue;
            }

            if (is_array($eagerLoadableCounts[$relation])) {
                $eagerLoadedCount = array_merge($eagerLoadedCount, $eagerLoadableCounts[$relation]);
            } else {
                $eagerLoadedCount[] = $eagerLoadableCounts[$relation];
            }
        }

        return $builder->withCount($eagerLoadedCount);
    }

    private function eagerLoadRelations(EloquentBuilder $builder, array $eagerLoadableRelations): EloquentBuilder
    {
        $relations = $this->relationQueryStringState->getRelations();

        $eagerLoadedRelations = [];
        foreach ($relations as $relation) {
            if (! array_key_exists($relation, $eagerLoadableRelations)) {
                continue;
            }

            if (is_array($eagerLoadableRelations[$relation])) {
                $eagerLoadedRelations = array_merge($eagerLoadedRelations, $eagerLoadableRelations[$relation]);
            } else {
                $eagerLoadedRelations[] = $eagerLoadableRelations[$relation];
            }
        }

        return $builder->with($eagerLoadedRelations);
    }
}
