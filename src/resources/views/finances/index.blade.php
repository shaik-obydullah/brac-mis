@extends('layouts.app')

@section('title', 'Finance')

@section('content')
<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Finance</h1>
        <p class="mt-1 text-sm text-slate-500">Double-entry journal, chart of accounts, and financial reports.</p>
    </div>
    @can('create finances')
        <a href="{{ route('finances.create') }}" class="btn-primary">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New journal entry
        </a>
    @endcan
</div>

@include('finances._tabs')

<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Journal entries</p>
        <p class="mt-1 text-2xl font-bold tracking-tight text-slate-900">{{ number_format($totals['entries']) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Total debit</p>
        <p class="mt-1 text-2xl font-bold tracking-tight text-brand-600">৳ {{ number_format($totals['debit'], 2) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Total credit</p>
        <p class="mt-1 text-2xl font-bold tracking-tight text-sky-600">৳ {{ number_format($totals['credit'], 2) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Draft entries</p>
        <p class="mt-1 text-2xl font-bold tracking-tight {{ $totals['draft'] > 0 ? 'text-amber-600' : 'text-slate-900' }}">{{ number_format($totals['draft']) }}</p>
    </div>
</div>

<div class="card mb-6 p-4">
    <form method="GET" action="{{ route('finances.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-6">
        <div class="md:col-span-2">
            <label class="field-label" for="search">Search</label>
            <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Entry no, reference, description..." class="field">
        </div>
        <div>
            <label class="field-label" for="status">Status</label>
            <select id="status" name="status" class="field">
                <option value="">All statuses</option>
                @foreach($statuses as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="field-label" for="branch_id">Branch</label>
            <select id="branch_id" name="branch_id" class="field">
                <option value="">All branches</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ (string) request('branch_id') === (string) $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                @endforeach
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
            <a href="{{ route('finances.index') }}" class="btn-secondary">Clear</a>
        </div>
    </form>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="table-head px-5 py-3">Date</th>
                    <th class="table-head px-5 py-3">Entry no</th>
                    <th class="table-head px-5 py-3">Reference</th>
                    <th class="table-head px-5 py-3">Description</th>
                    <th class="table-head px-5 py-3">Branch</th>
                    <th class="table-head px-5 py-3 text-center">Lines</th>
                    <th class="table-head px-5 py-3 text-right">Debit</th>
                    <th class="table-head px-5 py-3 text-right">Credit</th>
                    <th class="table-head px-5 py-3">Status</th>
                    <th class="table-head px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($entries as $entry)
                    <tr class="hover:bg-slate-50">
                        <td class="table-cell whitespace-nowrap">{{ $entry->entry_date?->format('d M Y') }}</td>
                        <td class="table-cell whitespace-nowrap font-semibold text-slate-900">{{ $entry->entry_no }}</td>
                        <td class="table-cell">{{ $entry->reference ?? 'N/A' }}</td>
                        <td class="table-cell max-w-xs truncate">{{ $entry->description ?? 'N/A' }}</td>
                        <td class="table-cell">{{ $entry->branch?->name ?? 'N/A' }}</td>
                        <td class="table-cell text-center">{{ $entry->lines_count }}</td>
                        <td class="table-cell text-right font-semibold text-brand-600">৳ {{ number_format($entry->total_debit, 2) }}</td>
                        <td class="table-cell text-right font-semibold text-sky-600">৳ {{ number_format($entry->total_credit, 2) }}</td>
                        <td class="table-cell">
                            <span class="badge {{ $entry->status === \App\Models\JournalEntry::STATUS_POSTED ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-amber-50 text-amber-700 ring-amber-200' }}">
                                {{ $entry->status_label }}
                            </span>
                        </td>
                        <td class="table-cell text-right whitespace-nowrap">
                            <a href="{{ route('finances.show', $entry) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">View</a>
                            @can('edit finances')
                                @if($entry->status !== \App\Models\JournalEntry::STATUS_POSTED || auth()->user()?->isSuperAdmin())
                                    <a href="{{ route('finances.edit', $entry) }}" class="ml-3 text-xs font-semibold text-brand-600 hover:text-brand-700">Edit</a>
                                @endif
                            @endcan
                            @can('delete finances')
                                @if($entry->status !== \App\Models\JournalEntry::STATUS_POSTED || auth()->user()?->isSuperAdmin())
                                    <form action="{{ route('finances.destroy', $entry) }}" method="POST" class="inline" onsubmit="return confirm('Delete this journal entry?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ml-3 text-xs font-semibold text-rose-600 hover:text-rose-700">Delete</button>
                                    </form>
                                @endif
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-5 py-12 text-center text-slate-500">
                            <p class="text-lg font-medium text-slate-700">No journal entries found</p>
                            <p class="mt-1 text-sm">Create a balanced debit/credit entry to get started.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($entries, 'links'))
        <div class="border-t border-slate-100 px-5 py-4">
            {{ $entries->links() }}
        </div>
    @endif
</div>
@endsection
