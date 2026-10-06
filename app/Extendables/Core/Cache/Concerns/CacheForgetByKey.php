<?php

namespace App\Extendables\Core\Cache\Concerns;

use Illuminate\Support\Facades\Cache;

trait CacheForgetByKey
{
    /**
     * {@inheritDoc}
     */
    public function forgetMultiCacheByKey(array $keys): void
    {
        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
}
