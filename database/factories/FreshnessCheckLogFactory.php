<?php

namespace Database\Factories;

use App\Enums\FreshnessSeverity;
use App\Models\BusinessProfile;
use App\Models\FreshnessCheckLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<FreshnessCheckLog> */
class FreshnessCheckLogFactory extends Factory
{
    protected $model = FreshnessCheckLog::class;

    public function definition(): array
    {
        return [
            'profile_id' => BusinessProfile::factory(),
            'checked_at' => now(),
            'discrepancies' => [],
            'severity' => FreshnessSeverity::Low,
        ];
    }
}
