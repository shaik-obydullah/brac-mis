<?php

namespace App\Services;

use App\Models\Beneficiary;
use App\Models\Migrant;
use App\Models\Returnee;
use App\Models\Branch;
use App\Models\ReturneeReintegrationPlan;
use App\Models\BeneficiaryIntervention;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function dashboardSummary(): array
    {
        return [
            'beneficiaries' => [
                'total' => Beneficiary::count(),
                'active' => Beneficiary::where('status', 'active')->count(),
                'by_branch' => Branch::withCount('beneficiaries')->get(['id', 'name']),
            ],
            'migrants' => [
                'total' => Migrant::count(),
                'deployed' => Migrant::where('status', 'deployed')->count(),
                'by_status' => Migrant::selectRaw('status, count(*) as total')
                    ->groupBy('status')
                    ->pluck('total', 'status'),
            ],
            'returnees' => [
                'total' => Returnee::count(),
                'reintegrated' => Returnee::where('status', 'reintegrated')->count(),
                'by_status' => Returnee::selectRaw('status, count(*) as total')
                    ->groupBy('status')
                    ->pluck('total', 'status'),
            ],
        ];
    }

    public function beneficiaryReport(array $filters = [])
    {
        $query = Beneficiary::with('branch', 'createdBy');

        if ($from = $filters['from'] ?? null) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $filters['to'] ?? null) {
            $query->whereDate('created_at', '<=', $to);
        }
        if ($branchId = $filters['branch_id'] ?? null) {
            $query->where('branch_id', $branchId);
        }
        if ($status = $filters['status'] ?? null) {
            $query->where('status', $status);
        }

        return $query->get();
    }

    public function migrationReport(array $filters = [])
    {
        $query = Migrant::with('beneficiary', 'destinationCountry');

        if ($from = $filters['from'] ?? null) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $filters['to'] ?? null) {
            $query->whereDate('created_at', '<=', $to);
        }
        if ($countryId = $filters['destination_country_id'] ?? null) {
            $query->where('destination_country_id', $countryId);
        }

        return $query->get();
    }
}
