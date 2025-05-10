<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class TokenExpirationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if (!$token) return response()->json(['message' => 'Токен отсутствует'], 401);

        $tokenData = PersonalAccessToken::findToken($token);
        if (!$tokenData || ($tokenData->expires_at && $tokenData->expires_at->isPast())) {
            return response()->json(['message' => 'Токен истёк'], 401);
        }

        return $next($request);
    }
}
