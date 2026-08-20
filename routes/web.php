<?php

use App\Http\Controllers\MarketingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Route;

// System routes — deliberately unprefixed by locale (see plan §3 "System").
Route::get('/robots.txt', [SystemController::class, 'robots'])->name('robots');
Route::get('/sitemap.xml', [SystemController::class, 'sitemap'])->name('sitemap');
Route::get('/llms.txt', [SystemController::class, 'llmsTxt'])->name('llms-txt');

// Bare `/` has no locale segment yet — send it to the default locale.
Route::get('/', [MarketingController::class, 'redirectToDefaultLocale']);

Route::prefix('{locale}')
    ->whereIn('locale', array_keys(config('locales.available')))
    ->middleware('locale')
    ->name('marketing.')
    ->group(function (): void {
        Route::get('/', [MarketingController::class, 'home'])->name('home');
        Route::get('/how-it-works', [MarketingController::class, 'howItWorks'])->name('how-it-works');
        Route::get('/visibility-check', [MarketingController::class, 'visibilityCheck'])->name('visibility-check');

        Route::get('/industries', [MarketingController::class, 'industriesIndex'])->name('industries.index');
        Route::get('/industries/{industry}', [MarketingController::class, 'industriesShow'])
            ->whereIn('industry', array_keys(config('industries')))
            ->name('industries.show');

        Route::get('/pricing', [MarketingController::class, 'pricing'])->name('pricing');
        Route::get('/about', [MarketingController::class, 'about'])->name('about');
        Route::get('/contact', [MarketingController::class, 'contact'])->name('contact');
        Route::post('/contact', [MarketingController::class, 'submitContact'])->name('contact.submit');
        Route::get('/claim', [MarketingController::class, 'claim'])->name('claim');

        Route::get('/p/{profile}', [ProfileController::class, 'show'])->name('profile.show');
    });
