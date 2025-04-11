<?php

namespace App\Features\Note\ValueObjects;

use App\Extendables\Core\Utils\ValueObject;

class NoteListDisplayDataValueObject extends ValueObject
{
    public function __construct(
        public readonly string $id,
        public readonly string $shortTitle,
        public readonly string $mediumTitle,
        public readonly string $type,
        public readonly string $shortContent,
        public readonly string $mediumContent,
        public readonly string $createdAt,
        public readonly string $updatedAt,
    ) {}
}
