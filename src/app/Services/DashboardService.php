<?php

namespace App\Services;

use App\Models\Beneficiary;
use App\Models\Migrant;
use App\Models\Returnee;
use App\Models\Branch;
use App\Models\BeneficiaryIntervention;
use App\Models\ReturneeReintegrationPlan;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getMetrics(): array
    {
        return [
            'total_beneficiaries' => Beneficiary::count(),
            'active_beneficiaries' => Beneficiary::where('status', 'active')->count(),
            'new_beneficiaries_this_month' => Beneficiary::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'total_migrants' => Migrant::count(),
            'deployed_migrants' => Migrant::where('status', 'deployed')->count(),
            'total_returnees' => Returnee::count(),
            'reintegrated_returnees' => Returnee::where('status', 'reintegrated')->count(),
            'total_branches' => Branch::count(),
            'active_branches' => Branch::where('status', 'active')->count(),
        ];
    }

    public function getBranchBreakdown(): array
    {
        $branches = Branch::withCount([
            'beneficiaries',
            'beneficiaries as active_beneficiaries_count' => function ($q) {
                $q->where('status', 'active');
            },
        ])->get(['id', 'name', 'code']);

        return $branches->toArray();
    }

    public function getMonthlyEnrollments(int $months = 6)
    {
        return Beneficiary::selectRaw(
            "DATE_FORMAT(created_at, '%Y-%m') as month, count(*) as total"
        )
            ->where('created_at', '>=', now()->subMonths($months))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');
    }

    public function getStatusDistribution(): array
    {
        return [
            'beneficiaries' => Beneficiary::selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
            'migrants' => Migrant::selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
            'returnees' => Returnee::selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
        ];
    }
}
