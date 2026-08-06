@extends('layouts.app')

@section('title', 'Returnees')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Returnees</h1>
        <p class="text-gray-600 mt-1">Manage all registered returnees</p>
    </div>
    <a href="{{ route('returnees.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Create New
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
    <form method="GET" action="{{ route('returnees.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input type="text" name="name" value="{{ request('name') }}" placeholder="Search by name..." class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Current Status</label>
            <select name="current_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500">
                <option value="">All Statuses</option>
                <option value="assessed" {{ request('current_status') == 'assessed' ? 'selected' : '' }}>Assessed</option>
                <option value="planning" {{ request('current_status') == 'planning' ? 'selected' : '' }}>Planning</option>
                <option value="in_progress" {{ request('current_status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ request('current_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="dropped" {{ request('current_status') == 'dropped' ? 'selected' : '' }}>Dropped</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Return Date From</label>
            <input type="date" name="return_date_from" value="{{ request('return_date_from') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Return Date To</label>
            <input type="date" name="return_date_to" value="{{ request('return_date_to') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500">
        </div>
        <div class="md:col-span-4 flex gap-2">
            <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg">Search</button>
            <a href="{{ route('returnees.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">Clear</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Migrant</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Return Date</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Return Reason</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Origin Country</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Current Status</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($returnees as $returnee)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $returnee->id }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $returnee->migrant->name ?? $returnee->beneficiary->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ optional($returnee->return_date)->format('Y-m-d') ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ Str::limit($returnee->return_reason, 30) ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $returnee->originCountry->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $returnee->current_status === 'completed' ? 'bg-green-100 text-green-800' : ($returnee->current_status === 'dropped' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ $returnee->current_status ? str_replace('_', ' ', ucfirst($returnee->current_status)) : 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $returnee->created_at->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('returnees.show', $returnee) }}" class="text-blue-600 hover:text-blue-800 mx-1">View</a>
                            <a href="{{ route('returnees.edit', $returnee) }}" class="text-green-600 hover:text-green-800 mx-1">Edit</a>
                            <form action="{{ route('returnees.destroy', $returnee) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 mx-1">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <p class="text-lg font-medium">No returnees found</p>
                                <p class="text-sm">Get started by creating a new returnee record.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($returnees, 'links'))
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $returnees->links() }}
        </div>
    @endif
</div>
@endsection
