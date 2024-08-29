<?php

namespace App\Extendables\Core\Http\Request\States\QueryString;

class FilterCondition
{
    /**
     * @param  string  $field
     * @param  string|bool|array|null  $condition
     */
    function __construct(
        public readonly string $field,
        public readonly string|bool|array|null $condition
    ) {
    }
}
