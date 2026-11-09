<?php

namespace App\Extendables\Core\Http\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * The `filter` query string is not shaped as `filter[field]=value` (e.g. `filter[field][]=value`, which PHP parses
 * into a nested array).
 */
class InvalidFilterQueryStringException extends Exception implements ExtendableException
{
    public function __construct(public readonly string $field)
    {
        parent::__construct('Malformed filter query string for field "'.$field.'".');
    }

    public function httpStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function httpErrorCode(): string
    {
        return 'invalid_filter_query_string';
    }

    public function httpErrorMessage(): string
    {
        return 'The filter query string is malformed. Use filter[field]=value.';
    }
}
