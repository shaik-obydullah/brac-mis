@extends('layouts.app')

@section('title', 'Reports')

@section('content')
@php
    $formatStyles = [
        'csv' => 'bg-brand-600 hover:bg-brand-700 text-white',
        'pdf' => 'bg-rose-600 hover:bg-rose-700 text-white',
        'excel' => 'bg-sky-600 hover:bg-sky-700 text-white',
    ];

    $formatLabels = [
        'csv' => 'CSV',
        'pdf' => 'PDF',
        'excel' => 'Excel',
    ];

    $reportSections = [
        [
            'title' => 'Profile Reports',
            'subtitle' => 'People, branches and profile-type breakdowns',
            'reports' => [
                ['label' => 'Profile List', 'description' => 'All profiles with contact details and profile type', 'type' => 'profiles', 'formats' => ['csv', 'pdf', 'excel']],
                ['label' => 'Profiles by Branch', 'description' => 'Profile totals grouped by branch', 'type' => 'profiles-by-branch', 'formats' => ['csv', 'pdf']],
                ['label' => 'Profiles by Profile Type', 'description' => 'Reintegrated versus potential customer totals', 'type' => 'profiles-by-profile-type', 'formats' => ['csv', 'pdf']],
            ],
        ],
        [
            'title' => 'Migration & Support',
            'subtitle' => 'Migration history and reintegration support records',
            'reports' => [
                ['label' => 'Migration History', 'description' => 'Every migration history record', 'type' => 'migration-histories', 'formats' => ['csv', 'pdf', 'excel']],
                ['label' => 'Support Records', 'description' => 'Support records with recipient and payment details', 'type' => 'support-records', 'formats' => ['csv', 'pdf', 'excel']],
            ],
        ],
        [
            'title' => 'Learning Reports',
            'subtitle' => 'Courses, enrollments and issued certificates',
            'reports' => [
                ['label' => 'Course List', 'description' => 'Courses with enrollment and certificate counts', 'type' => 'courses', 'formats' => ['csv', 'pdf', 'excel']],
                ['label' => 'Certificates', 'description' => 'Issued certificates with validity and verification codes', 'type' => 'certificates', 'formats' => ['csv', 'pdf', 'excel']],
            ],
        ],
        [
            'title' => 'HRM & Finance',
            'subtitle' => 'Staff incentives and accounting journal entries',
            'reports' => [
                ['label' => 'Staff Incentives', 'description' => 'Incentive payments, status and payment source', 'type' => 'staff-incentives', 'formats' => ['csv', 'pdf', 'excel']],
                ['label' => 'Journal Entries', 'description' => 'Accounting entries with debit and credit totals', 'type' => 'journal-entries', 'formats' => ['csv', 'pdf', 'excel']],
            ],
        ],
        [
            'title' => 'Staff & Branch Reports',
            'subtitle' => 'Organisation structure and staff directory',
            'reports' => [
                ['label' => 'Staff List', 'description' => 'All staff with designation, branch and contact details', 'type' => 'staff', 'formats' => ['csv', 'pdf', 'excel']],
                ['label' => 'Branch List', 'description' => 'All branches with hierarchy and location details', 'type' => 'branches', 'formats' => ['csv', 'pdf', 'excel']],
            ],
        ],
        [
            'title' => 'Summary Reports',
            'subtitle' => 'Registration trends and full-system extracts',
            'reports' => [
                ['label' => 'Monthly Summary', 'description' => 'New profiles registered each month this year', 'type' => 'monthly-summary', 'formats' => ['pdf', 'excel']],
                ['label' => 'Yearly Overview', 'description' => 'Profile registrations by year', 'type' => 'yearly-overview', 'formats' => ['pdf', 'excel']],
                ['label' => 'Full System Export', 'description' => 'Complete profile data export', 'type' => 'full-system', 'formats' => ['excel']],
            ],
        ],
    ];

    // Hide report cards whose export the current user is not allowed to run.
    // This mirrors the module-permission gates in ExportController.
    $canExport = auth()->user()?->can('export reports');

    $reportPermissions = [
        'profiles' => 'manage users',
        'migration-histories' => 'manage users',
        'support-records' => 'view immediate support',
        'staff' => 'view staff',
        'branches' => 'view branches',
        'courses' => 'view courses',
        'certificates' => 'view certificates',
        'staff-incentives' => 'view staff incentives',
        'journal-entries' => 'view finances',
        'monthly-summary' => 'manage users',
        'yearly-overview' => 'manage users',
        'full-system' => 'manage users',
    ];

    $reportSections = collect($reportSections)
        ->map(function ($section) use ($reportPermissions) {
            $section['reports'] = array_values(array_filter(
                $section['reports'],
                fn ($report) => ! isset($reportPermissions[$report['type']])
                    || auth()->user()?->can($reportPermissions[$report['type']])
            ));

            return $section;
        })
        ->filter(fn ($section) => count($section['reports']) > 0)
        ->values()
        ->all();
@endphp

<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Reports</h1>
        <p class="mt-1 text-sm text-slate-500">Export profile, learning, HRM, finance and organisation data.</p>
    </div>
    @can('view audit logs')
        <a href="{{ route('audit-logs.index') }}" class="btn-secondary">View audit log</a>
    @endcan
</div>

<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Total profiles</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totalProfiles ?? 0) }}</p>
        <div class="mt-2 flex justify-between text-xs text-slate-500">
            <span>Active: {{ number_format($activeProfiles ?? 0) }}</span>
            <span>Skills: {{ number_format($totalSkills ?? 0) }}</span>
        </div>
    </div>

    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Reintegrated profiles</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-brand-600">{{ number_format($reintegratedProfiles ?? 0) }}</p>
        <div class="mt-2 flex justify-between text-xs text-slate-500">
            <span>Potential customers: {{ number_format($potentialCustomers ?? 0) }}</span>
            <span>Migrations: {{ number_format($totalMigrationHistories ?? 0) }}</span>
        </div>
    </div>

    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Learning</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totalCourses ?? 0) }}</p>
        <div class="mt-2 flex justify-between text-xs text-slate-500">
            <span>Active courses: {{ number_format($activeCourses ?? 0) }}</span>
            <span>Certificates: {{ number_format($totalCertificates ?? 0) }} ({{ number_format($validCertificates ?? 0) }} valid)</span>
        </div>
    </div>

    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Operations</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totalSupportRecords ?? 0) }}</p>
        <div class="mt-2 flex justify-between text-xs text-slate-500">
            <span>Posted entries: {{ number_format($postedJournalEntries ?? 0) }}</span>
            <span>Incentives: {{ number_format($totalStaffIncentives ?? 0) }}</span>
        </div>
    </div>
</div>

<div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
    <div class="card p-4">
        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Staff</p>
        <p class="mt-1 text-xl font-bold text-slate-900">{{ number_format($totalStaff ?? 0) }}</p>
    </div>
    <div class="card p-4">
        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Branches</p>
        <p class="mt-1 text-xl font-bold text-slate-900">{{ number_format($totalBranches ?? 0) }}</p>
    </div>
    <div class="card p-4">
        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Staff incentives</p>
        <p class="mt-1 text-xl font-bold text-slate-900">{{ number_format($totalStaffIncentives ?? 0) }}</p>
        <p class="mt-0.5 text-xs text-slate-500">৳ {{ number_format($paidIncentiveAmount ?? 0, 2) }} paid</p>
    </div>
    <div class="card p-4">
        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Certificates</p>
        <p class="mt-1 text-xl font-bold text-slate-900">{{ number_format($totalCertificates ?? 0) }}</p>
        <p class="mt-0.5 text-xs text-slate-500">{{ number_format($validCertificates ?? 0) }} valid</p>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    @foreach($reportSections as $section)
        <div class="card p-5">
            <div class="mb-4">
                <h2 class="card-title">{{ $section['title'] }}</h2>
                <p class="mt-0.5 text-xs text-slate-500">{{ $section['subtitle'] }}</p>
            </div>

            <div class="space-y-3">
                @foreach($section['reports'] as $report)
                    <div class="flex flex-col justify-between gap-3 rounded-xl border border-slate-200 bg-slate-50/70 p-4 sm:flex-row sm:items-center">
                        <div>
                            <p class="text-sm font-semibold text-slate-800">{{ $report['label'] }}</p>
                            <p class="mt-0.5 text-xs text-slate-500">{{ $report['description'] }}</p>
                        </div>
                        <div class="flex shrink-0 gap-2">
                            @if($canExport)
                                @foreach($report['formats'] as $format)
                                    <a href="{{ route('reports.export', ['type' => $report['type'], 'format' => $format]) }}"
                                       class="rounded-md px-2.5 py-1 text-xs font-semibold transition {{ $formatStyles[$format] ?? 'bg-slate-600 hover:bg-slate-700 text-white' }}">
                                        {{ $formatLabels[$format] ?? strtoupper($format) }}
                                    </a>
                                @endforeach
                            @else
                                <span class="rounded-md bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-500">Export disabled</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>

<div class="card mt-6 overflow-hidden">
    <div class="border-b border-slate-100 px-5 py-4">
        <h2 class="card-title">Recent exports</h2>
        <p class="mt-0.5 text-xs text-slate-500">The latest report downloads generated by your team</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="table-head px-5 py-3">Report</th>
                    <th class="table-head px-5 py-3">Type</th>
                    <th class="table-head px-5 py-3">Generated by</th>
                    <th class="table-head px-5 py-3 text-right">Generated</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($recentExports ?? [] as $export)
                    <tr class="hover:bg-slate-50">
                        <td class="table-cell font-medium text-slate-900">{{ $export->title }}</td>
                        <td class="table-cell">{{ ucwords(str_replace('-', ' ', $export->type)) }}</td>
                        <td class="table-cell">{{ $export->generatedBy?->name ?? 'System' }}</td>
                        <td class="table-cell text-right whitespace-nowrap">{{ $export->created_at?->format('d M Y, H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center text-sm text-slate-500">
                            No exports generated yet. Choose a format above to create the first report.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
