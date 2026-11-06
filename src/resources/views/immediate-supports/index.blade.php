@extends('layouts.app')

@section('title', $categoryLabel)

@section('content')
@php
    $typeStyles = [
        'food' => 'bg-amber-100 text-amber-700',
        'bus_ticket' => 'bg-sky-100 text-sky-700',
        'shelter' => 'bg-emerald-100 text-emerald-700',
        'mental_psychological' => 'bg-violet-100 text-violet-700',
        'physical' => 'bg-rose-100 text-rose-700',
        'financial' => 'bg-blue-100 text-blue-700',
    ];
    $singleType = count($types) === 1 ? array_key_first($types) : null;
@endphp

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">{{ $categoryLabel }}</h1>
        <p class="text-gray-600 mt-1">
            @if($category === 'immediate')
                Food, bus ticket, and shelter support records
            @else
                {{ $categoryLabel }} records
            @endif
        </p>
    </div>
    <a href="{{ route('support.create', ['category' => $category] + ($singleType ? ['type' => $singleType] : [])) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Record Support
    </a>
</div>

@if(count($types) > 1)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        @foreach($types as $value => $label)
            <a href="{{ route('support.index', ['category' => $category, 'type' => $value]) }}"
               class="rounded-xl border bg-white p-4 shadow-sm transition hover:shadow-md {{ request('type') === $value ? 'border-green-500 ring-2 ring-green-200' : 'border-gray-200' }}">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-sm font-bold {{ $typeStyles[$value] ?? 'bg-gray-100 text-gray-700' }}">
                    {{ strtoupper(substr($label, 0, 1)) }}
                </span>
                <p class="mt-3 text-sm font-medium text-gray-700">{{ $label }}</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ $typeCounts[$value] ?? 0 }}</p>
            </a>
        @endforeach
    </div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
    <form method="GET" action="{{ route('support.index', ['category' => $category]) }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Recipient, phone or BRAC ID..." class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
        </div>
        @if(count($types) > 1)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">All Types</option>
                    @foreach($types as $value => $label)
                        <option value="{{ $value }}" {{ request('type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                <option value="">All Statuses</option>
                @foreach($statuses as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">From</label>
            <input type="date" name="from" value="{{ request('from') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">To</label>
            <input type="date" name="to" value="{{ request('to') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
        </div>
        <div class="md:col-span-6 flex items-center gap-3">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg">Filter</button>
            <a href="{{ route('support.index', ['category' => $category]) }}" class="text-gray-600 hover:text-gray-800">Reset</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Recipient</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($supports as $support)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $support->support_date?->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $typeStyles[$support->support_type] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $support->type_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <a href="{{ route('support.show', ['category' => $category, 'immediateSupport' => $support]) }}" class="font-medium text-gray-900 hover:text-green-700">
                                @if($support->user)
                                    {{ $support->user->name }}
                                @else
                                    {{ $support->recipient_name ?? 'N/A' }}
                                @endif
                            </a>
                            <p class="text-gray-500">
                                {{ $support->user?->profile_type_label ?? $support->recipient_phone ?? 'N/A' }}
                            </p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $support->amount !== null ? number_format((float) $support->amount, 2) : 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($support->status === 'provided')
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Provided</span>
                            @elseif($support->status === 'cancelled')
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">Cancelled</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800">Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right text-sm whitespace-nowrap">
                            <a href="{{ route('support.show', ['category' => $category, 'immediateSupport' => $support]) }}" class="text-blue-600 hover:text-blue-800 mx-1">View</a>
                            <a href="{{ route('support.edit', ['category' => $category, 'immediateSupport' => $support]) }}" class="text-green-600 hover:text-green-800 mx-1">Edit</a>
                            <form action="{{ route('support.destroy', ['category' => $category, 'immediateSupport' => $support]) }}" method="POST" class="inline" onsubmit="return confirm('Delete this support record?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 mx-1">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <p class="text-lg font-medium">No {{ strtolower($categoryLabel) }} records found</p>
                            <p class="text-sm">Record {{ strtolower($categoryLabel) }} to get started.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($supports, 'links'))
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $supports->links() }}
        </div>
    @endif
</div>
@endsection
