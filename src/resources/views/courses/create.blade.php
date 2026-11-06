@extends('layouts.app')

@section('title', 'Create Course')

@section('content')
<div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Create Course</h1>
        <p class="mt-1 text-sm text-slate-500">Add a new training course to the catalogue.</p>
    </div>
    <a href="{{ route('courses.index') }}" class="btn-secondary">Back to courses</a>
</div>

<div class="card">
    <form method="POST" action="{{ route('courses.store') }}" class="p-5">
        @csrf
        @include('courses._form')
        <div class="mt-6 flex items-center gap-3 border-t border-slate-100 pt-5">
            <button type="submit" class="btn-primary">Save Course</button>
            <a href="{{ route('courses.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
