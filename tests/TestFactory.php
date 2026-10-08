<?php

namespace Tests;

use Illuminate\Support\Collection;

abstract class TestFactory
{
    public static function new(): static
    {
        return new static;
    }

    /**
     * Concrete factory classes should add a return type
     */
    abstract public function create(array $extra = []): mixed;

    public function times(int $times, array $extra = []): Collection
    {
        return collect()
            ->times($times)
            ->map(fn () => $this->create($extra));
    }
}
