<?php

namespace App\Extendables\Core\Models\Queries\Filters;

class LessOrEqualFilter extends ComparisonFilter
{
    protected function operator(): string
    {
        return '<=';
    }
}
