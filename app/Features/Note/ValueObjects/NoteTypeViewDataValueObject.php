<?php

namespace App\Features\Note\ValueObjects;

use App\Extendables\Core\Utils\ValueObject;

class NoteTypeViewDataValueObject extends ValueObject
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $description,
        public readonly string $illustrationPath,
        public readonly string $illustrationAlt,
    ) {}
}
