<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Beneficiary;
use App\Models\BeneficiaryFollowUp;
use App\Models\Branch;
use App\Models\Migrant;
use App\Models\Returnee;
use App\Models\Staff;

class DashboardController extends Controller
{
    public function index()
    {
        $beneficiaries = Beneficiary::count();
        $activeBeneficiaries = Beneficiary::where('status', 'active')->count();
        $totalMigrants = Migrant::count();
        $activeMigrants = Migrant::where('status', 'deployed')->count();
        $returnees = Returnee::count();
        $completedReturnees = Returnee::where('current_status', 'completed')->count();
        $branches = Branch::count();
        $staff = Staff::count();

        $pendingFollowUps = BeneficiaryFollowUp::whereIn('status', ['Pending', 'pending'])->count();

        $migrantStatus = Migrant::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $returneeStatus = Returnee::selectRaw('current_status as status, count(*) as total')
            ->groupBy('current_status')
            ->pluck('total', 'status');

        $monthlyEnrollments = collect(range(5, 0))->map(function ($monthsAgo) {
            $date = now()->subMonths($monthsAgo);

            return [
                'label' => $date->format('M'),
                'value' => Beneficiary::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        });

        $recentBeneficiaries = Beneficiary::with('branch')->latest()->take(6)->get();
        $recentAuditLogs = AuditLog::with('user')->latest()->take(6)->get();

        $branchPerformance = Branch::withCount('beneficiaries')
            ->orderByDesc('beneficiaries_count')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'beneficiaries',
            'activeBeneficiaries',
            'totalMigrants',
            'activeMigrants',
            'returnees',
            'completedReturnees',
            'branches',
            'staff',
            'pendingFollowUps',
            'migrantStatus',
            'returneeStatus',
            'monthlyEnrollments',
            'recentBeneficiaries',
            'recentAuditLogs',
            'branchPerformance',
        ));
    }
}
