<?php

namespace Database\Factories;

use App\Enums\BotName;
use App\Models\BusinessProfile;
use App\Models\CrawlerVisitDailyAgg;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CrawlerVisitDailyAgg> */
class CrawlerVisitDailyAggFactory extends Factory
{
    protected $model = CrawlerVisitDailyAgg::class;

    public function definition(): array
    {
        return [
            'profile_id' => BusinessProfile::factory(),
            'date' => now()->toDateString(),
            'bot_name' => BotName::GptBot,
            'visit_count' => 1,
        ];
    }
}
