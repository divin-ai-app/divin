<?php

namespace App\Http\Middleware;

use App\Enums\BotName;
use App\Models\BusinessProfile;
use App\Models\CrawlerVisitLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Real (not simulated) AI-crawler tracking for public profile pages — plan
 * §5 CrawlerVisitLog. robots.txt deliberately allows every major AI
 * crawler (see SystemController), so any of them hitting a published
 * /p/{slug} page here is genuine traffic, not a mock. Attached only to
 * marketing.profile.show — see routes/web.php.
 *
 * IP is hashed (salted with APP_KEY) before storage, never kept raw.
 */
class LogCrawlerVisit
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            return $response;
        }

        $bot = BotName::fromUserAgent((string) $request->userAgent());
        $profile = $request->route('profile');

        if ($bot && $profile instanceof BusinessProfile) {
            CrawlerVisitLog::query()->create([
                'profile_id' => $profile->id,
                'bot_name' => $bot,
                'path' => $request->path(),
                'user_agent' => (string) $request->userAgent(),
                'ip_hash' => hash('sha256', $request->ip().'|'.config('app.key')),
                'timestamp' => now(),
            ]);
        }

        return $response;
    }
}
