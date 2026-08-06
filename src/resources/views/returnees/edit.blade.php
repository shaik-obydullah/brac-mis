@extends('layouts.app')

@section('title', 'Edit Returnee')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Edit Returnee</h1>
    <p class="text-gray-600 mt-1">Update returnee information</p>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <form action="{{ route('returnees.update', $returnee) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Migrant <span class="text-red-500">*</span></label>
                <select name="migrant_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500">
                    <option value="">Select Migrant</option>
                    @foreach($migrants as $migrant)
                        <option value="{{ $migrant->id }}" {{ old('migrant_id', $returnee->migrant_id) == $migrant->id ? 'selected' : '' }}>{{ $migrant->name }} ({{ $migrant->brac_id ?? $migrant->id }})</option>
                    @endforeach
                </select>
                @error('migrant_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Beneficiary</label>
                <select name="beneficiary_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500">
                    <option value="">Select Beneficiary (optional)</option>
                    @foreach($beneficiaries ?? [] as $beneficiary)
                        <option value="{{ $beneficiary->id }}" {{ old('beneficiary_id', $returnee->beneficiary_id) == $beneficiary->id ? 'selected' : '' }}>{{ $beneficiary->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Return Date <span class="text-red-500">*</span></label>
                <input type="date" name="return_date" value="{{ old('return_date', optional($returnee->return_date)->format('Y-m-d')) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500">
                @error('return_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Origin Country</label>
                <select name="origin_country_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500">
                    <option value="">Select Country</option>
                    @foreach($countries ?? [] as $country)
                        <option value="{{ $country->id }}" {{ old('origin_country_id', $returnee->origin_country_id) == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Return Reason</label>
                <textarea name="return_reason" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500">{{ old('return_reason', $returnee->return_reason) }}</textarea>
                @error('return_reason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Current Status <span class="text-red-500">*</span></label>
                <select name="current_status" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500">
                    <option value="">Select Status</option>
                    <option value="assessed" {{ old('current_status', $returnee->current_status) == 'assessed' ? 'selected' : '' }}>Assessed</option>
                    <option value="planning" {{ old('current_status', $returnee->current_status) == 'planning' ? 'selected' : '' }}>Planning</option>
                    <option value="in_progress" {{ old('current_status', $returnee->current_status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ old('current_status', $returnee->current_status) == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="dropped" {{ old('current_status', $returnee->current_status) == 'dropped' ? 'selected' : '' }}>Dropped</option>
                </select>
                @error('current_status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center gap-4 mt-8 pt-6 border-t border-gray-200">
            <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2.5 rounded-lg font-medium">Update Returnee</button>
            <a href="{{ route('returnees.index') }}" class="text-gray-600 hover:text-gray-800 px-4 py-2.5">Cancel</a>
        </div>
    </form>
</div>
@endsection
