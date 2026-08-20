<?php

namespace Database\Factories;

use App\Enums\ClaimStatus;
use App\Enums\Industry;
use App\Enums\PlanTier;
use App\Enums\ProfileStatus;
use App\Models\BusinessProfile;
use App\Models\CountryClearance;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<BusinessProfile> */
class BusinessProfileFactory extends Factory
{
    protected $model = BusinessProfile::class;

    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'canonical_id' => Str::slug($name).'-'.fake()->unique()->numerify('###'),
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('###'),
            'name' => $name,
            'industry' => fake()->randomElement(Industry::cases()),
            'category' => fake()->word(),
            'country_code' => fn () => CountryClearance::factory()->create()->country_code,
            'city' => fake()->city(),
            'address_line1' => fake()->streetAddress(),
            'description_short' => fake()->sentence(12),
            'status' => ProfileStatus::Published,
            'claim_status' => ClaimStatus::Unclaimed,
            'plan_tier' => PlanTier::None,
        ];
    }
}
