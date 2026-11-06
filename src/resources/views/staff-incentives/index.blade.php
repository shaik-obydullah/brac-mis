@extends('layouts.app')

@section('title', 'Staff Incentives')

@section('content')
<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Staff Incentives</h1>
        <p class="mt-1 text-sm text-slate-500">Record and track performance bonuses and other staff incentives.</p>
    </div>
    @can('create staff incentives')
        <a href="{{ route('staff-incentives.create') }}" class="btn-primary">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New incentive
        </a>
    @endcan
</div>

<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Total incentives</p>
        <p class="mt-1 text-2xl font-bold tracking-tight text-slate-900">৳ {{ number_format((float) $totals['total'], 2) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Paid</p>
        <p class="mt-1 text-2xl font-bold tracking-tight text-brand-600">৳ {{ number_format((float) $totals['paid'], 2) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Pending</p>
        <p class="mt-1 text-2xl font-bold tracking-tight text-amber-600">৳ {{ number_format((float) $totals['pending'], 2) }}</p>
    </div>
</div>

<div class="card mb-6 p-4">
    <form method="GET" action="{{ route('staff-incentives.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-6">
        <div class="md:col-span-2">
            <label class="field-label" for="search">Search</label>
            <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Title or notes..." class="field">
        </div>
        <div class="md:col-span-2">
            <label class="field-label" for="staff_id">Staff</label>
            <select id="staff_id" name="staff_id" class="field">
                <option value="">All staff</option>
                @foreach($staff as $member)
                    <option value="{{ $member->id }}" {{ (string) request('staff_id') === (string) $member->id ? 'selected' : '' }}>
                        {{ $member->user?->name ?? $member->employee_id }} — {{ $member->employee_id }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="field-label" for="status">Status</label>
            <select id="status" name="status" class="field">
                <option value="">All statuses</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
            </select>
        </div>
        <div>
            <label class="field-label" for="from">From</label>
            <input id="from" type="date" name="from" value="{{ request('from') }}" class="field">
        </div>
        <div>
            <label class="field-label" for="to">To</label>
            <input id="to" type="date" name="to" value="{{ request('to') }}" class="field">
        </div>
        <div class="flex items-end gap-2 md:col-span-6">
            <button type="submit" class="btn-primary">Search</button>
            <a href="{{ route('staff-incentives.index') }}" class="btn-secondary">Clear</a>
        </div>
    </form>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="table-head px-5 py-3">Date</th>
                    <th class="table-head px-5 py-3">Staff</th>
                    <th class="table-head px-5 py-3">Branch</th>
                    <th class="table-head px-5 py-3">Incentive</th>
                    <th class="table-head px-5 py-3 text-right">Amount</th>
                    <th class="table-head px-5 py-3">Status</th>
                    <th class="table-head px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($incentives as $incentive)
                    <tr class="hover:bg-slate-50">
                        <td class="table-cell">{{ $incentive->incentive_date?->format('d M Y') }}</td>
                        <td class="table-cell">
                            <p class="font-medium text-slate-900">{{ $incentive->staff?->user?->name ?? 'N/A' }}</p>
                            <p class="text-xs text-slate-500">{{ $incentive->staff?->employee_id }}</p>
                        </td>
                        <td class="table-cell">{{ $incentive->staff?->branch?->name ?? 'N/A' }}</td>
                        <td class="table-cell">
                            <p class="font-medium text-slate-900">{{ $incentive->title }}</p>
                            @if($incentive->notes)
                                <p class="max-w-xs truncate text-xs text-slate-500">{{ $incentive->notes }}</p>
                            @endif
                        </td>
                        <td class="table-cell text-right font-semibold text-slate-900">৳ {{ number_format((float) $incentive->amount, 2) }}</td>
                        <td class="table-cell">
                            <span class="badge {{ $incentive->status === 'paid' ? 'bg-brand-50 text-brand-700 ring-brand-200' : 'bg-amber-50 text-amber-700 ring-amber-200' }}">
                                {{ ucfirst($incentive->status) }}
                            </span>
                        </td>
                        <td class="table-cell text-right">
                            <a href="{{ route('staff-incentives.show', $incentive) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">View</a>
                            @can('edit staff incentives')
                                <a href="{{ route('staff-incentives.edit', $incentive) }}" class="ml-3 text-xs font-semibold text-brand-600 hover:text-brand-700">Edit</a>
                            @endcan
                            @can('delete staff incentives')
                                <form action="{{ route('staff-incentives.destroy', $incentive) }}" method="POST" class="inline" onsubmit="return confirm('Delete this incentive?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ml-3 text-xs font-semibold text-rose-600 hover:text-rose-700">Delete</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-slate-500">
                            <p class="text-lg font-medium text-slate-700">No staff incentives found</p>
                            <p class="mt-1 text-sm">Get started by recording a new incentive.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($incentives, 'links'))
        <div class="border-t border-slate-100 px-5 py-4">
            {{ $incentives->links() }}
        </div>
    @endif
</div>
@endsection
