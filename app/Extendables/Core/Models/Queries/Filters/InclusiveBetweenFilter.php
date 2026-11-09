<?php

namespace App\Extendables\Core\Models\Queries\Filters;

class InclusiveBetweenFilter extends BetweenFilter
{
    protected function isInclusive(): bool
    {
        return true;
    }
}
