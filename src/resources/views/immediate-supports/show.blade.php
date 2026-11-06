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
                <dt class="text-gray-500">Beneficiary</dt>
                <dd class="font-medium text-gray-800">
                    @if($support->beneficiary)
                        <a href="{{ route('beneficiaries.show', $support->beneficiary) }}" class="text-blue-600 hover:underline">
                            {{ $support->beneficiary->brac_id }} - {{ $support->beneficiary->name }}
                        </a>
                    @else
                        N/A
                    @endif
                </dd>
            </div>
            <div class="flex justify-between py-2 border-b">
                <dt class="text-gray-500">Returnee</dt>
                <dd class="font-medium text-gray-800">
                    @if($support->returnee)
                        <a href="{{ route('returnees.show', $support->returnee) }}" class="text-blue-600 hover:underline">
                            Returnee #{{ $support->returnee->id }}{{ $support->returnee->beneficiary ? ' - ' . $support->returnee->beneficiary->name : '' }}
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
                <dd class="font-medium text-gray-800">{{ $support->recipient_phone ?? $support->beneficiary->phone ?? 'N/A' }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b">
                <dt class="text-gray-500">Amount</dt>
                <dd class="font-medium text-gray-800">{{ $support->amount !== null ? number_format((float) $support->amount, 2) : 'N/A' }}</dd>
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
@endsection
