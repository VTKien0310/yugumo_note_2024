<?php

namespace App\Features\Note\ValueObjects;

use App\Extendables\Core\Utils\ValueObject;
use Illuminate\Support\Collection;

class NoteCountStatisticsValueObject extends ValueObject
{
    /**
     * @param  Collection<int, NoteCountByTypeStatisticsValueObject>  $byType
     */
    public function __construct(
        public readonly int $total,
        public readonly Collection $byType,
    ) {}
}
