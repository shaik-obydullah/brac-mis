<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'admin@bracmis.org')->first();
        if (! $user) {
            $this->command->warn('No admin user found for report seeding.');

            return;
        }

        $reports = [
            ['profile-summary', 'Profile Summary Report - July 2026', ['status' => 'active', 'from' => '2026-07-01', 'to' => '2026-07-31']],
            ['profile-summary', 'Profile Type Distribution Report', ['group_by' => 'profile_type']],
            ['migration-summary', 'Migration Summary Report - July 2026', ['status' => 'completed', 'from' => '2026-07-01', 'to' => '2026-07-31']],
            ['migration-summary', 'Migration Destination Country Report', ['group_by' => 'country_id']],
            ['profile-type-summary', 'Potential Customer Report - Q2 2026', ['profile_type' => 'potential_customer']],
            ['profile-type-summary', 'Reintegrated Profile Report', ['profile_type' => 'reintegrated']],
            ['branch-performance', 'Branch Performance Report - July 2026', ['branch_id' => 'all', 'from' => '2026-07-01', 'to' => '2026-07-31']],
            ['branch-performance', 'Branch Performance Report - Chittagong Branch', ['branch_id' => 'CTG-001']],
        ];

        foreach ($reports as $i => [$type, $title, $parameters]) {
            Report::firstOrCreate(
                ['type' => $type, 'title' => $title],
                [
                    'parameters' => $parameters,
                    'generated_by' => $user->id,
                    'file_path' => 'reports/'.$type.'_'.now()->format('Ymd').'_'.($i + 1).'.pdf',
                    'created_at' => now()->subDays(rand(1, 30)),
                ]
            );
        }

        $this->command->info('Created '.count($reports).' reports.');
    }
}
