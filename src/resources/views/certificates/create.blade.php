@extends('layouts.app')

@section('title', 'Issue Certificate')

@section('content')
<div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Issue Certificate</h1>
        <p class="mt-1 text-sm text-slate-500">Create a certificate for a course participant.</p>
    </div>
    <a href="{{ route('certificates.index') }}" class="btn-secondary">Back to certificates</a>
</div>

<div class="card">
    <form method="POST" action="{{ route('certificates.store') }}" class="p-5">
        @csrf
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div>
                <label class="field-label" for="course_id">Course</label>
                <select id="course_id" name="course_id" class="field" required>
                    <option value="">Select a course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ (string) old('course_id', $certificate->course_id) === (string) $course->id ? 'selected' : '' }}>{{ $course->code }} - {{ $course->title }}</option>
                    @endforeach
                </select>
                @error('course_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="field-label" for="course_enrollment_id">Enrollment</label>
                <select id="course_enrollment_id" name="course_enrollment_id" class="field">
                    <option value="">No enrollment (manual recipient)</option>
                    @foreach($enrollments as $enrollment)
                        <option value="{{ $enrollment->id }}" {{ (string) old('course_enrollment_id', $certificate->course_enrollment_id) === (string) $enrollment->id ? 'selected' : '' }}>
                            {{ $enrollment->course->code }} · {{ $enrollment->participant_name }} ({{ \App\Models\CourseEnrollment::STATUSES[$enrollment->status] ?? ucfirst($enrollment->status) }})
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-slate-500">Selecting an enrollment auto-fills the recipient and marks it completed.</p>
                @error('course_enrollment_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="field-label" for="recipient_name">Recipient name</label>
                <input id="recipient_name" type="text" name="recipient_name" value="{{ old('recipient_name', $certificate->recipient_name) }}" class="field" placeholder="Leave blank to use the enrollment participant">
                @error('recipient_name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="field-label" for="certificate_number">Certificate number</label>
                <input id="certificate_number" type="text" name="certificate_number" value="{{ old('certificate_number', $certificate->certificate_number) }}" class="field" placeholder="Auto-generated if blank">
                @error('certificate_number')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="field-label" for="issued_date">Issued date</label>
                <input id="issued_date" type="date" name="issued_date" value="{{ old('issued_date', optional($certificate->issued_date)->format('Y-m-d') ?? now()->toDateString()) }}" class="field" required>
                @error('issued_date')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="field-label" for="expiry_date">Expiry date</label>
                <input id="expiry_date" type="date" name="expiry_date" value="{{ old('expiry_date', optional($certificate->expiry_date)->format('Y-m-d')) }}" class="field">
                @error('expiry_date')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="field-label" for="status">Status</label>
                <select id="status" name="status" class="field" required>
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" {{ old('status', $certificate->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label class="field-label" for="remarks">Remarks</label>
                <textarea id="remarks" name="remarks" rows="3" class="field">{{ old('remarks', $certificate->remarks) }}</textarea>
                @error('remarks')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="mt-6 flex items-center gap-3 border-t border-slate-100 pt-5">
            <button type="submit" class="btn-primary">Create Certificate</button>
            <a href="{{ route('certificates.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
