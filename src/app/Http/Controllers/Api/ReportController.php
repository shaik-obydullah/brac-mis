<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use App\Models\Branch;
use App\Models\Migrant;
use App\Models\Report;
use App\Models\Returnee;
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
            'total_beneficiaries' => Beneficiary::count(),
            'active_beneficiaries' => Beneficiary::where('status', 'active')->count(),
            'total_migrants' => Migrant::count(),
            'deployed_migrants' => Migrant::where('status', 'deployed')->count(),
            'total_returnees' => Returnee::count(),
            'total_branches' => Branch::count(),
        ]);
    }

    public function beneficiariesByBranch(): JsonResponse
    {
        return response()->json(
            Branch::withCount('beneficiaries')->get(['id', 'name', 'code'])
        );
    }

    public function migrantsByStatus(): JsonResponse
    {
        return response()->json(
            Migrant::selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->get()
        );
    }

    public function returneesByStatus(): JsonResponse
    {
        return response()->json(
            Returnee::selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->get()
        );
    }

    public function export(Request $request, string $type): JsonResponse
    {
        $validTypes = ['beneficiary-summary', 'migration-summary', 'reintegration-summary', 'branch-performance'];
        if (!in_array($type, $validTypes)) {
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
