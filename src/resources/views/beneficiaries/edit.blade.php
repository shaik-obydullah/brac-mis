@extends('layouts.app')

@section('title', 'Edit Beneficiary')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Edit Beneficiary</h1>
    <p class="text-gray-600 mt-1">Update beneficiary information</p>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <form action="{{ route('beneficiaries.update', $beneficiary) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">BRAC ID</label>
                <input type="text" name="brac_id" value="{{ old('brac_id', $beneficiary->brac_id ?? $beneficiary->id) }}" readonly class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-gray-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $beneficiary->name) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Father's Name</label>
                <input type="text" name="father_name" value="{{ old('father_name', $beneficiary->father_name) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mother's Name</label>
                <input type="text" name="mother_name" value="{{ old('mother_name', $beneficiary->mother_name) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gender <span class="text-red-500">*</span></label>
                <select name="gender" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">Select Gender</option>
                    <option value="Male" {{ old('gender', $beneficiary->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('gender', $beneficiary->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Other" {{ old('gender', $beneficiary->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('gender') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                <input type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($beneficiary->date_of_birth)->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">NID Number</label>
                <input type="text" name="nid" value="{{ old('nid', $beneficiary->nid) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone <span class="text-red-500">*</span></label>
                <input type="text" name="phone" value="{{ old('phone', $beneficiary->phone) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Present Address</label>
                <textarea name="present_address" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">{{ old('present_address', $beneficiary->present_address) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Permanent Address</label>
                <textarea name="permanent_address" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">{{ old('permanent_address', $beneficiary->permanent_address) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Occupation</label>
                <input type="text" name="occupation" value="{{ old('occupation', $beneficiary->occupation) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Income</label>
                <input type="number" step="0.01" name="monthly_income" value="{{ old('monthly_income', $beneficiary->monthly_income) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Family Members</label>
                <input type="number" name="family_members" value="{{ old('family_members', $beneficiary->family_members) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Branch <span class="text-red-500">*</span></label>
                <select name="branch_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">Select Branch</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id', $beneficiary->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->name }} ({{ $branch->code ?? '' }})</option>
                    @endforeach
                </select>
                @error('branch_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select name="status" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">Select Status</option>
                    <option value="Active" {{ old('status', $beneficiary->status) == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ old('status', $beneficiary->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="Migrated" {{ old('status', $beneficiary->status) == 'Migrated' ? 'selected' : '' }}>Migrated</option>
                    <option value="Returned" {{ old('status', $beneficiary->status) == 'Returned' ? 'selected' : '' }}>Returned</option>
                </select>
                @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center gap-4 mt-8 pt-6 border-t border-gray-200">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-lg font-medium">Update Beneficiary</button>
            <a href="{{ route('beneficiaries.index') }}" class="text-gray-600 hover:text-gray-800 px-4 py-2.5">Cancel</a>
        </div>
    </form>
</div>
@endsection
