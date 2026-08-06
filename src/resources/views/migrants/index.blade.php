@extends('layouts.app')

@section('title', 'Migrants')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Migrants</h1>
        <p class="text-gray-600 mt-1">Manage all registered migrants</p>
    </div>
    <a href="{{ route('migrants.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Create New
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
    <form method="GET" action="{{ route('migrants.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input type="text" name="name" value="{{ request('name') }}" placeholder="Search by name..." class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">BRAC ID</label>
            <input type="text" name="brac_id" value="{{ request('brac_id') }}" placeholder="Search by BRAC ID..." class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Statuses</option>
                <option value="deployed" {{ request('status') == 'deployed' ? 'selected' : '' }}>Deployed</option>
                <option value="pre_departure" {{ request('status') == 'pre_departure' ? 'selected' : '' }}>Pre Departure</option>
                <option value="registered" {{ request('status') == 'registered' ? 'selected' : '' }}>Registered</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Destination Country</label>
            <select name="destination_country_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Countries</option>
                @foreach($countries ?? [] as $country)
                    <option value="{{ $country->id }}" {{ request('destination_country_id') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-4 flex gap-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Search</button>
            <a href="{{ route('migrants.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">Clear</a>
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
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Destination Country</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($migrants as $migrant)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $migrant->brac_id ?? $migrant->id }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $migrant->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $migrant->gender ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $migrant->destinationCountry->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $migrant->status === 'deployed' ? 'bg-blue-100 text-blue-800' : ($migrant->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ $migrant->status ? str_replace('_', ' ', ucfirst($migrant->status)) : 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $migrant->created_at->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('migrants.show', $migrant) }}" class="text-blue-600 hover:text-blue-800 mx-1">View</a>
                            <a href="{{ route('migrants.edit', $migrant) }}" class="text-green-600 hover:text-green-800 mx-1">Edit</a>
                            <form action="{{ route('migrants.destroy', $migrant) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
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
                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                <p class="text-lg font-medium">No migrants found</p>
                                <p class="text-sm">Get started by creating a new migrant record.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($migrants, 'links'))
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $migrants->links() }}
        </div>
    @endif
</div>
@endsection
