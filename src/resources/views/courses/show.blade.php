@extends('layouts.app')

@section('title', $course->title)

@section('content')
@php
    $statusStyles = [
        'enrolled' => 'bg-sky-50 text-sky-700 ring-sky-200',
        'in_progress' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'completed' => 'bg-brand-50 text-brand-700 ring-brand-200',
        'dropped' => 'bg-rose-50 text-rose-700 ring-rose-200',
    ];
    $courseStatusStyles = [
        'active' => 'bg-brand-50 text-brand-700 ring-brand-200',
        'inactive' => 'bg-slate-50 text-slate-700 ring-slate-200',
    ];
@endphp

<div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $course->code }}</p>
        <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">{{ $course->title }}</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $course->category ?: 'Uncategorised' }}</p>
    </div>
    <div class="flex flex-wrap gap-2">
        @can('edit courses')
            <a href="{{ route('courses.edit', $course) }}" class="btn-secondary">Edit Course</a>
        @endcan
        @can('create certificates')
            <a href="{{ route('certificates.create', ['course_id' => $course->id]) }}" class="btn-primary">Issue Certificate</a>
        @endcan
        <a href="{{ route('courses.index') }}" class="btn-secondary">Back</a>
    </div>
</div>

<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Level</p>
        <p class="mt-1 text-lg font-semibold text-slate-900">{{ \App\Models\Course::LEVELS[$course->level] ?? ucfirst($course->level) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Duration</p>
        <p class="mt-1 text-lg font-semibold text-slate-900">{{ $course->duration_hours ? $course->duration_hours . ' hours' : '—' }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Status</p>
        <p class="mt-2"><span class="badge {{ $courseStatusStyles[$course->status] ?? 'bg-slate-50 text-slate-700 ring-slate-200' }}">{{ \App\Models\Course::STATUSES[$course->status] ?? ucfirst($course->status) }}</span></p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Enrollments</p>
        <p class="mt-1 text-lg font-semibold text-slate-900">{{ $course->enrollments->count() }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Certificates</p>
        <p class="mt-1 text-lg font-semibold text-slate-900">{{ $course->certificates->count() }}</p>
    </div>
</div>

@if($course->description)
    <div class="card mb-6 p-5">
        <h2 class="card-title">Description</h2>
        <p class="mt-2 whitespace-pre-line text-sm text-slate-700">{{ $course->description }}</p>
        <p class="mt-3 text-xs text-slate-500">Created by {{ $course->createdBy->name ?? 'N/A' }}</p>
    </div>
@endif

@can('edit courses')
    <div class="card mb-6">
        <div class="card-header">
            <div>
                <h2 class="card-title">Enroll participant</h2>
                <p class="mt-0.5 text-xs text-slate-500">Add a user profile to this course</p>
            </div>
        </div>
        <form method="POST" action="{{ route('courses.enrollments.store', $course) }}" class="p-5">
            @csrf
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="md:col-span-2">
                    <label class="field-label" for="participant">Participant</label>
                    <select id="participant" name="participant" class="field" required>
                        <option value="">Select participant</option>
                        @foreach($participants['user'] as $user)
                            <option value="user:{{ $user->id }}" {{ old('participant') === 'user:' . $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('participant')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="field-label" for="enrolled_at">Enrolled on</label>
                    <input id="enrolled_at" type="date" name="enrolled_at" value="{{ old('enrolled_at', now()->toDateString()) }}" class="field" required>
                    @error('enrolled_at')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="field-label" for="status">Status</label>
                    <select id="status" name="status" class="field" required>
                        @foreach(\App\Models\CourseEnrollment::STATUSES as $value => $label)
                            <option value="{{ $value }}" {{ old('status', 'enrolled') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="field-label" for="score">Score</label>
                    <input id="score" type="number" name="score" value="{{ old('score') }}" min="0" max="100" step="0.01" class="field">
                    @error('score')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-3">
                    <label class="field-label" for="remarks">Remarks</label>
                    <textarea id="remarks" name="remarks" rows="2" class="field">{{ old('remarks') }}</textarea>
                    @error('remarks')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="mt-5">
                <button type="submit" class="btn-primary">Enroll Participant</button>
            </div>
        </form>
    </div>
@endcan

<div class="card mb-6 overflow-hidden">
    <div class="card-header">
        <div>
            <h2 class="card-title">Enrollments</h2>
            <p class="mt-0.5 text-xs text-slate-500">Participants enrolled in this course</p>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="table-head px-5 py-3">Participant</th>
                    <th class="table-head px-5 py-3">Type</th>
                    <th class="table-head px-5 py-3">Status</th>
                    <th class="table-head px-5 py-3">Enrolled</th>
                    <th class="table-head px-5 py-3">Completed</th>
                    <th class="table-head px-5 py-3 text-right">Score</th>
                    <th class="table-head px-5 py-3">Certificate</th>
                    <th class="table-head px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($course->enrollments as $enrollment)
                    <tr class="hover:bg-slate-50">
                        <td class="table-cell font-medium text-slate-900">{{ $enrollment->participant_name }}</td>
                        <td class="table-cell">{{ $enrollment->participant_type_label }}</td>
                        <td class="table-cell">
                            <span class="badge {{ $statusStyles[$enrollment->status] ?? 'bg-slate-50 text-slate-700 ring-slate-200' }}">{{ \App\Models\CourseEnrollment::STATUSES[$enrollment->status] ?? ucfirst($enrollment->status) }}</span>
                        </td>
                        <td class="table-cell">{{ optional($enrollment->enrolled_at)->format('d M Y') }}</td>
                        <td class="table-cell">{{ optional($enrollment->completed_at)->format('d M Y') ?? '—' }}</td>
                        <td class="table-cell text-right">{{ $enrollment->score !== null ? number_format((float) $enrollment->score, 2) : '—' }}</td>
                        <td class="table-cell">
                            @if($enrollment->certificate)
                                <a href="{{ route('certificates.show', $enrollment->certificate) }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">View</a>
                            @elseif($enrollment->status === 'completed')
                                @can('create certificates')
                                    <a href="{{ route('certificates.create', ['enrollment' => $enrollment->id]) }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">Issue</a>
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endcan
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="table-cell text-right whitespace-nowrap">
                            @can('edit courses')
                                <a href="{{ route('courses.enrollments.edit', [$course, $enrollment]) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-800">Edit</a>
                                <form action="{{ route('courses.enrollments.destroy', [$course, $enrollment]) }}" method="POST" class="inline" onsubmit="return confirm('Remove this enrollment?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ml-3 text-xs font-semibold text-rose-600 hover:text-rose-700">Remove</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center text-slate-500">
                            <p class="text-lg font-medium text-slate-700">No enrollments yet</p>
                            <p class="mt-1 text-sm">Enroll a participant using the form above.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="card-header">
        <div>
            <h2 class="card-title">Certificates</h2>
            <p class="mt-0.5 text-xs text-slate-500">Certificates issued for this course</p>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="table-head px-5 py-3">Certificate #</th>
                    <th class="table-head px-5 py-3">Recipient</th>
                    <th class="table-head px-5 py-3">Issued</th>
                    <th class="table-head px-5 py-3">Status</th>
                    <th class="table-head px-5 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($course->certificates as $certificate)
                    <tr class="hover:bg-slate-50">
                        <td class="table-cell font-mono text-xs text-slate-700">{{ $certificate->certificate_number }}</td>
                        <td class="table-cell font-medium text-slate-900">{{ $certificate->recipient_name }}</td>
                        <td class="table-cell">{{ optional($certificate->issued_date)->format('d M Y') }}</td>
                        <td class="table-cell">
                            <span class="badge {{ $certificate->status === 'valid' ? 'bg-brand-50 text-brand-700 ring-brand-200' : 'bg-rose-50 text-rose-700 ring-rose-200' }}">{{ ucfirst($certificate->status) }}</span>
                        </td>
                        <td class="table-cell text-right">
                            <a href="{{ route('certificates.show', $certificate) }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-500">No certificates issued yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
