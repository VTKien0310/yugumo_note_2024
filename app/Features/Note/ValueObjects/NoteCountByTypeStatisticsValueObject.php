<?php

namespace App\Features\Note\ValueObjects;

use App\Extendables\Core\Utils\ValueObject;

class NoteCountByTypeStatisticsValueObject extends ValueObject
{
    public function __construct(
        public readonly string $type,
        public readonly int $count,
    ) {}
}
