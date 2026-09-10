<?php

namespace Database\Seeders;

use App\Models\Beneficiary;
use App\Models\Branch;
use App\Models\DashboardMetric;
use App\Models\Migrant;
use App\Models\Returnee;
use Illuminate\Database\Seeder;

class DashboardMetricSeeder extends Seeder
{
    public function run(): void
    {
        $period = now()->format('Y-m');

        $branches = Branch::all();

        foreach ($branches as $branch) {
            $beneficiaryCount = Beneficiary::where('branch_id', $branch->id)->count();
            $beneficiaryIds = Beneficiary::where('branch_id', $branch->id)->pluck('id');

            $metrics = [
                ['total_beneficiaries', $beneficiaryCount],
                ['active_beneficiaries', Beneficiary::where('branch_id', $branch->id)->where('status', 'active')->count()],
                ['total_migrants', Migrant::whereIn('beneficiary_id', $beneficiaryIds)->count()],
                ['deployed_migrants', Migrant::whereIn('beneficiary_id', $beneficiaryIds)->where('status', 'deployed')->count()],
                ['total_returnees', Returnee::whereIn('beneficiary_id', $beneficiaryIds)->count()],
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
            ['total_beneficiaries', Beneficiary::count()],
            ['active_beneficiaries', Beneficiary::where('status', 'active')->count()],
            ['total_migrants', Migrant::count()],
            ['deployed_migrants', Migrant::where('status', 'deployed')->count()],
            ['total_returnees', Returnee::count()],
            ['total_staff', \App\Models\Staff::count()],
        ];

        foreach ($overall as [$name, $value]) {
            DashboardMetric::firstOrCreate(
                ['metric_name' => $name, 'period' => $period, 'branch_id' => null],
                ['metric_value' => $value]
            );
        }

        $this->command->info('Created dashboard metrics for ' . $branches->count() . ' branches and overall totals.');
    }
}
