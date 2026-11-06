@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">Reports Dashboard</h1>
    <p class="text-gray-600 mt-1">Profile, migration, support and organisation report exports</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-green-100 p-3 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <span class="text-2xl font-bold text-gray-800">{{ $totalProfiles ?? 0 }}</span>
        </div>
        <p class="text-sm text-gray-500 uppercase tracking-wide">Total Profiles</p>
        <div class="mt-2 flex justify-between text-xs text-gray-500">
            <span>Active: {{ $activeProfiles ?? 0 }}</span>
            <span>Skills: {{ $totalSkills ?? 0 }}</span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-emerald-100 p-3 rounded-lg">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-2xl font-bold text-gray-800">{{ $reintegratedProfiles ?? 0 }}</span>
        </div>
        <p class="text-sm text-gray-500 uppercase tracking-wide">Reintegrated Profiles</p>
        <p class="mt-2 text-xs text-gray-500">Users with a reintegrated profile type</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-pink-100 p-3 rounded-lg">
                <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <span class="text-2xl font-bold text-gray-800">{{ $potentialCustomers ?? 0 }}</span>
        </div>
        <p class="text-sm text-gray-500 uppercase tracking-wide">Potential Customers</p>
        <p class="mt-2 text-xs text-gray-500">Profiles published to the job board</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-purple-100 p-3 rounded-lg">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <span class="text-2xl font-bold text-gray-800">{{ $totalBranches ?? 0 }}</span>
        </div>
        <p class="text-sm text-gray-500 uppercase tracking-wide">Total Branches</p>
        <div class="mt-2 flex justify-between text-xs text-gray-500">
            <span>Staff: {{ $totalStaff ?? 0 }}</span>
            <span>Migrations: {{ $totalMigrationHistories ?? 0 }}</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Profile Reports</h2>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-medium text-gray-800">Profile List</p>
                    <p class="text-sm text-gray-500">Export all profiles with contact and profile type</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('reports.export', ['type' => 'profiles', 'format' => 'csv']) }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-sm">CSV</a>
                    <a href="{{ route('reports.export', ['type' => 'profiles', 'format' => 'pdf']) }}" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-sm">PDF</a>
                    <a href="{{ route('reports.export', ['type' => 'profiles', 'format' => 'excel']) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-sm">Excel</a>
                </div>
            </div>
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-medium text-gray-800">Profiles by Branch</p>
                    <p class="text-sm text-gray-500">Summary grouped by branch</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('reports.export', ['type' => 'profiles-by-branch', 'format' => 'csv']) }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-sm">CSV</a>
                    <a href="{{ route('reports.export', ['type' => 'profiles-by-branch', 'format' => 'pdf']) }}" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-sm">PDF</a>
                </div>
            </div>
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-medium text-gray-800">Profiles by Profile Type</p>
                    <p class="text-sm text-gray-500">Reintegrated versus potential customer totals</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('reports.export', ['type' => 'profiles-by-profile-type', 'format' => 'csv']) }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-sm">CSV</a>
                    <a href="{{ route('reports.export', ['type' => 'profiles-by-profile-type', 'format' => 'pdf']) }}" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-sm">PDF</a>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Migration Reports</h2>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-medium text-gray-800">Migration History</p>
                    <p class="text-sm text-gray-500">Export every migration history record</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('reports.export', ['type' => 'migration-histories', 'format' => 'csv']) }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-sm">CSV</a>
                    <a href="{{ route('reports.export', ['type' => 'migration-histories', 'format' => 'pdf']) }}" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-sm">PDF</a>
                    <a href="{{ route('reports.export', ['type' => 'migration-histories', 'format' => 'excel']) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-sm">Excel</a>
                </div>
            </div>
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-medium text-gray-800">Support Records</p>
                    <p class="text-sm text-gray-500">Export support records and linked user profiles</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('reports.export', ['type' => 'support-records', 'format' => 'csv']) }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-sm">CSV</a>
                    <a href="{{ route('reports.export', ['type' => 'support-records', 'format' => 'pdf']) }}" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-sm">PDF</a>
                    <a href="{{ route('reports.export', ['type' => 'support-records', 'format' => 'excel']) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-sm">Excel</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Staff & Branch Reports</h2>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-medium text-gray-800">Staff List</p>
                    <p class="text-sm text-gray-500">Export all staff with details</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('reports.export', ['type' => 'staff', 'format' => 'csv']) }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-sm">CSV</a>
                    <a href="{{ route('reports.export', ['type' => 'staff', 'format' => 'pdf']) }}" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-sm">PDF</a>
                    <a href="{{ route('reports.export', ['type' => 'staff', 'format' => 'excel']) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-sm">Excel</a>
                </div>
            </div>
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-medium text-gray-800">Branch List</p>
                    <p class="text-sm text-gray-500">Export all branches with details</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('reports.export', ['type' => 'branches', 'format' => 'csv']) }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-sm">CSV</a>
                    <a href="{{ route('reports.export', ['type' => 'branches', 'format' => 'pdf']) }}" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-sm">PDF</a>
                    <a href="{{ route('reports.export', ['type' => 'branches', 'format' => 'excel']) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-sm">Excel</a>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Summary Reports</h2>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-medium text-gray-800">Monthly Summary</p>
                    <p class="text-sm text-gray-500">Monthly profile registration summary</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('reports.export', ['type' => 'monthly-summary', 'format' => 'pdf']) }}" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-sm">PDF</a>
                    <a href="{{ route('reports.export', ['type' => 'monthly-summary', 'format' => 'excel']) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-sm">Excel</a>
                </div>
            </div>
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-medium text-gray-800">Yearly Overview</p>
                    <p class="text-sm text-gray-500">Comprehensive yearly statistics</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('reports.export', ['type' => 'yearly-overview', 'format' => 'pdf']) }}" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-sm">PDF</a>
                    <a href="{{ route('reports.export', ['type' => 'yearly-overview', 'format' => 'excel']) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-sm">Excel</a>
                </div>
            </div>
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-medium text-gray-800">Full System Export</p>
                    <p class="text-sm text-gray-500">Complete profile data export</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('reports.export', ['type' => 'full-system', 'format' => 'excel']) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-sm">Excel</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
