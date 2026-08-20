<?php

namespace Tests\Feature;

use App\Enums\LegalStatus;
use App\Enums\PlanTier;
use App\Enums\SubscriptionStatus;
use App\Models\BusinessProfile;
use App\Models\CountryClearance;
use App\Models\ProfileImage;
use App\Models\ProfileOwnership;
use App\Models\ProfileService;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        CountryClearance::factory()->create(['country_code' => 'MU', 'legal_status' => LegalStatus::Cleared]);
    }

    public function test_non_owner_cannot_access_another_users_profile_dashboard(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $profile = BusinessProfile::factory()->create(['country_code' => 'MU']);
        ProfileOwnership::factory()->create(['user_id' => $owner->id, 'profile_id' => $profile->id]);

        $this->actingAs($intruder)
            ->get("/en/dashboard/{$profile->slug}")
            ->assertForbidden();
    }

    public function test_owner_can_view_and_update_their_profile(): void
    {
        $owner = User::factory()->create();
        $profile = BusinessProfile::factory()->create(['country_code' => 'MU', 'name' => 'Old Name']);
        ProfileOwnership::factory()->create(['user_id' => $owner->id, 'profile_id' => $profile->id]);

        $this->actingAs($owner)->get("/en/dashboard/{$profile->slug}")->assertOk();
        $this->actingAs($owner)->get("/en/dashboard/{$profile->slug}/edit")->assertOk();

        $this->actingAs($owner)->put("/en/dashboard/{$profile->slug}/edit", [
            'name' => 'New Name',
            'category' => $profile->category,
            'description_short' => 'Updated description.',
            'hours' => ['monday' => ['closed' => '1']],
        ])->assertRedirect();

        $profile->refresh();
        $this->assertSame('New Name', $profile->name);
        $this->assertSame('Updated description.', $profile->description_short);
        $this->assertSame('closed', $profile->hours['monday']);

        // Public page reflects the edit — plan §7 Phase 4 "done" criteria.
        $this->get("/en/p/{$profile->slug}")->assertSee('New Name');
    }

    public function test_owner_can_add_and_remove_a_service(): void
    {
        $owner = User::factory()->create();
        $profile = BusinessProfile::factory()->create(['country_code' => 'MU']);
        ProfileOwnership::factory()->create(['user_id' => $owner->id, 'profile_id' => $profile->id]);

        $this->actingAs($owner)->post("/en/dashboard/{$profile->slug}/services", [
            'name' => 'Airport transfer',
            'price' => '$20',
        ])->assertRedirect();

        $service = ProfileService::query()->where('profile_id', $profile->id)->firstOrFail();
        $this->assertSame('Airport transfer', $service->name);

        $this->actingAs($owner)
            ->delete("/en/dashboard/{$profile->slug}/services/{$service->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('profile_services', ['id' => $service->id]);
    }

    public function test_owner_can_upload_and_remove_an_image(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create();
        $profile = BusinessProfile::factory()->create(['country_code' => 'MU']);
        ProfileOwnership::factory()->create(['user_id' => $owner->id, 'profile_id' => $profile->id]);

        $this->actingAs($owner)->post("/en/dashboard/{$profile->slug}/images", [
            'image' => UploadedFile::fake()->image('photo.jpg'),
            'alt_text' => 'Front of the building',
        ])->assertRedirect();

        $image = ProfileImage::query()->where('profile_id', $profile->id)->firstOrFail();
        $this->assertSame('Front of the building', $image->alt_text);

        $this->actingAs($owner)
            ->delete("/en/dashboard/{$profile->slug}/images/{$image->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('profile_images', ['id' => $image->id]);
    }

    public function test_cannot_delete_another_profiles_service_via_a_profile_you_own(): void
    {
        $owner = User::factory()->create();
        $myProfile = BusinessProfile::factory()->create(['country_code' => 'MU']);
        $otherProfile = BusinessProfile::factory()->create(['country_code' => 'MU']);
        ProfileOwnership::factory()->create(['user_id' => $owner->id, 'profile_id' => $myProfile->id]);
        $otherService = ProfileService::factory()->create(['profile_id' => $otherProfile->id]);

        $this->actingAs($owner)
            ->delete("/en/dashboard/{$myProfile->slug}/services/{$otherService->id}")
            ->assertNotFound();

        $this->assertDatabaseHas('profile_services', ['id' => $otherService->id]);
    }

    public function test_freshness_report_is_gated_behind_managed_tier(): void
    {
        $owner = User::factory()->create();
        $profile = BusinessProfile::factory()->create(['country_code' => 'MU', 'plan_tier' => PlanTier::Registered]);
        ProfileOwnership::factory()->create(['user_id' => $owner->id, 'profile_id' => $profile->id]);

        $this->actingAs($owner)
            ->get("/en/dashboard/{$profile->slug}/freshness")
            ->assertOk()
            ->assertSee('Managed plan required');

        $profile->update(['plan_tier' => PlanTier::Managed]);

        $this->actingAs($owner)
            ->get("/en/dashboard/{$profile->slug}/freshness")
            ->assertOk()
            ->assertDontSee('Managed plan required');
    }

    public function test_billing_page_shows_the_subscription(): void
    {
        $owner = User::factory()->create();
        $profile = BusinessProfile::factory()->create(['country_code' => 'MU', 'plan_tier' => PlanTier::Managed]);
        ProfileOwnership::factory()->create(['user_id' => $owner->id, 'profile_id' => $profile->id]);
        Subscription::factory()->create([
            'profile_id' => $profile->id,
            'tier' => PlanTier::Managed,
            'status' => SubscriptionStatus::Active,
        ]);

        $this->actingAs($owner)
            ->get("/en/dashboard/{$profile->slug}/billing")
            ->assertOk()
            ->assertSee('Managed plan');
    }
}
