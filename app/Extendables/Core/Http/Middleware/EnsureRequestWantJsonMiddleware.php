<?php

namespace App\Extendables\Core\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EnsureRequestWantJsonMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->header('Accept') === 'application/json') {
            return $next($request);
        }
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
