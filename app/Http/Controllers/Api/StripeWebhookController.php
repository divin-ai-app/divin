<?php

namespace App\Http\Controllers\Api;

use App\Enums\ClaimStatus;
use App\Enums\PlanTier;
use App\Enums\SubscriptionStatus;
use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

/**
 * Source of truth for subscription activation — the claim confirmation page
 * (ClaimController::confirmation) only ever shows a "processing" state;
 * this is what actually marks a BusinessProfile Verified. Scaffolded per
 * plan §1/§8 ("full payment processing... not fully wired to production
 * billing") — handles the one event the claim flow needs, not the full
 * subscription lifecycle (renewals/cancellations land alongside the
 * billing dashboard in a later phase).
 */
class StripeWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature', ''),
                $secret,
            );
        } catch (SignatureVerificationException|\UnexpectedValueException $e) {
            Log::warning('Stripe webhook signature verification failed', ['error' => $e->getMessage()]);

            return response('Invalid signature', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $this->activateSubscription($event->data->object);
        }

        return response('ok', 200);
    }

    private function activateSubscription(Session $session): void
    {
        $profileId = $session->metadata->profile_id ?? null;
        $tier = $session->metadata->tier ?? null;

        if (! $profileId || ! $tier) {
            Log::warning('Stripe checkout.session.completed missing profile_id/tier metadata', [
                'session_id' => $session->id,
            ]);

            return;
        }

        $profile = BusinessProfile::query()->find($profileId);

        if (! $profile) {
            Log::warning('Stripe webhook referenced an unknown profile', ['profile_id' => $profileId]);

            return;
        }

        Subscription::query()->updateOrCreate(
            ['profile_id' => $profile->id],
            [
                'tier' => PlanTier::from($tier),
                'status' => SubscriptionStatus::Active,
                'stripe_customer_id' => $session->customer,
                'stripe_subscription_id' => $session->subscription,
                'current_period_start' => now(),
                'current_period_end' => now()->addYear(),
                'renewal_date' => now()->addYear(),
            ],
        );

        $profile->update([
            'claim_status' => ClaimStatus::Verified,
            'plan_tier' => PlanTier::from($tier),
            'last_verified_at' => now(),
        ]);
    }
}
