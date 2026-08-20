<?php

namespace Database\Factories;

use App\Models\BusinessProfile;
use App\Models\ProfileImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ProfileImage> */
class ProfileImageFactory extends Factory
{
    protected $model = ProfileImage::class;

    public function definition(): array
    {
        return [
            'profile_id' => BusinessProfile::factory(),
            'url' => 'https://example.test/uploads/'.$this->faker->uuid().'.jpg',
            'sort_order' => 0,
        ];
    }
}
