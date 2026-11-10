<?php

namespace App\Extendables\Core\Models\Queries\Filters;

class GreaterFilter extends ComparisonFilter
{
    protected function operator(): string
    {
        return '>';
    }
}
