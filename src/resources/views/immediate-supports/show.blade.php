@extends('layouts.app')

@section('title', $categoryLabel . ' Details')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">{{ $support->type_label }}</h1>
        <p class="text-gray-600">{{ $categoryLabel }} &middot; {{ $support->support_date?->format('Y-m-d') }}</p>
    </div>
    <div class="flex space-x-3">
        <a href="{{ route('support.edit', ['category' => $category, 'immediateSupport' => $support]) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Edit</a>
        <a href="{{ route('support.index', ['category' => $category]) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">Back to List</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold mb-4">Support Information</h2>
        <dl class="space-y-3">
            <div class="flex justify-between py-2 border-b">
                <dt class="text-gray-500">Support Type</dt>
                <dd class="font-medium text-gray-800">{{ $support->type_label }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b">
                <dt class="text-gray-500">Support Date</dt>
                <dd class="font-medium text-gray-800">{{ $support->support_date?->format('Y-m-d') ?? 'N/A' }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b">
                <dt class="text-gray-500">Status</dt>
                <dd class="font-medium text-gray-800">{{ ucfirst($support->status) }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b">
                <dt class="text-gray-500">User Profile</dt>
                <dd class="font-medium text-gray-800">
                    @if($support->user)
                        <a href="{{ route('users.show', $support->user) }}" class="text-blue-600 hover:underline">
                            {{ $support->user->name }} &middot; {{ $support->user->profile_type_label }}
                        </a>
                    @else
                        N/A
                    @endif
                </dd>
            </div>
            <div class="flex justify-between py-2 border-b">
                <dt class="text-gray-500">Recipient Name</dt>
                <dd class="font-medium text-gray-800">{{ $support->recipient_name ?? 'N/A' }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b">
                <dt class="text-gray-500">Recipient Phone</dt>
                <dd class="font-medium text-gray-800">{{ $support->recipient_phone ?? $support->user?->phone ?? 'N/A' }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b">
                <dt class="text-gray-500">Amount</dt>
                <dd class="font-medium text-gray-800">{{ $support->amount !== null ? number_format((float) $support->amount, 2) : 'N/A' }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b">
                <dt class="text-gray-500">Paid From</dt>
                <dd class="font-medium text-gray-800">{{ $support->paid_from_label }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b">
                <dt class="text-gray-500">Accounting Entry</dt>
                <dd class="font-medium text-gray-800">
                    @if($support->journalEntry)
                        @can('view finances')
                            <a href="{{ route('finances.show', $support->journalEntry) }}" class="text-blue-600 hover:underline">
                                {{ $support->journalEntry->entry_no }}
                            </a>
                        @else
                            {{ $support->journalEntry->entry_no }}
                        @endcan
                        <span class="ml-2 inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $support->journalEntry->status === 'posted' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $support->journalEntry->status_label }}
                        </span>
                    @else
                        <span class="text-gray-400">Not posted</span>
                    @endif
                </dd>
            </div>
            <div class="flex justify-between py-2 border-b">
                <dt class="text-gray-500">Provided By</dt>
                <dd class="font-medium text-gray-800">{{ $support->providedBy->name ?? 'N/A' }}</dd>
            </div>
            <div class="py-2 border-b">
                <dt class="text-gray-500 mb-1">Description</dt>
                <dd class="text-gray-800">{{ $support->description ?? 'N/A' }}</dd>
            </div>
            <div class="py-2">
                <dt class="text-gray-500 mb-1">Notes</dt>
                <dd class="text-gray-800">{{ $support->notes ?? 'N/A' }}</dd>
            </div>
        </dl>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold mb-4">Actions</h2>
        <div class="space-y-3">
            <a href="{{ route('support.edit', ['category' => $category, 'immediateSupport' => $support]) }}" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Edit Support</a>
            <form action="{{ route('support.destroy', ['category' => $category, 'immediateSupport' => $support]) }}" method="POST" onsubmit="return confirm('Delete this support record?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">Delete Support</button>
            </form>
            <a href="{{ route('support.index', ['category' => $category]) }}" class="block w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg">Back to List</a>
        </div>
    </div>
</div>

<div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Follow-ups</h2>
            <p class="text-sm text-gray-500">Follow-up history for this support record</p>
        </div>
        @can('create immediate support')
            <a href="{{ route('support-follow-ups.create', ['support_id' => $support->id]) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                Add Follow-up
            </a>
        @endcan
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Outcome</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Next Date</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Followed By</th>
                    <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($support->followUps as $followUp)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">{{ $followUp->follow_up_date?->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $followUp->method_label }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($followUp->status === 'completed')
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Completed</span>
                            @elseif($followUp->status === 'missed')
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">Missed</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800">Scheduled</span>
                            @endif
                            @if($followUp->is_reintegrated)
                                <span class="ml-1 inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800">Reintegrated</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $followUp->outcome ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">{{ $followUp->next_follow_up_date?->format('Y-m-d') ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $followUp->followedBy->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-right text-sm whitespace-nowrap">
                            @can('edit immediate support')
                                <a href="{{ route('support-follow-ups.edit', $followUp) }}" class="text-green-600 hover:text-green-800 mx-1">Edit</a>
                            @endcan
                            @can('delete immediate support')
                                <form action="{{ route('support-follow-ups.destroy', $followUp) }}" method="POST" class="inline" onsubmit="return confirm('Delete this follow-up?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 mx-1">Delete</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                            <p class="text-lg font-medium">No follow-ups yet</p>
                            <p class="text-sm">Record a follow-up to track the outcome of this support.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
