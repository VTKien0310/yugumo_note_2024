<?php

use App\Extendables\Core\Http\Exception\JsonApiExceptionHandler;
use App\Extendables\Core\Http\Middleware\EnsureRequestWantJsonMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../app/Http/Web/web.php',
        health: '/up',
        then: function () {
            Route::middleware([
                'web',
                EnsureRequestWantJsonMiddleware::class,
                'throttle:60,1',
            ])
                ->prefix('bff')
                ->as('bff.')
                ->group(app_path('Http/Bff/bff.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(fn () => route('auth.login'));
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PORT |
            Request::HEADER_X_FORWARDED_PROTO |
            Request::HEADER_X_FORWARDED_AWS_ELB
        );
    })
    ->withExceptions(new JsonApiExceptionHandler)->create();
