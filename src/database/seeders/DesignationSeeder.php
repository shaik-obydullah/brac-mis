<?php

namespace Database\Seeders;

use App\Models\Designation;
use Illuminate\Database\Seeder;

class DesignationSeeder extends Seeder
{
    public function run(): void
    {
        $designations = [
            'Regional Manager',
            'Branch Manager',
            'Program Manager',
            'Senior Officer',
            'Officer',
            'Field Officer',
            'Data Entry Operator',
            'Monitoring & Evaluation Officer',
            'Finance Officer',
            'Case Worker',
        ];

        foreach ($designations as $name) {
            Designation::firstOrCreate(['name' => $name], ['status' => true]);
        }

        $this->command->info('Created ' . count($designations) . ' designations.');
    }
}
