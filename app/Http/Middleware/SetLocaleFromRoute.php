<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies the {locale} route segment to the app locale. The segment itself
 * is validated at the route-group level (->whereIn, see routes/web.php), so
 * by the time this runs the value is already one of config('locales.available').
 */
class SetLocaleFromRoute
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        if ($locale && array_key_exists($locale, config('locales.available'))) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
