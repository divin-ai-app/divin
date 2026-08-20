<?php

use App\Http\Middleware\EnsureIsStaff;
use App\Http\Middleware\LogCrawlerVisit;
use App\Http\Middleware\SetLocaleFromRoute;
use App\Http\Middleware\VerifyIngestionKey;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'locale' => SetLocaleFromRoute::class,
            'ingestion.key' => VerifyIngestionKey::class,
            'staff' => EnsureIsStaff::class,
            'crawler.log' => LogCrawlerVisit::class,
        ]);

        // Login lives at the locale-scoped `marketing.login`, not the
        // conventional bare `login` route name the `auth` middleware
        // defaults to — send guests there, preserving their current locale.
        $middleware->redirectGuestsTo(fn (Request $request) => route('marketing.login', [
            'locale' => $request->route('locale') ?? config('locales.default'),
        ]));

        // The flip side: an already-authenticated user hitting a guest-only
        // route (e.g. /login while still signed in) previously fell back to
        // Laravel's unconfigured default (bare '/', which redirects to the
        // marketing homepage) — confusing, since it looks like the login
        // page doesn't exist rather than "you're already signed in."
        $middleware->redirectUsersTo(fn (Request $request) => route('marketing.dashboard.index', [
            'locale' => $request->route('locale') ?? config('locales.default'),
        ]));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
