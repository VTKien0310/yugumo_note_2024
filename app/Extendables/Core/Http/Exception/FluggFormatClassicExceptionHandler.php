<?php

namespace App\Extendables\Core\Http\Exception;

use App\Extendables\Core\Http\Enums\CommonHttpErrorCodeEnum;
use App\Extendables\Core\Http\Response\FluggFormatResponseBuilder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FluggFormatClassicExceptionHandler extends ExceptionHandler
{
    private readonly FluggFormatResponseBuilder $responseBuilder;

    /**
     * @inheritDoc
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->responseBuilder = new FluggFormatResponseBuilder();
    }

    /**
     * @inheritDoc
     */
    public function render($request, $exception)
    {
        if ($request->wantsJson()) {
            return match (true) {
                $exception instanceof AuthenticationException => $this->renderResponseForHttpException(Response::HTTP_UNAUTHORIZED),
                $exception instanceof UnauthorizedException, $exception instanceof AuthorizationException => $this->renderResponseForHttpException(Response::HTTP_FORBIDDEN),
                $exception instanceof ValidationException => $this->renderResponseForValidationException($exception),
                $exception instanceof HttpException => $this->renderResponseForHttpException($exception->getStatusCode()),
                $exception instanceof ModelNotFoundException => $this->renderResponseForModelNotFound($exception),
                default => parent::render($request, $exception)
            };
        }

        return parent::render($request, $exception);
    }

    /**
     * @param  int  $statusCode
     * @param  string  $errorCode
     * @param  string  $errorMessage
     * @return array
     */
    private function makeErrorResponseData(int $statusCode, string $errorCode = '', string $errorMessage = ''): array
    {
        return $this->responseBuilder->makeErrorResponseData($errorCode, $errorMessage, $statusCode);
    }

    /**
     * @param  \Illuminate\Validation\ValidationException  $exception
     * @return \Illuminate\Http\JsonResponse
     */
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
                'errors' => $errors
            ];
        }

        return response()->json($response, $statusCode);
    }

    /**
     * @param  int  $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * @param  int  $statusCode
     * @return string
     */
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

    /**
     * @param  int  $statusCode
     * @return string
     */
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

    /**
     * @inheritDoc
     */
    protected function convertExceptionToArray(\Throwable $e): array
    {
        return config('app.debug')
            ? $this->convertForDebugEnv($e)
            : $this->convertForNonDebugEnv($e);
    }

    /**
     * @param  \Throwable  $e
     * @return int
     */
    private function getUnknownErrorStatusCode(\Throwable $e): int
    {
        return $this->isHttpException($e) ? $e->getStatusCode() : 500;
    }

    /**
     * @param  \Throwable  $e
     * @return array
     */
    private function convertForDebugEnv(\Throwable $e): array
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
            'trace' => collect($e->getTrace())->map(fn($trace) => Arr::except($trace, ['args']))->all(),
        ]);

        return $response;
    }

    /**
     * @param  \Throwable  $e
     * @return string[]
     */
    private function convertForNonDebugEnv(\Throwable $e): array
    {
        return $this->makeErrorResponseData(
            $this->getUnknownErrorStatusCode($e),
            CommonHttpErrorCodeEnum::SERVER_ERROR->value,
            $this->isHttpException($e) ? $e->getMessage() : 'Server Error.'
        );
    }

    /**
     * @param  ModelNotFoundException  $exception
     * @return JsonResponse
     */
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
}
