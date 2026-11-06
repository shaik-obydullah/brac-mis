@extends('layouts.app')

@section('title', 'Staff Details')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">{{ $staff->full_name ?? $staff->user?->name ?? 'Staff' }}</h1>
        <p class="text-gray-600">{{ $staff->employee_id }}</p>
    </div>
    <div class="flex space-x-3">
        <a href="{{ route('staff.edit', $staff) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Edit</a>
        <a href="{{ route('staff.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">Back to List</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold mb-4">Personal Information</h2>
        <dl class="space-y-3">
            <div class="flex justify-between py-2 border-b">
                <dt class="text-gray-500">Employee ID</dt>
                <dd class="font-medium">{{ $staff->employee_id }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b">
                <dt class="text-gray-500">Name</dt>
                <dd class="font-medium">{{ $staff->user?->name ?? 'N/A' }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b">
                <dt class="text-gray-500">Email</dt>
                <dd class="font-medium">{{ $staff->user?->email ?? 'N/A' }}</dd>
            </div>
            <div class="flex justify-between py-2">
                <dt class="text-gray-500">Phone</dt>
                <dd class="font-medium">{{ $staff->phone ?? 'N/A' }}</dd>
            </div>
        </dl>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold mb-4">Employment Details</h2>
        <dl class="space-y-3">
            <div class="flex justify-between py-2 border-b">
                <dt class="text-gray-500">Designation</dt>
                <dd class="font-medium">{{ $staff->designation ?? 'N/A' }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b">
                <dt class="text-gray-500">Branch</dt>
                <dd class="font-medium">{{ $staff->branch?->name ?? 'N/A' }}</dd>
            </div>
            <div class="flex justify-between py-2">
                <dt class="text-gray-500">User Status</dt>
                <dd><span class="px-2 py-1 rounded-full text-xs {{ ($staff->user?->status ?? 'active') === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ ucfirst($staff->user?->status ?? 'N/A') }}</span></dd>
            </div>
        </dl>
    </div>
</div>

<div class="mt-6 card overflow-hidden">
    <div class="card-header">
        <div>
            <h2 class="card-title">Incentives</h2>
            <p class="mt-0.5 text-xs text-slate-500">{{ $staff->incentives->count() }} {{ Str::plural('incentive', $staff->incentives->count()) }} recorded for this staff member</p>
        </div>
        @can('create staff incentives')
            <a href="{{ route('staff-incentives.create', ['staff_id' => $staff->id]) }}" class="btn-primary">Add incentive</a>
        @endcan
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="table-head px-5 py-3">Date</th>
                    <th class="table-head px-5 py-3">Incentive</th>
                    <th class="table-head px-5 py-3 text-right">Amount</th>
                    <th class="table-head px-5 py-3">Status</th>
                    <th class="table-head px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($staff->incentives->sortByDesc('incentive_date') as $incentive)
                    <tr class="hover:bg-slate-50">
                        <td class="table-cell">{{ $incentive->incentive_date?->format('d M Y') }}</td>
                        <td class="table-cell font-medium text-slate-900">{{ $incentive->title }}</td>
                        <td class="table-cell text-right font-semibold text-slate-900">৳ {{ number_format((float) $incentive->amount, 2) }}</td>
                        <td class="table-cell">
                            <span class="badge {{ $incentive->status === 'paid' ? 'bg-brand-50 text-brand-700 ring-brand-200' : 'bg-amber-50 text-amber-700 ring-amber-200' }}">
                                {{ ucfirst($incentive->status) }}
                            </span>
                        </td>
                        <td class="table-cell text-right">
                            <a href="{{ route('staff-incentives.show', $incentive) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-500">No incentives recorded for this staff member yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
