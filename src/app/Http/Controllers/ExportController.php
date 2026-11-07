<?php

namespace App\Http\Controllers;

use App\Exports\GenericExport;
use App\Models\Branch;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\ImmediateSupport;
use App\Models\JournalEntry;
use App\Models\Report;
use App\Models\Staff;
use App\Models\StaffIncentive;
use App\Models\User;
use App\Models\UserMigrationHistory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    /**
     * Export types that require a module-specific permission.
     */
    private const REPORT_PERMISSIONS = [
        'courses' => 'view courses',
        'certificates' => 'view certificates',
        'staff-incentives' => 'view staff incentives',
        'journal-entries' => 'view finances',
    ];

    public function export(Request $request, string $type, string $format)
    {
        if (isset(self::REPORT_PERMISSIONS[$type]) && ! auth()->user()?->can(self::REPORT_PERMISSIONS[$type])) {
            abort(403);
        }

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

    public function exportCoursesCsv()
    {
        $courses = Course::withCount('enrollments', 'certificates')->orderBy('code')->get();

        $data = $courses->map(fn ($course) => [
            'code' => $course->code,
            'title' => $course->title,
            'category' => $course->category,
            'level' => Course::LEVELS[$course->level] ?? ucfirst($course->level),
            'duration_hours' => $course->duration_hours,
            'status' => Course::STATUSES[$course->status] ?? ucfirst($course->status),
            'enrollments' => $course->enrollments_count,
            'certificates' => $course->certificates_count,
        ])->all();

        return $this->csvResponse(
            $data,
            ['code', 'title', 'category', 'level', 'duration_hours', 'status', 'enrollments', 'certificates'],
            'courses.csv'
        );
    }

    public function exportCoursesPdf()
    {
        $rows = Course::withCount('enrollments', 'certificates')->orderBy('code')->get()
            ->map(fn ($course) => [
                $course->code,
                $course->title,
                $course->category,
                Course::LEVELS[$course->level] ?? ucfirst($course->level),
                $course->duration_hours,
                Course::STATUSES[$course->status] ?? ucfirst($course->status),
                $course->enrollments_count,
                $course->certificates_count,
            ]);

        $pdf = Pdf::loadView('exports.pdf', [
            'title' => 'Courses Report',
            'headers' => ['Code', 'Title', 'Category', 'Level', 'Hours', 'Status', 'Enrollments', 'Certificates'],
            'rows' => $rows,
        ]);

        return $pdf->download('courses.pdf');
    }

    public function exportCoursesExcel()
    {
        return Excel::download(
            new GenericExport('App\\Models\\Course', ['id', 'code', 'title', 'category', 'duration_hours', 'level', 'status']),
            'courses.xlsx'
        );
    }

    public function exportCertificatesCsv()
    {
        $certificates = Certificate::with('course')->latest('issued_date')->latest('id')->get();

        $data = $certificates->map(fn ($certificate) => [
            'certificate_number' => $certificate->certificate_number,
            'verification_code' => $certificate->verification_code,
            'recipient_name' => $certificate->recipient_name,
            'course' => $certificate->course?->title,
            'course_code' => $certificate->course?->code,
            'issued_date' => $certificate->issued_date?->format('Y-m-d'),
            'expiry_date' => $certificate->expiry_date?->format('Y-m-d'),
            'status' => $certificate->validity_label,
        ])->all();

        return $this->csvResponse(
            $data,
            ['certificate_number', 'verification_code', 'recipient_name', 'course', 'course_code', 'issued_date', 'expiry_date', 'status'],
            'certificates.csv'
        );
    }

    public function exportCertificatesPdf()
    {
        $rows = Certificate::with('course')->latest('issued_date')->latest('id')->get()
            ->map(fn ($certificate) => [
                $certificate->certificate_number,
                $certificate->recipient_name,
                $certificate->course?->title,
                $certificate->issued_date?->format('Y-m-d'),
                $certificate->expiry_date?->format('Y-m-d'),
                $certificate->validity_label,
            ]);

        $pdf = Pdf::loadView('exports.pdf', [
            'title' => 'Certificates Report',
            'headers' => ['Certificate #', 'Recipient', 'Course', 'Issued', 'Expires', 'Status'],
            'rows' => $rows,
        ]);

        return $pdf->download('certificates.pdf');
    }

    public function exportCertificatesExcel()
    {
        return Excel::download(
            new GenericExport('App\\Models\\Certificate', [
                'id', 'course_id', 'certificate_number', 'recipient_name', 'issued_date', 'expiry_date', 'status',
            ]),
            'certificates.xlsx'
        );
    }

    public function exportStaffIncentivesCsv()
    {
        $incentives = StaffIncentive::with('staff.user', 'staff.branch')->latest('incentive_date')->latest('id')->get();

        $data = $incentives->map(fn ($incentive) => [
            'incentive_date' => $incentive->incentive_date?->format('Y-m-d'),
            'staff' => $incentive->staff?->user?->name,
            'employee_id' => $incentive->staff?->employee_id,
            'branch' => $incentive->staff?->branch?->name,
            'title' => $incentive->title,
            'amount' => $incentive->amount,
            'paid_from' => $incentive->paid_from_label,
            'status' => ucfirst($incentive->status),
        ])->all();

        return $this->csvResponse(
            $data,
            ['incentive_date', 'staff', 'employee_id', 'branch', 'title', 'amount', 'paid_from', 'status'],
            'staff-incentives.csv'
        );
    }

    public function exportStaffIncentivesPdf()
    {
        $rows = StaffIncentive::with('staff.user', 'staff.branch')->latest('incentive_date')->latest('id')->get()
            ->map(fn ($incentive) => [
                $incentive->incentive_date?->format('Y-m-d'),
                $incentive->staff?->user?->name,
                $incentive->staff?->employee_id,
                $incentive->staff?->branch?->name,
                $incentive->title,
                $incentive->amount,
                $incentive->paid_from_label,
                ucfirst($incentive->status),
            ]);

        $pdf = Pdf::loadView('exports.pdf', [
            'title' => 'Staff Incentives Report',
            'headers' => ['Date', 'Staff', 'Employee ID', 'Branch', 'Incentive', 'Amount', 'Paid From', 'Status'],
            'rows' => $rows,
        ]);

        return $pdf->download('staff-incentives.pdf');
    }

    public function exportStaffIncentivesExcel()
    {
        return Excel::download(
            new GenericExport('App\\Models\\StaffIncentive', [
                'id', 'staff_id', 'title', 'amount', 'paid_from', 'incentive_date', 'status',
            ]),
            'staff-incentives.xlsx'
        );
    }

    public function exportJournalEntriesCsv()
    {
        $entries = JournalEntry::with('branch')->with('lines')->latest('entry_date')->latest('id')->get();

        $data = $entries->map(fn ($entry) => [
            'entry_no' => $entry->entry_no,
            'entry_date' => $entry->entry_date?->format('Y-m-d'),
            'reference' => $entry->reference,
            'description' => $entry->description,
            'branch' => $entry->branch?->name,
            'status' => $entry->status_label,
            'total_debit' => (float) $entry->lines->sum('debit'),
            'total_credit' => (float) $entry->lines->sum('credit'),
        ])->all();

        return $this->csvResponse(
            $data,
            ['entry_no', 'entry_date', 'reference', 'description', 'branch', 'status', 'total_debit', 'total_credit'],
            'journal-entries.csv'
        );
    }

    public function exportJournalEntriesPdf()
    {
        $rows = JournalEntry::with('branch')->with('lines')->latest('entry_date')->latest('id')->get()
            ->map(fn ($entry) => [
                $entry->entry_no,
                $entry->entry_date?->format('Y-m-d'),
                $entry->reference,
                $entry->description,
                $entry->branch?->name,
                $entry->status_label,
                number_format((float) $entry->lines->sum('debit'), 2),
                number_format((float) $entry->lines->sum('credit'), 2),
            ]);

        $pdf = Pdf::loadView('exports.pdf', [
            'title' => 'Journal Entries Report',
            'headers' => ['Entry #', 'Date', 'Reference', 'Description', 'Branch', 'Status', 'Debit', 'Credit'],
            'rows' => $rows,
        ]);

        return $pdf->download('journal-entries.pdf');
    }

    public function exportJournalEntriesExcel()
    {
        return Excel::download(
            new GenericExport('App\\Models\\JournalEntry', [
                'id', 'entry_no', 'entry_date', 'reference', 'description', 'branch_id', 'status', 'created_by',
            ]),
            'journal-entries.xlsx'
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
