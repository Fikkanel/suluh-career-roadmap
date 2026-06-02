<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BasicAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization', '');

        if (! str_starts_with($header, 'Basic ')) {
            return response()->json(['message' => 'Basic Auth header diperlukan.'], 401);
        }

        $decoded = base64_decode(substr($header, 6));
        [$email, $password] = array_pad(explode(':', $decoded, 2), 2, '');

        if (! $email || ! $password) {
            return response()->json(['message' => 'Format Basic Auth tidak valid.'], 401);
        }

        if (! Auth::once(['email' => $email, 'password' => $password])) {
            return response()->json(['message' => 'Email atau kata sandi tidak cocok.'], 401);
        }

        return $next($request);
    }
}
