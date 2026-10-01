<?php

namespace App\Extendables\Core\Http\Exception;

/**
 * Interface for making custom exceptions that can be rendered by the library's ExceptionHandler
 */
interface ExtendableException
{
    public function httpStatusCode(): int;

    public function httpErrorCode(): string;

    public function httpErrorMessage(): string;
}
