@extends('layouts.app')

@section('title', 'Job Board Inquiries')

@section('content')
@php
    $statusStyles = [
        'new' => 'bg-sky-50 text-sky-700 ring-sky-200',
        'contacted' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'shared' => 'bg-brand-50 text-brand-700 ring-brand-200',
        'closed' => 'bg-slate-100 text-slate-700 ring-slate-200',
    ];
    $filterQuery = array_filter([
        'search' => request('search'),
        'status' => request('status'),
    ], fn ($value) => $value !== null && $value !== '');
@endphp

<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Job Board Inquiries</h1>
        <p class="mt-1 text-sm text-slate-500">Review employer contact requests submitted from the public job board and follow up with candidates.</p>
    </div>
    <a href="{{ route('job-board.index') }}" target="_blank" rel="noopener" class="btn-secondary">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        View public job board
    </a>
</div>

<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">All inquiries</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totals['all']) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">New</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-sky-600">{{ number_format($totals['new']) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Contacted</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-amber-600">{{ number_format($totals['contacted']) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Contact shared</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-brand-600">{{ number_format($totals['shared']) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Closed</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-slate-600">{{ number_format($totals['closed']) }}</p>
    </div>
</div>

<div class="card mb-6 p-4">
    <form method="GET" action="{{ route('job-board.inquiries.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="md:col-span-2">
            <label class="field-label" for="search">Search</label>
            <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Employer, organisation, candidate or role..." class="field">
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
        <div class="flex items-end gap-2">
            <button type="submit" class="btn-primary">Filter</button>
            <a href="{{ route('job-board.inquiries.index') }}" class="btn-secondary">Clear</a>
        </div>
    </form>
</div>

<div class="card overflow-hidden">
    <div class="card-header">
        <div>
            <h2 class="card-title">Contact requests</h2>
            <p class="mt-0.5 text-xs text-slate-500">Requests matching the selected filters</p>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="table-head px-5 py-3">Received</th>
                    <th class="table-head px-5 py-3">Candidate</th>
                    <th class="table-head px-5 py-3">Employer</th>
                    <th class="table-head px-5 py-3">Role</th>
                    <th class="table-head px-5 py-3">Status</th>
                    <th class="table-head px-5 py-3">Handled by</th>
                    <th class="table-head px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($inquiries as $inquiry)
                    <tr class="hover:bg-slate-50">
                        <td class="table-cell whitespace-nowrap">
                            {{ $inquiry->created_at?->format('d M Y') }}
                            <p class="text-xs text-slate-500">{{ $inquiry->created_at?->format('H:i') }}</p>
                        </td>
                        <td class="table-cell">
                            @if($inquiry->candidate)
                                <a href="{{ route('job-board.inquiries.show', $inquiry) }}" class="font-medium text-slate-900 hover:text-brand-600">{{ $inquiry->candidate->name }}</a>
                                <p class="text-xs text-slate-500">{{ $inquiry->candidate->branch?->district ?? 'Location not recorded' }}</p>
                            @else
                                <span class="text-slate-500">Candidate removed</span>
                            @endif
                        </td>
                        <td class="table-cell">
                            <p class="font-medium text-slate-900">{{ $inquiry->employer_name }}</p>
                            <p class="text-xs text-slate-500">{{ $inquiry->employer_organization ?: $inquiry->employer_email }}</p>
                        </td>
                        <td class="table-cell">{{ $inquiry->job_title ?: '—' }}</td>
                        <td class="table-cell">
                            <span class="badge {{ $statusStyles[$inquiry->status] ?? 'bg-slate-50 text-slate-700 ring-slate-200' }}">{{ $inquiry->status_label }}</span>
                        </td>
                        <td class="table-cell">
                            {{ $inquiry->handledBy?->name ?? '—' }}
                            @if($inquiry->handled_at)
                                <p class="text-xs text-slate-500">{{ $inquiry->handled_at->format('d M Y') }}</p>
                            @endif
                        </td>
                        <td class="table-cell text-right whitespace-nowrap">
                            <a href="{{ route('job-board.inquiries.show', $inquiry) }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">Review</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-slate-500">
                            <p class="text-lg font-medium text-slate-700">No inquiries found</p>
                            <p class="mt-1 text-sm">Contact requests submitted from the public job board will appear here.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($inquiries, 'links'))
        <div class="border-t border-slate-100 px-5 py-4">
            {{ $inquiries->links() }}
        </div>
    @endif
</div>

@if($filterQuery)
    <p class="mt-3 text-xs text-slate-500">Showing filtered results. <a href="{{ route('job-board.inquiries.index') }}" class="font-semibold text-brand-600 hover:text-brand-700">Clear filters</a></p>
@endif
@endsection
