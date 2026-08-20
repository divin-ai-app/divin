<?php

namespace Tests\Feature;

use App\Enums\CoherenceStatus;
use App\Enums\LegalStatus;
use App\Enums\PlanTier;
use App\Enums\ProfileStatus;
use App\Enums\ResolutionAction;
use App\Enums\SourceType;
use App\Mail\FreshnessAlertMail;
use App\Models\BusinessProfile;
use App\Models\CountryClearance;
use App\Models\CrawlerVisitDailyAgg;
use App\Models\CrawlerVisitLog;
use App\Models\DataSource;
use App\Models\FreshnessCheckLog;
use App\Models\ProfileOwnership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class FreshnessAndCrawlerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        CountryClearance::factory()->create(['country_code' => 'MU', 'legal_status' => LegalStatus::Cleared]);
    }

    public function test_owner_can_accept_a_freshness_discrepancy_and_it_updates_the_profile(): void
    {
        $owner = User::factory()->create();
        $profile = BusinessProfile::factory()->create([
            'country_code' => 'MU',
            'plan_tier' => PlanTier::Managed,
            'phone' => '+230 111 1111',
        ]);
        ProfileOwnership::factory()->create(['user_id' => $owner->id, 'profile_id' => $profile->id]);
        $dataSource = DataSource::factory()->create(['profile_id' => $profile->id]);
        $log = FreshnessCheckLog::factory()->create([
            'profile_id' => $profile->id,
            'data_source_id' => $dataSource->id,
            'discrepancies' => [
                ['field' => 'phone', 'label' => 'Phone', 'current_value' => '+230 111 1111', 'source_value' => '+230 222 2222'],
            ],
        ]);

        $this->actingAs($owner)
            ->put("/en/dashboard/{$profile->slug}/freshness/{$log->id}", ['field' => 'phone', 'action' => 'accepted_new_value'])
            ->assertRedirect();

        $this->assertSame('+230 222 2222', $profile->fresh()->phone);
        $log->refresh();
        $this->assertNotNull($log->resolved_at);
        $this->assertSame(ResolutionAction::AcceptedNewValue, $log->resolution_action);
    }

    public function test_owner_can_keep_current_value_without_changing_the_profile(): void
    {
        $owner = User::factory()->create();
        $profile = BusinessProfile::factory()->create([
            'country_code' => 'MU',
            'plan_tier' => PlanTier::Managed,
            'phone' => '+230 111 1111',
        ]);
        ProfileOwnership::factory()->create(['user_id' => $owner->id, 'profile_id' => $profile->id]);
        $log = FreshnessCheckLog::factory()->create([
            'profile_id' => $profile->id,
            'discrepancies' => [
                ['field' => 'phone', 'label' => 'Phone', 'current_value' => '+230 111 1111', 'source_value' => '+230 222 2222'],
            ],
        ]);

        $this->actingAs($owner)
            ->put("/en/dashboard/{$profile->slug}/freshness/{$log->id}", ['field' => 'phone', 'action' => 'kept_current_value'])
            ->assertRedirect();

        $this->assertSame('+230 111 1111', $profile->fresh()->phone);
        $this->assertSame(ResolutionAction::KeptCurrentValue, $log->fresh()->resolution_action);
    }

    public function test_owner_can_resolve_multiple_discrepancies_in_a_log_independently(): void
    {
        $owner = User::factory()->create();
        $profile = BusinessProfile::factory()->create([
            'country_code' => 'MU',
            'plan_tier' => PlanTier::Managed,
            'phone' => '+230 111 1111',
            'website' => null,
        ]);
        ProfileOwnership::factory()->create(['user_id' => $owner->id, 'profile_id' => $profile->id]);
        $log = FreshnessCheckLog::factory()->create([
            'profile_id' => $profile->id,
            'discrepancies' => [
                ['field' => 'phone', 'label' => 'Phone', 'current_value' => '+230 111 1111', 'source_value' => '+230 222 2222', 'resolution' => null],
                ['field' => 'website', 'label' => 'Website', 'current_value' => null, 'source_value' => 'https://example.mu', 'resolution' => null],
            ],
        ]);

        // Keep the phone as-is.
        $this->actingAs($owner)
            ->put("/en/dashboard/{$profile->slug}/freshness/{$log->id}", ['field' => 'phone', 'action' => 'kept_current_value'])
            ->assertRedirect();

        $this->assertSame('+230 111 1111', $profile->fresh()->phone);
        $this->assertNull($log->fresh()->resolved_at, 'log should stay open until every field is resolved');

        // Accept the website.
        $this->actingAs($owner)
            ->put("/en/dashboard/{$profile->slug}/freshness/{$log->id}", ['field' => 'website', 'action' => 'accepted_new_value'])
            ->assertRedirect();

        $profile->refresh();
        $log->refresh();
        $this->assertSame('https://example.mu', $profile->website);
        $this->assertSame('+230 111 1111', $profile->phone);
        $this->assertNotNull($log->resolved_at);
        $this->assertNull($log->resolution_action, 'mixed resolutions should not collapse to a single action');

        // The phone was already resolved — re-submitting it must 404, not silently overwrite.
        $this->actingAs($owner)
            ->put("/en/dashboard/{$profile->slug}/freshness/{$log->id}", ['field' => 'phone', 'action' => 'accepted_new_value'])
            ->assertNotFound();

        $this->assertSame('+230 111 1111', $profile->fresh()->phone);
    }

    public function test_cannot_resolve_another_profiles_freshness_log(): void
    {
        $owner = User::factory()->create();
        $myProfile = BusinessProfile::factory()->create(['country_code' => 'MU']);
        $otherProfile = BusinessProfile::factory()->create(['country_code' => 'MU']);
        ProfileOwnership::factory()->create(['user_id' => $owner->id, 'profile_id' => $myProfile->id]);
        $otherLog = FreshnessCheckLog::factory()->create(['profile_id' => $otherProfile->id]);

        $this->actingAs($owner)
            ->put("/en/dashboard/{$myProfile->slug}/freshness/{$otherLog->id}", ['action' => 'kept_current_value'])
            ->assertNotFound();
    }

    public function test_cannot_resolve_an_already_resolved_log(): void
    {
        $owner = User::factory()->create();
        $profile = BusinessProfile::factory()->create(['country_code' => 'MU']);
        ProfileOwnership::factory()->create(['user_id' => $owner->id, 'profile_id' => $profile->id]);
        $log = FreshnessCheckLog::factory()->create(['profile_id' => $profile->id, 'resolved_at' => now()]);

        $this->actingAs($owner)
            ->put("/en/dashboard/{$profile->slug}/freshness/{$log->id}", ['action' => 'kept_current_value'])
            ->assertNotFound();
    }

    public function test_crawler_activity_page_is_gated_behind_a_paid_plan(): void
    {
        $owner = User::factory()->create();
        $profile = BusinessProfile::factory()->create(['country_code' => 'MU', 'plan_tier' => PlanTier::None]);
        ProfileOwnership::factory()->create(['user_id' => $owner->id, 'profile_id' => $profile->id]);

        $this->actingAs($owner)
            ->get("/en/dashboard/{$profile->slug}/crawler-activity")
            ->assertOk()
            ->assertSee('Claim this profile to see crawler activity');

        $profile->update(['plan_tier' => PlanTier::Registered]);

        $this->actingAs($owner)
            ->get("/en/dashboard/{$profile->slug}/crawler-activity")
            ->assertOk()
            ->assertDontSee('Claim this profile to see crawler activity');
    }

    public function test_crawler_activity_daily_chart_reflects_todays_visits(): void
    {
        $owner = User::factory()->create();
        $profile = BusinessProfile::factory()->create(['country_code' => 'MU', 'plan_tier' => PlanTier::Managed]);
        ProfileOwnership::factory()->create(['user_id' => $owner->id, 'profile_id' => $profile->id]);
        CrawlerVisitDailyAgg::factory()->create([
            'profile_id' => $profile->id,
            'date' => now()->toDateString(),
            'bot_name' => 'gptbot',
            'visit_count' => 9,
        ]);

        // The per-day bar chart is built from a Carbon-vs-string date
        // comparison that's easy to silently get wrong (see
        // CrawlerVisitDailyAgg::incrementFor's docblock) — assert the
        // count actually reaches the view, not just the bot-totals table.
        $this->actingAs($owner)
            ->get("/en/dashboard/{$profile->slug}/crawler-activity")
            ->assertOk()
            ->assertSee('9');
    }

    public function test_owner_can_simulate_a_crawler_visit(): void
    {
        $owner = User::factory()->create();
        $profile = BusinessProfile::factory()->create(['country_code' => 'MU', 'plan_tier' => PlanTier::Registered]);
        ProfileOwnership::factory()->create(['user_id' => $owner->id, 'profile_id' => $profile->id]);

        $this->actingAs($owner)
            ->post("/en/dashboard/{$profile->slug}/crawler-activity/simulate")
            ->assertRedirect();

        $this->assertSame(1, CrawlerVisitDailyAgg::query()->where('profile_id', $profile->id)->sum('visit_count'));
    }

    public function test_a_bot_visiting_a_published_profile_is_logged(): void
    {
        $profile = BusinessProfile::factory()->create(['country_code' => 'MU', 'status' => ProfileStatus::Published]);

        $this->get("/en/p/{$profile->slug}", ['User-Agent' => 'Mozilla/5.0 (compatible; ClaudeBot/1.0; +https://www.anthropic.com)'])
            ->assertOk();

        $this->assertDatabaseHas('crawler_visit_logs', [
            'profile_id' => $profile->id,
            'bot_name' => 'claudebot',
        ]);
    }

    public function test_a_normal_browser_visit_is_not_logged_as_a_crawler(): void
    {
        $profile = BusinessProfile::factory()->create(['country_code' => 'MU', 'status' => ProfileStatus::Published]);

        $this->get("/en/p/{$profile->slug}", ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0'])
            ->assertOk();

        $this->assertDatabaseCount('crawler_visit_logs', 0);
    }

    public function test_check_freshness_command_creates_a_log_and_alerts_the_owner_once(): void
    {
        Mail::fake();

        $owner = User::factory()->create();
        $profile = BusinessProfile::factory()->create([
            'country_code' => 'MU',
            'plan_tier' => PlanTier::Managed,
            'phone' => '+230 111 1111',
            'address_line1' => 'Old Address',
        ]);
        ProfileOwnership::factory()->create(['user_id' => $owner->id, 'profile_id' => $profile->id]);
        DataSource::factory()->create([
            'profile_id' => $profile->id,
            'source_type' => SourceType::Facebook,
            'current_snapshot' => ['phone' => '+230 999 9999', 'address_line1' => 'Old Address'],
        ]);

        Artisan::call('freshness:check');

        $this->assertDatabaseHas('freshness_check_logs', ['profile_id' => $profile->id]);
        Mail::assertSent(FreshnessAlertMail::class, 1);

        // Re-running while the log is still unresolved must not re-alert.
        Artisan::call('freshness:check');
        Mail::assertSent(FreshnessAlertMail::class, 1);
    }

    public function test_check_freshness_command_marks_a_matching_source_aligned(): void
    {
        $profile = BusinessProfile::factory()->create([
            'country_code' => 'MU',
            'plan_tier' => PlanTier::Managed,
            'phone' => '+230 111 1111',
        ]);
        $dataSource = DataSource::factory()->create([
            'profile_id' => $profile->id,
            'current_snapshot' => ['phone' => '+230 111 1111'],
            'coherence_status' => CoherenceStatus::NotChecked,
        ]);

        Artisan::call('freshness:check');

        $this->assertSame(CoherenceStatus::Aligned, $dataSource->fresh()->coherence_status);
        $this->assertDatabaseCount('freshness_check_logs', 0);
    }

    public function test_check_freshness_command_ignores_non_managed_profiles(): void
    {
        $profile = BusinessProfile::factory()->create([
            'country_code' => 'MU',
            'plan_tier' => PlanTier::Registered,
            'phone' => '+230 111 1111',
        ]);
        DataSource::factory()->create([
            'profile_id' => $profile->id,
            'current_snapshot' => ['phone' => '+230 999 9999'],
        ]);

        Artisan::call('freshness:check');

        $this->assertDatabaseCount('freshness_check_logs', 0);
    }

    public function test_crawler_rollup_command_aggregates_and_prunes_old_raw_logs(): void
    {
        $profile = BusinessProfile::factory()->create(['country_code' => 'MU']);
        CrawlerVisitDailyAgg::query()->create([
            'profile_id' => $profile->id,
            'date' => now()->subDay()->toDateString(),
            'bot_name' => 'claudebot',
            'visit_count' => 4,
        ]);
        CrawlerVisitLog::query()->create([
            'profile_id' => $profile->id,
            'bot_name' => 'claudebot',
            'path' => 'en/p/test',
            'user_agent' => 'ClaudeBot/1.0',
            'ip_hash' => 'abc',
            'timestamp' => now()->subDay(),
        ]);
        // Today's raw log must NOT be rolled up yet.
        CrawlerVisitLog::query()->create([
            'profile_id' => $profile->id,
            'bot_name' => 'claudebot',
            'path' => 'en/p/test',
            'user_agent' => 'ClaudeBot/1.0',
            'ip_hash' => 'abc',
            'timestamp' => now(),
        ]);

        Artisan::call('crawler:rollup');

        $this->assertSame(5, CrawlerVisitDailyAgg::query()
            ->where('profile_id', $profile->id)
            ->whereDate('date', now()->subDay()->toDateString())
            ->value('visit_count'));
        $this->assertDatabaseCount('crawler_visit_logs', 1);
    }
}
