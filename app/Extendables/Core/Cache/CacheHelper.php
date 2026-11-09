<?php

namespace App\Extendables\Core\Cache;

use Illuminate\Database\Eloquent\Model;

interface CacheHelper
{
    /**
     * @param  string[]  $identifierSalts
     */
    public function generateCacheKey(string $namespace, array $identifierSalts = [], string $identifier = ''): string;

    public function generateCacheKeyForModel(Model $model): string;

    /**
     * @return string[]
     */
    public function getAllKeysWithPattern(string $pattern): array;

    /**
     * @param  string[]  $keys
     */
    public function forgetMultiCacheByKey(array $keys): void;

    public function bindCachePrefixToString(string $subject, string $bindingIndicator = '?'): string;
}
