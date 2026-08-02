@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">Reports Dashboard</h1>
    <p class="text-gray-600 mt-1">View summary statistics and export reports</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-green-100 p-3 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <span class="text-2xl font-bold text-gray-800">{{ $totalBeneficiaries ?? 0 }}</span>
        </div>
        <p class="text-sm text-gray-500 uppercase tracking-wide">Total Beneficiaries</p>
        <div class="mt-2 flex justify-between text-xs text-gray-500">
            <span>Active: {{ $activeBeneficiaries ?? 0 }}</span>
            <span>Migrated: {{ $migratedBeneficiaries ?? 0 }}</span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-blue-100 p-3 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            </div>
            <span class="text-2xl font-bold text-gray-800">{{ $totalMigrants ?? 0 }}</span>
        </div>
        <p class="text-sm text-gray-500 uppercase tracking-wide">Total Migrants</p>
        <div class="mt-2 flex justify-between text-xs text-gray-500">
            <span>Active: {{ $activeMigrants ?? 0 }}</span>
            <span>Returned: {{ $returnedMigrants ?? 0 }}</span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-orange-100 p-3 rounded-lg">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <span class="text-2xl font-bold text-gray-800">{{ $totalReturnees ?? 0 }}</span>
        </div>
        <p class="text-sm text-gray-500 uppercase tracking-wide">Total Returnees</p>
        <div class="mt-2 flex justify-between text-xs text-gray-500">
            <span>Reintegrated: {{ $reintegratedReturnees ?? 0 }}</span>
            <span>In Progress: {{ $inProgressReturnees ?? 0 }}</span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-purple-100 p-3 rounded-lg">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <span class="text-2xl font-bold text-gray-800">{{ $totalBranches ?? 0 }}</span>
        </div>
        <p class="text-sm text-gray-500 uppercase tracking-wide">Total Branches</p>
        <p class="mt-2 text-xs text-gray-500">Staff: {{ $totalStaff ?? 0 }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Beneficiary Reports</h2>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-medium text-gray-800">Beneficiary List</p>
                    <p class="text-sm text-gray-500">Export all beneficiaries with complete details</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('reports.export', ['type' => 'beneficiaries', 'format' => 'csv']) }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-sm">CSV</a>
                    <a href="{{ route('reports.export', ['type' => 'beneficiaries', 'format' => 'pdf']) }}" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-sm">PDF</a>
                    <a href="{{ route('reports.export', ['type' => 'beneficiaries', 'format' => 'excel']) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-sm">Excel</a>
                </div>
            </div>
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-medium text-gray-800">Beneficiaries by Branch</p>
                    <p class="text-sm text-gray-500">Summary grouped by branch</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('reports.export', ['type' => 'beneficiaries-by-branch', 'format' => 'csv']) }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-sm">CSV</a>
                    <a href="{{ route('reports.export', ['type' => 'beneficiaries-by-branch', 'format' => 'pdf']) }}" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-sm">PDF</a>
                </div>
            </div>
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-medium text-gray-800">Beneficiaries by Gender</p>
                    <p class="text-sm text-gray-500">Demographic breakdown</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('reports.export', ['type' => 'beneficiaries-by-gender', 'format' => 'csv']) }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-sm">CSV</a>
                    <a href="{{ route('reports.export', ['type' => 'beneficiaries-by-gender', 'format' => 'pdf']) }}" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-sm">PDF</a>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Migration Reports</h2>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-medium text-gray-800">Migrant List</p>
                    <p class="text-sm text-gray-500">Export all migrants with details</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('reports.export', ['type' => 'migrants', 'format' => 'csv']) }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-sm">CSV</a>
                    <a href="{{ route('reports.export', ['type' => 'migrants', 'format' => 'pdf']) }}" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-sm">PDF</a>
                    <a href="{{ route('reports.export', ['type' => 'migrants', 'format' => 'excel']) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-sm">Excel</a>
                </div>
            </div>
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-medium text-gray-800">Migrants by Destination</p>
                    <p class="text-sm text-gray-500">Grouped by destination country</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('reports.export', ['type' => 'migrants-by-destination', 'format' => 'csv']) }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-sm">CSV</a>
                    <a href="{{ route('reports.export', ['type' => 'migrants-by-destination', 'format' => 'pdf']) }}" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-sm">PDF</a>
                </div>
            </div>
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-medium text-gray-800">Returnee List</p>
                    <p class="text-sm text-gray-500">Export all returnees with details</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('reports.export', ['type' => 'returnees', 'format' => 'csv']) }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-sm">CSV</a>
                    <a href="{{ route('reports.export', ['type' => 'returnees', 'format' => 'pdf']) }}" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-sm">PDF</a>
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
                    <p class="text-sm text-gray-500">Monthly registration and activity summary</p>
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
                    <p class="text-sm text-gray-500">Complete system data export</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('reports.export', ['type' => 'full-system', 'format' => 'excel']) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-sm">Excel</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
