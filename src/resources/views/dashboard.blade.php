@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $reintegratedCount = (int) ($profileDistribution[\App\Models\User::PROFILE_REINTEGRATED] ?? 0);
    $potentialCount = (int) ($profileDistribution[\App\Models\User::PROFILE_POTENTIAL_CUSTOMER] ?? 0);
    $generalCount = (int) ($profileDistribution[\App\Models\User::PROFILE_GENERAL_USER] ?? 0);
    $profileTotal = max(1, $reintegratedCount + $potentialCount + $generalCount);
    $maxRegistration = max(1, $monthlyRegistrations->max('value'));
@endphp

<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <p class="text-sm font-medium text-brand-600">{{ now()->format('l, d F Y') }}</p>
        <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Dashboard</h1>
        <p class="mt-1 text-sm text-slate-500">A live view of reintegrated and potential customer profiles.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('users.create') }}" class="btn-primary">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New profile
        </a>
        <a href="{{ route('skill-access.index') }}" class="btn-secondary">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.674M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            Skill access
        </a>
        @can('view awareness programs')
            <a href="{{ url('/awareness-programs') }}" class="btn-secondary">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                Awareness programs
            </a>
        @endcan
    </div>
</div>

@can('manage job board')
<div class="card mb-6 overflow-hidden">
    <div class="card-header">
        <div>
            <h2 class="card-title">Job board inquiries</h2>
            <p class="mt-0.5 text-xs text-slate-500">Employer contact requests submitted from the public job board.</p>
        </div>
        <a href="{{ route('job-board.inquiries.index') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">Open inbox</a>
    </div>

    <div class="grid grid-cols-2 divide-x divide-slate-100 border-b border-slate-100 sm:grid-cols-4">
        <a href="{{ route('job-board.inquiries.index', ['status' => 'new']) }}" class="px-5 py-4 transition hover:bg-slate-50">
            <p class="text-xs font-medium text-slate-500">New</p>
            <p class="mt-1 text-2xl font-bold tracking-tight text-sky-600">{{ number_format($jobBoardInquiryTotals['new']) }}</p>
        </a>
        <a href="{{ route('job-board.inquiries.index', ['status' => 'contacted']) }}" class="px-5 py-4 transition hover:bg-slate-50">
            <p class="text-xs font-medium text-slate-500">Contacted</p>
            <p class="mt-1 text-2xl font-bold tracking-tight text-amber-600">{{ number_format($jobBoardInquiryTotals['contacted']) }}</p>
        </a>
        <a href="{{ route('job-board.inquiries.index', ['status' => 'shared']) }}" class="px-5 py-4 transition hover:bg-slate-50">
            <p class="text-xs font-medium text-slate-500">Contact shared</p>
            <p class="mt-1 text-2xl font-bold tracking-tight text-brand-600">{{ number_format($jobBoardInquiryTotals['shared']) }}</p>
        </a>
        <a href="{{ route('job-board.inquiries.index', ['status' => 'closed']) }}" class="px-5 py-4 transition hover:bg-slate-50">
            <p class="text-xs font-medium text-slate-500">Closed</p>
            <p class="mt-1 text-2xl font-bold tracking-tight text-slate-600">{{ number_format($jobBoardInquiryTotals['closed']) }}</p>
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="table-head">
                    <th class="px-5 py-3">Received</th>
                    <th class="px-5 py-3">Candidate</th>
                    <th class="px-5 py-3">Employer</th>
                    <th class="px-5 py-3">Role</th>
                    <th class="px-5 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($recentJobBoardInquiries as $inquiry)
                    <tr class="transition hover:bg-slate-50/70">
                        <td class="whitespace-nowrap px-5 py-3.5 text-sm text-slate-600">
                            {{ $inquiry->created_at?->format('d M Y') }}
                            <p class="text-xs text-slate-400">{{ $inquiry->created_at?->diffForHumans() }}</p>
                        </td>
                        <td class="px-5 py-3.5">
                            <a href="{{ route('job-board.inquiries.show', $inquiry) }}" class="text-sm font-semibold text-slate-800 hover:text-brand-700">{{ $inquiry->candidate?->name ?? 'Candidate removed' }}</a>
                        </td>
                        <td class="px-5 py-3.5 text-sm text-slate-600">
                            {{ $inquiry->employer_name }}
                            @if($inquiry->employer_organization)
                                <p class="text-xs text-slate-400">{{ $inquiry->employer_organization }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-sm text-slate-600">{{ $inquiry->job_title ?: '—' }}</td>
                        <td class="whitespace-nowrap px-5 py-3.5 text-right">
                            <a href="{{ route('job-board.inquiries.show', $inquiry) }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">Review</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center">
                            <p class="text-sm font-medium text-slate-700">No new inquiries</p>
                            <p class="mt-1 text-xs text-slate-500">New employer contact requests will appear here first.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endcan

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <a href="{{ route('users.index') }}" class="group card p-5 transition hover:-translate-y-0.5 hover:shadow-md">
        <div class="flex items-start justify-between">
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-600 ring-1 ring-inset ring-brand-100">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </span>
            <span class="badge bg-brand-50 text-brand-700 ring-brand-200">{{ number_format($activeProfiles) }} active</span>
        </div>
        <p class="mt-5 text-sm font-medium text-slate-500">Total profiles</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totalProfiles) }}</p>
        <p class="mt-2 flex items-center gap-1 text-xs font-medium text-slate-400 group-hover:text-brand-600">
            View all profiles
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </p>
    </a>

    <a href="{{ route('users.index', ['profile_type' => \App\Models\User::PROFILE_REINTEGRATED]) }}" class="group card p-5 transition hover:-translate-y-0.5 hover:shadow-md">
        <div class="flex items-start justify-between">
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 ring-1 ring-inset ring-emerald-100">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            <span class="badge bg-emerald-50 text-emerald-700 ring-emerald-200">{{ $totalProfiles > 0 ? round(($reintegratedCount / max(1, $totalProfiles)) * 100) : 0 }}%</span>
        </div>
        <p class="mt-5 text-sm font-medium text-slate-500">Reintegrated profiles</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($reintegratedProfiles) }}</p>
        <p class="mt-2 flex items-center gap-1 text-xs font-medium text-slate-400 group-hover:text-emerald-600">
            View reintegrated
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </p>
    </a>

    <a href="{{ route('users.index', ['profile_type' => \App\Models\User::PROFILE_POTENTIAL_CUSTOMER]) }}" class="group card p-5 transition hover:-translate-y-0.5 hover:shadow-md">
        <div class="flex items-start justify-between">
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-pink-50 text-pink-600 ring-1 ring-inset ring-pink-100">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </span>
            <span class="badge bg-pink-50 text-pink-700 ring-pink-200">{{ $totalProfiles > 0 ? round(($potentialCount / max(1, $totalProfiles)) * 100) : 0 }}%</span>
        </div>
        <p class="mt-5 text-sm font-medium text-slate-500">Potential customers</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($potentialCustomers) }}</p>
        <p class="mt-2 flex items-center gap-1 text-xs font-medium text-slate-400 group-hover:text-pink-600">
            View potential customers
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </p>
    </a>

    <a href="{{ route('branches.index') }}" class="group card p-5 transition hover:-translate-y-0.5 hover:shadow-md">
        <div class="flex items-start justify-between">
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-violet-50 text-violet-600 ring-1 ring-inset ring-violet-100">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </span>
            <span class="badge bg-violet-50 text-violet-700 ring-violet-200">{{ number_format($staff) }} staff</span>
        </div>
        <p class="mt-5 text-sm font-medium text-slate-500">Branches</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($branches) }}</p>
        <p class="mt-2 flex items-center gap-1 text-xs font-medium text-slate-400 group-hover:text-violet-600">
            View organization
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </p>
    </a>
</div>

@can('view awareness programs')
<div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
    <div class="card overflow-hidden xl:col-span-2">
        <div class="card-header">
            <div>
                <h2 class="card-title">Awareness programs</h2>
                <p class="mt-0.5 text-xs text-slate-500">Participant understanding across recent programs.</p>
            </div>
            <a href="{{ url('/awareness-programs') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">View all</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="table-head">
                        <th class="px-5 py-3">Program</th>
                        <th class="px-5 py-3">Participants</th>
                        <th class="px-5 py-3">Understood</th>
                        <th class="px-5 py-3">Location</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($awarenessProgramList as $program)
                        @php
                            $programPercentage = $program->outcomes_count > 0
                                ? (int) round(($program->understood_outcomes_count / $program->outcomes_count) * 100)
                                : null;
                        @endphp
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="px-5 py-3.5">
                                <a href="{{ route('awareness-programs.show', $program) }}" class="text-sm font-semibold text-slate-800 hover:text-brand-700">{{ $program->program_name }}</a>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-slate-600">{{ number_format($program->outcomes_count) }}</td>
                            <td class="px-5 py-3.5">
                                @if($programPercentage === null)
                                    <span class="text-xs text-slate-500">Not tested</span>
                                @else
                                    <span class="badge {{ $programPercentage >= 50 ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-rose-50 text-rose-700 ring-rose-200' }}">{{ $programPercentage }}%</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-sm text-slate-600">{{ $program->upazila }}, {{ $program->district }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-sm text-slate-500">
                                No awareness programs yet.
                                <a href="{{ url('/awareness-programs') }}" class="font-semibold text-brand-600 hover:text-brand-700">Open awareness programs</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">Recent participant outcomes</h2>
                <p class="mt-0.5 text-xs text-slate-500">Latest awareness message checks.</p>
            </div>
        </div>
        <div class="space-y-3 p-5">
            @forelse($recentAwarenessOutcomes as $outcome)
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium text-slate-800">{{ $outcome->participant_name }}</p>
                        <p class="truncate text-xs text-slate-500">{{ $outcome->awarenessProgram?->program_name ?? 'Program removed' }}</p>
                    </div>
                    @if($outcome->understood)
                        <span class="badge bg-emerald-50 text-emerald-700 ring-emerald-200">Understood</span>
                    @else
                        <span class="badge bg-rose-50 text-rose-700 ring-rose-200">Not understood</span>
                    @endif
                </div>
            @empty
                <p class="text-sm text-slate-500">No participant outcomes recorded yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endcan

<div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
    <div class="card xl:col-span-2">
        <div class="card-header">
            <div>
                <h2 class="card-title">Profile distribution</h2>
                <p class="mt-0.5 text-xs text-slate-500">How the user base splits across profile types.</p>
            </div>
            <a href="{{ route('users.index') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">View all</a>
        </div>
        <div class="p-5">
            <div class="flex h-3 w-full overflow-hidden rounded-full bg-slate-100">
                @if($generalCount > 0)
                    <div class="h-full bg-amber-400" style="width: {{ ($generalCount / $profileTotal) * 100 }}%" title="General users: {{ $generalCount }}"></div>
                @endif
                @if($reintegratedCount > 0)
                    <div class="h-full bg-emerald-500" style="width: {{ ($reintegratedCount / $profileTotal) * 100 }}%" title="Reintegrated: {{ $reintegratedCount }}"></div>
                @endif
                @if($potentialCount > 0)
                    <div class="h-full bg-pink-500" style="width: {{ ($potentialCount / $profileTotal) * 100 }}%" title="Potential customers: {{ $potentialCount }}"></div>
                @endif
            </div>
            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-xl border border-slate-200 p-4">
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span>
                        <span class="text-sm font-medium text-slate-600">General users</span>
                    </div>
                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ number_format($generalCount) }}</p>
                    <a href="{{ route('users.index', ['profile_type' => \App\Models\User::PROFILE_GENERAL_USER]) }}" class="mt-1 inline-flex text-xs font-semibold text-brand-600 hover:text-brand-700">To be classified</a>
                </div>
                <div class="rounded-xl border border-slate-200 p-4">
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                        <span class="text-sm font-medium text-slate-600">Reintegrated profiles</span>
                    </div>
                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ number_format($reintegratedCount) }}</p>
                    <a href="{{ route('users.index', ['profile_type' => \App\Models\User::PROFILE_REINTEGRATED]) }}" class="mt-1 inline-flex text-xs font-semibold text-brand-600 hover:text-brand-700">Manage profiles</a>
                </div>
                <div class="rounded-xl border border-slate-200 p-4">
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-pink-500"></span>
                        <span class="text-sm font-medium text-slate-600">Potential customers</span>
                    </div>
                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ number_format($potentialCount) }}</p>
                    <a href="{{ route('job-board.index') }}" target="_blank" rel="noopener" class="mt-1 inline-flex text-xs font-semibold text-brand-600 hover:text-brand-700">View public job board</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">Programme snapshot</h2>
                <p class="mt-0.5 text-xs text-slate-500">Skills, migration and support activity.</p>
            </div>
        </div>
        <div class="space-y-4 p-5">
            <div class="flex items-center justify-between">
                <span class="text-sm text-slate-600">Profiles with skills</span>
                <span class="text-sm font-semibold text-slate-900">{{ number_format($usersWithSkills) }} / {{ number_format($totalProfiles) }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-slate-600">Skills recorded</span>
                <span class="text-sm font-semibold text-slate-900">{{ number_format($totalSkills) }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-slate-600">Migration histories</span>
                <span class="text-sm font-semibold text-slate-900">{{ number_format($totalMigrationHistories) }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-slate-600">Support records</span>
                <span class="text-sm font-semibold text-slate-900">{{ number_format($totalSupports) }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-slate-600">Awareness programs</span>
                <span class="text-sm font-semibold text-slate-900">{{ number_format($awarenessPrograms) }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-slate-600">Awareness participants understood</span>
                <span class="badge bg-emerald-50 text-emerald-700 ring-emerald-200">{{ $awarenessUnderstoodPercentage }}%</span>
            </div>
            <div class="flex items-center justify-between border-t border-slate-100 pt-4">
                <span class="text-sm text-slate-600">Scheduled follow-ups</span>
                <span class="badge bg-amber-50 text-amber-700 ring-amber-200">{{ number_format($pendingFollowUps) }}</span>
            </div>
        </div>
    </div>
</div>

<div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
    <div class="card xl:col-span-2">
        <div class="card-header">
            <div>
                <h2 class="card-title">Recently registered profiles</h2>
                <p class="mt-0.5 text-xs text-slate-500">Latest people added to the system.</p>
            </div>
            <a href="{{ route('users.index') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">View all</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="table-head">
                        <th class="px-5 py-3">Profile</th>
                        <th class="px-5 py-3">Type</th>
                        <th class="px-5 py-3">Branch</th>
                        <th class="px-5 py-3 text-right">Added</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentProfiles as $profile)
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600">
                                        {{ strtoupper(substr($profile->name ?? '?', 0, 2)) }}
                                    </span>
                                    <div class="min-w-0">
                                        <a href="{{ route('users.show', $profile) }}" class="block truncate text-sm font-semibold text-slate-800 hover:text-brand-700">{{ $profile->name }}</a>
                                        <p class="truncate text-xs text-slate-500">{{ $profile->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="badge {{ $profile->isPotentialCustomer() ? 'bg-pink-50 text-pink-700 ring-pink-200' : 'bg-emerald-50 text-emerald-700 ring-emerald-200' }}">
                                    {{ $profile->profile_type_label }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-slate-600">{{ $profile->branch->name ?? 'Unassigned' }}</td>
                            <td class="px-5 py-3.5 text-right text-xs text-slate-400">{{ $profile->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-sm text-slate-500">No profiles yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">Activity</h2>
                <p class="mt-0.5 text-xs text-slate-500">Latest system changes.</p>
            </div>
            <a href="{{ url('/audit-logs') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">Audit log</a>
        </div>
        <div class="p-5">
            <ol class="relative space-y-5 before:absolute before:bottom-1 before:left-[15px] before:top-1 before:w-px before:bg-slate-200">
                @forelse($recentAuditLogs as $log)
                    @php
                        $action = strtolower($log->action ?? '');
                        $dot = str_contains($action, 'delete') ? 'bg-rose-500' : (str_contains($action, 'create') ? 'bg-brand-500' : 'bg-sky-500');
                    @endphp
                    <li class="relative flex gap-3">
                        <span class="relative z-10 mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white ring-1 ring-slate-200">
                            <span class="h-2 w-2 rounded-full {{ $dot }}"></span>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm text-slate-700">
                                <span class="font-semibold text-slate-900">{{ $log->user->name ?? 'System' }}</span>
                                {{ strtolower($log->action ?? 'updated') }}
                                <span class="font-medium text-slate-800">{{ class_basename($log->subject_type ?? 'record') }}</span>
                            </p>
                            <p class="mt-0.5 text-xs text-slate-400">{{ $log->created_at?->diffForHumans() }}</p>
                        </div>
                    </li>
                @empty
                    <li class="text-sm text-slate-500">No activity recorded yet.</li>
                @endforelse
            </ol>
        </div>
    </div>
</div>

<div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
    <div class="card xl:col-span-2">
        <div class="card-header">
            <div>
                <h2 class="card-title">Profile registrations</h2>
                <p class="mt-0.5 text-xs text-slate-500">New profiles added over the last 6 months.</p>
            </div>
        </div>
        <div class="p-5">
            <div class="flex h-44 items-end gap-3 sm:gap-5">
                @foreach($monthlyRegistrations as $month)
                    <div class="flex flex-1 flex-col items-center gap-2">
                        <div class="flex w-full flex-1 items-end">
                            <div class="group relative w-full rounded-t-lg bg-brand-500/15 transition hover:bg-brand-500/30" style="height: {{ max(6, ($month['value'] / $maxRegistration) * 100) }}%">
                                <div class="absolute inset-x-0 bottom-0 rounded-t-lg bg-brand-500" style="height: {{ $maxRegistration > 0 ? ($month['value'] / $maxRegistration) * 100 : 0 }}%"></div>
                                <span class="absolute -top-7 left-1/2 hidden -translate-x-1/2 rounded-md bg-slate-900 px-2 py-1 text-[10px] font-semibold text-white group-hover:block">{{ $month['value'] }}</span>
                            </div>
                        </div>
                        <span class="text-[11px] font-medium text-slate-500">{{ $month['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">Branch load</h2>
                <p class="mt-0.5 text-xs text-slate-500">Profiles by top branches.</p>
            </div>
            <a href="{{ url('/branches') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">View all</a>
        </div>
        <div class="space-y-4 p-5">
            @php $topBranchMax = max(1, (int) ($branchPerformance->max('users_count') ?? 1)); @endphp
            @forelse($branchPerformance as $branch)
                <div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="truncate font-medium text-slate-600">{{ $branch->name }}</span>
                        <span class="ml-2 font-semibold text-slate-900">{{ $branch->users_count }}</span>
                    </div>
                    <div class="mt-1.5 h-2 w-full overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-slate-800" style="width: {{ ($branch->users_count / $topBranchMax) * 100 }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-500">No branch data yet.</p>
            @endforelse
        </div>
    </div>
</div>

<div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
    @can('view awareness programs')
        <a href="{{ url('/awareness-programs') }}" class="group flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-emerald-200 hover:bg-emerald-50/40">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg></span>
            <div><p class="text-sm font-semibold text-slate-800">Awareness programs</p><p class="text-xs text-slate-500">{{ $awarenessPrograms }} programs · {{ $awarenessUnderstoodPercentage }}% understood</p></div>
        </a>
    @endcan
    <a href="{{ route('users.create') }}" class="group flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-brand-200 hover:bg-brand-50/40">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg></span>
        <div><p class="text-sm font-semibold text-slate-800">Create profile</p><p class="text-xs text-slate-500">Register a new user</p></div>
    </a>
    <a href="{{ route('skill-access.index') }}" class="group flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-pink-200 hover:bg-pink-50/40">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-pink-50 text-pink-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9.663 17h4.674M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg></span>
        <div><p class="text-sm font-semibold text-slate-800">Manage skills</p><p class="text-xs text-slate-500">Assign skills & profile type</p></div>
    </a>
    <a href="{{ url('/reports') }}" class="group flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-violet-200 hover:bg-violet-50/40">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-50 text-violet-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></span>
        <div><p class="text-sm font-semibold text-slate-800">Generate report</p><p class="text-xs text-slate-500">Export to CSV, Excel, PDF</p></div>
    </a>
    <a href="{{ url('/job-board') }}" target="_blank" rel="noopener" class="group flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-sky-200 hover:bg-sky-50/40">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 text-sky-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></span>
        <div><p class="text-sm font-semibold text-slate-800">Public job board</p><p class="text-xs text-slate-500">Potential customer profiles</p></div>
    </a>
</div>
@endsection
