<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        // Låt den vara no-op i icke-prod/stage (extra skydd)
        if (!app()->environment(['production', 'staging'])) {
            return $next($request);
        }

        $response = $next($request);

        return $response->withHeaders([
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
            'Content-Security-Policy'   => 'upgrade-insecure-requests',
            'X-Content-Type-Options'    => 'nosniff',
        ]);
    }
}
