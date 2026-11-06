@extends('layouts.app')

@section('title', 'System Activity Logger')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">System Activity Logger</h1>
    <p class="mt-1 text-sm text-slate-500">Audit trail of every create, update, and delete performed in the system.</p>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="table-head px-5 py-3">Time</th>
                    <th class="table-head px-5 py-3">User</th>
                    <th class="table-head px-5 py-3">Action</th>
                    <th class="table-head px-5 py-3">Subject</th>
                    <th class="table-head px-5 py-3">IP Address</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($logs as $log)
                    <tr class="hover:bg-slate-50">
                        <td class="table-cell whitespace-nowrap">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                        <td class="table-cell">{{ $log->user?->name ?? 'System' }}</td>
                        <td class="table-cell">
                            <span class="badge
                                @if($log->action === 'created') bg-brand-50 text-brand-700 ring-brand-200
                                @elseif($log->action === 'updated') bg-sky-50 text-sky-700 ring-sky-200
                                @elseif($log->action === 'deleted') bg-rose-50 text-rose-700 ring-rose-200
                                @else bg-slate-100 text-slate-700 ring-slate-200 @endif">
                                {{ ucfirst($log->action) }}
                            </span>
                        </td>
                        <td class="table-cell">{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</td>
                        <td class="table-cell font-mono text-xs">{{ $log->ip_address }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-slate-500">
                            <p class="text-lg font-medium text-slate-700">No activity recorded yet</p>
                            <p class="mt-1 text-sm">System activity will appear here once records are changed.</p>
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
