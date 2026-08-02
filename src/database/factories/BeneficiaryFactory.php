<?php

namespace Database\Factories;

use App\Models\Beneficiary;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BeneficiaryFactory extends Factory
{
    protected $model = Beneficiary::class;

    public function definition(): array
    {
        return [
            'brac_id' => fake()->unique()->regexify('BRAC-BEN-\d{5}'),
            'branch_id' => Branch::factory(),
            'name' => fake()->name(),
            'father_name' => fake()->name('male'),
            'mother_name' => fake()->name('female'),
            'gender' => fake()->randomElement(['male', 'female']),
            'date_of_birth' => fake()->date(max: '-18 years'),
            'nid_number' => fake()->unique()->numerify('#############'),
            'phone' => fake()->phoneNumber(),
            'address_line_1' => fake()->streetAddress(),
            'occupation' => fake()->jobTitle(),
            'monthly_income' => fake()->randomFloat(2, 5000, 100000),
            'family_size' => fake()->numberBetween(1, 10),
            'status' => 'active',
            'created_by' => User::factory(),
        ];
    }
}
