@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
    <p class="text-gray-600 mt-1">Welcome to BRAC Migration Information System</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-wide">Beneficiaries</p>
                <p class="text-3xl font-bold text-green-600">{{ $beneficiaries ?? 0 }}</p>
            </div>
            <div class="bg-green-100 p-3 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
        </div>
        <a href="{{ url('/beneficiaries') }}" class="mt-4 block text-sm text-green-600 hover:text-green-800">View all &rarr;</a>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-wide">Active Migrants</p>
                <p class="text-3xl font-bold text-blue-600">{{ $activeMigrants ?? 0 }}</p>
            </div>
            <div class="bg-blue-100 p-3 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            </div>
        </div>
        <a href="{{ url('/migrants') }}" class="mt-4 block text-sm text-blue-600 hover:text-blue-800">View all &rarr;</a>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-wide">Returnees</p>
                <p class="text-3xl font-bold text-orange-600">{{ $returnees ?? 0 }}</p>
            </div>
            <div class="bg-orange-100 p-3 rounded-lg">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
        </div>
        <a href="{{ url('/returnees') }}" class="mt-4 block text-sm text-orange-600 hover:text-orange-800">View all &rarr;</a>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-wide">Branches</p>
                <p class="text-3xl font-bold text-purple-600">{{ $branches ?? 0 }}</p>
            </div>
            <div class="bg-purple-100 p-3 rounded-lg">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
        </div>
        <a href="{{ url('/branches') }}" class="mt-4 block text-sm text-purple-600 hover:text-purple-800">View all &rarr;</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold mb-4">Quick Actions</h2>
        <div class="grid grid-cols-2 gap-4">
            <a href="{{ url('/beneficiaries/create') }}" class="p-4 bg-green-50 rounded-lg border border-green-200 hover:bg-green-100">
                <h3 class="font-semibold text-green-800">Register Beneficiary</h3>
                <p class="text-xs text-green-600 mt-1">Add a new beneficiary</p>
            </a>
            <a href="{{ url('/migrants/create') }}" class="p-4 bg-blue-50 rounded-lg border border-blue-200 hover:bg-blue-100">
                <h3 class="font-semibold text-blue-800">New Migration</h3>
                <p class="text-xs text-blue-600 mt-1">Register a migrant</p>
            </a>
            <a href="{{ url('/returnees/create') }}" class="p-4 bg-orange-50 rounded-lg border border-orange-200 hover:bg-orange-100">
                <h3 class="font-semibold text-orange-800">Record Returnee</h3>
                <p class="text-xs text-orange-600 mt-1">Register a returnee</p>
            </a>
            <a href="{{ url('/reports') }}" class="p-4 bg-purple-50 rounded-lg border border-purple-200 hover:bg-purple-100">
                <h3 class="font-semibold text-purple-800">View Reports</h3>
                <p class="text-xs text-purple-600 mt-1">Generate reports</p>
            </a>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold mb-4">Recent Activity</h2>
        <div class="space-y-3">
            @forelse(($recentBeneficiaries ?? []) as $b)
                <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $b->name }}</p>
                        <p class="text-xs text-gray-500">{{ $b->brac_id }} &middot; {{ $b->branch?->name ?? 'N/A' }}</p>
                    </div>
                    <span class="text-xs text-gray-400">{{ $b->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <p class="text-sm text-gray-500">No recent activity</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
