<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminCanMiddleware
{
    public function handle(Request $request, Closure $next, string ...$abilities): Response
    {
        $admin = auth('admin')->user();

        foreach ($abilities as $ability) {
            if (!$admin->hasAbility($ability)) {
                abort(403, 'You do not have permission to perform this action.');
            }
        }

        return $next($request);
    }
}
