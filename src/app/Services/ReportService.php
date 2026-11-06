<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\User;
use App\Models\UserMigrationHistory;

class ReportService
{
    public function dashboardSummary(): array
    {
        return [
            'profiles' => [
                'total' => User::count(),
                'active' => User::where('status', 'active')->count(),
                'reintegrated' => User::reintegrated()->count(),
                'potential_customers' => User::potentialCustomers()->count(),
                'by_branch' => Branch::withCount('users')->get(['id', 'name']),
            ],
            'migrations' => [
                'total' => UserMigrationHistory::count(),
                'by_status' => UserMigrationHistory::selectRaw('status, count(*) as total')
                    ->groupBy('status')
                    ->pluck('total', 'status'),
            ],
        ];
    }

    public function profileReport(array $filters = [])
    {
        $query = User::with('branch');

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
        if ($profileType = $filters['profile_type'] ?? null) {
            $query->where('profile_type', $profileType);
        }

        return $query->get();
    }

    public function migrationReport(array $filters = [])
    {
        $query = UserMigrationHistory::with('user', 'country');

        if ($from = $filters['from'] ?? null) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $filters['to'] ?? null) {
            $query->whereDate('created_at', '<=', $to);
        }
        if ($countryId = $filters['country_id'] ?? null) {
            $query->where('country_id', $countryId);
        }

        return $query->get();
    }
}
