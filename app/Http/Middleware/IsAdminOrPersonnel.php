<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdminOrPersonnel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (!auth()->check()) {
            abort(403);
        }

        if (!in_array(auth()->user()->role, ['admin', 'personnel'])) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
