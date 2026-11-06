@extends('layouts.app')

@section('title', 'Awareness Program Details')

@section('content')
<div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">{{ $program->program_name }}</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $program->upazila }}, {{ $program->district }}, {{ $program->division }}</p>
    </div>
    <div class="flex flex-wrap items-center gap-3">
        @can('edit awareness programs')
            <a href="{{ route('awareness-programs.edit', $program) }}" class="btn-primary">Edit</a>
        @endcan
        <a href="{{ route('awareness-programs.index') }}" class="btn-secondary">Back to List</a>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <div class="card p-6">
        <h2 class="card-title mb-4">Program Information</h2>
        <dl class="space-y-3">
            <div class="flex justify-between border-b border-slate-100 py-2">
                <dt class="text-sm text-slate-500">Program name</dt>
                <dd class="text-sm font-medium text-slate-900">{{ $program->program_name }}</dd>
            </div>
            <div class="flex justify-between border-b border-slate-100 py-2">
                <dt class="text-sm text-slate-500">Division</dt>
                <dd class="text-sm font-medium text-slate-900">{{ $program->division }}</dd>
            </div>
            <div class="flex justify-between border-b border-slate-100 py-2">
                <dt class="text-sm text-slate-500">District</dt>
                <dd class="text-sm font-medium text-slate-900">{{ $program->district }}</dd>
            </div>
            <div class="flex justify-between border-b border-slate-100 py-2">
                <dt class="text-sm text-slate-500">Upazila</dt>
                <dd class="text-sm font-medium text-slate-900">{{ $program->upazila }}</dd>
            </div>
            <div class="flex justify-between border-b border-slate-100 py-2">
                <dt class="text-sm text-slate-500">Address</dt>
                <dd class="text-sm font-medium text-slate-900">{{ $program->address ?? 'N/A' }}</dd>
            </div>
            <div class="flex justify-between border-b border-slate-100 py-2">
                <dt class="text-sm text-slate-500">Created by</dt>
                <dd class="text-sm font-medium text-slate-900">{{ $program->createdBy?->name ?? 'N/A' }}</dd>
            </div>
            <div class="flex justify-between py-2">
                <dt class="text-sm text-slate-500">Created at</dt>
                <dd class="text-sm font-medium text-slate-900">{{ $program->created_at?->format('d M Y, h:i A') }}</dd>
            </div>
        </dl>
    </div>
</div>
@endsection
