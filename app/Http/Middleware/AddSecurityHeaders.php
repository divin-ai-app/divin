<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Baseline security response headers (plan §7 Phase 7 hardening pass).
 * Deliberately not a Content-Security-Policy — this app has no inline
 * script/style budget audit done yet, and a wrong CSP silently breaks
 * pages rather than failing loudly; these four are safe, well-understood,
 * and have no plausible downside for a server-rendered Blade app with no
 * embedding use case.
 */
class AddSecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        // No legitimate reason for divin.ai to ever be framed by another
        // site — blocks clickjacking outright.
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        // No camera/mic/geolocation/etc. anywhere in this app.
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        return $response;
    }
}
