<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\BusinessProfile;
use App\Models\ProfileOwnership;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ProfileOwnership> */
class ProfileOwnershipFactory extends Factory
{
    protected $model = ProfileOwnership::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'profile_id' => BusinessProfile::factory(),
            'role' => Role::Owner,
            'granted_at' => now(),
        ];
    }
}
