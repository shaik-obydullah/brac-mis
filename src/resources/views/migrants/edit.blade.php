@extends('layouts.app')

@section('title', 'Edit Migrant')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Edit Migrant</h1>
    <p class="text-gray-600 mt-1">Update migrant information</p>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <form action="{{ route('migrants.update', $migrant) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">BRAC ID</label>
                <input type="text" name="brac_id" value="{{ old('brac_id', $migrant->brac_id ?? $migrant->id) }}" readonly class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-gray-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $migrant->name) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gender <span class="text-red-500">*</span></label>
                <select name="gender" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select Gender</option>
                    <option value="Male" {{ old('gender', $migrant->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('gender', $migrant->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Other" {{ old('gender', $migrant->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('gender') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                <input type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($migrant->date_of_birth)->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">NID Number</label>
                <input type="text" name="nid_number" value="{{ old('nid_number', $migrant->nid_number) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone <span class="text-red-500">*</span></label>
                <input type="text" name="phone" value="{{ old('phone', $migrant->phone) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Passport Number</label>
                <input type="text" name="passport_number" value="{{ old('passport_number', $migrant->passport_number) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Destination Country <span class="text-red-500">*</span></label>
                <select name="destination_country_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select Country</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ old('destination_country_id', $migrant->destination_country_id) == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                    @endforeach
                </select>
                @error('destination_country_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Destination City</label>
                <input type="text" name="destination_city" value="{{ old('destination_city', $migrant->destination_city) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Skill Level</label>
                <select name="skill_level" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select Skill Level</option>
                    <option value="Skilled" {{ old('skill_level', $migrant->skill_level) == 'Skilled' ? 'selected' : '' }}>Skilled</option>
                    <option value="Semi-skilled" {{ old('skill_level', $migrant->skill_level) == 'Semi-skilled' ? 'selected' : '' }}>Semi-skilled</option>
                    <option value="Unskilled" {{ old('skill_level', $migrant->skill_level) == 'Unskilled' ? 'selected' : '' }}>Unskilled</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Education Level</label>
                <input type="text" name="education_level" value="{{ old('education_level', $migrant->education_level) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Occupation</label>
                <input type="text" name="occupation" value="{{ old('occupation', $migrant->occupation) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Beneficiary</label>
                <select name="beneficiary_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select Beneficiary (optional)</option>
                    @foreach($beneficiaries ?? [] as $beneficiary)
                        <option value="{{ $beneficiary->id }}" {{ old('beneficiary_id', $migrant->beneficiary_id) == $beneficiary->id ? 'selected' : '' }}>{{ $beneficiary->name }} ({{ $beneficiary->brac_id ?? $beneficiary->id }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select name="status" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select Status</option>
                    <option value="Active" {{ old('status', $migrant->status) == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ old('status', $migrant->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="Returned" {{ old('status', $migrant->status) == 'Returned' ? 'selected' : '' }}>Returned</option>
                </select>
                @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center gap-4 mt-8 pt-6 border-t border-gray-200">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-medium">Update Migrant</button>
            <a href="{{ route('migrants.index') }}" class="text-gray-600 hover:text-gray-800 px-4 py-2.5">Cancel</a>
        </div>
    </form>
</div>
@endsection
