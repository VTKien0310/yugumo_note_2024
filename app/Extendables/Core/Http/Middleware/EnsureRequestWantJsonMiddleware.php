<?php

namespace App\Extendables\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRequestWantJsonMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
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
