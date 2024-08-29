<?php

namespace App\Extendables\Core\Http\Request\States\QueryString\JsonApi;

use App\Extendables\Core\Http\Enums\HttpRequestParamEnum;
use App\Extendables\Core\Http\Request\States\QueryString\OnlyQueryStringState;
use Illuminate\Http\Request;

class JsonApiOnlyQueryStringState implements OnlyQueryStringState
{
    /**
     * @var array[]
     */
    private readonly array $only;

    public function __construct(
        mixed $onlyRequestData,
        private readonly string $dataSeparator = ',',
        private readonly string $resourceSeparator = '.'
    ) {
        if (! empty($onlyRequestData) && is_string($onlyRequestData)) {
            $this->only = $this->processOnlyRequestData($onlyRequestData);
        } else {
            $this->only = [];
        }
    }

    private function processOnlyRequestData(string $requestData): array
    {
        $result = [];

        $requestData = explode($this->dataSeparator, $requestData);
        foreach ($requestData as $data) {
            if (substr_count($data, $this->resourceSeparator) === 1) {
                $data = explode($this->resourceSeparator, $data);
                $result[$data[0]][] = $data[1];
            }
        }

        return $result;
    }

    public function getOnly(): array
    {
        return $this->only;
    }

    public function getOnlyOfResource(string $resource, bool $returnValueWithResourcePrefix = false): array
    {
        $onlyOfResource = $this->only[$resource] ?? [];

        if (empty($onlyOfResource) || ! $returnValueWithResourcePrefix) {
            return $onlyOfResource;
        }

        return array_map(
            fn (string $value): string => "$resource.$value",
            $onlyOfResource
        );
    }
}
