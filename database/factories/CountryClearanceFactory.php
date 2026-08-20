<?php

namespace Database\Factories;

use App\Enums\LegalStatus;
use App\Models\CountryClearance;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CountryClearance> */
class CountryClearanceFactory extends Factory
{
    protected $model = CountryClearance::class;

    public function definition(): array
    {
        return [
            'country_code' => strtoupper(fake()->unique()->lexify('??')),
            'country_name' => fake()->country(),
            'legal_status' => LegalStatus::Cleared,
            'gdpr_excluded' => false,
        ];
    }
}
