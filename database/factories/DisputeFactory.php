<?php

namespace Database\Factories;

use App\Enums\DisputeStatus;
use App\Enums\DisputeType;
use App\Models\BusinessProfile;
use App\Models\Dispute;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Dispute> */
class DisputeFactory extends Factory
{
    protected $model = Dispute::class;

    public function definition(): array
    {
        return [
            'profile_id' => BusinessProfile::factory(),
            'submitter_email' => $this->faker->safeEmail(),
            'type' => DisputeType::IncorrectData,
            'description' => $this->faker->sentence(),
            'status' => DisputeStatus::Open,
        ];
    }
}
