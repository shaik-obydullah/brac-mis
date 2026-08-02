@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">Audit Logs</h1>
    <p class="text-gray-600 mt-1">System activity trail</p>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-6 py-3 font-semibold text-gray-600">Time</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-600">User</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-600">Action</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-600">Subject</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-600">IP Address</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-gray-500 whitespace-nowrap">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                        <td class="px-6 py-4">{{ $log->user?->name ?? 'System' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded text-xs font-medium
                                @if($log->action === 'created') bg-green-100 text-green-700
                                @elseif($log->action === 'updated') bg-blue-100 text-blue-700
                                @elseif($log->action === 'deleted') bg-red-100 text-red-700
                                @else bg-gray-100 text-gray-700 @endif">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</td>
                        <td class="px-6 py-4 text-gray-500 font-mono text-xs">{{ $log->ip_address }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">No audit logs recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $logs->links() }}
    </div>
</div>
@endsection
