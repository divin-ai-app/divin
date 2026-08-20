<?php

namespace Database\Factories;

use App\Enums\ClaimRequestStatus;
use App\Enums\VerificationMethod;
use App\Models\BusinessProfile;
use App\Models\ClaimRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ClaimRequest> */
class ClaimRequestFactory extends Factory
{
    protected $model = ClaimRequest::class;

    public function definition(): array
    {
        return [
            'profile_id' => BusinessProfile::factory(),
            'requested_by_user_id' => User::factory(),
            'verification_method' => VerificationMethod::DocumentUpload,
            'contact_value' => $this->faker->safeEmail(),
            'status' => ClaimRequestStatus::AwaitingVerification,
        ];
    }
}
