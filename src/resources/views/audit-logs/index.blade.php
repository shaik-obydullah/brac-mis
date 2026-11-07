@extends('layouts.app')

@section('title', 'System Activity Logger')

@section('content')
@php
    $actionStyles = [
        'created' => 'bg-brand-50 text-brand-700 ring-brand-200',
        'updated' => 'bg-sky-50 text-sky-700 ring-sky-200',
        'update' => 'bg-sky-50 text-sky-700 ring-sky-200',
        'deleted' => 'bg-rose-50 text-rose-700 ring-rose-200',
    ];
@endphp

<div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">System Activity Logger</h1>
        <p class="mt-1 text-sm text-slate-500">Audit trail of every create, update, and delete performed across every module.</p>
    </div>
    <p class="text-sm text-slate-500">{{ number_format($logs->total()) }} {{ Str::plural('activity entry', $logs->total()) }}</p>
</div>

<div class="card mb-6 p-4">
    <form method="GET" action="{{ route('audit-logs.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="md:col-span-2">
            <label class="field-label" for="module">Module</label>
            <select id="module" name="module" class="field">
                <option value="">All modules</option>
                @foreach($modules as $type => $label)
                    <option value="{{ $type }}" {{ request('module') === $type ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="field-label" for="action">Action</label>
            <select id="action" name="action" class="field">
                <option value="">All actions</option>
                @foreach($actions as $value => $label)
                    <option value="{{ $value }}" {{ request('action') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2 md:col-span-4">
            <button type="submit" class="btn-primary">Filter</button>
            <a href="{{ route('audit-logs.index') }}" class="btn-secondary">Clear</a>
        </div>
    </form>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="table-head px-5 py-3">Time</th>
                    <th class="table-head px-5 py-3">User</th>
                    <th class="table-head px-5 py-3">Action</th>
                    <th class="table-head px-5 py-3">Module</th>
                    <th class="table-head px-5 py-3">Record</th>
                    <th class="table-head px-5 py-3">IP Address</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($logs as $log)
                    <tr class="hover:bg-slate-50">
                        <td class="table-cell whitespace-nowrap">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                        <td class="table-cell">{{ $log->user?->name ?? 'System' }}</td>
                        <td class="table-cell">
                            <span class="badge {{ $actionStyles[$log->action] ?? 'bg-amber-50 text-amber-700 ring-amber-200' }}">
                                {{ $log->action_label }}
                            </span>
                        </td>
                        <td class="table-cell font-medium text-slate-900">{{ $log->subject_label }}</td>
                        <td class="table-cell">
                            <span class="font-mono text-xs text-slate-500">#{{ $log->subject_id }}</span>
                            @if($log->subject_name)
                                <span class="ml-1 text-slate-700">{{ $log->subject_name }}</span>
                            @endif
                        </td>
                        <td class="table-cell font-mono text-xs">{{ $log->ip_address }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-slate-500">
                            <p class="text-lg font-medium text-slate-700">No activity found</p>
                            <p class="mt-1 text-sm">
                                {{ request()->hasAny(['module', 'action'])
                                    ? 'No activity matches the selected filters. Try clearing them.'
                                    : 'System activity will appear here once records are changed.' }}
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-slate-100 px-5 py-4">
        {{ $logs->links() }}
    </div>
</div>
@endsection
