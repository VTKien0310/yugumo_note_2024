<?php

namespace App\Extendables\Core\Http\Request\States\QueryString\JsonApi;

use App\Extendables\Core\Http\Request\States\QueryString\PaginateQueryStringState;
use Illuminate\Support\Arr;

class JsonApiPaginateQueryStringState implements PaginateQueryStringState
{
    private readonly int $pageNumber;

    private readonly int $pageSize;

    public function __construct(
        mixed $paginateRequestData,
        int $defaultPageNumber = 1,
        int $defaultPageSize = 30
    ) {
        if (! empty($paginateRequestData) && is_array($paginateRequestData) && Arr::isAssoc($paginateRequestData)) {
            $this->pageNumber = $this->processRequestData($paginateRequestData, 'number', $defaultPageNumber);
            $this->pageSize = $this->processRequestData($paginateRequestData, 'size', $defaultPageSize);
        } else {
            $this->pageNumber = $defaultPageNumber;
            $this->pageSize = $defaultPageSize;
        }
    }

    private function processRequestData(array $requestData, string $requestDataField, int $defaultValue): int
    {
        if (empty($requestData[$requestDataField])) {
            return $defaultValue;
        }
        $requestDataValue = $requestData[$requestDataField];

        if (filter_var($requestDataValue, FILTER_VALIDATE_INT) !== false && (int) $requestDataValue >= 1) {
            return (int) $requestDataValue;
        }

        return $defaultValue;
    }

    /**
     * {@inheritDoc}
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * {@inheritDoc}
     */
    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }
}
