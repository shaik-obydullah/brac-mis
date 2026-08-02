@extends('layouts.app')

@section('title', 'Beneficiary Details')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Beneficiary Details</h1>
        <p class="text-gray-600 mt-1">View complete beneficiary information</p>
    </div>
    <a href="{{ route('beneficiaries.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg flex items-center gap-2">
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
                    <dd class="text-sm font-medium text-gray-900 mt-1">{{ $beneficiary->brac_id ?? $beneficiary->id }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Name</dt>
                    <dd class="text-sm font-medium text-gray-900 mt-1">{{ $beneficiary->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Father's Name</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $beneficiary->father_name ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Mother's Name</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $beneficiary->mother_name ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Gender</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $beneficiary->gender ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Date of Birth</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ optional($beneficiary->date_of_birth)->format('d M Y') ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">NID Number</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $beneficiary->nid ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Phone</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $beneficiary->phone ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Occupation</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $beneficiary->occupation ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Monthly Income</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $beneficiary->monthly_income ? '৳ ' . number_format($beneficiary->monthly_income, 2) : 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Family Members</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $beneficiary->family_members ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Education Level</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $beneficiary->education_level ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Marital Status</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $beneficiary->marital_status ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Spouse Name</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $beneficiary->spouse_name ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Address Information</h2>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Present Address</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $beneficiary->present_address ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Permanent Address</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $beneficiary->permanent_address ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">District</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $beneficiary->district ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Upazila</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $beneficiary->upazila ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Status & Branch</h2>
            <dl class="space-y-4">
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Status</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $beneficiary->status === 'Active' ? 'bg-green-100 text-green-800' : ($beneficiary->status === 'Inactive' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ $beneficiary->status ?? 'N/A' }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Branch</dt>
                    <dd class="text-sm font-medium text-gray-900 mt-1">{{ $beneficiary->branch->name ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Enrollment Date</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ optional($beneficiary->enrollment_date)->format('d M Y') ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Created By</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $beneficiary->createdBy->name ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Created At</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $beneficiary->created_at->format('d M Y, h:i A') }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Last Updated</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $beneficiary->updated_at->diffForHumans() }}</dd>
                </div>
            </dl>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Remarks</h2>
            <p class="text-sm text-gray-700">{{ $beneficiary->remarks ?? 'No remarks recorded.' }}</p>
        </div>
    </div>
</div>

@if($beneficiary->households->count() > 0)
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Household Members</h2>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Relationship</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Age</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Occupation</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Monthly Income</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($beneficiary->households as $member)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $member->member_name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $member->relationship }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $member->age }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $member->occupation ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $member->monthly_income ? '৳ ' . number_format($member->monthly_income, 2) : 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@if($beneficiary->interventions->count() > 0)
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Interventions</h2>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Start Date</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">End Date</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Notes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($beneficiary->interventions as $intervention)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $intervention->type }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ optional($intervention->start_date)->format('d M Y') ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ optional($intervention->end_date)->format('d M Y') ?? 'N/A' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $intervention->status === 'Completed' ? 'bg-green-100 text-green-800' : ($intervention->status === 'Ongoing' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ $intervention->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $intervention->notes ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@if($beneficiary->followUps->count() > 0)
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Follow-ups</h2>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Staff</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Notes</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Next Date</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($beneficiary->followUps as $followUp)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">{{ optional($followUp->date)->format('d M Y') ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $followUp->type }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $followUp->staff->user->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $followUp->notes ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ optional($followUp->next_date)->format('d M Y') ?? 'N/A' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $followUp->status === 'Completed' ? 'bg-green-100 text-green-800' : ($followUp->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ $followUp->status }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="flex items-center gap-4">
    <a href="{{ route('beneficiaries.edit', $beneficiary) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">Edit Beneficiary</a>
    <a href="{{ route('beneficiaries.index') }}" class="text-gray-600 hover:text-gray-800 px-4 py-2">Back to List</a>
</div>
@endsection
