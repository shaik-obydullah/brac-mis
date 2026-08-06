@extends('layouts.app')

@section('title', 'Staff')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Staff</h1>
        <p class="text-gray-600 mt-1">Manage all staff members</p>
    </div>
    <a href="{{ route('staff.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Create New
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
    <form method="GET" action="{{ route('staff.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input type="text" name="name" value="{{ request('name') }}" placeholder="Search by name..." class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Employee ID</label>
            <input type="text" name="employee_id" value="{{ request('employee_id') }}" placeholder="Search by employee ID..." class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Designation</label>
            <input type="text" name="designation" value="{{ request('designation') }}" placeholder="Search by designation..." class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Branch</label>
            <select name="branch_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                <option value="">All Branches</option>
                @foreach($branches ?? [] as $branch)
                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-4 flex gap-2">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">Search</button>
            <a href="{{ route('staff.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">Clear</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Employee ID</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Designation</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Branch</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                    <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($staff as $staffMember)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $staffMember->id }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $staffMember->employee_id ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $staffMember->user->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $staffMember->user->email ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $staffMember->designation ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $staffMember->branch->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $staffMember->phone ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('staff.edit', $staffMember) }}" class="text-green-600 hover:text-green-800 mx-1">Edit</a>
                            <form action="{{ route('staff.destroy', $staffMember) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
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
                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <p class="text-lg font-medium">No staff found</p>
                                <p class="text-sm">Get started by creating a new staff member.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($staff, 'links'))
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $staff->links() }}
        </div>
    @endif
</div>
@endsection
