<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsMentor
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || (auth()->user()->role !== 'mentor' && !auth()->user()->is_admin)) {
            abort(403, 'Anda tidak memiliki akses sebagai Mentor.');
        }

        return $next($request);
    }
}
