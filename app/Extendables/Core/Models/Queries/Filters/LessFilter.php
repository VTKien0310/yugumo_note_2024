<?php

namespace App\Extendables\Core\Models\Queries\Filters;

class LessFilter extends ComparisonFilter
{
    protected function operator(): string
    {
        return '<';
    }
}
