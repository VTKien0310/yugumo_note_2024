<?php

namespace App\Extendables\Core\Cache;

use App\Extendables\Core\Cache\Concerns\CacheForgetByKey;
use App\Extendables\Core\Cache\Concerns\CacheKeyGeneration;
use App\Extendables\Core\Cache\Concerns\CachePrefixManipulation;
use Illuminate\Support\Facades\Redis;

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

        $redis = Redis::connection('cache');

        $cursor = null;
        do {
            $responseFromRedis = $redis->scan($cursor, [
                'match' => $pattern,
                'count' => 100,
            ]);

            if ($responseFromRedis === false) {
                break;
            }

            [$cursor, $retrievedKeys] = $responseFromRedis;

            $result = array_merge($result, $retrievedKeys);
        } while ($cursor !== 0);

        return $result;
    }
}
