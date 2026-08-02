<?php

namespace App\Console\Commands;

use App\Models\Beneficiary;
use App\Models\Branch;
use App\Models\DashboardMetric;
use App\Models\Migrant;
use App\Models\Returnee;
use Illuminate\Console\Command;

class RecordDashboardMetrics extends Command
{
    protected $signature = 'mis:record-metrics';
    protected $description = 'Record current dashboard metrics to the database';

    public function handle(): void
    {
        $period = now()->format('Y-m-d');

        $metrics = [
            'total_beneficiaries' => Beneficiary::count(),
            'active_beneficiaries' => Beneficiary::where('status', 'active')->count(),
            'total_migrants' => Migrant::count(),
            'deployed_migrants' => Migrant::where('status', 'deployed')->count(),
            'total_returnees' => Returnee::count(),
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
            $branchMetrics = [
                'branch_beneficiaries' => Beneficiary::where('branch_id', $branch->id)->count(),
                'branch_migrants' => Migrant::whereIn('beneficiary_id', Beneficiary::where('branch_id', $branch->id)->pluck('id'))->count(),
            ];
            foreach ($branchMetrics as $name => $value) {
                DashboardMetric::create([
                    'metric_name' => $name,
                    'metric_value' => $value,
                    'period' => $period,
                    'branch_id' => $branch->id,
                ]);
            }
        }

        $this->info('Dashboard metrics recorded for ' . $period);
    }
}
