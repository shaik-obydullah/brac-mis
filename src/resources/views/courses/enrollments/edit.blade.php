@extends('layouts.app')

@section('title', 'Edit Enrollment')

@section('content')
<div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Edit Enrollment</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $course->title }} &middot; {{ $enrollment->participant_name }}</p>
    </div>
    <a href="{{ route('courses.show', $course) }}" class="btn-secondary">Back to course</a>
</div>

<div class="card">
    <form method="POST" action="{{ route('courses.enrollments.update', [$course, $enrollment]) }}" class="p-5">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div class="md:col-span-2 rounded-xl bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Participant</p>
                <p class="mt-1 font-semibold text-slate-900">{{ $enrollment->participant_name }}</p>
                <p class="text-sm text-slate-500">{{ $enrollment->participant_type_label }}</p>
            </div>
            <div>
                <label class="field-label" for="enrolled_at">Enrolled on</label>
                <input id="enrolled_at" type="date" name="enrolled_at" value="{{ old('enrolled_at', optional($enrollment->enrolled_at)->format('Y-m-d')) }}" class="field" required>
                @error('enrolled_at')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="field-label" for="completed_at">Completed on</label>
                <input id="completed_at" type="date" name="completed_at" value="{{ old('completed_at', optional($enrollment->completed_at)->format('Y-m-d')) }}" class="field">
                @error('completed_at')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="field-label" for="status">Status</label>
                <select id="status" name="status" class="field" required>
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" {{ old('status', $enrollment->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="field-label" for="score">Score</label>
                <input id="score" type="number" name="score" value="{{ old('score', $enrollment->score) }}" min="0" max="100" step="0.01" class="field">
                @error('score')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label class="field-label" for="remarks">Remarks</label>
                <textarea id="remarks" name="remarks" rows="3" class="field">{{ old('remarks', $enrollment->remarks) }}</textarea>
                @error('remarks')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="mt-6 flex items-center gap-3 border-t border-slate-100 pt-5">
            <button type="submit" class="btn-primary">Update Enrollment</button>
            <a href="{{ route('courses.show', $course) }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
