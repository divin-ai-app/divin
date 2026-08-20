<?php

namespace Database\Factories;

use App\Models\BusinessProfile;
use App\Models\ProfileService;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ProfileService> */
class ProfileServiceFactory extends Factory
{
    protected $model = ProfileService::class;

    public function definition(): array
    {
        return [
            'profile_id' => BusinessProfile::factory(),
            'name' => fake()->words(3, true),
            'price' => '$'.fake()->numberBetween(5, 200),
        ];
    }
}
