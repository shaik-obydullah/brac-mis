@extends('layouts.app')

@section('title', 'HRM')

@section('content')
<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Human Resource Management</h1>
        <p class="mt-1 text-sm text-slate-500">Overview of staff, designations and their distribution across branches.</p>
    </div>
    @can('create staff')
        <a href="{{ route('staff.create') }}" class="btn-primary">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add staff
        </a>
    @endcan
</div>

<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <a href="{{ route('staff.index') }}" class="card p-5 transition hover:border-brand-300 hover:shadow-md">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Total staff</p>
                <p class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totalStaff) }}</p>
            </div>
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </span>
        </div>
        <p class="mt-3 text-xs font-semibold text-brand-600">Manage staff &rarr;</p>
    </a>

    <a href="{{ route('designations.index') }}" class="card p-5 transition hover:border-brand-300 hover:shadow-md">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Designations</p>
                <p class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totalDesignations) }}</p>
            </div>
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </span>
        </div>
        <p class="mt-3 text-xs font-semibold text-brand-600">Manage designations &rarr;</p>
    </a>

    <a href="{{ route('branches.index') }}" class="card p-5 transition hover:border-brand-300 hover:shadow-md">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Branches</p>
                <p class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totalBranches) }}</p>
            </div>
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </span>
        </div>
        <p class="mt-3 text-xs font-semibold text-brand-600">View branches &rarr;</p>
    </a>

    <a href="{{ route('staff-incentives.index') }}" class="card p-5 transition hover:border-brand-300 hover:shadow-md">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Incentives paid</p>
                <p class="mt-1 text-2xl font-bold tracking-tight text-slate-900">৳ {{ number_format((float) $paidIncentives, 2) }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $pendingIncentives }} pending</p>
            </div>
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
        </div>
        <p class="mt-3 text-xs font-semibold text-brand-600">Manage incentives &rarr;</p>
    </a>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">Staff by branch</h2>
                <p class="mt-0.5 text-xs text-slate-500">Top branches by headcount</p>
            </div>
            <a href="{{ route('staff.index') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">View all</a>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($staffByBranch as $branch)
                <div class="flex items-center justify-between gap-3 px-5 py-3">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium text-slate-900">{{ $branch->name }}</p>
                        <p class="text-xs text-slate-500">{{ $branch->code ?? 'N/A' }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="h-1.5 w-24 overflow-hidden rounded-full bg-slate-100">
                            @php($percent = $totalStaff > 0 ? round(($branch->staff_count / $totalStaff) * 100) : 0)
                            <div class="h-full rounded-full bg-brand-500" style="width: {{ $percent }}%"></div>
                        </div>
                        <span class="w-8 text-right text-sm font-semibold text-slate-700">{{ $branch->staff_count }}</span>
                    </div>
                </div>
            @empty
                <p class="px-5 py-10 text-center text-sm text-slate-500">No branch data available.</p>
            @endforelse
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">Staff by designation</h2>
                <p class="mt-0.5 text-xs text-slate-500">Most common job titles</p>
            </div>
            <a href="{{ route('designations.index') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">View all</a>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($staffByDesignation as $row)
                <div class="flex items-center justify-between gap-3 px-5 py-3">
                    <p class="truncate text-sm font-medium text-slate-900">{{ $row->designation }}</p>
                    <div class="flex items-center gap-3">
                        <div class="h-1.5 w-24 overflow-hidden rounded-full bg-slate-100">
                            @php($percent = $totalStaff > 0 ? round(($row->total / $totalStaff) * 100) : 0)
                            <div class="h-full rounded-full bg-indigo-500" style="width: {{ $percent }}%"></div>
                        </div>
                        <span class="w-8 text-right text-sm font-semibold text-slate-700">{{ $row->total }}</span>
                    </div>
                </div>
            @empty
                <p class="px-5 py-10 text-center text-sm text-slate-500">No staff designations recorded yet.</p>
            @endforelse
        </div>
    </div>
</div>

<div class="card mt-6 overflow-hidden">
    <div class="card-header">
        <div>
            <h2 class="card-title">Recent incentives</h2>
            <p class="mt-0.5 text-xs text-slate-500">Latest staff incentives recorded</p>
        </div>
        <a href="{{ route('staff-incentives.index') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">View all</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="table-head px-5 py-3">Date</th>
                    <th class="table-head px-5 py-3">Staff</th>
                    <th class="table-head px-5 py-3">Incentive</th>
                    <th class="table-head px-5 py-3 text-right">Amount</th>
                    <th class="table-head px-5 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($recentIncentives as $incentive)
                    <tr class="hover:bg-slate-50">
                        <td class="table-cell">{{ $incentive->incentive_date?->format('d M Y') }}</td>
                        <td class="table-cell">
                            <a href="{{ route('staff-incentives.show', $incentive) }}" class="font-medium text-slate-900 hover:text-brand-600">
                                {{ $incentive->staff?->user?->name ?? 'N/A' }}
                            </a>
                            <p class="text-xs text-slate-500">{{ $incentive->staff?->employee_id }}</p>
                        </td>
                        <td class="table-cell">{{ $incentive->title }}</td>
                        <td class="table-cell text-right font-semibold text-slate-900">৳ {{ number_format((float) $incentive->amount, 2) }}</td>
                        <td class="table-cell">
                            <span class="badge {{ $incentive->status === 'paid' ? 'bg-brand-50 text-brand-700 ring-brand-200' : 'bg-amber-50 text-amber-700 ring-amber-200' }}">
                                {{ ucfirst($incentive->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-500">No staff incentives recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
