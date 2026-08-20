<?php

namespace Database\Seeders;

use App\Enums\ClaimRequestStatus;
use App\Enums\ClaimStatus;
use App\Enums\DisputeStatus;
use App\Enums\DisputeType;
use App\Enums\Industry;
use App\Enums\LegalStatus;
use App\Enums\PlanTier;
use App\Enums\ProfileStatus;
use App\Enums\Role;
use App\Enums\SubscriptionStatus;
use App\Enums\VerificationMethod;
use App\Models\BusinessProfile;
use App\Models\ClaimRequest;
use App\Models\CountryClearance;
use App\Models\Dispute;
use App\Models\ProfileOwnership;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seeds enough real-shaped data to exercise every claim/plan state the
     * dashboard (Phase 4) and admin back-office (Phase 5) will need — not
     * just enough to make the public profile pages non-empty. See plan §7
     * Phase 2 "done" criteria and §5 data model.
     */
    public function run(): void
    {
        $this->seedCountryClearances();
        $demoOwner = $this->seedDemoUsers();
        $this->seedBusinessProfiles($demoOwner);
        $this->seedAdminQueueSamples($demoOwner);
    }

    private function seedCountryClearances(): void
    {
        $countries = [
            ['country_code' => 'MU', 'country_name' => 'Mauritius', 'legal_status' => LegalStatus::Cleared, 'gdpr_excluded' => false, 'cleared_at' => now()->subMonths(2)],
            ['country_code' => 'ZA', 'country_name' => 'South Africa', 'legal_status' => LegalStatus::InReview, 'gdpr_excluded' => false, 'cleared_at' => null],
            ['country_code' => 'FR', 'country_name' => 'France', 'legal_status' => LegalStatus::ExcludedGdpr, 'gdpr_excluded' => true, 'cleared_at' => null],
            ['country_code' => 'RE', 'country_name' => 'Réunion', 'legal_status' => LegalStatus::ExcludedGdpr, 'gdpr_excluded' => true, 'cleared_at' => null],
        ];

        foreach ($countries as $country) {
            CountryClearance::query()->updateOrCreate(
                ['country_code' => $country['country_code']],
                $country,
            );
        }
    }

    private function seedDemoUsers(): User
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@divin.ai'],
            ['name' => 'divin.ai Admin', 'role' => Role::Admin, 'password' => Str::random(32)],
        );

        User::query()->updateOrCreate(
            ['email' => 'staff@divin.ai'],
            ['name' => 'divin.ai Staff', 'role' => Role::Staff, 'password' => Str::random(32)],
        );

        return User::query()->updateOrCreate(
            ['email' => 'demo-owner@divin.ai'],
            ['name' => 'Demo Owner', 'role' => Role::Owner, 'password' => Str::random(32)],
        );
    }

    private function seedBusinessProfiles(User $demoOwner): void
    {
        foreach ($this->profileDefinitions() as $index => $definition) {
            // Cycle through claim/plan states so seeded data actually exercises
            // every dashboard/admin UI branch, not just "everything unclaimed".
            $stateIndex = $index % 4;
            [$claimStatus, $planTier] = match ($stateIndex) {
                0 => [ClaimStatus::Unclaimed, PlanTier::None],
                1 => [ClaimStatus::PendingClaim, PlanTier::None],
                2 => [ClaimStatus::Claimed, PlanTier::Registered],
                default => [ClaimStatus::Verified, PlanTier::Managed],
            };

            $profile = BusinessProfile::query()->updateOrCreate(
                ['slug' => $definition['slug']],
                [
                    'canonical_id' => $definition['slug'],
                    'name' => $definition['name'],
                    'industry' => $definition['industry'],
                    'category' => $definition['category'],
                    'country_code' => 'MU',
                    'city' => $definition['city'],
                    'address_line1' => $definition['address'],
                    'phone' => $definition['phone'],
                    // Only this one flagship demo profile has an on-file public
                    // email, so it's the one that exercises the OTP claim path
                    // end-to-end (delivered to a real, checkable inbox); every
                    // other seeded profile has none, so claiming them exercises
                    // the document-upload fallback path instead. See plan §4.
                    'public_email' => $definition['slug'] === 'coral-bay-guesthouse' ? 'hello@divin.ai' : null,
                    'description_short' => $definition['description'],
                    'hours' => $definition['hours'],
                    'price_range' => $definition['price_range'],
                    'status' => ProfileStatus::Published,
                    'claim_status' => $claimStatus,
                    'plan_tier' => $planTier,
                    'last_verified_at' => $claimStatus === ClaimStatus::Verified ? now()->subDays(3) : null,
                    'published_at' => now()->subMonths(1),
                ],
            );

            if (in_array($claimStatus, [ClaimStatus::Claimed, ClaimStatus::Verified], true)) {
                ProfileOwnership::query()->updateOrCreate(
                    ['user_id' => $demoOwner->id, 'profile_id' => $profile->id],
                    ['role' => Role::Owner, 'granted_at' => now()->subWeeks(2)],
                );

                Subscription::query()->updateOrCreate(
                    ['profile_id' => $profile->id],
                    [
                        'tier' => $planTier,
                        'status' => SubscriptionStatus::Active,
                        'current_period_start' => now()->subMonths(2),
                        'current_period_end' => now()->addMonths(10),
                        'renewal_date' => now()->addMonths(10),
                    ],
                );
            }
        }
    }

    /**
     * A couple of non-empty rows in each Phase 5 admin queue, so the back-
     * office doesn't look broken/empty the first time staff open it.
     */
    private function seedAdminQueueSamples(User $demoOwner): void
    {
        $pendingProfile = BusinessProfile::query()->where('claim_status', ClaimStatus::PendingClaim)->first();

        if ($pendingProfile && ! $pendingProfile->claimRequests()->exists()) {
            ClaimRequest::query()->create([
                'profile_id' => $pendingProfile->id,
                'requested_by_user_id' => $demoOwner->id,
                'verification_method' => VerificationMethod::DocumentUpload,
                'contact_value' => '',
                'status' => ClaimRequestStatus::AwaitingVerification,
                'review_notes' => "I'm the manager here — attached a copy of my business registration certificate.",
            ]);
        }

        $publishedProfile = BusinessProfile::query()->where('status', ProfileStatus::Published)
            ->where('id', '!=', $pendingProfile?->id)
            ->orderBy('id')
            ->first();

        if ($publishedProfile && ! $publishedProfile->disputes()->exists()) {
            Dispute::query()->create([
                'profile_id' => $publishedProfile->id,
                'submitter_email' => 'reporter@example.com',
                'type' => DisputeType::IncorrectData,
                'description' => 'The listed opening hours are wrong — this business is now closed on Sundays.',
                'status' => DisputeStatus::Open,
            ]);
        }
    }

    /**
     * @return array<int, array{slug: string, name: string, industry: Industry,
     *   category: string, city: string, address: string, phone: string,
     *   description: string, hours: array, price_range: string}>
     */
    private function profileDefinitions(): array
    {
        $weekdayHours = ['open' => '09:00', 'close' => '18:00'];
        $everyDay = fn (?array $override = null) => [
            'monday' => $weekdayHours, 'tuesday' => $weekdayHours, 'wednesday' => $weekdayHours,
            'thursday' => $weekdayHours, 'friday' => $weekdayHours,
            'saturday' => $override['saturday'] ?? ['open' => '09:00', 'close' => '13:00'],
            'sunday' => $override['sunday'] ?? 'closed',
        ];

        $raw = [
            // Healthcare
            ['Grand Baie Family Clinic', Industry::Healthcare, 'General Practice', 'Grand Baie', 'Royal Road', '+230 263 4021', 'A family medicine clinic serving Grand Baie and surrounding areas, offering general consultations, vaccinations, and basic diagnostics.', $everyDay(['sunday' => 'closed']), '$$'],
            ['Curepipe Dental Care', Industry::Healthcare, 'Dental Clinic', 'Curepipe', 'Chasteauneuf Street', '+230 676 2233', 'Modern dental practice offering routine checkups, cosmetic dentistry, and emergency care in Curepipe.', $everyDay(), '$$'],
            ['Quatre Bornes Physiotherapy Centre', Industry::Healthcare, 'Physiotherapy', 'Quatre Bornes', 'Sivananda Avenue', '+230 424 5566', 'Physiotherapy and rehabilitation clinic specializing in sports injuries and post-surgical recovery.', $everyDay(['saturday' => 'closed']), '$$'],
            ['Port Louis Eye Institute', Industry::Healthcare, 'Ophthalmology', 'Port Louis', 'Pope Hennessy Street', '+230 208 7744', 'Specialist eye clinic offering comprehensive vision care, cataract surgery, and pediatric ophthalmology.', $everyDay(), '$$$'],

            // Hospitality
            ['Coral Bay Guesthouse', Industry::Hospitality, 'Guesthouse', 'Grand Baie', 'Coastal Road', '+230 263 8890', 'A family-run beachfront guesthouse in Grand Baie with 8 rooms, a pool, and easy access to public beaches and watersports.', $everyDay(['saturday' => ['open' => '00:00', 'close' => '23:59'], 'sunday' => ['open' => '00:00', 'close' => '23:59']]), '$$'],
            ['Tamarin Bay Boutique Hotel', Industry::Hospitality, 'Boutique Hotel', 'Tamarin', 'La Mivoie Road', '+230 483 1122', 'A 12-room boutique hotel overlooking Tamarin Bay, popular with surfers and sunset watchers.', $everyDay(['saturday' => ['open' => '00:00', 'close' => '23:59'], 'sunday' => ['open' => '00:00', 'close' => '23:59']]), '$$$'],
            ['Flic en Flac Beach Lodge', Industry::Hospitality, 'Lodge', 'Flic en Flac', 'Beach Access Road', '+230 453 6677', 'Budget-friendly beach lodge steps from Flic en Flac public beach, with self-catering apartments.', $everyDay(['saturday' => ['open' => '00:00', 'close' => '23:59'], 'sunday' => ['open' => '00:00', 'close' => '23:59']]), '$'],
            ['Mahebourg Heritage Inn', Industry::Hospitality, 'Guesthouse', 'Mahébourg', 'Rue des Maëfat', '+230 631 4455', 'A restored colonial-era house turned 6-room guesthouse near Mahébourg\'s waterfront and market.', $everyDay(['saturday' => ['open' => '00:00', 'close' => '23:59'], 'sunday' => ['open' => '00:00', 'close' => '23:59']]), '$$'],

            // Retail
            ['Rose Hill Fabric House', Industry::Retail, 'Textiles', 'Rose Hill', 'Eugene Laurent Street', '+230 464 3321', 'Family-owned fabric and textile shop stocking imported and local cloth, since 1985.', $everyDay(), '$'],
            ['Ébène Tech Corner', Industry::Retail, 'Electronics', 'Ébène', 'Cybercity Avenue', '+230 465 9988', 'Electronics and computer accessories retailer serving the Ébène Cybercity business district.', $everyDay(['sunday' => 'closed']), '$$'],
            ['Port Louis Spice Market Stall', Industry::Retail, 'Grocery', 'Port Louis', 'Central Market', '+230 208 3344', 'A long-standing spice and dried-goods stall in Port Louis\'s Central Market.', $everyDay(['saturday' => ['open' => '06:00', 'close' => '12:00'], 'sunday' => 'closed']), '$'],
            ['Grand Baie Surf & Skate Shop', Industry::Retail, 'Sporting Goods', 'Grand Baie', 'La Salette Road', '+230 263 5544', 'Surf, skate, and beach gear retailer with board rentals for tourists and locals.', $everyDay(), '$$'],

            // Food
            ['Le Capitaine Seafood House', Industry::Food, 'Seafood Restaurant', 'Grand Baie', 'Sunset Boulevard', '+230 263 7788', 'Waterfront seafood restaurant known for fresh catch-of-the-day and Mauritian-Creole fusion dishes.', $everyDay(), '$$$'],
            ['Chez Tante Marie', Industry::Food, 'Creole Restaurant', 'Curepipe', 'Church Street', '+230 676 9911', 'A local institution serving traditional Mauritian Creole home cooking since 1978.', $everyDay(['sunday' => 'closed']), '$$'],
            ['Flic en Flac Beach Grill', Industry::Food, 'Casual Dining', 'Flic en Flac', 'Public Beach Road', '+230 453 2233', 'Casual beachside grill serving grilled fish, chicken, and cocktails, open late.', $everyDay(['saturday' => ['open' => '11:00', 'close' => '23:00'], 'sunday' => ['open' => '11:00', 'close' => '23:00']]), '$$'],
            ['Quatre Bornes Night Market Kitchen', Industry::Food, 'Street Food', 'Quatre Bornes', 'Coromandel Road', '+230 424 6688', 'A street-food stall inside Quatre Bornes\'s famous night market, known for dholl puri and gateaux piments.', $everyDay(['saturday' => ['open' => '17:00', 'close' => '22:00'], 'sunday' => 'closed']), '$'],

            // Financial Services
            ['Ébène Realty Partners', Industry::FinancialServices, 'Real Estate Agency', 'Ébène', 'Bank Street', '+230 465 1122', 'Real estate agency specializing in residential and commercial property across the Ébène/Moka corridor.', $everyDay(['saturday' => 'closed']), '$$'],
            ['Port Louis Wealth Advisory', Industry::FinancialServices, 'Financial Advisory', 'Port Louis', 'Sir William Newton Street', '+230 208 4433', 'Independent financial advisory firm offering retirement planning and investment guidance.', $everyDay(['saturday' => 'closed']), '$$$'],
            ['Grand Baie Insurance Brokers', Industry::FinancialServices, 'Insurance Broker', 'Grand Baie', 'Royal Road', '+230 263 9900', 'Independent insurance brokerage covering home, auto, and travel insurance for the northern coast.', $everyDay(['saturday' => 'closed']), '$$'],
            ['Rose Hill Property Consultants', Industry::FinancialServices, 'Real Estate Agency', 'Rose Hill', 'St Jean Road', '+230 464 7766', 'Boutique property consultancy focused on first-time buyers and rental management in the plains.', $everyDay(['saturday' => 'closed']), '$$'],
        ];

        return array_map(function (array $row) {
            [$name, $industry, $category, $city, $address, $phone, $description, $hours, $priceRange] = $row;

            return [
                'slug' => Str::slug($name),
                'name' => $name,
                'industry' => $industry,
                'category' => $category,
                'city' => $city,
                'address' => $address,
                'phone' => $phone,
                'description' => $description,
                'hours' => $hours,
                'price_range' => $priceRange,
            ];
        }, $raw);
    }
}
