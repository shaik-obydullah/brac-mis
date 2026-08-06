@extends('layouts.app')

@section('title', 'Returnee Details')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Returnee Details</h1>
        <p class="text-gray-600 mt-1">View complete returnee information</p>
    </div>
    <a href="{{ route('returnees.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to List
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Return Information</h2>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Migrant</dt>
                    <dd class="text-sm font-medium text-gray-900 mt-1">{{ $returnee->migrant->name ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Beneficiary</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $returnee->beneficiary->name ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Return Date</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ optional($returnee->return_date)->format('d M Y') ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Origin Country</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $returnee->originCountry->name ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Current Status</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $returnee->current_status === 'completed' ? 'bg-green-100 text-green-800' : ($returnee->current_status === 'dropped' ? 'bg-red-100 text-red-800' : ($returnee->current_status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800')) }}">
                            {{ $returnee->current_status ? str_replace('_', ' ', ucfirst($returnee->current_status)) : 'N/A' }}
                        </span>
                    </dd>
                </div>
            </dl>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Return Reason</h2>
            <p class="text-sm text-gray-700">{{ $returnee->return_reason ?? 'No reason recorded.' }}</p>
        </div>
    </div>

    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Record Info</h2>
            <dl class="space-y-4">
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Created At</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $returnee->created_at->format('d M Y, h:i A') }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wide">Last Updated</dt>
                    <dd class="text-sm text-gray-700 mt-1">{{ $returnee->updated_at->diffForHumans() }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>

@if($returnee->reintegrationPlans->count() > 0)
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Reintegration Plans</h2>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Goal</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Timeline</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Staff</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($returnee->reintegrationPlans as $plan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $plan->goal }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $plan->timeline ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $plan->staff->user->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $plan->status === 'Completed' ? 'bg-green-100 text-green-800' : ($plan->status === 'In Progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ $plan->status }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@if($returnee->skillAssessments->count() > 0)
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Skill Assessments</h2>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Skill Name</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Proficiency</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Certification</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Assessed By</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Assessed Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($returnee->skillAssessments as $skill)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $skill->skill_name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $skill->proficiency_level }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $skill->certification ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $skill->assessed_by ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ optional($skill->assessed_date)->format('d M Y') ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@if($returnee->livelihoodSupport->count() > 0)
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Livelihood Support</h2>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Provider</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Start Date</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">End Date</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($returnee->livelihoodSupport as $support)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $support->type }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $support->amount ? '৳ ' . number_format($support->amount, 2) : 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $support->provider ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ optional($support->start_date)->format('d M Y') ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ optional($support->end_date)->format('d M Y') ?? 'N/A' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $support->status === 'Active' ? 'bg-green-100 text-green-800' : ($support->status === 'Completed' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ $support->status }}
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
    <a href="{{ route('returnees.edit', $returnee) }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg">Edit Returnee</a>
    <a href="{{ route('returnees.index') }}" class="text-gray-600 hover:text-gray-800 px-4 py-2">Back to List</a>
</div>
@endsection
