<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\DashboardMetric;
use App\Models\User;
use Illuminate\Console\Command;

class RecordDashboardMetrics extends Command
{
    protected $signature = 'mis:record-metrics';

    protected $description = 'Record current dashboard metrics to the database';

    public function handle(): void
    {
        $period = now()->format('Y-m-d');

        $metrics = [
            'total_profiles' => User::count(),
            'active_profiles' => User::where('status', 'active')->count(),
            'reintegrated_profiles' => User::reintegrated()->count(),
            'potential_customers' => User::potentialCustomers()->count(),
            'total_branches' => Branch::where('status', true)->count(),
        ];

        foreach ($metrics as $name => $value) {
            DashboardMetric::create([
                'metric_name' => $name,
                'metric_value' => $value,
                'period' => $period,
            ]);
        }

        foreach (Branch::all() as $branch) {
            DashboardMetric::create([
                'metric_name' => 'branch_profiles',
                'metric_value' => User::where('branch_id', $branch->id)->count(),
                'period' => $period,
                'branch_id' => $branch->id,
            ]);
        }

        $this->info('Dashboard metrics recorded for '.$period);
    }
}
