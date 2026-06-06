<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventWebAccessFromApiDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        // Cek jika host berawalan dengan "api." (misalnya api.suluhkarir.my.id atau api.localhost)
        if (str_starts_with($host, 'api.')) {
            // Kita hanya izinkan route API (berawalan /api atau /api/v1), endpoint health check /up,
            // dan dokumentasi API Scramble (di /docs/api atau /docs/api.json)
            if (!$request->is('api/*') && 
                !$request->is('api/v1/*') && 
                !$request->is('up') && 
                !$request->is('docs/*') && 
                !$request->is('docs')) {
                
                // Dapatkan domain utama dengan menghapus "api." di awal
                $mainDomain = substr($host, 4);
                
                // Gunakan skema https untuk domain utama di production
                $scheme = $request->isSecure() ? 'https://' : 'http://';
                if ($host === 'api.suluhkarir.my.id') {
                    $scheme = 'https://';
                }
                
                return redirect()->away($scheme . $mainDomain . $request->getRequestUri());
            }
        }

        return $next($request);
    }
}
