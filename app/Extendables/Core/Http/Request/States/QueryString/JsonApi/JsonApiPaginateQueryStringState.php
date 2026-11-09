<?php

namespace App\Extendables\Core\Http\Request\States\QueryString\JsonApi;

use App\Extendables\Core\Http\Constants\HttpRequestParamConstants;
use App\Extendables\Core\Http\Request\States\QueryString\PaginateQueryStringState;
use Illuminate\Support\Arr;

class JsonApiPaginateQueryStringState implements PaginateQueryStringState
{
    private readonly int $pageNumber;

    private readonly int $pageSize;

    private readonly string $cursor;

    public function __construct(
        mixed $paginateRequestData,
        int $defaultPageNumber = 1,
        int $defaultPageSize = 20
    ) {
        if (! empty($paginateRequestData) && is_array($paginateRequestData) && Arr::isAssoc($paginateRequestData)) {
            $this->pageNumber = $this->getIntValueFromRequestData($paginateRequestData, HttpRequestParamConstants::PAGINATE_NUMBER, $defaultPageNumber);
            $this->pageSize = $this->getIntValueFromRequestData($paginateRequestData, HttpRequestParamConstants::PAGINATE_SIZE, $defaultPageSize);
            $this->cursor = $this->getStringValueFromRequestData($paginateRequestData, HttpRequestParamConstants::PAGINATE_CURSOR, '');
        } else {
            $this->pageNumber = $defaultPageNumber;
            $this->pageSize = $defaultPageSize;
            $this->cursor = '';
        }
    }

    private function getIntValueFromRequestData(array $requestData, string $requestDataField, int $defaultValue): int
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

    private function getStringValueFromRequestData(array $requestData, string $requestDataField, string $defaultValue): string
    {
        if (empty($requestData[$requestDataField])) {
            return $defaultValue;
        }
        $requestDataValue = $requestData[$requestDataField];

        if (is_string($requestDataValue)) {
            return strip_tags($requestDataValue);
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

    /**
     * {@inheritDoc}
     */
    public function getCursor(): string
    {
        return $this->cursor;
    }
}
