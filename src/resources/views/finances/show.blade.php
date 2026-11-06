@extends('layouts.app')

@section('title', 'Finance Entry')

@section('content')
<div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Finance Entry #{{ $finance->id }}</h1>
        <p class="mt-1 text-sm text-slate-500">Recorded {{ $finance->created_at?->format('d M Y, H:i') }}</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('finances.edit', $finance) }}" class="btn-primary">Edit</a>
        <a href="{{ route('finances.index') }}" class="btn-secondary">Back to list</a>
    </div>
</div>

<div class="card p-6">
    <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div>
            <dt class="field-label">Entry date</dt>
            <dd class="text-sm font-medium text-slate-900">{{ $finance->entry_date?->format('d M Y') }}</dd>
        </div>
        <div>
            <dt class="field-label">Type</dt>
            <dd>
                <span class="badge {{ $finance->type === 'income' ? 'bg-brand-50 text-brand-700 ring-brand-200' : 'bg-rose-50 text-rose-700 ring-rose-200' }}">
                    {{ ucfirst($finance->type) }}
                </span>
            </dd>
        </div>
        <div>
            <dt class="field-label">Category</dt>
            <dd class="text-sm font-medium text-slate-900">{{ $finance->category ?? 'N/A' }}</dd>
        </div>
        <div>
            <dt class="field-label">Amount</dt>
            <dd class="text-sm font-semibold {{ $finance->type === 'income' ? 'text-brand-600' : 'text-rose-600' }}">
                {{ number_format((float) $finance->amount, 2) }} {{ $finance->currency }}
            </dd>
        </div>
        <div>
            <dt class="field-label">Branch</dt>
            <dd class="text-sm font-medium text-slate-900">{{ $finance->branch?->name ?? 'N/A' }}</dd>
        </div>
        <div>
            <dt class="field-label">Recorded by</dt>
            <dd class="text-sm font-medium text-slate-900">{{ $finance->createdBy?->name ?? 'N/A' }}</dd>
        </div>
        <div class="sm:col-span-2">
            <dt class="field-label">Description</dt>
            <dd class="text-sm text-slate-700">{{ $finance->description ?? 'N/A' }}</dd>
        </div>
    </dl>
</div>
@endsection
