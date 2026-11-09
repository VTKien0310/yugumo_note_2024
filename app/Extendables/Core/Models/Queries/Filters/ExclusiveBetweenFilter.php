<?php

namespace App\Extendables\Core\Models\Queries\Filters;

class ExclusiveBetweenFilter extends BetweenFilter
{
    protected function isInclusive(): bool
    {
        return false;
    }
}
