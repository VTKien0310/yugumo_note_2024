<?php

namespace App\Extendables\Core\Http\Request\States\QueryString;

interface RelationQueryStringState
{
    /**
     * @return array
     */
    function getRelations(): array;

    /**
     * @param  string  $relation
     * @return bool
     */
    function isRelationRequested(string $relation): bool;

    /**
     * @param  array  $relations
     * @return bool
     */
    function areAllRelationsRequested(array $relations): bool;
}
