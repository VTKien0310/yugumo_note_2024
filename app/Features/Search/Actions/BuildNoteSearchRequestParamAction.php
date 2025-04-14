<?php

namespace App\Features\Search\Actions;

use App\Extendables\Core\Http\Enums\HttpRequestParamEnum;
use App\Extendables\Core\Http\Request\Builders\QueryString\JsonApi\SortQueryStringBuilder;
use App\Extendables\Core\Http\Request\States\QueryString\SortCondition;
use App\Extendables\Core\Utils\SortDirectionEnum;
use App\Features\Note\Queries\NoteSortFieldEnum;

readonly class BuildNoteSearchRequestParamAction
{
    public function __construct(
        private SortQueryStringBuilder $sortQueryStringBuilder
    ) {}

    public function handle(
        array $filterConditions = [],
        array $sortConditions = [],
        int $pageNumber = 1,
        int $pageSize = 20
    ): array {
        return [
            HttpRequestParamEnum::PAGINATE->value => [
                HttpRequestParamEnum::PAGE_SIZE->value => $pageSize,
                HttpRequestParamEnum::PAGE_NUMBER->value => $pageNumber,
            ],
            HttpRequestParamEnum::SORT->value => $this->buildSortParam($sortConditions),
            HttpRequestParamEnum::FILTER->value => $filterConditions,
        ];
    }

    private function buildSortParam(array $sortConditions): string
    {
        // default to sort by "updated_at" in descending order
        if (empty($sortConditions)) {
            $sortConditions[] = new SortCondition(
                field: NoteSortFieldEnum::UPDATED_AT->value,
                direction: SortDirectionEnum::DESC
            );
        }

        // append default "id" sort to provide sorting consistency
        $sortConditions[] = new SortCondition(
            field: NoteSortFieldEnum::ID->value,
            direction: SortDirectionEnum::ASC
        );

        return $this->sortQueryStringBuilder->handle($sortConditions);
    }
}
