<?php

namespace Database\Factories;

use App\Enums\CoherenceStatus;
use App\Enums\SourceType;
use App\Models\BusinessProfile;
use App\Models\DataSource;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<DataSource> */
class DataSourceFactory extends Factory
{
    protected $model = DataSource::class;

    public function definition(): array
    {
        return [
            'profile_id' => BusinessProfile::factory(),
            'source_type' => SourceType::Facebook,
            'current_snapshot' => [],
            'coherence_status' => CoherenceStatus::NotChecked,
        ];
    }
}
