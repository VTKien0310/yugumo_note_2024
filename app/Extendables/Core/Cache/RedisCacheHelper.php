<?php

namespace App\Extendables\Core\Cache;

use App\Extendables\Core\Cache\Concerns\CacheForgetByKey;
use App\Extendables\Core\Cache\Concerns\CacheKeyGeneration;
use App\Extendables\Core\Cache\Concerns\CachePrefixManipulation;
use Illuminate\Support\Facades\Cache;

class RedisCacheHelper implements CacheHelper
{
    use CacheForgetByKey,
        CacheKeyGeneration,
        CachePrefixManipulation;

    /**
     * {@inheritDoc}
     */
    public function getAllKeysWithPattern(string $pattern): array
    {
        $result = [];

        $redis = Cache::getRedis()->connection('cache');

        $terminationCursorValue = '0';
        $currentCursor = $terminationCursorValue;
        do {
            $responseFromRedis = $redis->scan($currentCursor, [
                'match' => $pattern,
                'count' => 100,
            ]);

            if (! $responseFromRedis) {
                break;
            }

            if (! is_array($responseFromRedis)) {
                break;
            }

            $retrievedKeys = $responseFromRedis[1] ?? [];
            if (count($retrievedKeys) > 0) {
                array_push($result, ...$retrievedKeys);
            }

            $currentCursor = $responseFromRedis[0] ?? $terminationCursorValue;
        } while ($currentCursor !== $terminationCursorValue);

        return $result;
    }
}
