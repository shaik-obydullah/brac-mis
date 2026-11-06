@extends('layouts.app')

@section('title', 'Branch Details')

@section('content')
@php
    $typeLabels = ['head_office' => 'Head Office', 'district' => 'District', 'upazila' => 'Upazila'];
    $typeClasses = [
        'head_office' => 'bg-purple-100 text-purple-800',
        'district' => 'bg-blue-100 text-blue-800',
        'upazila' => 'bg-amber-100 text-amber-800',
    ];
@endphp
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
                <dt class="text-gray-500">Type</dt>
                <dd>
                    <span class="px-2 py-1 rounded-full text-xs {{ $typeClasses[$branch->type] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ $typeLabels[$branch->type] ?? ucfirst($branch->type) }}
                    </span>
                </dd>
            </div>
            <div class="flex justify-between py-2 border-b">
                <dt class="text-gray-500">Parent</dt>
                <dd class="font-medium">
                    @if($branch->parent)
                        <a href="{{ route('branches.show', $branch->parent) }}" class="text-blue-600 hover:underline">{{ $branch->parent->name }}</a>
                    @else
                        —
                    @endif
                </dd>
            </div>
            <div class="flex justify-between py-2 border-b">
                <dt class="text-gray-500">Division</dt>
                <dd class="font-medium">{{ $branch->division }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b">
                <dt class="text-gray-500">District</dt>
                <dd class="font-medium">{{ $branch->district }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b">
                <dt class="text-gray-500">Upazila</dt>
                <dd class="font-medium">{{ $branch->upazila }}</dd>
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

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold mb-4">Child Branches ({{ $branch->children->count() }})</h2>
        @if($branch->children->count())
            <div class="space-y-2">
                @foreach($branch->children as $child)
                    <div class="flex items-center justify-between gap-3 py-2 border-b last:border-0">
                        <a href="{{ route('branches.show', $child) }}" class="font-medium text-blue-600 hover:underline">{{ $child->name }}</a>
                        <span class="px-2 py-0.5 rounded-full text-xs {{ $typeClasses[$child->type] ?? 'bg-gray-100 text-gray-700' }}">{{ $typeLabels[$child->type] ?? ucfirst($child->type) }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500">No child branches.</p>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 lg:col-span-2">
        <h2 class="text-lg font-semibold mb-4">User Profiles ({{ $branch->users->count() }})</h2>
        @if($branch->users->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left">
                            <th class="py-2 pr-4">Profile Type</th>
                            <th class="py-2 pr-4">Name</th>
                            <th class="py-2 pr-4">Phone</th>
                            <th class="py-2 pr-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($branch->users as $profile)
                            <tr class="border-b">
                                <td class="py-2 pr-4">{{ $profile->profile_type_label }}</td>
                                <td class="py-2 pr-4"><a href="{{ route('users.show', $profile) }}" class="text-blue-600 hover:underline">{{ $profile->name }}</a></td>
                                <td class="py-2 pr-4">{{ $profile->phone }}</td>
                                <td class="py-2 pr-4"><span class="px-2 py-0.5 rounded-full text-xs {{ $profile->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ ucfirst($profile->status) }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500">No user profiles registered.</p>
        @endif
    </div>
</div>
@endsection
