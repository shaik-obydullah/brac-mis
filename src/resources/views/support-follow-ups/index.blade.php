@extends('layouts.app')

@section('title', 'Support Follow-ups')

@section('content')
@php
    $statusStyles = [
        'scheduled' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'completed' => 'bg-brand-50 text-brand-700 ring-brand-200',
        'missed' => 'bg-rose-50 text-rose-700 ring-rose-200',
    ];
    $methodLabels = \App\Models\SupportFollowUp::METHODS;
    $filterQuery = array_filter([
        'search' => request('search'),
        'category' => request('category'),
        'status' => request('status'),
        'method' => request('method'),
        'from' => request('from'),
        'to' => request('to'),
    ], fn ($value) => $value !== null && $value !== '');
@endphp

<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Support Follow-ups</h1>
        <p class="mt-1 text-sm text-slate-500">Track follow-ups for immediate, mental, physical and financial support.</p>
    </div>
    @can('create immediate support')
        <a href="{{ route('support-follow-ups.create') }}" class="btn-primary">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Record Follow-up
        </a>
    @endcan
</div>

<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Total follow-ups</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totals['records']) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Completed</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-brand-600">{{ number_format($totals['completed']) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Scheduled</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-amber-600">{{ number_format($totals['scheduled']) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Missed</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-rose-600">{{ number_format($totals['missed']) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Due / overdue</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totals['due']) }}</p>
    </div>
</div>

<div class="card mb-6 p-4">
    <form method="GET" action="{{ route('support-follow-ups.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-6">
        <div class="md:col-span-2">
            <label class="field-label" for="search">Search</label>
            <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Recipient, phone, outcome..." class="field">
        </div>
        <div>
            <label class="field-label" for="category">Support type</label>
            <select id="category" name="category" class="field">
                <option value="">All support types</option>
                @foreach($categories as $value => $config)
                    <option value="{{ $value }}" {{ request('category') === $value ? 'selected' : '' }}>{{ $config['label'] }}</option>
                @endforeach
            </select>
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
            <label class="field-label" for="method">Method</label>
            <select id="method" name="method" class="field">
                <option value="">All methods</option>
                @foreach($methods as $value => $label)
                    <option value="{{ $value }}" {{ request('method') === $value ? 'selected' : '' }}>{{ $label }}</option>
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
            <button type="submit" class="btn-primary">Filter</button>
            <a href="{{ route('support-follow-ups.index') }}" class="btn-secondary">Clear</a>
        </div>
    </form>
</div>

<div class="card overflow-hidden">
    <div class="card-header">
        <div>
            <h2 class="card-title">Follow-up records</h2>
            <p class="mt-0.5 text-xs text-slate-500">Follow-ups matching the selected filters</p>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="table-head px-5 py-3">Follow-up date</th>
                    <th class="table-head px-5 py-3">Support</th>
                    <th class="table-head px-5 py-3">Method</th>
                    <th class="table-head px-5 py-3">Status</th>
                    <th class="table-head px-5 py-3">Outcome</th>
                    <th class="table-head px-5 py-3">Next date</th>
                    <th class="table-head px-5 py-3">Followed by</th>
                    <th class="table-head px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($followUps as $followUp)
                    @php($support = $followUp->support)
                    @php($category = $support ? \App\Models\ImmediateSupport::categoryForType($support->support_type) : null)
                    @php($recipient = $support?->beneficiary?->name ?? $support?->returnee?->beneficiary?->name ?? $support?->recipient_name)
                    <tr class="hover:bg-slate-50">
                        <td class="table-cell whitespace-nowrap">{{ optional($followUp->follow_up_date)->format('d M Y') }}</td>
                        <td class="table-cell">
                            @if($support && $category)
                                <a href="{{ route('support.show', ['category' => $category, 'immediateSupport' => $support]) }}" class="font-medium text-slate-900 hover:text-brand-600">
                                    {{ $support->type_label }}
                                </a>
                                <p class="text-xs text-slate-500">{{ $recipient ?? 'N/A' }}</p>
                            @else
                                <span class="text-slate-500">N/A</span>
                            @endif
                        </td>
                        <td class="table-cell">{{ $methodLabels[$followUp->method] ?? ucfirst(str_replace('_', ' ', $followUp->method)) }}</td>
                        <td class="table-cell">
                            <span class="badge {{ $statusStyles[$followUp->status] ?? 'bg-slate-50 text-slate-700 ring-slate-200' }}">
                                {{ $followUp->status_label }}
                            </span>
                            @if($followUp->is_reintegrated)
                                <span class="badge ml-1 bg-emerald-50 text-emerald-700 ring-emerald-200">Reintegrated</span>
                            @endif
                        </td>
                        <td class="table-cell max-w-xs">
                            <p class="line-clamp-2 text-slate-700">{{ $followUp->outcome ?: '—' }}</p>
                        </td>
                        <td class="table-cell whitespace-nowrap">{{ optional($followUp->next_follow_up_date)->format('d M Y') ?? '—' }}</td>
                        <td class="table-cell">{{ $followUp->followedBy?->name ?? '—' }}</td>
                        <td class="table-cell text-right whitespace-nowrap">
                            @can('edit immediate support')
                                <a href="{{ route('support-follow-ups.edit', $followUp) }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">Edit</a>
                            @endcan
                            @can('delete immediate support')
                                <form action="{{ route('support-follow-ups.destroy', $followUp) }}" method="POST" class="inline" onsubmit="return confirm('Delete this follow-up?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ml-3 text-xs font-semibold text-rose-600 hover:text-rose-700">Delete</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center text-slate-500">
                            <p class="text-lg font-medium text-slate-700">No follow-ups found</p>
                            <p class="mt-1 text-sm">Record a follow-up against a support entry to get started.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($followUps, 'links'))
        <div class="border-t border-slate-100 px-5 py-4">
            {{ $followUps->links() }}
        </div>
    @endif
</div>
@endsection
