<?php

namespace App\Extendables\Core\Http\Response;

use App\Extendables\Core\Http\Enums\CommonHttpErrorCodeEnum;
use App\Extendables\Core\Http\Resource\RawContentCollectionResource;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

class Responder
{
    public function __construct(
        protected readonly ResponseBuilder $responseBuilder
    ) {}

    public function responseNoContent(): JsonResponse
    {
        return $this->responseBuilder->responseNoContent();
    }

    public function responseRawContent(mixed $data): JsonResponse
    {
        return $this->responseBuilder->responseSuccess($data);
    }

    public function responseRawPaginatedContent(LengthAwarePaginator $data): JsonResponse
    {
        return $this->responseRawContent(new RawContentCollectionResource($data));
    }

    public function responseCount(int $count): JsonResponse
    {
        return $this->responseBuilder->responseSuccess(['count' => $count]);
    }

    public function responseUnauthorized(): JsonResponse
    {
        return $this->responseBuilder->responseForbidden(
            CommonHttpErrorCodeEnum::UNAUTHORIZED->value,
            'You are not authorized for this request.'
        );
    }

    public function responseUnauthenticated(): JsonResponse
    {
        return $this->responseBuilder->responseUnauthorized(
            CommonHttpErrorCodeEnum::UNAUTHENTICATED->value,
            'You are not authenticated for this request.'
        );
    }
}
