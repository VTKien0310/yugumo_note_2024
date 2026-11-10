<?php

namespace App\Extendables\Core\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * This trait should only be used in older Laravel versions.
 * For newer versions, consider using Laravel's built-in HasUlids trait.
 */
trait UlidEloquent
{
    /**
     * {@inheritDoc}
     */
    public function getKeyType(): string
    {
        return 'string';
    }

    /**
     * {@inheritDoc}
     */
    public function getIncrementing(): bool
    {
        return false;
    }

    /**
     * Booting method
     */
    public static function bootUlidEloquent()
    {
        static::creating(function (Model $model) {
            if (! $model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::ulid();
            }
        });
    }
}
