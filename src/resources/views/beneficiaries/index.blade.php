@extends('layouts.app')

@section('title', 'Beneficiaries')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Beneficiaries</h1>
        <p class="text-gray-600 mt-1">Manage all registered beneficiaries</p>
    </div>
    <a href="{{ route('beneficiaries.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Create New
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
    <form method="GET" action="{{ route('beneficiaries.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input type="text" name="name" value="{{ request('name') }}" placeholder="Search by name..." class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">BRAC ID</label>
            <input type="text" name="brac_id" value="{{ request('brac_id') }}" placeholder="Search by BRAC ID..." class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
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
            <a href="{{ route('beneficiaries.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">Clear</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">BRAC ID</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Branch</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($beneficiaries as $beneficiary)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $beneficiary->brac_id ?? $beneficiary->id }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $beneficiary->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $beneficiary->gender ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $beneficiary->phone ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $beneficiary->branch->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $beneficiary->status === 'Active' ? 'bg-green-100 text-green-800' : ($beneficiary->status === 'Inactive' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ $beneficiary->status ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $beneficiary->created_at->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('beneficiaries.show', $beneficiary) }}" class="text-blue-600 hover:text-blue-800 mx-1">View</a>
                            <a href="{{ route('beneficiaries.edit', $beneficiary) }}" class="text-green-600 hover:text-green-800 mx-1">Edit</a>
                            <form action="{{ route('beneficiaries.destroy', $beneficiary) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
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
                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <p class="text-lg font-medium">No beneficiaries found</p>
                                <p class="text-sm">Get started by creating a new beneficiary.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($beneficiaries, 'links'))
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $beneficiaries->links() }}
        </div>
    @endif
</div>
@endsection
