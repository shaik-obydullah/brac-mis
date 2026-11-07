<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Reject API requests from accounts that have been deactivated after
     * their token was issued.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->status !== 'active') {
            return response()->json(['message' => 'This account is inactive.'], 403);
        }

        return $next($request);
    }
}
