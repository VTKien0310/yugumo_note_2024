<?php

namespace App\Extendables\Core\Http\Request\States\QueryString;

interface RelationQueryStringState
{
    public function getRelations(): array;

    public function isRelationRequested(string $relation): bool;

    public function areAllRelationsRequested(array $relations): bool;
}
