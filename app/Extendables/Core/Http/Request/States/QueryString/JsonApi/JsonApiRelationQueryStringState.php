<?php

namespace App\Extendables\Core\Http\Request\States\QueryString\JsonApi;

use App\Extendables\Core\Http\Enums\HttpRequestParamEnum;
use App\Extendables\Core\Http\Request\States\QueryString\RelationQueryStringState;
use Illuminate\Http\Request;

class JsonApiRelationQueryStringState implements RelationQueryStringState
{
    /**
     * @var string[]
     */
    private readonly array $includes;

    public function __construct(
        mixed $includeRequestData,
        private readonly string $dataSeparator = ','
    ) {
        if (! empty($includeRequestData) && is_string($includeRequestData)) {
            $this->includes = explode($this->dataSeparator, $includeRequestData);
        } else {
            $this->includes = [];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isRelationRequested(string $relation): bool
    {
        return in_array($relation, $this->includes);
    }

    /**
     * {@inheritDoc}
     */
    public function getRelations(): array
    {
        return $this->includes;
    }

    /**
     * {@inheritDoc}
     */
    public function areAllRelationsRequested(array $relations): bool
    {
        return empty(array_diff($relations, $this->includes));
    }
}
