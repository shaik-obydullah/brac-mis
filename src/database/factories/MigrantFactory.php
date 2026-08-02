<?php

namespace Database\Factories;

use App\Models\Beneficiary;
use App\Models\Country;
use App\Models\Migrant;
use Illuminate\Database\Eloquent\Factories\Factory;

class MigrantFactory extends Factory
{
    protected $model = Migrant::class;

    public function definition(): array
    {
        return [
            'beneficiary_id' => Beneficiary::factory(),
            'brac_id' => fake()->unique()->regexify('BRAC-MIG-\d{3}'),
            'name' => fake()->name(),
            'gender' => fake()->randomElement(['male', 'female']),
            'date_of_birth' => fake()->date(max: '-20 years'),
            'nid_number' => fake()->numerify('#############'),
            'phone' => fake()->phoneNumber(),
            'passport_number' => fake()->regexify('[A-Z]{2}\d{7}'),
            'destination_country_id' => Country::factory(),
            'destination_city' => fake()->city(),
            'skill_level' => fake()->randomElement(['basic', 'intermediate', 'advanced']),
            'education_level' => fake()->randomElement(['Class 5', 'SSC', 'HSC', 'Bachelor']),
            'occupation' => fake()->jobTitle(),
            'status' => fake()->randomElement(['registered', 'pre_departure', 'deployed', 'returned', 'cancelled']),
        ];
    }
}
