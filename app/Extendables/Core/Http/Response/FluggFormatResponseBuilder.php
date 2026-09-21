<?php

namespace App\Extendables\Core\Http\Response;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class FluggFormatResponseBuilder implements ResponseBuilder
{
    /**
     * {@inheritDoc}
     */
    public function responseSuccess(mixed $data): JsonResponse
    {
        return $this->makeSuccessResponse($data);
    }

    private function makeSuccessResponse(mixed $data, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        $baseResponse = $this->baseSuccessResponse($statusCode);

        if ($data instanceof JsonResource) {
            return $data->additional($baseResponse)->response();
        }

        return response()->json(array_merge($baseResponse, ['data' => $data]));
    }

    private function baseSuccessResponse(int $statusCode = Response::HTTP_OK): array
    {
        return [
            'status' => $statusCode,
            'success' => true,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function responseCreated(mixed $data): JsonResponse
    {
        return $this->makeSuccessResponse($data, Response::HTTP_CREATED);
    }

    /**
     * {@inheritDoc}
     */
    public function responseNoContent(): JsonResponse
    {
        return $this->makeSuccessResponse(null);
    }

    /**
     * {@inheritDoc}
     */
    public function responseNotFound(string $errorCode, ?string $message = null): JsonResponse
    {
        return $this->makeErrorResponse($errorCode, $message, Response::HTTP_NOT_FOUND);
    }

    public function makeErrorResponseData(string $errorCode, ?string $message, int $statusCode): array
    {
        return [
            'status' => $statusCode,
            'success' => false,
            'error' => [
                'code' => $errorCode,
                'message' => $message,
            ],
        ];
    }

    private function makeErrorResponse(
        string $errorCode,
        ?string $message,
        int $statusCode = Response::HTTP_BAD_REQUEST
    ): JsonResponse {
        return response()->json(
            $this->makeErrorResponseData($errorCode, $message, $statusCode),
            $statusCode
        );
    }

    /**
     * {@inheritDoc}
     */
    public function responseBadRequest(string $errorCode, ?string $message = null): JsonResponse
    {
        return $this->makeErrorResponse($errorCode, $message);
    }

    /**
     * {@inheritDoc}
     */
    public function responseForbidden(string $errorCode, ?string $message = null): JsonResponse
    {
        return $this->makeErrorResponse($errorCode, $message, Response::HTTP_FORBIDDEN);
    }

    /**
     * {@inheritDoc}
     */
    public function responseUnauthorized(string $errorCode, ?string $message = null): JsonResponse
    {
        return $this->makeErrorResponse($errorCode, $message, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * {@inheritDoc}
     */
    public function responseUnknownError(string $errorCode, ?string $message = null): JsonResponse
    {
        return $this->makeErrorResponse($errorCode, $message, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * {@inheritDoc}
     */
    public function responseTooManyRequests(string $errorCode, ?string $message = null): JsonResponse
    {
        return $this->makeErrorResponse($errorCode, $message, Response::HTTP_TOO_MANY_REQUESTS);
    }
}
