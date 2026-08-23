<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    /**
     * Handle an incoming request for admin routes.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Access denied: Admin privileges required.'], 403);
        }

        return $next($request);
    }
}
