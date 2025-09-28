<?php

namespace App\Http\Middleware;

use Closure;

class RequireMfaAbility
{
    public function handle($request, Closure $next, $ability = 'live:control')
    {
        $user = $request->user();
        if (!$user || !$user->tokenCan($ability)) {
            return response()->json(['error' => 'MFA required'], 403);
        }
        return $next($request);
    }
}
