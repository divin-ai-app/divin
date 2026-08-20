<?php

use App\Http\Controllers\Admin\ClaimController as AdminClaimController;
use App\Http\Controllers\Admin\CountryClearanceController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DataSourceController;
use App\Http\Controllers\Admin\DisputeController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
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

        Route::get('/p/{profile}', [ProfileController::class, 'show'])->middleware('crawler.log')->name('profile.show');
        Route::get('/p/{profile}/report', [ProfileController::class, 'report'])->name('profile.report');
        Route::post('/p/{profile}/report', [ProfileController::class, 'submitReport'])
            ->middleware('throttle:5,1')
            ->name('profile.report.submit');

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

        // --- Dashboard (Phase 4) — every {profile} route requires ownership
        // (BusinessProfilePolicy@manage), not just login.
        Route::middleware('auth')->prefix('dashboard')->name('dashboard.')->group(function (): void {
            Route::get('/', [DashboardController::class, 'index'])->name('index');

            Route::middleware('can:manage,profile')->prefix('{profile}')->group(function (): void {
                Route::get('/', [DashboardController::class, 'overview'])->name('overview');

                Route::get('/edit', [DashboardController::class, 'edit'])->name('edit');
                Route::put('/edit', [DashboardController::class, 'update'])->name('update');
                Route::post('/services', [DashboardController::class, 'storeService'])->name('services.store');
                Route::delete('/services/{service}', [DashboardController::class, 'destroyService'])->name('services.destroy');
                Route::post('/images', [DashboardController::class, 'storeImage'])->name('images.store');
                Route::delete('/images/{image}', [DashboardController::class, 'destroyImage'])->name('images.destroy');

                Route::get('/billing', [DashboardController::class, 'billing'])->name('billing');

                Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
                Route::put('/settings', [DashboardController::class, 'updateSettings'])->name('settings.update');

                Route::get('/freshness', [DashboardController::class, 'freshness'])->name('freshness');
                Route::put('/freshness/{freshnessCheckLog}', [DashboardController::class, 'resolveFreshness'])->name('freshness.resolve');

                Route::get('/crawler-activity', [DashboardController::class, 'crawlerActivity'])->name('crawler-activity');
                Route::post('/crawler-activity/simulate', [DashboardController::class, 'simulateCrawlerVisit'])->name('crawler-activity.simulate');
            });
        });

        // --- Admin back-office (Phase 5) — staff/admin only, see EnsureIsStaff.
        // Profile *editing* deliberately reuses the dashboard.edit route
        // (BusinessProfilePolicy@manage allows staff) rather than
        // duplicating that form here.
        Route::middleware(['auth', 'staff'])->prefix('admin')->name('admin.')->group(function (): void {
            Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

            Route::get('/profiles', [AdminProfileController::class, 'index'])->name('profiles.index');
            Route::get('/profiles/{profile}', [AdminProfileController::class, 'show'])->name('profiles.show');

            Route::get('/claims', [AdminClaimController::class, 'index'])->name('claims.index');
            Route::post('/claims/{claimRequest}/approve', [AdminClaimController::class, 'approve'])->name('claims.approve');
            Route::post('/claims/{claimRequest}/reject', [AdminClaimController::class, 'reject'])->name('claims.reject');

            Route::get('/disputes', [DisputeController::class, 'index'])->name('disputes.index');
            Route::put('/disputes/{dispute}', [DisputeController::class, 'resolve'])->name('disputes.resolve');

            Route::get('/country-clearance', [CountryClearanceController::class, 'index'])->name('country-clearance.index');
            Route::put('/country-clearance/{clearance}', [CountryClearanceController::class, 'update'])->name('country-clearance.update');

            Route::get('/data-sources', [DataSourceController::class, 'index'])->name('data-sources.index');

            Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        });
    });
