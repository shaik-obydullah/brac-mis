<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\ImmediateSupport;
use App\Models\User;
use App\Models\UserMigrationHistory;
use App\Models\UserSkill;

class DashboardService
{
    public function getMetrics(): array
    {
        return [
            'total_profiles' => User::count(),
            'active_profiles' => User::where('status', 'active')->count(),
            'new_profiles_this_month' => User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'reintegrated_profiles' => User::reintegrated()->count(),
            'potential_customers' => User::potentialCustomers()->count(),
            'total_skills' => UserSkill::count(),
            'total_migration_histories' => UserMigrationHistory::count(),
            'total_supports' => ImmediateSupport::count(),
            'total_branches' => Branch::count(),
            'active_branches' => Branch::where('status', true)->count(),
        ];
    }

    public function getBranchBreakdown(): array
    {
        $branches = Branch::withCount([
            'users',
            'users as active_users_count' => function ($q) {
                $q->where('status', 'active');
            },
        ])->get(['id', 'name', 'code']);

        return $branches->toArray();
    }

    public function getMonthlyEnrollments(int $months = 6)
    {
        return User::selectRaw(
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
            'profiles' => User::selectRaw('profile_type as status, count(*) as total')
                ->groupBy('profile_type')
                ->pluck('total', 'status'),
            'migrations' => UserMigrationHistory::selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
        ];
    }
}
