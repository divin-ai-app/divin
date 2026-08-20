<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gates the ingestion API contract (see plan §4 "Ingestion API contract")
 * behind a shared secret the future crawler/ingestion pipeline sends as
 * X-Ingestion-Key. That pipeline itself is out of scope this phase — this
 * just defines and protects the contract it will call.
 */
class VerifyIngestionKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = config('services.ingestion.key');
        $provided = $request->header('X-Ingestion-Key');

        if (! $expected || ! $provided || ! hash_equals($expected, $provided)) {
            abort(401, 'Invalid or missing X-Ingestion-Key.');
        }

        return $next($request);
    }
}
