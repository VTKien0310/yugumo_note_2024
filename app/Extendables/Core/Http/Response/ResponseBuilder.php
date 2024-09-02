<?php

namespace App\Extendables\Core\Http\Response;

use Illuminate\Http\JsonResponse;

interface ResponseBuilder
{
    public function responseSuccess(mixed $data): JsonResponse;

    public function responseCreated(mixed $data): JsonResponse;

    public function responseNoContent(): JsonResponse;

    public function responseNotFound(string $errorCode, ?string $message = null): JsonResponse;

    public function responseBadRequest(string $errorCode, ?string $message = null): JsonResponse;

    public function responseForbidden(string $errorCode, ?string $message = null): JsonResponse;

    public function responseUnauthorized(string $errorCode, ?string $message = null): JsonResponse;

    public function responseUnknownError(string $errorCode, ?string $message = null): JsonResponse;

    public function responseTooManyRequests(string $errorCode, ?string $message = null): JsonResponse;
}
