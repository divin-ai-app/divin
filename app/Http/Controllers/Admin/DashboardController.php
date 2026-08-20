<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ClaimRequestStatus;
use App\Enums\DisputeStatus;
use App\Enums\LegalStatus;
use App\Enums\ProfileStatus;
use App\Enums\SubscriptionStatus;
use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\ClaimRequest;
use App\Models\CountryClearance;
use App\Models\Dispute;
use App\Models\Subscription;
use Illuminate\Contracts\View\View;

/**
 * Staff-only back-office (plan §7 Phase 5). Everything under this namespace
 * sits behind the `auth` + `staff` middleware (routes/web.php's admin
 * group) — see EnsureIsStaff for the role check.
 *
 * Every method takes $locale first for the same positional-route-binding
 * reason documented on MarketingController.
 */
class DashboardController extends Controller
{
    public function index(string $locale): View
    {
        $stats = [
            'profiles_published' => BusinessProfile::query()->where('status', ProfileStatus::Published)->count(),
            'profiles_total' => BusinessProfile::query()->count(),
            'claims_pending' => ClaimRequest::query()
                ->whereIn('status', [ClaimRequestStatus::Submitted, ClaimRequestStatus::AwaitingVerification])
                ->count(),
            'disputes_open' => Dispute::query()->whereIn('status', [DisputeStatus::Open, DisputeStatus::InReview])->count(),
            'countries_in_review' => CountryClearance::query()->where('legal_status', LegalStatus::InReview)->count(),
            'active_subscriptions' => Subscription::query()->where('status', SubscriptionStatus::Active)->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
