@extends('layouts.app')

@section('title', 'Branch Details')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">{{ $branch->name }}</h1>
        <p class="text-gray-600">{{ $branch->code }}</p>
    </div>
    <div class="flex space-x-3">
        <a href="{{ route('branches.edit', $branch) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Edit</a>
        <a href="{{ route('branches.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">Back to List</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold mb-4">Branch Information</h2>
        <dl class="space-y-3">
            <div class="flex justify-between py-2 border-b">
                <dt class="text-gray-500">Name</dt>
                <dd class="font-medium">{{ $branch->name }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b">
                <dt class="text-gray-500">Code</dt>
                <dd class="font-medium">{{ $branch->code }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b">
                <dt class="text-gray-500">District</dt>
                <dd class="font-medium">{{ $branch->district }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b">
                <dt class="text-gray-500">Division</dt>
                <dd class="font-medium">{{ $branch->division }}</dd>
            </div>
            <div class="flex justify-between py-2">
                <dt class="text-gray-500">Status</dt>
                <dd><span class="px-2 py-1 rounded-full text-xs {{ $branch->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $branch->status ? 'Active' : 'Inactive' }}</span></dd>
            </div>
        </dl>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold mb-4">Staff ({{ $branch->staff->count() }})</h2>
        @if($branch->staff->count())
            <div class="space-y-2">
                @foreach($branch->staff as $staff)
                    <div class="flex justify-between py-2 border-b last:border-0">
                        <span class="font-medium">{{ $staff->user?->name ?? 'N/A' }}</span>
                        <span class="text-gray-500">{{ $staff->designation }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500">No staff assigned.</p>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 lg:col-span-2">
        <h2 class="text-lg font-semibold mb-4">Beneficiaries ({{ $branch->beneficiaries->count() }})</h2>
        @if($branch->beneficiaries->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left">
                            <th class="py-2 pr-4">BRAC ID</th>
                            <th class="py-2 pr-4">Name</th>
                            <th class="py-2 pr-4">Gender</th>
                            <th class="py-2 pr-4">Phone</th>
                            <th class="py-2 pr-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($branch->beneficiaries as $b)
                            <tr class="border-b">
                                <td class="py-2 pr-4">{{ $b->brac_id }}</td>
                                <td class="py-2 pr-4"><a href="{{ route('beneficiaries.show', $b) }}" class="text-blue-600 hover:underline">{{ $b->name }}</a></td>
                                <td class="py-2 pr-4">{{ ucfirst($b->gender) }}</td>
                                <td class="py-2 pr-4">{{ $b->phone }}</td>
                                <td class="py-2 pr-4"><span class="px-2 py-0.5 rounded-full text-xs {{ $b->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ ucfirst($b->status) }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500">No beneficiaries registered.</p>
        @endif
    </div>
</div>
@endsection
