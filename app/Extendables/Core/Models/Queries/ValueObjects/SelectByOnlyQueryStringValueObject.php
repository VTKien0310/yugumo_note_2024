<?php

namespace App\Extendables\Core\Models\Queries\ValueObjects;

use App\Extendables\Core\Utils\ValueObject;
use Illuminate\Support\Str;

class SelectByOnlyQueryStringValueObject extends ValueObject
{
    public readonly string $table;

    public readonly array $defaultSelect;

    public readonly array $forcedSelect;

    public function __construct(
        public readonly string $resourceName,
        array $defaultSelect = ['*'],
        array $forcedSelect = ['id'],
        string $table = ''
    ) {
        $this->defaultSelect = in_array('*', $defaultSelect) ? ['*'] : $defaultSelect;

        $this->forcedSelect = in_array('*', $forcedSelect)
            ? array_filter($forcedSelect, fn (string $column): bool => $column !== '*')
            : $forcedSelect;

        $this->table = $table ?: Str::plural($this->resourceName);
    }

    public function isDefaultSelectAll(): bool
    {
        return in_array('*', $this->defaultSelect);
    }
}
