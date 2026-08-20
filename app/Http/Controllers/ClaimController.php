<?php

namespace App\Http\Controllers;

use App\Enums\ClaimRequestStatus;
use App\Enums\ClaimStatus;
use App\Enums\PlanTier;
use App\Enums\Role;
use App\Enums\VerificationMethod;
use App\Mail\ClaimOtpMail;
use App\Models\BusinessProfile;
use App\Models\ClaimRequest;
use App\Models\ProfileOwnership;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Stripe\StripeClient;

/**
 * Ownership verification for a specific business — proving control of the
 * BUSINESS, not just of a login inbox. Deliberately separate from account
 * login (AuthController), and always runs after it: every action here sits
 * behind the `auth` middleware. See plan §4 "Core flows".
 *
 * Every method takes `$locale` first for the same positional-route-binding
 * reason documented on MarketingController.
 */
class ClaimController extends Controller
{
    private const OTP_MINUTES_VALID = 15;

    public function show(string $locale, BusinessProfile $profile): View|RedirectResponse
    {
        if (in_array($profile->claim_status, [ClaimStatus::Claimed, ClaimStatus::Verified], true)) {
            return view('claim.already-claimed', compact('profile'));
        }

        $user = Auth::user();
        $claimRequest = $this->activeClaimRequestFor($profile, $user->id);

        if (! $claimRequest) {
            $claimRequest = $this->startClaimRequest($profile, $user);
        }

        if ($claimRequest->verification_method === VerificationMethod::EmailMatch) {
            return view('claim.otp', [
                'profile' => $profile,
                'claimRequest' => $claimRequest,
                'contact' => $this->maskEmail($claimRequest->contact_value),
            ]);
        }

        return view('claim.document', compact('profile', 'claimRequest'));
    }

    public function resendOtp(string $locale, BusinessProfile $profile): RedirectResponse
    {
        $claimRequest = $this->activeClaimRequestFor($profile, Auth::id());

        if ($claimRequest && $claimRequest->verification_method === VerificationMethod::EmailMatch) {
            $this->sendOtp($claimRequest, $profile);
        }

        return back()->with('status', 'A new code is on its way.');
    }

    public function verifyOtp(string $locale, BusinessProfile $profile, Request $request): RedirectResponse
    {
        $data = $request->validate(['code' => ['required', 'string', 'size:6']]);

        $claimRequest = $this->activeClaimRequestFor($profile, Auth::id());

        if (! $claimRequest || ! $claimRequest->otpMatches($data['code'])) {
            return back()->withErrors(['code' => 'That code is incorrect or has expired.']);
        }

        $this->markVerified($claimRequest, $profile);

        return redirect()->route('marketing.claim.plan', ['locale' => $locale, 'profile' => $profile]);
    }

    public function submitDocumentClaim(string $locale, BusinessProfile $profile, Request $request): View
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $claimRequest = $this->activeClaimRequestFor($profile, Auth::id());

        if ($claimRequest) {
            $claimRequest->update([
                'status' => ClaimRequestStatus::AwaitingVerification,
                'review_notes' => $data['message'],
            ]);
            $claimRequest->events()->create([
                'actor_user_id' => Auth::id(),
                'action' => 'submitted_for_manual_review',
            ]);
        }

        return view('claim.document-submitted', compact('profile'));
    }

    public function plan(string $locale, BusinessProfile $profile): View|RedirectResponse
    {
        if (! $this->userOwns($profile)) {
            return redirect()->route('marketing.claim', ['locale' => $locale, 'profile' => $profile]);
        }

        return view('claim.plan', compact('profile'));
    }

    public function checkout(string $locale, BusinessProfile $profile, Request $request): RedirectResponse
    {
        if (! $this->userOwns($profile)) {
            return redirect()->route('marketing.claim', ['locale' => $locale, 'profile' => $profile]);
        }

        $data = $request->validate(['tier' => ['required', 'in:registered,managed']]);
        $tier = PlanTier::from($data['tier']);

        $priceId = $tier === PlanTier::Managed
            ? config('services.stripe.price_managed')
            : config('services.stripe.price_registered');

        if (! config('services.stripe.secret') || ! $priceId) {
            return back()->withErrors(['tier' => 'Payments aren\'t configured yet — contact us to finish claiming this profile.']);
        }

        $stripe = new StripeClient(config('services.stripe.secret'));

        $session = $stripe->checkout->sessions->create([
            'mode' => 'subscription',
            'customer_email' => Auth::user()->email,
            'line_items' => [['price' => $priceId, 'quantity' => 1]],
            // No {CHECKOUT_SESSION_ID} query param here on purpose: the
            // confirmation view never reads it (the webhook is the sole
            // source of truth for activation, per this class's docblock),
            // and the host's ModSecurity/WAF blocks the redirect when
            // Stripe substitutes that long token into the URL.
            'success_url' => route('marketing.claim.confirmation', ['locale' => $locale, 'profile' => $profile]),
            'cancel_url' => route('marketing.claim.plan', ['locale' => $locale, 'profile' => $profile]),
            'metadata' => ['profile_id' => $profile->id, 'tier' => $tier->value],
        ]);

        return redirect()->away($session->url);
    }

    public function confirmation(string $locale, BusinessProfile $profile): View
    {
        return view('claim.confirmation', compact('profile'));
    }

    private function activeClaimRequestFor(BusinessProfile $profile, int $userId): ?ClaimRequest
    {
        return ClaimRequest::query()
            ->where('profile_id', $profile->id)
            ->where('requested_by_user_id', $userId)
            ->whereIn('status', [ClaimRequestStatus::Submitted, ClaimRequestStatus::AwaitingVerification])
            ->latest()
            ->first();
    }

    private function startClaimRequest(BusinessProfile $profile, $user): ClaimRequest
    {
        $method = $profile->public_email ? VerificationMethod::EmailMatch : VerificationMethod::DocumentUpload;

        $claimRequest = ClaimRequest::query()->create([
            'profile_id' => $profile->id,
            'requested_by_user_id' => $user->id,
            'verification_method' => $method,
            'contact_value' => $profile->public_email ?? '',
            'status' => ClaimRequestStatus::AwaitingVerification,
        ]);

        $claimRequest->events()->create([
            'actor_user_id' => $user->id,
            'action' => 'claim_started',
            'metadata' => ['method' => $method->value],
        ]);

        $profile->update(['claim_status' => ClaimStatus::PendingClaim]);

        if ($method === VerificationMethod::EmailMatch) {
            $this->sendOtp($claimRequest, $profile);
        }

        return $claimRequest;
    }

    private function sendOtp(ClaimRequest $claimRequest, BusinessProfile $profile): void
    {
        $code = $claimRequest->issueOtp(self::OTP_MINUTES_VALID);

        Mail::to($claimRequest->contact_value)
            ->send(new ClaimOtpMail($profile->name, $code, self::OTP_MINUTES_VALID));
    }

    private function markVerified(ClaimRequest $claimRequest, BusinessProfile $profile): void
    {
        $claimRequest->update(['status' => ClaimRequestStatus::Verified]);

        ProfileOwnership::query()->updateOrCreate(
            ['user_id' => $claimRequest->requested_by_user_id, 'profile_id' => $profile->id],
            ['role' => Role::Owner, 'granted_at' => now(), 'granted_via_claim_request_id' => $claimRequest->id],
        );

        $claimRequest->events()->create([
            'actor_user_id' => $claimRequest->requested_by_user_id,
            'action' => 'otp_verified',
        ]);

        $profile->update(['claim_status' => ClaimStatus::Claimed]);
    }

    private function userOwns(BusinessProfile $profile): bool
    {
        return ProfileOwnership::query()
            ->where('user_id', Auth::id())
            ->where('profile_id', $profile->id)
            ->exists();
    }

    private function maskEmail(string $email): string
    {
        [$name, $domain] = array_pad(explode('@', $email, 2), 2, '');

        if (strlen($name) <= 2) {
            return "{$name}***@{$domain}";
        }

        return substr($name, 0, 2).str_repeat('*', max(strlen($name) - 2, 3))."@{$domain}";
    }
}
