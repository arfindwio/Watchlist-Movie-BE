<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Ambil token dari Authorization header
        $accessToken = $request->bearerToken();

        if (! $accessToken) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Token is missing or invalid.',
                'data' => null,
            ], 401);
        }

        // Cek token valid dan milik user
        $token = PersonalAccessToken::findToken($accessToken);

        if (! $token || ! $token->tokenable) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Token is missing or invalid.',
                'data' => null,
            ], 401);
        }

        // Tetapkan user ke request agar $request->user() bisa digunakan
        $request->setUserResolver(function () use ($token) {
            return $token->tokenable;
        });

        return $next($request);
    }

    
}
