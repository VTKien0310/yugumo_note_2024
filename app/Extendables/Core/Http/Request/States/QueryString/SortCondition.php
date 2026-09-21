<?php

namespace App\Extendables\Core\Http\Request\States\QueryString;

use App\Extendables\Core\Utils\SortDirectionEnum;

class SortCondition
{
    public function __construct(
        public readonly string $field,
        public readonly SortDirectionEnum $direction
    ) {}
}
