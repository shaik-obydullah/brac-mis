@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">{{ $user->name }}</h1>
        <p class="text-gray-600">{{ $user->email }}</p>
    </div>
    <div class="flex space-x-3">
        <a href="{{ route('users.edit', $user) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Edit</a>
        <a href="{{ route('users.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">Back to List</a>
    </div>
</div>

<div class="mb-6 flex flex-wrap gap-2">
    <a href="#account" class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50">Account</a>
    <a href="#skills" class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50">Skills</a>
    <a href="#migration-history" class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50">Migration History</a>
    <a href="#documents" class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50">Documents</a>
</div>

@if($errors->any())
    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
        <p class="font-medium text-red-800 mb-2">Please fix the following errors:</p>
        <ul class="list-disc list-inside text-sm text-red-700">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div id="account" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Account Information</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <p class="text-sm text-gray-500">Phone</p>
            <p class="font-medium text-gray-800">{{ $user->phone ?? 'N/A' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Branch</p>
            <p class="font-medium text-gray-800">{{ $user->branch->name ?? 'N/A' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Status</p>
            @if($user->status === 'active')
                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Active</span>
            @else
                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-700">Inactive</span>
            @endif
        </div>
        <div>
            <p class="text-sm text-gray-500">Roles</p>
            <div class="mt-1 flex flex-wrap gap-1">
                @forelse($user->roles as $role)
                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">{{ $role->name }}</span>
                @empty
                    <span class="text-gray-500">No roles assigned</span>
                @endforelse
            </div>
        </div>
        <div>
            <p class="text-sm text-gray-500">Created</p>
            <p class="font-medium text-gray-800">{{ $user->created_at?->format('Y-m-d') ?? 'N/A' }}</p>
        </div>
    </div>
</div>

<div id="skills" class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold mb-4">Add Skill</h2>
        <form method="POST" action="{{ route('users.skills.store', $user) }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Skill Name</label>
                <input type="text" name="skill_name" value="{{ old('skill_name') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Proficiency</label>
                <select name="proficiency" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    @foreach(['beginner', 'intermediate', 'advanced', 'expert'] as $level)
                        <option value="{{ $level }}" {{ old('proficiency') === $level ? 'selected' : '' }}>{{ ucfirst($level) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Years of Experience</label>
                <input type="number" name="years_of_experience" value="{{ old('years_of_experience') }}" min="0" max="60" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Certification</label>
                <input type="text" name="certification" value="{{ old('certification') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">Add Skill</button>
        </form>
    </div>

    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold">Skills ({{ $user->skills->count() }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Skill</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Proficiency</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Experience</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Certification</th>
                        <th class="text-right px-6 py-3 font-semibold text-gray-600">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($user->skills as $skill)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $skill->skill_name }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ ucfirst($skill->proficiency) }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ $skill->years_of_experience !== null ? $skill->years_of_experience . ' years' : 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ $skill->certification ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('users.skills.destroy', [$user, $skill]) }}" onsubmit="return confirm('Remove this skill?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">No skills added.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="migration-history" class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold mb-4">Add Migration History</h2>
        <form method="POST" action="{{ route('users.migration-histories.store', $user) }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Destination Country</label>
                <select name="country_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Select Country</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Employer</label>
                <input type="text" name="employer_name" value="{{ old('employer_name') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Job Title</label>
                <input type="text" name="job_title" value="{{ old('job_title') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Visa Type</label>
                <input type="text" name="visa_type" value="{{ old('visa_type') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="mb-4 grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    @foreach(['ongoing', 'completed', 'returned'] as $status)
                        <option value="{{ $status }}" {{ old('status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('notes') }}</textarea>
            </div>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">Add History</button>
        </form>
    </div>

    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold">Migration History ({{ $user->migrationHistories->count() }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Country</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Employer / Job</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Period</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Status</th>
                        <th class="text-right px-6 py-3 font-semibold text-gray-600">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($user->migrationHistories as $history)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-800">{{ $history->country->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-700">
                                <p>{{ $history->employer_name ?? 'N/A' }}</p>
                                <p class="text-gray-500">{{ $history->job_title ?? 'N/A' }}</p>
                            </td>
                            <td class="px-6 py-4 text-gray-700">
                                {{ $history->start_date?->format('Y-m-d') }}
                                &ndash;
                                {{ $history->end_date?->format('Y-m-d') ?? 'Present' }}
                            </td>
                            <td class="px-6 py-4 text-gray-700">{{ ucfirst($history->status) }}</td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('users.migration-histories.destroy', [$user, $history]) }}" onsubmit="return confirm('Remove this migration history?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">No migration history added.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="documents" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold mb-4">Upload Document</h2>
        <form method="POST" action="{{ route('users.documents.store', $user) }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Document Type</label>
                <select name="type" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="Passport">Passport</option>
                    <option value="NID">NID</option>
                    <option value="Visa">Visa</option>
                    <option value="Certificate">Certificate</option>
                    <option value="Contract">Contract</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">File</label>
                <input type="file" name="file" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG, DOC or DOCX. Max 10 MB.</p>
            </div>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">Upload</button>
        </form>
    </div>

    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold">Documents ({{ $user->documents->count() }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Type</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">File</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Expiry</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Uploaded</th>
                        <th class="text-right px-6 py-3 font-semibold text-gray-600">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($user->documents as $document)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $document->type }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="text-blue-600 hover:underline">View</a>
                            </td>
                            <td class="px-6 py-4 text-gray-700">{{ $document->expiry_date?->format('Y-m-d') ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $document->created_at->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('users.documents.destroy', [$user, $document]) }}" onsubmit="return confirm('Delete this document?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">No documents uploaded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
