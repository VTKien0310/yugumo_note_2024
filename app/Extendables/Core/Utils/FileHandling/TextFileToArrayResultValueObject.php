<?php

namespace App\Extendables\Core\Utils\FileHandling;

use App\Extendables\Core\Utils\ValueObject;

class TextFileToArrayResultValueObject extends ValueObject
{
    public function __construct(
        public readonly bool $isSuccess,
        public readonly ?array $result,
        public readonly int $readLinesCount,
        public readonly ?TextFileToArrayErrorEnum $error = null
    ) {
    }
}
