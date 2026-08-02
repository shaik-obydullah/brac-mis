<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\Migrant;
use App\Models\Returnee;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReturneeFactory extends Factory
{
    protected $model = Returnee::class;

    public function definition(): array
    {
        $migrant = Migrant::factory()->create();
        return [
            'migrant_id' => $migrant->id,
            'beneficiary_id' => $migrant->beneficiary_id,
            'return_date' => fake()->date(),
            'return_reason' => fake()->randomElement(['Contract completed', 'Personal reasons', 'Family emergency', 'Medical reasons']),
            'origin_country_id' => Country::factory(),
            'current_status' => fake()->randomElement(['assessed', 'planning', 'in_progress', 'completed', 'dropped']),
        ];
    }
}
