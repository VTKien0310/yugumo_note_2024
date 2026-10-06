<?php

namespace App\Extendables\Core\Cache\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

trait CacheKeyGeneration
{
    /**
     * {@inheritDoc}
     */
    public function generateCacheKey(string $namespace, array $identifierSalts = [], string $identifier = ''): string
    {
        $identifier = $identifier ?: URL::full();
        $saltedIdentifier = sha1($identifier.implode('', $identifierSalts));

        return "$namespace:$saltedIdentifier";
    }

    /**
     * {@inheritDoc}
     */
    public function generateCacheKeyForModel(Model $model): string
    {
        return get_class($model).':'.$model->getKey();
    }
}
