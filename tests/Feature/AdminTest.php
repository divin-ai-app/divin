<?php

namespace Tests\Feature;

use App\Enums\ClaimRequestStatus;
use App\Enums\ClaimStatus;
use App\Enums\DisputeStatus;
use App\Enums\DisputeType;
use App\Enums\LegalStatus;
use App\Enums\ProfileStatus;
use App\Enums\Role;
use App\Enums\VerificationMethod;
use App\Models\BusinessProfile;
use App\Models\ClaimRequest;
use App\Models\CountryClearance;
use App\Models\Dispute;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private CountryClearance $clearedCountry;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clearedCountry = CountryClearance::factory()->create([
            'country_code' => 'MU',
            'country_name' => 'Mauritius',
            'legal_status' => LegalStatus::Cleared,
        ]);
    }

    public function test_guest_hitting_admin_is_redirected_to_login(): void
    {
        $this->get('/en/admin')->assertRedirect('/en/login');
    }

    public function test_signed_in_owner_is_forbidden_from_admin(): void
    {
        $owner = User::factory()->create(['role' => Role::Owner]);

        $this->actingAs($owner)->get('/en/admin')->assertForbidden();
    }

    public function test_staff_can_view_admin_dashboard(): void
    {
        $staff = User::factory()->create(['role' => Role::Staff]);

        $this->actingAs($staff)->get('/en/admin')->assertOk();
    }

    public function test_admin_can_approve_a_document_upload_claim_and_grants_ownership(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $requester = User::factory()->create();
        $profile = BusinessProfile::factory()->create([
            'country_code' => 'MU',
            'claim_status' => ClaimStatus::PendingClaim,
        ]);

        $claimRequest = ClaimRequest::factory()->create([
            'profile_id' => $profile->id,
            'requested_by_user_id' => $requester->id,
            'verification_method' => VerificationMethod::DocumentUpload,
            'status' => ClaimRequestStatus::AwaitingVerification,
        ]);

        $this->actingAs($admin)
            ->post("/en/admin/claims/{$claimRequest->id}/approve")
            ->assertRedirect();

        $this->assertDatabaseHas('profile_ownerships', ['user_id' => $requester->id, 'profile_id' => $profile->id]);
        $this->assertSame(ClaimStatus::Claimed, $profile->fresh()->claim_status);
        $this->assertSame(ClaimRequestStatus::Approved, $claimRequest->fresh()->status);
    }

    public function test_admin_can_reject_a_claim_with_a_reason(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $profile = BusinessProfile::factory()->create(['country_code' => 'MU']);
        $claimRequest = ClaimRequest::factory()->create([
            'profile_id' => $profile->id,
            'status' => ClaimRequestStatus::AwaitingVerification,
        ]);

        $this->actingAs($admin)
            ->post("/en/admin/claims/{$claimRequest->id}/reject", ['review_notes' => 'Documents did not match.'])
            ->assertRedirect();

        $this->assertSame(ClaimRequestStatus::Rejected, $claimRequest->fresh()->status);
        $this->assertDatabaseMissing('profile_ownerships', ['profile_id' => $profile->id]);
    }

    public function test_admin_reject_requires_a_reason(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $claimRequest = ClaimRequest::factory()->create([
            'profile_id' => BusinessProfile::factory()->create(['country_code' => 'MU'])->id,
            'status' => ClaimRequestStatus::AwaitingVerification,
        ]);

        $this->actingAs($admin)
            ->post("/en/admin/claims/{$claimRequest->id}/reject", [])
            ->assertSessionHasErrors('review_notes');
    }

    public function test_admin_resolving_a_dispute_as_removed_unpublishes_the_profile(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $profile = BusinessProfile::factory()->create([
            'country_code' => 'MU',
            'status' => ProfileStatus::Published,
        ]);
        $dispute = Dispute::factory()->create([
            'profile_id' => $profile->id,
            'type' => DisputeType::NotMyBusiness,
            'status' => DisputeStatus::Open,
        ]);

        $this->actingAs($admin)
            ->put("/en/admin/disputes/{$dispute->id}", [
                'status' => 'removed',
                'resolution_notes' => 'Confirmed not a real business listing.',
            ])
            ->assertRedirect();

        $this->assertSame(DisputeStatus::Removed, $dispute->fresh()->status);
        $this->assertSame(ProfileStatus::Removed, $profile->fresh()->status);
    }

    public function test_admin_resolving_a_dispute_as_corrected_does_not_change_profile_status(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $profile = BusinessProfile::factory()->create([
            'country_code' => 'MU',
            'status' => ProfileStatus::Published,
        ]);
        $dispute = Dispute::factory()->create([
            'profile_id' => $profile->id,
            'status' => DisputeStatus::Open,
        ]);

        $this->actingAs($admin)
            ->put("/en/admin/disputes/{$dispute->id}", [
                'status' => 'corrected',
                'resolution_notes' => 'Fixed the phone number.',
            ])
            ->assertRedirect();

        $this->assertSame(DisputeStatus::Corrected, $dispute->fresh()->status);
        $this->assertSame(ProfileStatus::Published, $profile->fresh()->status);
    }

    public function test_admin_can_update_country_clearance_status(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $clearance = CountryClearance::factory()->create([
            'country_code' => 'ZA',
            'country_name' => 'South Africa',
            'legal_status' => LegalStatus::InReview,
        ]);

        $this->actingAs($admin)
            ->put("/en/admin/country-clearance/{$clearance->id}", [
                'legal_status' => 'cleared',
                'gdpr_excluded' => '0',
            ])
            ->assertRedirect();

        $clearance->refresh();
        $this->assertSame(LegalStatus::Cleared, $clearance->legal_status);
        $this->assertNotNull($clearance->cleared_at);
    }

    public function test_staff_can_reach_owner_dashboard_edit_for_any_profile(): void
    {
        $staff = User::factory()->create(['role' => Role::Staff]);
        $profile = BusinessProfile::factory()->create(['country_code' => 'MU']);

        $this->actingAs($staff)->get("/en/dashboard/{$profile->slug}/edit")->assertOk();
    }

    public function test_admin_profiles_index_filters_by_status(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        BusinessProfile::factory()->create(['country_code' => 'MU', 'name' => 'Published Co', 'status' => ProfileStatus::Published]);
        BusinessProfile::factory()->create(['country_code' => 'MU', 'name' => 'Draft Co', 'status' => ProfileStatus::Draft]);

        $response = $this->actingAs($admin)->get('/en/admin/profiles?status=published');

        $response->assertOk()->assertSee('Published Co')->assertDontSee('Draft Co');
    }
}
