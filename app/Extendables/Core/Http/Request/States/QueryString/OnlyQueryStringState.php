<?php

namespace App\Extendables\Core\Http\Request\States\QueryString;

interface OnlyQueryStringState
{
    public function getOnly(): array;

    public function getOnlyOfResource(string $resource, bool $returnValueWithResourcePrefix = false): array;
}
