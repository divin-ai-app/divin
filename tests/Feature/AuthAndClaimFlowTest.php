<?php

namespace Tests\Feature;

use App\Enums\ClaimStatus;
use App\Enums\LegalStatus;
use App\Enums\PlanTier;
use App\Mail\ClaimOtpMail;
use App\Mail\LoginLinkMail;
use App\Models\BusinessProfile;
use App\Models\CountryClearance;
use App\Models\LoginLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthAndClaimFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        CountryClearance::factory()->create([
            'country_code' => 'MU',
            'country_name' => 'Mauritius',
            'legal_status' => LegalStatus::Cleared,
        ]);
    }

    public function test_login_link_email_creates_a_single_use_link_and_signs_in_the_user(): void
    {
        Mail::fake();

        $this->post('/en/login', ['email' => 'newuser@example.com'])
            ->assertRedirect('/en/verify-request');

        Mail::assertSent(LoginLinkMail::class);

        $link = LoginLink::query()->where('email', 'newuser@example.com')->first();
        $this->assertNotNull($link);

        // Extract the raw token the way the email would have carried it: the
        // model only stores the hash, so re-derive it from the mailable arg.
        Mail::assertSent(LoginLinkMail::class, function (LoginLinkMail $mail) use (&$rawToken) {
            $rawToken = str($mail->url)->afterLast('/')->toString();

            return true;
        });

        $this->get("/en/login/{$rawToken}")
            ->assertRedirect('/en/dashboard');

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);

        // Single-use: the same link can't be consumed twice.
        auth()->logout();
        $this->get("/en/login/{$rawToken}")->assertRedirect('/en/login');
    }

    public function test_guest_is_redirected_to_locale_scoped_login_when_visiting_dashboard(): void
    {
        $this->get('/en/dashboard')->assertRedirect('/en/login');
    }

    public function test_claim_with_otp_grants_ownership_and_advances_to_plan_selection(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $profile = BusinessProfile::factory()->create([
            'country_code' => 'MU',
            'public_email' => 'owner@example.com',
            'claim_status' => ClaimStatus::Unclaimed,
        ]);

        $this->actingAs($user)->get("/en/claim/{$profile->slug}")->assertOk();

        Mail::assertSent(ClaimOtpMail::class);
        $this->assertDatabaseHas('claim_requests', ['profile_id' => $profile->id, 'requested_by_user_id' => $user->id]);

        // The code itself is never exposed to the response (only its hash is
        // stored) — read it back the way the OTP email would have carried it.
        $code = null;
        Mail::assertSent(ClaimOtpMail::class, function (ClaimOtpMail $mail) use (&$code) {
            $code = $mail->code;

            return true;
        });

        $this->actingAs($user)
            ->post("/en/claim/{$profile->slug}/otp/verify", ['code' => $code])
            ->assertRedirect("/en/claim/{$profile->slug}/plan");

        $this->assertDatabaseHas('profile_ownerships', ['user_id' => $user->id, 'profile_id' => $profile->id]);
        $this->assertSame(ClaimStatus::Claimed, $profile->fresh()->claim_status);
    }

    public function test_claim_without_public_email_falls_back_to_document_upload(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $profile = BusinessProfile::factory()->create([
            'country_code' => 'MU',
            'public_email' => null,
            'claim_status' => ClaimStatus::Unclaimed,
        ]);

        $this->actingAs($user)->get("/en/claim/{$profile->slug}")->assertOk();
        Mail::assertNotSent(ClaimOtpMail::class);

        $this->actingAs($user)
            ->post("/en/claim/{$profile->slug}/document", ['message' => 'I run this business.'])
            ->assertOk();

        $this->assertDatabaseHas('claim_requests', [
            'profile_id' => $profile->id,
            'verification_method' => 'document_upload',
            'status' => 'awaiting_verification',
        ]);
        // No ownership yet — awaits admin review (Phase 5).
        $this->assertDatabaseMissing('profile_ownerships', ['user_id' => $user->id, 'profile_id' => $profile->id]);
    }

    public function test_stripe_webhook_activates_subscription_and_verifies_profile(): void
    {
        $profile = BusinessProfile::factory()->create([
            'country_code' => 'MU',
            'claim_status' => ClaimStatus::Claimed,
            'plan_tier' => PlanTier::None,
        ]);

        $payload = json_encode([
            'id' => 'evt_test_123',
            'object' => 'event',
            'type' => 'checkout.session.completed',
            'data' => ['object' => [
                'id' => 'cs_test_123',
                'object' => 'checkout.session',
                'customer' => 'cus_test_123',
                'subscription' => 'sub_test_123',
                'metadata' => ['profile_id' => (string) $profile->id, 'tier' => 'managed'],
            ]],
        ]);

        $secret = config('services.stripe.webhook_secret');
        $timestamp = time();
        $signature = hash_hmac('sha256', "{$timestamp}.{$payload}", $secret);

        $this->call('POST', '/api/stripe/webhook', [], [], [], [
            'HTTP_Stripe-Signature' => "t={$timestamp},v1={$signature}",
            'CONTENT_TYPE' => 'application/json',
        ], $payload)->assertOk();

        $profile->refresh();
        $this->assertSame(ClaimStatus::Verified, $profile->claim_status);
        $this->assertSame(PlanTier::Managed, $profile->plan_tier);
        $this->assertDatabaseHas('subscriptions', [
            'profile_id' => $profile->id,
            'status' => 'active',
            'stripe_customer_id' => 'cus_test_123',
        ]);
    }

    public function test_stripe_webhook_rejects_bad_signature(): void
    {
        $this->call('POST', '/api/stripe/webhook', [], [], [], [
            'HTTP_Stripe-Signature' => 't=1,v1=not-a-real-signature',
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['type' => 'checkout.session.completed']))
            ->assertStatus(400);
    }
}
