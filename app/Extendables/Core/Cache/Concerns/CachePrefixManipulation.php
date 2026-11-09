<?php

namespace App\Extendables\Core\Cache\Concerns;

use Illuminate\Support\Facades\Cache;

trait CachePrefixManipulation
{
    /**
     * {@inheritDoc}
     */
    public function bindCachePrefixToString(string $subject, string $bindingIndicator = '?'): string
    {
        $prefix = Cache::getPrefix();

        return str_replace($bindingIndicator, $prefix, $subject);
    }
}
