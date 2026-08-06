@extends('layouts.app')

@section('title', 'Branches')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Branches</h1>
        <p class="text-gray-600 mt-1">Manage all branches</p>
    </div>
    <a href="{{ route('branches.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Create New
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
    <form method="GET" action="{{ route('branches.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input type="text" name="name" value="{{ request('name') }}" placeholder="Search by name..." class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-purple-500 focus:border-purple-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
            <input type="text" name="code" value="{{ request('code') }}" placeholder="Search by code..." class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-purple-500 focus:border-purple-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">District</label>
            <input type="text" name="district" value="{{ request('district') }}" placeholder="Search by district..." class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-purple-500 focus:border-purple-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-purple-500 focus:border-purple-500">
                <option value="">All Statuses</option>
                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="md:col-span-4 flex gap-2">
            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">Search</button>
            <a href="{{ route('branches.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">Clear</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">District</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Division</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($branches as $branch)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $branch->id }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $branch->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $branch->code ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $branch->district ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $branch->division ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $branch->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $branch->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('branches.edit', $branch) }}" class="text-green-600 hover:text-green-800 mx-1">Edit</a>
                            <form action="{{ route('branches.destroy', $branch) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 mx-1">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <p class="text-lg font-medium">No branches found</p>
                                <p class="text-sm">Get started by creating a new branch.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($branches, 'links'))
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $branches->links() }}
        </div>
    @endif
</div>
@endsection
