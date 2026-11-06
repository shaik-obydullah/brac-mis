<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company().' Branch',
            'code' => fake()->unique()->regexify('[A-Z]{3}-\d{3}'),
            'type' => Branch::TYPE_DISTRICT,
            'parent_id' => null,
            'district' => fake()->city(),
            'division' => fake()->city(),
            'status' => true,
        ];
    }
}
