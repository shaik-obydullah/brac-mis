@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $migrantPipeline = [
        'registered' => ['label' => 'Registered', 'class' => 'bg-slate-400'],
        'pre_departure' => ['label' => 'Pre-departure', 'class' => 'bg-amber-400'],
        'deployed' => ['label' => 'Deployed', 'class' => 'bg-brand-500'],
        'returned' => ['label' => 'Returned', 'class' => 'bg-sky-500'],
        'cancelled' => ['label' => 'Cancelled', 'class' => 'bg-rose-400'],
    ];
    $returneePipeline = [
        'assessed' => ['label' => 'Assessed', 'class' => 'bg-sky-500'],
        'planning' => ['label' => 'Planning', 'class' => 'bg-indigo-500'],
        'in_progress' => ['label' => 'In progress', 'class' => 'bg-amber-500'],
        'completed' => ['label' => 'Reintegrated', 'class' => 'bg-brand-500'],
        'dropped' => ['label' => 'Dropped', 'class' => 'bg-slate-400'],
    ];
    $maxEnrollment = max(1, $monthlyEnrollments->max('value'));
@endphp

<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <p class="text-sm font-medium text-brand-600">{{ now()->format('l, d F Y') }}</p>
        <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Dashboard</h1>
        <p class="mt-1 text-sm text-slate-500">A live view of the migration and reintegration lifecycle.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ url('/beneficiaries/create') }}" class="btn-primary">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New beneficiary
        </a>
        <a href="{{ url('/migrants/create') }}" class="btn-secondary">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            New migrant
        </a>
    </div>
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <a href="{{ url('/beneficiaries') }}" class="group card p-5 transition hover:-translate-y-0.5 hover:shadow-md">
        <div class="flex items-start justify-between">
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-600 ring-1 ring-inset ring-brand-100">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </span>
            <span class="badge bg-brand-50 text-brand-700 ring-brand-200">{{ $activeBeneficiaries }} active</span>
        </div>
        <p class="mt-5 text-sm font-medium text-slate-500">Total beneficiaries</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($beneficiaries) }}</p>
        <p class="mt-2 flex items-center gap-1 text-xs font-medium text-slate-400 group-hover:text-brand-600">
            View all beneficiaries
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </p>
    </a>

    <a href="{{ url('/migrants') }}" class="group card p-5 transition hover:-translate-y-0.5 hover:shadow-md">
        <div class="flex items-start justify-between">
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-sky-50 text-sky-600 ring-1 ring-inset ring-sky-100">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            </span>
            <span class="badge bg-sky-50 text-sky-700 ring-sky-200">{{ $activeMigrants }} deployed</span>
        </div>
        <p class="mt-5 text-sm font-medium text-slate-500">Migrants tracked</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totalMigrants) }}</p>
        <p class="mt-2 flex items-center gap-1 text-xs font-medium text-slate-400 group-hover:text-sky-600">
            View migration records
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </p>
    </a>

    <a href="{{ url('/returnees') }}" class="group card p-5 transition hover:-translate-y-0.5 hover:shadow-md">
        <div class="flex items-start justify-between">
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600 ring-1 ring-inset ring-amber-100">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </span>
            <span class="badge bg-amber-50 text-amber-700 ring-amber-200">{{ $completedReturnees }} reintegrated</span>
        </div>
        <p class="mt-5 text-sm font-medium text-slate-500">Returnees supported</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($returnees) }}</p>
        <p class="mt-2 flex items-center gap-1 text-xs font-medium text-slate-400 group-hover:text-amber-600">
            View reintegration cases
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </p>
    </a>

    <a href="{{ url('/branches') }}" class="group card p-5 transition hover:-translate-y-0.5 hover:shadow-md">
        <div class="flex items-start justify-between">
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-violet-50 text-violet-600 ring-1 ring-inset ring-violet-100">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </span>
            <span class="badge bg-violet-50 text-violet-700 ring-violet-200">{{ $staff }} staff</span>
        </div>
        <p class="mt-5 text-sm font-medium text-slate-500">Branches</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($branches) }}</p>
        <p class="mt-2 flex items-center gap-1 text-xs font-medium text-slate-400 group-hover:text-violet-600">
            View organization
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </p>
    </a>
</div>

<div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
    <div class="card xl:col-span-2">
        <div class="card-header">
            <div>
                <h2 class="card-title">Migration pipeline</h2>
                <p class="mt-0.5 text-xs text-slate-500">Where migrants currently sit in the journey abroad.</p>
            </div>
            <a href="{{ url('/migrants') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">View all</a>
        </div>
        <div class="p-5">
            <div class="flex h-3 w-full overflow-hidden rounded-full bg-slate-100">
                @foreach($migrantPipeline as $key => $meta)
                    @php $count = (int) ($migrantStatus[$key] ?? 0); @endphp
                    @if($count > 0)
                        <div class="{{ $meta['class'] }} h-full transition-all" style="width: {{ ($count / max(1, $totalMigrants)) * 100 }}%" title="{{ $meta['label'] }}: {{ $count }}"></div>
                    @endif
                @endforeach
            </div>
            <div class="mt-6 grid grid-cols-2 gap-x-6 gap-y-4 sm:grid-cols-5">
                @foreach($migrantPipeline as $key => $meta)
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full {{ $meta['class'] }}"></span>
                            <span class="text-xs font-medium text-slate-500">{{ $meta['label'] }}</span>
                        </div>
                        <p class="mt-1 text-xl font-bold text-slate-900">{{ number_format((int) ($migrantStatus[$key] ?? 0)) }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">Reintegration progress</h2>
                <p class="mt-0.5 text-xs text-slate-500">Returnee case status.</p>
            </div>
            <a href="{{ url('/returnees') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">View all</a>
        </div>
        <div class="space-y-4 p-5">
            @foreach($returneePipeline as $key => $meta)
                @php
                    $count = (int) ($returneeStatus[$key] ?? 0);
                    $percent = $returnees > 0 ? ($count / $returnees) * 100 : 0;
                @endphp
                <div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-medium text-slate-600">{{ $meta['label'] }}</span>
                        <span class="font-semibold text-slate-900">{{ number_format($count) }}</span>
                    </div>
                    <div class="mt-1.5 h-2 w-full overflow-hidden rounded-full bg-slate-100">
                        <div class="{{ $meta['class'] }} h-full rounded-full" style="width: {{ $percent }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
    <div class="card xl:col-span-2">
        <div class="card-header">
            <div>
                <h2 class="card-title">Recently registered beneficiaries</h2>
                <p class="mt-0.5 text-xs text-slate-500">Latest people added to the programme.</p>
            </div>
            <a href="{{ url('/beneficiaries') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">View all</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="table-head">
                        <th class="px-5 py-3">Beneficiary</th>
                        <th class="px-5 py-3">Branch</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Added</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentBeneficiaries as $beneficiary)
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600">
                                        {{ strtoupper(substr($beneficiary->name ?? '?', 0, 2)) }}
                                    </span>
                                    <div class="min-w-0">
                                        <a href="{{ route('beneficiaries.show', $beneficiary) }}" class="block truncate text-sm font-semibold text-slate-800 hover:text-brand-700">{{ $beneficiary->name }}</a>
                                        <p class="truncate text-xs text-slate-500">{{ $beneficiary->brac_id ?? 'No BRAC ID' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-slate-600">{{ $beneficiary->branch->name ?? 'Unassigned' }}</td>
                            <td class="px-5 py-3.5">
                                <span class="badge {{ $beneficiary->status === 'active' ? 'bg-brand-50 text-brand-700 ring-brand-200' : ($beneficiary->status === 'inactive' ? 'bg-rose-50 text-rose-700 ring-rose-200' : 'bg-amber-50 text-amber-700 ring-amber-200') }}">
                                    {{ $beneficiary->status ? ucfirst($beneficiary->status) : 'N/A' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right text-xs text-slate-400">{{ $beneficiary->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-sm text-slate-500">No beneficiaries yet.</td>
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
                <h2 class="card-title">Beneficiary enrollment</h2>
                <p class="mt-0.5 text-xs text-slate-500">New beneficiaries registered over the last 6 months.</p>
            </div>
        </div>
        <div class="p-5">
            <div class="flex h-44 items-end gap-3 sm:gap-5">
                @foreach($monthlyEnrollments as $month)
                    <div class="flex flex-1 flex-col items-center gap-2">
                        <div class="flex w-full flex-1 items-end">
                            <div class="group relative w-full rounded-t-lg bg-brand-500/15 transition hover:bg-brand-500/30" style="height: {{ max(6, ($month['value'] / $maxEnrollment) * 100) }}%">
                                <div class="absolute inset-x-0 bottom-0 rounded-t-lg bg-brand-500" style="height: {{ $maxEnrollment > 0 ? ($month['value'] / $maxEnrollment) * 100 : 0 }}%"></div>
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
                <p class="mt-0.5 text-xs text-slate-500">Beneficiaries by top branches.</p>
            </div>
            <a href="{{ url('/branches') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">View all</a>
        </div>
        <div class="space-y-4 p-5">
            @php $topBranchMax = max(1, (int) ($branchPerformance->max('beneficiaries_count') ?? 1)); @endphp
            @forelse($branchPerformance as $branch)
                <div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="truncate font-medium text-slate-600">{{ $branch->name }}</span>
                        <span class="ml-2 font-semibold text-slate-900">{{ $branch->beneficiaries_count }}</span>
                    </div>
                    <div class="mt-1.5 h-2 w-full overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-slate-800" style="width: {{ ($branch->beneficiaries_count / $topBranchMax) * 100 }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-500">No branch data yet.</p>
            @endforelse
        </div>
    </div>
</div>

<div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <a href="{{ url('/beneficiaries/create') }}" class="group flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-brand-200 hover:bg-brand-50/40">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg></span>
        <div><p class="text-sm font-semibold text-slate-800">Register beneficiary</p><p class="text-xs text-slate-500">Add a person at risk</p></div>
    </a>
    <a href="{{ url('/migrants/create') }}" class="group flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-sky-200 hover:bg-sky-50/40">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 text-sky-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg></span>
        <div><p class="text-sm font-semibold text-slate-800">New migration</p><p class="text-xs text-slate-500">Start a deployment record</p></div>
    </a>
    <a href="{{ url('/returnees/create') }}" class="group flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-amber-200 hover:bg-amber-50/40">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></span>
        <div><p class="text-sm font-semibold text-slate-800">Record returnee</p><p class="text-xs text-slate-500">Begin reintegration</p></div>
    </a>
    <a href="{{ url('/reports') }}" class="group flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-violet-200 hover:bg-violet-50/40">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-50 text-violet-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></span>
        <div><p class="text-sm font-semibold text-slate-800">Generate report</p><p class="text-xs text-slate-500">Export to CSV, Excel, PDF</p></div>
    </a>
</div>
@endsection
