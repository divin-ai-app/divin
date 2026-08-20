<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PlanTier;
use App\Enums\SubscriptionStatus;
use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Contracts\View\View;

/**
 * Basic MRR / plan-mix view over Subscription (plan §7 Phase 5: "Customer &
 * subscription management — basic MRR/plan-mix/cohort view"). Annual prices
 * mirror marketing.pricing (MarketingController::pricing) — Stripe itself
 * remains the source of truth for actual billing; this is an internal
 * approximation, not a reconciled ledger (Invoice rows aren't written yet,
 * see this class's note on Invoice).
 */
class CustomerController extends Controller
{
    private const ANNUAL_PRICE_CENTS = [
        'registered' => 2388,
        'managed' => 5988,
    ];

    public function index(string $locale): View
    {
        $subscriptions = Subscription::query()
            ->with(['profile.owners.user'])
            ->latest()
            ->paginate(20);

        $active = Subscription::query()->where('status', SubscriptionStatus::Active)->get();

        $mrrCents = $active->sum(fn (Subscription $subscription) => (self::ANNUAL_PRICE_CENTS[$subscription->tier->value] ?? 0) / 12);

        $planMix = [
            PlanTier::Registered->value => $active->where('tier', PlanTier::Registered)->count(),
            PlanTier::Managed->value => $active->where('tier', PlanTier::Managed)->count(),
        ];

        return view('admin.customers.index', [
            'subscriptions' => $subscriptions,
            'mrrCents' => (int) round($mrrCents),
            'planMix' => $planMix,
            'activeCount' => $active->count(),
        ]);
    }
}
