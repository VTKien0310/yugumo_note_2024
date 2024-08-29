<?php

namespace App\Extendables\Core\Http\Response;

use Illuminate\Http\JsonResponse;

interface ResponseBuilder
{
    /**
     * @param  mixed  $data
     * @return JsonResponse
     */
    function responseSuccess(mixed $data): JsonResponse;

    /**
     * @param  mixed  $data
     * @return JsonResponse
     */
    function responseCreated(mixed $data): JsonResponse;

    /**
     * @return JsonResponse
     */
    function responseNoContent(): JsonResponse;

    /**
     * @param  string  $errorCode
     * @param  string|null  $message
     * @return JsonResponse
     */
    function responseNotFound(string $errorCode, ?string $message = null): JsonResponse;

    /**
     * @param  string  $errorCode
     * @param  string|null  $message
     * @return JsonResponse
     */
    function responseBadRequest(string $errorCode, ?string $message = null): JsonResponse;

    /**
     * @param  string  $errorCode
     * @param  string|null  $message
     * @return JsonResponse
     */
    function responseForbidden(string $errorCode, ?string $message = null): JsonResponse;

    /**
     * @param  string  $errorCode
     * @param  string|null  $message
     * @return JsonResponse
     */
    function responseUnauthorized(string $errorCode, ?string $message = null): JsonResponse;

    /**
     * @param  string  $errorCode
     * @param  string|null  $message
     * @return JsonResponse
     */
    function responseUnknownError(string $errorCode, ?string $message = null): JsonResponse;

    /**
     * @param  string  $errorCode
     * @param  string|null  $message
     * @return JsonResponse
     */
    public function responseTooManyRequests(string $errorCode, string $message = null): JsonResponse;
}
