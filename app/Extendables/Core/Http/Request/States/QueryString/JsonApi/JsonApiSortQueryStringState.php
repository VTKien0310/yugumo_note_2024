<?php

namespace App\Extendables\Core\Http\Request\States\QueryString\JsonApi;

use App\Extendables\Core\Http\Request\States\QueryString\SortCondition;
use App\Extendables\Core\Http\Request\States\QueryString\SortQueryStringState;
use App\Extendables\Core\Utils\SortDirectionEnum;

class JsonApiSortQueryStringState implements SortQueryStringState
{
    /**
     * @var SortCondition[]
     */
    private readonly array $sorts;

    public function __construct(
        mixed $sortRequestData,
        private readonly string $dataSeparator = ','
    ) {
        if (! empty($sortRequestData) && is_string($sortRequestData)) {
            $this->sorts = $this->processRequestData($sortRequestData);
        } else {
            $this->sorts = [];
        }
    }

    /**
     * @return SortCondition[]
     */
    private function processRequestData(string $requestData): array
    {
        $requestData = explode($this->dataSeparator, $requestData);

        return array_map(
            fn (string $condition): SortCondition => $this->processCondition($condition),
            $requestData
        );
    }

    private function processCondition(string $condition): SortCondition
    {
        if (str_starts_with($condition, '-')) {
            return new SortCondition(substr($condition, 1), SortDirectionEnum::DESC);
        }

        return new SortCondition($condition, SortDirectionEnum::ASC);
    }

    /**
     * {@inheritDoc}
     */
    public function getSortConditions(): array
    {
        return $this->sorts;
    }
}
