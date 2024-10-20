<?php

namespace App\Features\Note\ValueObjects;

use App\Extendables\Core\Utils\ValueObject;

class ChecklistItemValueObject extends ValueObject
{
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly int $completed,
    ) {}
}
