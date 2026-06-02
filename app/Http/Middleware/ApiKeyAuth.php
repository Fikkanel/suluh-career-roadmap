<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('X-API-KEY');
        $validKey = config('services.api.key');

        if (! $key || $key !== $validKey) {
            return response()->json(['message' => 'API Key tidak valid.'], 401);
        }

        return $next($request);
    }
}
