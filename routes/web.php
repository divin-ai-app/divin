<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\DashboardController;
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

        // --- Account login (magic-link) — see AuthController's docblock ---
        Route::middleware('guest')->group(function (): void {
            Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
            Route::post('/login', [AuthController::class, 'sendLoginLink'])
                ->middleware('throttle:6,1')
                ->name('login.send');
            Route::get('/verify-request', [AuthController::class, 'verifyRequest'])->name('verify-request');
            Route::get('/login/{token}', [AuthController::class, 'consume'])->name('login.consume');
        });
        Route::post('/logout', [AuthController::class, 'logout'])
            ->middleware('auth')
            ->name('logout');

        // --- Claim flow (ownership verification) — see ClaimController's docblock ---
        Route::middleware('auth')->prefix('claim/{profile}')->name('claim.')->group(function (): void {
            Route::get('/', [ClaimController::class, 'show'])->name('show');
            Route::post('/otp/verify', [ClaimController::class, 'verifyOtp'])->middleware('throttle:10,1')->name('otp.verify');
            Route::post('/otp/resend', [ClaimController::class, 'resendOtp'])->middleware('throttle:3,1')->name('otp.resend');
            Route::post('/document', [ClaimController::class, 'submitDocumentClaim'])->name('document.submit');
            Route::get('/plan', [ClaimController::class, 'plan'])->name('plan');
            Route::post('/checkout', [ClaimController::class, 'checkout'])->name('checkout');
            Route::get('/confirmation', [ClaimController::class, 'confirmation'])->name('confirmation');
        });

        // --- Dashboard shell (Phase 4 builds this out fully) ---
        Route::middleware('auth')->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    });
