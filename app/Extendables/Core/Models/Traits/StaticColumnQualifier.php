<?php

namespace App\Extendables\Core\Models\Traits;

trait StaticColumnQualifier
{
    public static function qualifiedColumn(string $column): string
    {
        if (str_contains($column, '.')) {
            return $column;
        }

        return static::table().'.'.$column;
    }

    public static function table(): string
    {
        return (new static())->getTable();
    }
}
