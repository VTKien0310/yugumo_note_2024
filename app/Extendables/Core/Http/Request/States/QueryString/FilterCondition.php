<?php

namespace App\Extendables\Core\Http\Request\States\QueryString;

class FilterCondition
{
    public function __construct(
        public readonly string $field,
        public readonly string|bool|array|null $condition
    ) {}
}
