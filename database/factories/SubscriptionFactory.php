<?php

namespace Database\Factories;

use App\Enums\BillingCycle;
use App\Enums\PlanTier;
use App\Enums\SubscriptionStatus;
use App\Models\BusinessProfile;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Subscription> */
class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
            'profile_id' => BusinessProfile::factory(),
            'tier' => PlanTier::Registered,
            'billing_cycle' => BillingCycle::Annual,
            'status' => SubscriptionStatus::Active,
            'current_period_start' => now(),
            'current_period_end' => now()->addYear(),
            'renewal_date' => now()->addYear(),
        ];
    }
}
