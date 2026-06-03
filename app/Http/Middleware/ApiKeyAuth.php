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
        if (! $key) {
            return response()->json(['message' => 'API Key tidak valid.'], 401);
        }

        $validMasterKey = config('services.api.key');
        if ($key === $validMasterKey) {
            return $next($request);
        }

        // Check if it is a valid API key registered to a university/institution partner
        $isValidInstitutionKey = \App\Models\User::where('role', 'institution')
            ->whereNotNull('api_key')
            ->where('api_key', $key)
            ->exists();

        if (! $isValidInstitutionKey) {
            return response()->json(['message' => 'API Key tidak valid.'], 401);
        }

        return $next($request);
    }
}
