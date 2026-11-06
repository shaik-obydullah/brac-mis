@extends('layouts.app')

@section('title', 'Edit Course')

@section('content')
<div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Edit Course</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $course->code }} &middot; {{ $course->title }}</p>
    </div>
    <a href="{{ route('courses.show', $course) }}" class="btn-secondary">Back to course</a>
</div>

<div class="card">
    <form method="POST" action="{{ route('courses.update', $course) }}" class="p-5">
        @csrf
        @method('PUT')
        @include('courses._form')
        <div class="mt-6 flex items-center gap-3 border-t border-slate-100 pt-5">
            <button type="submit" class="btn-primary">Update Course</button>
            <a href="{{ route('courses.show', $course) }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
