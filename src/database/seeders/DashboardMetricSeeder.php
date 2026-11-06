<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\DashboardMetric;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;

class DashboardMetricSeeder extends Seeder
{
    public function run(): void
    {
        $period = now()->format('Y-m');

        $branches = Branch::all();

        foreach ($branches as $branch) {
            $metrics = [
                ['total_profiles', User::where('branch_id', $branch->id)->count()],
                ['active_profiles', User::where('branch_id', $branch->id)->where('status', 'active')->count()],
                ['reintegrated_profiles', User::where('branch_id', $branch->id)->reintegrated()->count()],
                ['potential_customers', User::where('branch_id', $branch->id)->potentialCustomers()->count()],
                ['total_staff', $branch->staff()->count()],
            ];

            foreach ($metrics as [$name, $value]) {
                DashboardMetric::firstOrCreate(
                    ['metric_name' => $name, 'period' => $period, 'branch_id' => $branch->id],
                    ['metric_value' => $value]
                );
            }
        }

        $overall = [
            ['total_profiles', User::count()],
            ['active_profiles', User::where('status', 'active')->count()],
            ['reintegrated_profiles', User::reintegrated()->count()],
            ['potential_customers', User::potentialCustomers()->count()],
            ['total_staff', Staff::count()],
        ];

        foreach ($overall as [$name, $value]) {
            DashboardMetric::firstOrCreate(
                ['metric_name' => $name, 'period' => $period, 'branch_id' => null],
                ['metric_value' => $value]
            );
        }

        $this->command->info('Created dashboard metrics for '.$branches->count().' branches and overall totals.');
    }
}
