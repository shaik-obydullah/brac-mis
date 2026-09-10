@extends('layouts.app')

@section('title', 'Documents - ' . $beneficiary->name)

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">Documents</h1>
    <p class="text-gray-600 mt-1">{{ $beneficiary->name }} ({{ $beneficiary->brac_id }})</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold mb-4">Upload Document</h2>
            <form method="POST" action="{{ route('beneficiaries.documents.upload', $beneficiary) }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Document Type</label>
                    <select name="type" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="NID">NID</option>
                        <option value="Passport">Passport</option>
                        <option value="Photo">Photo</option>
                        <option value="Certificate">Certificate</option>
                        <option value="Assessment">Assessment</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">File</label>
                    <input type="file" name="file" required class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2">
                </div>
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg text-sm font-medium">Upload</button>
            </form>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Type</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">File</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Uploaded</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($documents as $doc)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium">{{ $doc->type }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="text-blue-600 hover:underline">View</a>
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ $doc->created_at->format('Y-m-d') }}</td>
                            <td class="px-6 py-4">
                                <form method="POST" action="{{ route('beneficiaries.documents.destroy', [$beneficiary, $doc]) }}" onsubmit="return confirm('Delete this document?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">No documents uploaded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
