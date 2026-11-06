<?php

namespace App\Http\Controllers;

use App\Exports\GenericExport;
use App\Models\Branch;
use App\Models\ImmediateSupport;
use App\Models\Report;
use App\Models\Staff;
use App\Models\User;
use App\Models\UserMigrationHistory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function export(Request $request, string $type, string $format)
    {
        $method = 'export'.str_replace(' ', '', ucwords(str_replace('-', ' ', $type))).ucfirst($format);

        if (! method_exists($this, $method)) {
            return redirect()->route('reports.index')->with('error', 'Export type not supported.');
        }

        $response = $this->$method();

        if (auth()->check()) {
            $title = str_replace('-', ' ', $type);
            Report::create([
                'type' => $type,
                'title' => ucwords($title)." ({$format})",
                'parameters' => ['format' => $format, 'exported_at' => now()->toDateTimeString()],
                'generated_by' => auth()->id(),
                'file_path' => null,
            ]);
        }

        return $response;
    }

    public function exportProfilesCsv()
    {
        $profiles = User::with('branch')->orderBy('name')->get();

        $data = $profiles->map(fn ($user) => [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'profile_type' => $user->profile_type_label,
            'branch' => $user->branch?->name,
            'status' => $user->status,
        ])->all();

        return $this->csvResponse($data, ['name', 'email', 'phone', 'profile_type', 'branch', 'status'], 'profiles.csv');
    }

    public function exportProfilesPdf()
    {
        $rows = User::with('branch')->orderBy('name')->get()
            ->map(fn ($user) => [
                $user->name,
                $user->email,
                $user->phone,
                $user->profile_type_label,
                $user->branch?->name,
                $user->status,
            ]);

        $pdf = Pdf::loadView('exports.pdf', [
            'title' => 'Profiles Report',
            'headers' => ['Name', 'Email', 'Phone', 'Profile Type', 'Branch', 'Status'],
            'rows' => $rows,
        ]);

        return $pdf->download('profiles.pdf');
    }

    public function exportProfilesExcel()
    {
        return Excel::download(
            new GenericExport('App\\Models\\User', ['id', 'name', 'email', 'phone', 'profile_type', 'branch_id', 'status']),
            'profiles.xlsx'
        );
    }

    public function exportProfilesByBranchCsv()
    {
        $groups = User::selectRaw('branch_id, count(*) as total')
            ->groupBy('branch_id')
            ->with('branch')
            ->get();

        $data = $groups->map(fn ($group) => [
            'branch' => $group->branch?->name ?? 'Unassigned',
            'total' => $group->total,
        ])->all();

        return $this->csvResponse($data, ['branch', 'total'], 'profiles-by-branch.csv');
    }

    public function exportProfilesByBranchPdf()
    {
        $rows = User::selectRaw('branch_id, count(*) as total')
            ->groupBy('branch_id')
            ->with('branch')
            ->get()
            ->map(fn ($group) => [$group->branch?->name ?? 'Unassigned', $group->total]);

        $pdf = Pdf::loadView('exports.pdf', [
            'title' => 'Profiles by Branch',
            'headers' => ['Branch', 'Total'],
            'rows' => $rows,
        ]);

        return $pdf->download('profiles-by-branch.pdf');
    }

    public function exportProfilesByProfileTypeCsv()
    {
        $groups = User::selectRaw('profile_type, count(*) as total')
            ->groupBy('profile_type')
            ->get();

        $data = $groups->map(fn ($group) => [
            'profile_type' => User::PROFILE_TYPES[$group->profile_type] ?? ucfirst((string) $group->profile_type),
            'total' => $group->total,
        ])->all();

        return $this->csvResponse($data, ['profile_type', 'total'], 'profiles-by-profile-type.csv');
    }

    public function exportProfilesByProfileTypePdf()
    {
        $rows = User::selectRaw('profile_type, count(*) as total')
            ->groupBy('profile_type')
            ->get()
            ->map(fn ($group) => [
                User::PROFILE_TYPES[$group->profile_type] ?? ucfirst((string) $group->profile_type),
                $group->total,
            ]);

        $pdf = Pdf::loadView('exports.pdf', [
            'title' => 'Profiles by Profile Type',
            'headers' => ['Profile Type', 'Total'],
            'rows' => $rows,
        ]);

        return $pdf->download('profiles-by-profile-type.pdf');
    }

    public function exportMigrationHistoriesCsv()
    {
        $histories = UserMigrationHistory::with('user', 'country')->latest('start_date')->get();

        $data = $histories->map(fn ($history) => [
            'name' => $history->user?->name,
            'country' => $history->country?->name,
            'employer_name' => $history->employer_name,
            'job_title' => $history->job_title,
            'visa_type' => $history->visa_type,
            'start_date' => $history->start_date?->format('Y-m-d'),
            'end_date' => $history->end_date?->format('Y-m-d'),
            'status' => $history->status,
        ])->all();

        return $this->csvResponse(
            $data,
            ['name', 'country', 'employer_name', 'job_title', 'visa_type', 'start_date', 'end_date', 'status'],
            'migration-histories.csv'
        );
    }

    public function exportMigrationHistoriesPdf()
    {
        $rows = UserMigrationHistory::with('user', 'country')->latest('start_date')->get()
            ->map(fn ($history) => [
                $history->user?->name,
                $history->country?->name,
                $history->employer_name,
                $history->job_title,
                $history->start_date?->format('Y-m-d'),
                $history->end_date?->format('Y-m-d'),
                $history->status,
            ]);

        $pdf = Pdf::loadView('exports.pdf', [
            'title' => 'Migration History Report',
            'headers' => ['Name', 'Country', 'Employer', 'Job Title', 'Start', 'End', 'Status'],
            'rows' => $rows,
        ]);

        return $pdf->download('migration-histories.pdf');
    }

    public function exportMigrationHistoriesExcel()
    {
        return Excel::download(
            new GenericExport('App\\Models\\UserMigrationHistory', [
                'id', 'user_id', 'country_id', 'employer_name', 'job_title', 'visa_type', 'start_date', 'end_date', 'status',
            ]),
            'migration-histories.xlsx'
        );
    }

    public function exportSupportRecordsCsv()
    {
        $supports = ImmediateSupport::with('user', 'providedBy')->latest('support_date')->get();

        $data = $supports->map(fn ($support) => [
            'date' => $support->support_date?->format('Y-m-d'),
            'type' => $support->type_label,
            'recipient' => $support->recipient_display_name,
            'phone' => $support->recipient_phone,
            'amount' => $support->amount,
            'status' => $support->status,
            'provided_by' => $support->providedBy?->name,
        ])->all();

        return $this->csvResponse(
            $data,
            ['date', 'type', 'recipient', 'phone', 'amount', 'status', 'provided_by'],
            'support-records.csv'
        );
    }

    public function exportSupportRecordsPdf()
    {
        $rows = ImmediateSupport::with('user', 'providedBy')->latest('support_date')->get()
            ->map(fn ($support) => [
                $support->support_date?->format('Y-m-d'),
                $support->type_label,
                $support->recipient_display_name,
                $support->recipient_phone,
                $support->amount,
                $support->status,
                $support->providedBy?->name,
            ]);

        $pdf = Pdf::loadView('exports.pdf', [
            'title' => 'Support Records Report',
            'headers' => ['Date', 'Type', 'Recipient', 'Phone', 'Amount', 'Status', 'Provided By'],
            'rows' => $rows,
        ]);

        return $pdf->download('support-records.pdf');
    }

    public function exportSupportRecordsExcel()
    {
        return Excel::download(
            new GenericExport('App\\Models\\ImmediateSupport', [
                'id', 'support_type', 'user_id', 'recipient_name', 'recipient_phone', 'support_date', 'amount', 'status', 'provided_by',
            ]),
            'support-records.xlsx'
        );
    }

    public function exportStaffCsv()
    {
        $staff = Staff::with('user', 'branch')->get();

        $data = $staff->map(fn ($member) => [
            'employee_id' => $member->employee_id,
            'name' => $member->user?->name,
            'email' => $member->user?->email,
            'designation' => $member->designation,
            'branch' => $member->branch?->name,
            'phone' => $member->phone,
        ])->all();

        return $this->csvResponse(
            $data,
            ['employee_id', 'name', 'email', 'designation', 'branch', 'phone'],
            'staff.csv'
        );
    }

    public function exportStaffPdf()
    {
        $rows = Staff::with('user', 'branch')->get()
            ->map(fn ($member) => [
                $member->employee_id,
                $member->user?->name,
                $member->user?->email,
                $member->designation,
                $member->branch?->name,
                $member->phone,
            ]);

        $pdf = Pdf::loadView('exports.pdf', [
            'title' => 'Staff Report',
            'headers' => ['Employee ID', 'Name', 'Email', 'Designation', 'Branch', 'Phone'],
            'rows' => $rows,
        ]);

        return $pdf->download('staff.pdf');
    }

    public function exportStaffExcel()
    {
        return Excel::download(
            new GenericExport('App\\Models\\Staff', ['employee_id', 'designation', 'phone']),
            'staff.xlsx'
        );
    }

    public function exportBranchesCsv()
    {
        $data = Branch::all()->map(fn ($branch) => [
            'name' => $branch->name,
            'code' => $branch->code,
            'division' => $branch->division,
            'district' => $branch->district,
            'upazila' => $branch->upazila,
            'status' => $branch->status,
        ])->all();

        return $this->csvResponse(
            $data,
            ['name', 'code', 'division', 'district', 'upazila', 'status'],
            'branches.csv'
        );
    }

    public function exportBranchesPdf()
    {
        $rows = Branch::all()
            ->map(fn ($branch) => [
                $branch->name,
                $branch->code,
                $branch->division,
                $branch->district,
                $branch->upazila,
                $branch->status ? 'Active' : 'Inactive',
            ]);

        $pdf = Pdf::loadView('exports.pdf', [
            'title' => 'Branches Report',
            'headers' => ['Name', 'Code', 'Division', 'District', 'Upazila', 'Status'],
            'rows' => $rows,
        ]);

        return $pdf->download('branches.pdf');
    }

    public function exportBranchesExcel()
    {
        return Excel::download(
            new GenericExport('App\\Models\\Branch', ['name', 'code', 'division', 'district', 'upazila', 'status']),
            'branches.xlsx'
        );
    }

    public function exportMonthlySummaryPdf()
    {
        $rows = collect(range(1, 12))->map(function ($month) {
            return [
                now()->month($month)->format('F'),
                User::whereYear('created_at', now()->year)->whereMonth('created_at', $month)->count(),
            ];
        });

        $pdf = Pdf::loadView('exports.pdf', [
            'title' => 'Monthly Summary - '.now()->year,
            'headers' => ['Month', 'New Profiles'],
            'rows' => $rows,
        ]);

        return $pdf->download('monthly-summary.pdf');
    }

    public function exportMonthlySummaryExcel()
    {
        return Excel::download(
            new GenericExport('App\\Models\\User', ['id', 'name', 'email', 'profile_type', 'created_at']),
            'monthly-summary.xlsx'
        );
    }

    public function exportYearlyOverviewPdf()
    {
        $firstYear = User::min('created_at');
        $startYear = $firstYear ? (int) date('Y', strtotime($firstYear)) : now()->year;

        $rows = collect(range($startYear, now()->year))->map(fn ($year) => [
            $year,
            User::whereYear('created_at', $year)->count(),
        ]);

        $pdf = Pdf::loadView('exports.pdf', [
            'title' => 'Yearly Overview',
            'headers' => ['Year', 'New Profiles'],
            'rows' => $rows,
        ]);

        return $pdf->download('yearly-overview.pdf');
    }

    public function exportYearlyOverviewExcel()
    {
        return Excel::download(
            new GenericExport('App\\Models\\User', ['id', 'name', 'email', 'profile_type', 'created_at']),
            'yearly-overview.xlsx'
        );
    }

    public function exportFullSystemExcel()
    {
        return Excel::download(
            new GenericExport('App\\Models\\User', [
                'id', 'name', 'email', 'phone', 'profile_type', 'branch_id', 'status', 'created_at',
            ]),
            'full-system.xlsx'
        );
    }

    private function csvResponse(array $data, array $columns, string $filename)
    {
        return response($this->toCsv($data, $columns), 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    private function toCsv(array $data, ?array $columns = null): string
    {
        $columns = $columns ?? (count($data) > 0 ? array_keys($data[0]) : []);
        $output = fopen('php://temp', 'r+');
        fputcsv($output, $columns);

        foreach ($data as $row) {
            $values = [];
            foreach ($columns as $column) {
                $values[] = $row[$column] ?? '';
            }
            fputcsv($output, $values);
        }

        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);

        return $content;
    }
}
