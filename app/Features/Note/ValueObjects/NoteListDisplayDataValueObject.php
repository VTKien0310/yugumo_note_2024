<?php

namespace App\Features\Note\ValueObjects;

use App\Extendables\Core\Utils\ValueObject;

class NoteListDisplayDataValueObject extends ValueObject
{
    public function __construct(
        public readonly string $id,
        public readonly string $shortenedTitle,
        public readonly string $type,
        public readonly string $shortenedContent,
        public readonly string $createdAt,
        public readonly string $updatedAt,
    ) {}
}
