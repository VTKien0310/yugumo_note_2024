<?php

namespace App\Extendables\Core\Http\Response;

use App\Extendables\Core\Http\Enums\CommonHttpErrorCodeEnum;
use App\Extendables\Core\Http\Resource\RawContentCollectionResource;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

class Responder
{
    /**
     * @param  ResponseBuilder  $responseBuilder
     */
    function __construct(
        protected readonly ResponseBuilder $responseBuilder
    ) {
    }

    /**
     * @return JsonResponse
     */
    function responseNoContent(): JsonResponse
    {
        return $this->responseBuilder->responseNoContent();
    }

    /**
     * @param  mixed  $data
     * @return JsonResponse
     */
    function responseRawContent(mixed $data): JsonResponse
    {
        return $this->responseBuilder->responseSuccess($data);
    }

    /**
     * @param  LengthAwarePaginator  $data
     * @return JsonResponse
     */
    public function responseRawPaginatedContent(LengthAwarePaginator $data): JsonResponse
    {
        return $this->responseRawContent(new RawContentCollectionResource($data));
    }

    /**
     * @param  int  $count
     * @return JsonResponse
     */
    function responseCount(int $count): JsonResponse
    {
        return $this->responseBuilder->responseSuccess(['count' => $count]);
    }

    /**
     * @return JsonResponse
     */
    function responseUnauthorized(): JsonResponse
    {
        return $this->responseBuilder->responseForbidden(
            CommonHttpErrorCodeEnum::UNAUTHORIZED->value,
            'You are not authorized for this request.'
        );
    }

    /**
     * @return JsonResponse
     */
    function responseUnauthenticated(): JsonResponse
    {
        return $this->responseBuilder->responseUnauthorized(
            CommonHttpErrorCodeEnum::UNAUTHENTICATED->value,
            'You are not authenticated for this request.'
        );
    }
}
