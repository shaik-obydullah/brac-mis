<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Report;
use App\Models\User;
use App\Models\UserMigrationHistory;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function summary(): JsonResponse
    {
        return response()->json([
            'total_profiles' => User::count(),
            'active_profiles' => User::where('status', 'active')->count(),
            'reintegrated_profiles' => User::reintegrated()->count(),
            'potential_customers' => User::potentialCustomers()->count(),
            'total_branches' => Branch::count(),
        ]);
    }

    public function profilesByBranch(): JsonResponse
    {
        return response()->json(
            Branch::withCount('users')->get(['id', 'name', 'code'])
        );
    }

    public function profilesByType(): JsonResponse
    {
        return response()->json(
            User::selectRaw('profile_type, count(*) as total')
                ->groupBy('profile_type')
                ->get()
        );
    }

    public function migrationSummary(): JsonResponse
    {
        return response()->json(
            UserMigrationHistory::selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->get()
        );
    }

    public function branchPerformance(): JsonResponse
    {
        return response()->json(
            Branch::withCount([
                'users',
                'users as potential_customers_count' => fn ($query) => $query->potentialCustomers(),
            ])->get(['id', 'name', 'code'])
        );
    }

    public function export(Request $request, string $type): JsonResponse
    {
        $validTypes = ['profile-summary', 'migration-summary', 'profile-type-summary', 'branch-performance'];

        if (! in_array($type, $validTypes, true)) {
            return response()->json(['error' => 'Invalid report type.'], 422);
        }

        $report = Report::create([
            'type' => $type,
            'title' => ucwords(str_replace('-', ' ', $type)),
            'parameters' => $request->all(),
            'generated_by' => auth()->id(),
            'file_path' => null,
        ]);

        return response()->json(['message' => 'Report queued for generation.', 'report_id' => $report->id]);
    }
}
