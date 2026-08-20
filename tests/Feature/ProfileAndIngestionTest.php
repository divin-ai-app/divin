<?php

namespace Tests\Feature;

use App\Enums\DisputeStatus;
use App\Enums\Industry;
use App\Enums\LegalStatus;
use App\Enums\ProfileStatus;
use App\Models\BusinessProfile;
use App\Models\CountryClearance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileAndIngestionTest extends TestCase
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
            'gdpr_excluded' => false,
        ]);
    }

    public function test_published_profile_page_renders_with_correct_schema_org_type(): void
    {
        BusinessProfile::factory()->create([
            'country_code' => 'MU',
            'slug' => 'test-hotel',
            'name' => 'Test Hotel',
            'industry' => Industry::Hospitality,
            'status' => ProfileStatus::Published,
        ]);

        $response = $this->get('/en/p/test-hotel');

        $response->assertOk()
            ->assertSee('Test Hotel')
            ->assertSee('LodgingBusiness', false);
    }

    public function test_unpublished_profile_404s(): void
    {
        BusinessProfile::factory()->create([
            'country_code' => 'MU',
            'slug' => 'draft-business',
            'status' => ProfileStatus::Draft,
        ]);

        $this->get('/en/p/draft-business')->assertNotFound();
    }

    public function test_anyone_can_report_a_published_profile_without_logging_in(): void
    {
        $profile = BusinessProfile::factory()->create([
            'country_code' => 'MU',
            'slug' => 'report-me',
            'status' => ProfileStatus::Published,
        ]);

        $this->get('/en/p/report-me/report')->assertOk();

        $this->post('/en/p/report-me/report', [
            'type' => 'incorrect_data',
            'submitter_email' => 'reporter@example.com',
            'description' => 'The phone number listed is wrong.',
        ])->assertRedirect('/en/p/report-me');

        $this->assertDatabaseHas('disputes', [
            'profile_id' => $profile->id,
            'submitter_email' => 'reporter@example.com',
            'type' => 'incorrect_data',
            'status' => DisputeStatus::Open->value,
        ]);
    }

    public function test_report_form_404s_for_unpublished_profile(): void
    {
        BusinessProfile::factory()->create([
            'country_code' => 'MU',
            'slug' => 'draft-report-target',
            'status' => ProfileStatus::Draft,
        ]);

        $this->get('/en/p/draft-report-target/report')->assertNotFound();

        $this->post('/en/p/draft-report-target/report', [
            'type' => 'incorrect_data',
            'submitter_email' => 'reporter@example.com',
            'description' => 'Test.',
        ])->assertNotFound();
    }

    public function test_report_submission_validates_required_fields(): void
    {
        BusinessProfile::factory()->create([
            'country_code' => 'MU',
            'slug' => 'validate-me',
            'status' => ProfileStatus::Published,
        ]);

        $this->post('/en/p/validate-me/report', [])
            ->assertSessionHasErrors(['type', 'submitter_email', 'description']);
    }

    public function test_sitemap_includes_published_profiles_only(): void
    {
        BusinessProfile::factory()->create(['country_code' => 'MU', 'slug' => 'visible-one', 'status' => ProfileStatus::Published]);
        BusinessProfile::factory()->create(['country_code' => 'MU', 'slug' => 'hidden-one', 'status' => ProfileStatus::Draft]);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('/en/p/visible-one', false)
            ->assertDontSee('/en/p/hidden-one', false);
    }

    public function test_ingestion_endpoint_rejects_missing_key(): void
    {
        $this->postJson('/api/ingestion/profiles', [])->assertUnauthorized();
    }

    public function test_ingestion_endpoint_rejects_unverified_gdpr_country(): void
    {
        CountryClearance::factory()->create([
            'country_code' => 'FR',
            'country_name' => 'France',
            'legal_status' => LegalStatus::ExcludedGdpr,
            'gdpr_excluded' => true,
        ]);

        $response = $this->postJson('/api/ingestion/profiles', $this->validIngestionPayload(['country_code' => 'FR']), [
            'X-Ingestion-Key' => config('services.ingestion.key'),
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('business_profiles', ['country_code' => 'FR']);
    }

    public function test_ingestion_endpoint_creates_then_idempotently_updates_by_external_source_id(): void
    {
        $headers = ['X-Ingestion-Key' => config('services.ingestion.key')];

        $first = $this->postJson('/api/ingestion/profiles', $this->validIngestionPayload(), $headers);
        $first->assertStatus(201);
        $slug = $first->json('profile.slug');

        $second = $this->postJson('/api/ingestion/profiles', $this->validIngestionPayload(['name' => 'Renamed Business']), $headers);
        $second->assertStatus(200);

        // Slug/canonical_id stay stable across re-ingestion even though the
        // name changed — see IngestionController's docblock.
        $this->assertSame($slug, $second->json('profile.slug'));
        $this->assertSame(1, BusinessProfile::query()->where('canonical_id', 'MU-ext-123')->count());
        $this->assertDatabaseHas('business_profiles', ['canonical_id' => 'MU-ext-123', 'name' => 'Renamed Business']);
    }

    private function validIngestionPayload(array $overrides = []): array
    {
        return array_merge([
            'country_code' => 'MU',
            'source_type' => 'registry',
            'external_source_id' => 'ext-123',
            'name' => 'Ingested Test Business',
            'industry' => 'retail',
            'category' => 'Test Category',
            'city' => 'Port Louis',
            'address_line1' => '1 Test Street',
            'description_short' => 'A test business.',
            'source_snapshot' => ['raw' => 'test'],
        ], $overrides);
    }
}
