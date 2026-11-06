@extends('layouts.app')

@section('title', 'Staff Incentive Details')

@section('content')
<div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">{{ $incentive->title }}</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $incentive->incentive_date?->format('d M Y') }}</p>
    </div>
    <div class="flex flex-wrap items-center gap-3">
        @can('edit staff incentives')
            <a href="{{ route('staff-incentives.edit', $incentive) }}" class="btn-primary">Edit</a>
        @endcan
        <a href="{{ route('staff-incentives.index') }}" class="btn-secondary">Back to List</a>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <div class="card p-6">
        <h2 class="card-title mb-4">Incentive Information</h2>
        <dl class="space-y-3">
            <div class="flex justify-between border-b border-slate-100 py-2">
                <dt class="text-sm text-slate-500">Title</dt>
                <dd class="text-sm font-medium text-slate-900">{{ $incentive->title }}</dd>
            </div>
            <div class="flex justify-between border-b border-slate-100 py-2">
                <dt class="text-sm text-slate-500">Amount</dt>
                <dd class="text-sm font-semibold text-slate-900">৳ {{ number_format((float) $incentive->amount, 2) }}</dd>
            </div>
            <div class="flex justify-between border-b border-slate-100 py-2">
                <dt class="text-sm text-slate-500">Date</dt>
                <dd class="text-sm font-medium text-slate-900">{{ $incentive->incentive_date?->format('d M Y') }}</dd>
            </div>
            <div class="flex justify-between border-b border-slate-100 py-2">
                <dt class="text-sm text-slate-500">Status</dt>
                <dd>
                    <span class="badge {{ $incentive->status === 'paid' ? 'bg-brand-50 text-brand-700 ring-brand-200' : 'bg-amber-50 text-amber-700 ring-amber-200' }}">
                        {{ ucfirst($incentive->status) }}
                    </span>
                </dd>
            </div>
            <div class="flex justify-between py-2">
                <dt class="text-sm text-slate-500">Recorded by</dt>
                <dd class="text-sm font-medium text-slate-900">{{ $incentive->createdBy?->name ?? 'N/A' }}</dd>
            </div>
        </dl>

        @if($incentive->notes)
            <div class="mt-6 border-t border-slate-100 pt-4">
                <h3 class="text-sm font-semibold text-slate-700">Notes</h3>
                <p class="mt-1 whitespace-pre-line text-sm text-slate-600">{{ $incentive->notes }}</p>
            </div>
        @endif
    </div>

    <div class="card p-6">
        <h2 class="card-title mb-4">Staff Member</h2>
        <dl class="space-y-3">
            <div class="flex justify-between border-b border-slate-100 py-2">
                <dt class="text-sm text-slate-500">Name</dt>
                <dd class="text-sm font-medium text-slate-900">{{ $incentive->staff?->user?->name ?? 'N/A' }}</dd>
            </div>
            <div class="flex justify-between border-b border-slate-100 py-2">
                <dt class="text-sm text-slate-500">Employee ID</dt>
                <dd class="text-sm font-medium text-slate-900">{{ $incentive->staff?->employee_id ?? 'N/A' }}</dd>
            </div>
            <div class="flex justify-between border-b border-slate-100 py-2">
                <dt class="text-sm text-slate-500">Designation</dt>
                <dd class="text-sm font-medium text-slate-900">{{ $incentive->staff?->designation ?? 'N/A' }}</dd>
            </div>
            <div class="flex justify-between py-2">
                <dt class="text-sm text-slate-500">Branch</dt>
                <dd class="text-sm font-medium text-slate-900">{{ $incentive->staff?->branch?->name ?? 'N/A' }}</dd>
            </div>
        </dl>

        @if($incentive->staff)
            <a href="{{ route('staff.show', $incentive->staff) }}" class="mt-6 inline-flex text-sm font-semibold text-brand-600 hover:text-brand-700">
                View staff profile &rarr;
            </a>
        @endif
    </div>
</div>
@endsection
