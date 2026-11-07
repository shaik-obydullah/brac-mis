<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\AwarenessProgram;
use App\Models\AwarenessProgramOutcome;
use App\Models\Branch;
use App\Models\ImmediateSupport;
use App\Models\JobBoardInquiry;
use App\Models\Staff;
use App\Models\SupportFollowUp;
use App\Models\User;
use App\Models\UserMigrationHistory;
use App\Models\UserSkill;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProfiles = User::count();
        $activeProfiles = User::where('status', 'active')->count();
        $reintegratedProfiles = User::reintegrated()->count();
        $potentialCustomers = User::potentialCustomers()->count();
        $usersWithSkills = User::has('skills')->count();
        $totalSkills = UserSkill::count();
        $totalMigrationHistories = UserMigrationHistory::count();
        $totalSupports = ImmediateSupport::count();
        $pendingFollowUps = SupportFollowUp::where('status', 'scheduled')->count();
        $branches = Branch::count();
        $staff = Staff::count();

        $awarenessPrograms = AwarenessProgram::count();
        $awarenessParticipants = AwarenessProgramOutcome::count();
        $awarenessUnderstood = AwarenessProgramOutcome::where('understood', true)->count();
        $awarenessUnderstoodPercentage = $awarenessParticipants > 0
            ? (int) round(($awarenessUnderstood / $awarenessParticipants) * 100)
            : 0;

        $awarenessProgramList = AwarenessProgram::withCount([
            'outcomes',
            'outcomes as understood_outcomes_count' => fn ($outcome) => $outcome->where('understood', true),
        ])->latest('id')->take(5)->get();

        $recentAwarenessOutcomes = AwarenessProgramOutcome::with('awarenessProgram')
            ->latest('id')
            ->take(5)
            ->get();

        $profileDistribution = User::selectRaw('profile_type, count(*) as total')
            ->groupBy('profile_type')
            ->pluck('total', 'profile_type');

        $monthlyRegistrations = collect(range(5, 0))->map(function ($monthsAgo) {
            $date = now()->subMonths($monthsAgo);

            return [
                'label' => $date->format('M'),
                'value' => User::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        });

        $recentProfiles = User::with('branch')->latest()->take(6)->get();
        $recentAuditLogs = AuditLog::with('user')->latest()->take(6)->get();

        $branchPerformance = Branch::withCount('users')
            ->orderByDesc('users_count')
            ->take(5)
            ->get();

        $jobBoardInquiryTotals = [
            'new' => 0,
            'contacted' => 0,
            'shared' => 0,
            'closed' => 0,
        ];
        $recentJobBoardInquiries = collect();

        if (auth()->user()?->can('manage job board')) {
            $inquiryCounts = JobBoardInquiry::query()
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            foreach (array_keys($jobBoardInquiryTotals) as $status) {
                $jobBoardInquiryTotals[$status] = (int) ($inquiryCounts[$status] ?? 0);
            }

            $recentJobBoardInquiries = JobBoardInquiry::with('candidate')
                ->where('status', 'new')
                ->latest()
                ->take(5)
                ->get();
        }

        return view('dashboard', compact(
            'totalProfiles',
            'activeProfiles',
            'reintegratedProfiles',
            'potentialCustomers',
            'usersWithSkills',
            'totalSkills',
            'totalMigrationHistories',
            'totalSupports',
            'pendingFollowUps',
            'branches',
            'staff',
            'awarenessPrograms',
            'awarenessParticipants',
            'awarenessUnderstood',
            'awarenessUnderstoodPercentage',
            'awarenessProgramList',
            'recentAwarenessOutcomes',
            'profileDistribution',
            'monthlyRegistrations',
            'recentProfiles',
            'recentAuditLogs',
            'branchPerformance',
            'jobBoardInquiryTotals',
            'recentJobBoardInquiries',
        ));
    }
}
