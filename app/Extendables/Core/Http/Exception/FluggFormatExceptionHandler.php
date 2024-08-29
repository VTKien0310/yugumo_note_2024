<?php

namespace App\Extendables\Core\Http\Exception;

use App\Extendables\Core\Http\Enums\CommonHttpErrorCodeEnum;
use App\Extendables\Core\Http\Response\FluggFormatResponseBuilder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class FluggFormatExceptionHandler
{
    private readonly FluggFormatResponseBuilder $responseBuilder;

    public function __construct()
    {
        $this->responseBuilder = new FluggFormatResponseBuilder();
    }

    public function __invoke(Exceptions $exceptions): void
    {
        $customRenderer = function (Throwable $exception) {
            return match (true) {
                $exception instanceof AuthenticationException => $this->renderResponseForHttpException(Response::HTTP_UNAUTHORIZED),
                $exception instanceof UnauthorizedException, $exception instanceof AuthorizationException => $this->renderResponseForHttpException(Response::HTTP_FORBIDDEN),
                $exception instanceof ValidationException => $this->renderResponseForValidationException($exception),
                $exception instanceof HttpException => $this->renderResponseForHttpException($exception->getStatusCode()),
                $exception instanceof ModelNotFoundException => $this->renderResponseForModelNotFound($exception),
                default => $this->renderResponseForUnknownException($exception)
            };
        };

        $exceptions->render($customRenderer);
    }

    private function makeErrorResponseData(int $statusCode, string $errorCode = '', string $errorMessage = ''): array
    {
        return $this->responseBuilder->makeErrorResponseData($errorCode, $errorMessage, $statusCode);
    }

    private function renderResponseForValidationException(ValidationException $exception): JsonResponse
    {
        $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

        $response = $this->makeErrorResponseData(
            $statusCode,
            $this->getHttpExceptionCode($statusCode),
            $this->getHttpExceptionMessage($statusCode)
        );

        foreach ($exception->validator->errors()->messages() as $param => $errors) {
            $response['error']['details'][] = [
                'param' => $param,
                'errors' => $errors,
            ];
        }

        return response()->json($response, $statusCode);
    }

    private function renderResponseForHttpException(int $statusCode): JsonResponse
    {
        return response()->json(
            $this->makeErrorResponseData(
                $statusCode,
                $this->getHttpExceptionCode($statusCode),
                $this->getHttpExceptionMessage($statusCode)
            ),
            $statusCode
        );
    }

    private function getHttpExceptionMessage(int $statusCode): string
    {
        return match ($statusCode) {
            Response::HTTP_UNAUTHORIZED => 'You are not authenticated for this request.',
            Response::HTTP_FORBIDDEN => 'You are not authorized for this request.',
            Response::HTTP_NOT_FOUND => 'The requested route does not exist.',
            Response::HTTP_UNPROCESSABLE_ENTITY => 'The given data failed to be processed.',
            Response::HTTP_TOO_MANY_REQUESTS => 'You have exceeded the rate limit. Please wait before making more requests.',
            Response::HTTP_INTERNAL_SERVER_ERROR => 'Internal server error.',
            default => 'The request could not be understood by the server.',
        };
    }

    private function getHttpExceptionCode(int $statusCode): string
    {
        return match ($statusCode) {
            Response::HTTP_UNAUTHORIZED => CommonHttpErrorCodeEnum::UNAUTHENTICATED->value,
            Response::HTTP_FORBIDDEN => CommonHttpErrorCodeEnum::UNAUTHORIZED->value,
            Response::HTTP_NOT_FOUND => CommonHttpErrorCodeEnum::ROUTE_NOT_FOUND->value,
            Response::HTTP_UNPROCESSABLE_ENTITY => CommonHttpErrorCodeEnum::UNPROCESSABLE_ENTITY->value,
            Response::HTTP_TOO_MANY_REQUESTS => CommonHttpErrorCodeEnum::TOO_MANY_REQUESTS->value,
            Response::HTTP_INTERNAL_SERVER_ERROR => CommonHttpErrorCodeEnum::SERVER_ERROR->value,
            default => CommonHttpErrorCodeEnum::BAD_REQUEST->value,
        };
    }

    private function getUnknownErrorStatusCode(Throwable $e): int
    {
        return $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;
    }

    private function getUnknownErrorMessage(Throwable $e): string
    {
        return $e instanceof HttpExceptionInterface ? $e->getMessage() : 'Server Error.';
    }

    private function convertForDebugEnv(Throwable $e): array
    {
        $response = $this->makeErrorResponseData(
            $this->getUnknownErrorStatusCode($e),
            CommonHttpErrorCodeEnum::SERVER_ERROR->value,
            $e->getMessage()
        );

        $response['error'] = array_merge($response['error'], [
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => collect($e->getTrace())->map(fn ($trace) => Arr::except($trace, ['args']))->all(),
        ]);

        return $response;
    }

    private function convertForNonDebugEnv(Throwable $e): array
    {
        return $this->makeErrorResponseData(
            $this->getUnknownErrorStatusCode($e),
            CommonHttpErrorCodeEnum::SERVER_ERROR->value,
            $this->getUnknownErrorMessage($e)
        );
    }

    private function renderResponseForModelNotFound(ModelNotFoundException $exception): JsonResponse
    {
        $statusCode = Response::HTTP_NOT_FOUND;

        $modelIds = implode(',', $exception->getIds());

        $modelName = explode('\\', $exception->getModel());
        $modelName = Str::snake(end($modelName));

        $engModelName = str_replace('_', ' ', $modelName);
        $engModelName = ucwords($engModelName);

        return response()->json(
            $this->makeErrorResponseData(
                $statusCode,
                "{$modelName}_not_found",
                "$engModelName with id $modelIds not found."
            ),
            $statusCode
        );
    }

    private function renderResponseForUnknownException(Throwable $e): JsonResponse
    {
        $unknownErrorResponseData = config('app.debug')
            ? $this->convertForDebugEnv($e)
            : $this->convertForNonDebugEnv($e);

        return response()->json($unknownErrorResponseData, $this->getUnknownErrorStatusCode($e));
    }
}
