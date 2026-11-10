<?php

namespace App\Extendables\Core\Models\Queries\Filters;

class GreaterOrEqualFilter extends ComparisonFilter
{
    protected function operator(): string
    {
        return '>=';
    }
}
