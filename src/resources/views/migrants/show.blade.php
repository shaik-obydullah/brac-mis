@extends('layouts.app')

@section('title', 'Migrant Details')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Migrant Details</h1>
        <p class="text-gray-600 mt-1">View complete migrant information</p>
    </div>
    <a href="{{ route('migrants.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to List
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Personal Information</h2>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">BRAC ID</dt>
                    <dd class="text-sm font-medium text-gray-900 mt-1">{{ $migrant->brac_id ?? $migrant->id }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Name</dt>
                    <dd class="text-sm font-medium text-gray-900 mt-1">{{ $migrant->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Gender</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $migrant->gender ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Date of Birth</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ optional($migrant->date_of_birth)->format('d M Y') ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">NID Number</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $migrant->nid_number ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Phone</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $migrant->phone ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Passport Number</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $migrant->passport_number ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Education Level</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $migrant->education_level ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Skill Level</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $migrant->skill_level ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Occupation</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $migrant->occupation ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Migration Details</h2>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Destination Country</dt>
                    <dd class="text-sm font-medium text-gray-900 mt-1">{{ $migrant->destinationCountry->name ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Destination City</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $migrant->destination_city ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Status</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $migrant->status === 'deployed' ? 'bg-blue-100 text-blue-800' : ($migrant->status === 'cancelled' ? 'bg-red-100 text-red-800' : ($migrant->status === 'returned' ? 'bg-orange-100 text-orange-800' : 'bg-yellow-100 text-yellow-800')) }}">
                            {{ $migrant->status ? str_replace('_', ' ', ucfirst($migrant->status)) : 'N/A' }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Beneficiary</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $migrant->beneficiary->name ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Record Info</h2>
            <dl class="space-y-4">
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Created At</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $migrant->created_at->format('d M Y, h:i A') }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Last Updated</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $migrant->updated_at->diffForHumans() }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>

@if($migrant->destinations->count() > 0)
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Destinations</h2>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Country</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">City</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Employer</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Contract</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Salary</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($migrant->destinations as $destination)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $destination->country->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $destination->city ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $destination->employer_name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ optional($destination->contract_start)->format('d M Y') }} - {{ optional($destination->contract_end)->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $destination->salary_amount ? number_format($destination->salary_amount, 2) . ' ' . $destination->salary_currency : 'N/A' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $destination->status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $destination->status }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@if($migrant->documents->count() > 0)
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Documents</h2>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">File</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Expiry Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($migrant->documents as $document)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $document->type }}</td>
                        <td class="px-4 py-3 text-sm text-blue-600 hover:text-blue-800">
                            <a href="{{ asset($document->file_path) }}" target="_blank">View Document</a>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ optional($document->expiry_date)->format('d M Y') ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@if($migrant->financialRecords->count() > 0)
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Financial Records</h2>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Currency</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Description</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($migrant->financialRecords as $record)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $record->type }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ number_format($record->amount, 2) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $record->currency }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ optional($record->date)->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $record->description ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="flex items-center gap-4">
    <a href="{{ route('migrants.edit', $migrant) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Edit Migrant</a>
    <a href="{{ route('migrants.index') }}" class="text-gray-600 hover:text-gray-800 px-4 py-2">Back to List</a>
</div>
@endsection
