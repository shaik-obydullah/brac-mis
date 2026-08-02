<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class CountryFactory extends Factory
{
    protected $model = Country::class;

    public function definition(): array
    {
        return [
            'name' => fake()->country(),
            'code' => fake()->unique()->countryCode(),
            'currency' => fake()->currencyCode(),
            'status' => true,
        ];
    }
}
