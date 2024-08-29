<?php

namespace App\Extendables\Core\Http\Request\States\QueryString\JsonApi;

use App\Extendables\Core\Http\Enums\HttpRequestParamEnum;
use App\Extendables\Core\Http\Request\States\QueryString\FilterCondition;
use App\Extendables\Core\Http\Request\States\QueryString\FilterQueryStringState;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class JsonApiFilterQueryStringState implements FilterQueryStringState
{
    /**
     * @var FilterCondition[]
     */
    private readonly array $filters;

    public function __construct(
        mixed $filterRequestData,
        private readonly string $dataSeparator = ','
    ) {
        if (
            ! empty($filterRequestData)
            && is_array($filterRequestData)
            && Arr::isAssoc($filterRequestData)
        ) {
            $filterConditions = [];
            foreach ($filterRequestData as $field => $value) {
                if (! is_null($value)) {
                    $filterConditions[] = new FilterCondition($field, $this->processRequestFilterValue($value));
                }
            }
            $this->filters = $filterConditions;
        } else {
            $this->filters = [];
        }
    }

    /**
     * @param  string  $value
     * @return string|bool|array|null
     */
    private function processRequestFilterValue(string $value): string|bool|array|null
    {
        if ($value === 'null') {
            return null;
        }

        if ($value === 'true') {
            return true;
        }

        if ($value === 'false') {
            return false;
        }

        if (str_contains($value, $this->dataSeparator)) {
            return explode($this->dataSeparator, $value);
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    function getFilterConditions(): array
    {
        return $this->filters;
    }
}
