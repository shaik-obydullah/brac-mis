@extends('layouts.app')

@section('title', 'Finance')

@section('content')
<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Finance</h1>
        <p class="mt-1 text-sm text-slate-500">Track income and expenses across the organisation.</p>
    </div>
    <a href="{{ route('finances.create') }}" class="btn-primary">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New entry
    </a>
</div>

<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Income</p>
        <p class="mt-1 text-2xl font-bold tracking-tight text-brand-600">৳ {{ number_format((float) $totals['income'], 2) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Expense</p>
        <p class="mt-1 text-2xl font-bold tracking-tight text-rose-600">৳ {{ number_format((float) $totals['expense'], 2) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Net</p>
        <p class="mt-1 text-2xl font-bold tracking-tight {{ $totals['net'] < 0 ? 'text-rose-600' : 'text-slate-900' }}">৳ {{ number_format((float) $totals['net'], 2) }}</p>
    </div>
</div>

<div class="card mb-6 p-4">
    <form method="GET" action="{{ route('finances.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-6">
        <div class="md:col-span-2">
            <label class="field-label" for="search">Search</label>
            <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Category or description..." class="field">
        </div>
        <div>
            <label class="field-label" for="type">Type</label>
            <select id="type" name="type" class="field">
                <option value="">All types</option>
                <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Income</option>
                <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense</option>
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
                    <th class="table-head px-5 py-3">Type</th>
                    <th class="table-head px-5 py-3">Category</th>
                    <th class="table-head px-5 py-3">Branch</th>
                    <th class="table-head px-5 py-3">Description</th>
                    <th class="table-head px-5 py-3 text-right">Amount</th>
                    <th class="table-head px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($finances as $finance)
                    <tr class="hover:bg-slate-50">
                        <td class="table-cell">{{ $finance->entry_date?->format('d M Y') }}</td>
                        <td class="table-cell">
                            <span class="badge {{ $finance->type === 'income' ? 'bg-brand-50 text-brand-700 ring-brand-200' : 'bg-rose-50 text-rose-700 ring-rose-200' }}">
                                {{ ucfirst($finance->type) }}
                            </span>
                        </td>
                        <td class="table-cell">{{ $finance->category ?? 'N/A' }}</td>
                        <td class="table-cell">{{ $finance->branch?->name ?? 'N/A' }}</td>
                        <td class="table-cell max-w-xs truncate">{{ $finance->description ?? 'N/A' }}</td>
                        <td class="table-cell text-right font-semibold {{ $finance->type === 'income' ? 'text-brand-600' : 'text-rose-600' }}">
                            {{ $finance->type === 'income' ? '+' : '−' }} {{ number_format((float) $finance->amount, 2) }} {{ $finance->currency }}
                        </td>
                        <td class="table-cell text-right">
                            <a href="{{ route('finances.show', $finance) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">View</a>
                            <a href="{{ route('finances.edit', $finance) }}" class="ml-3 text-xs font-semibold text-brand-600 hover:text-brand-700">Edit</a>
                            <form action="{{ route('finances.destroy', $finance) }}" method="POST" class="inline" onsubmit="return confirm('Delete this finance entry?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ml-3 text-xs font-semibold text-rose-600 hover:text-rose-700">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-slate-500">
                            <p class="text-lg font-medium text-slate-700">No finance entries found</p>
                            <p class="mt-1 text-sm">Get started by creating a new entry.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($finances, 'links'))
        <div class="border-t border-slate-100 px-5 py-4">
            {{ $finances->links() }}
        </div>
    @endif
</div>
@endsection
