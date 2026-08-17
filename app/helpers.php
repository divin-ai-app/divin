<?php

use Illuminate\Support\Facades\App;

if (! function_exists('lroute')) {
    /**
     * route() that automatically prepends the current app locale, so Blade
     * views never have to thread {locale} through every route() call by
     * hand. See config/locales.php and App\Http\Middleware\SetLocaleFromRoute.
     */
    function lroute(string $name, array $parameters = [], bool $absolute = true): string
    {
        return route($name, ['locale' => App::getLocale(), ...$parameters], $absolute);
    }
}
