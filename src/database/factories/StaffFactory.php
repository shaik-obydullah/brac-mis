<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StaffFactory extends Factory
{
    protected $model = Staff::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'employee_id' => fake()->unique()->regexify('EMP-\d{4}'),
            'designation' => fake()->randomElement(['Field Officer', 'Manager', 'Coordinator', 'Assistant']),
            'branch_id' => Branch::factory(),
            'phone' => fake()->phoneNumber(),
        ];
    }
}
