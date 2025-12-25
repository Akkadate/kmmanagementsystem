<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            abort(401, 'Unauthorized');
        }

        if (!$request->user()->is_active) {
            abort(403, 'Account is inactive');
        }

        if (!in_array($request->user()->role, $roles)) {
            abort(403, 'Access denied');
        }

        return $next($request);
    }
}
