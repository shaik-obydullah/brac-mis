@extends('layouts.app')

@section('title', 'Courses')

@section('content')
@php
    $statusStyles = [
        'active' => 'bg-brand-50 text-brand-700 ring-brand-200',
        'inactive' => 'bg-slate-50 text-slate-700 ring-slate-200',
    ];
    $levelStyles = [
        'beginner' => 'bg-sky-50 text-sky-700 ring-sky-200',
        'intermediate' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'advanced' => 'bg-violet-50 text-violet-700 ring-violet-200',
    ];
@endphp

<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Courses</h1>
        <p class="mt-1 text-sm text-slate-500">Manage training courses, enrollments and certificates.</p>
    </div>
    @can('create courses')
        <a href="{{ route('courses.create') }}" class="btn-primary">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create Course
        </a>
    @endcan
</div>

<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Total courses</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totals['courses']) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Active courses</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-brand-600">{{ number_format($totals['active']) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Enrollments</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totals['enrollments']) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Certificates issued</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totals['certificates']) }}</p>
    </div>
</div>

<div class="card mb-6 p-4">
    <form method="GET" action="{{ route('courses.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="md:col-span-2">
            <label class="field-label" for="search">Search</label>
            <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Code, title or category..." class="field">
        </div>
        <div>
            <label class="field-label" for="status">Status</label>
            <select id="status" name="status" class="field">
                <option value="">All statuses</option>
                @foreach(\App\Models\Course::STATUSES as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="field-label" for="level">Level</label>
            <select id="level" name="level" class="field">
                <option value="">All levels</option>
                @foreach(\App\Models\Course::LEVELS as $value => $label)
                    <option value="{{ $value }}" {{ request('level') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2 md:col-span-4">
            <button type="submit" class="btn-primary">Filter</button>
            <a href="{{ route('courses.index') }}" class="btn-secondary">Clear</a>
        </div>
    </form>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="table-head px-5 py-3">Course</th>
                    <th class="table-head px-5 py-3">Level</th>
                    <th class="table-head px-5 py-3">Duration</th>
                    <th class="table-head px-5 py-3 text-right">Enrollments</th>
                    <th class="table-head px-5 py-3 text-right">Certificates</th>
                    <th class="table-head px-5 py-3">Status</th>
                    <th class="table-head px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($courses as $course)
                    <tr class="hover:bg-slate-50">
                        <td class="table-cell">
                            <a href="{{ route('courses.show', $course) }}" class="font-semibold text-slate-900 hover:text-brand-600">{{ $course->title }}</a>
                            <p class="text-xs text-slate-500">{{ $course->code }}{{ $course->category ? ' · ' . $course->category : '' }}</p>
                        </td>
                        <td class="table-cell">
                            <span class="badge {{ $levelStyles[$course->level] ?? 'bg-slate-50 text-slate-700 ring-slate-200' }}">{{ \App\Models\Course::LEVELS[$course->level] ?? ucfirst($course->level) }}</span>
                        </td>
                        <td class="table-cell">{{ $course->duration_hours ? $course->duration_hours . ' hrs' : '—' }}</td>
                        <td class="table-cell text-right">{{ number_format($course->enrollments_count) }}</td>
                        <td class="table-cell text-right">{{ number_format($course->certificates_count) }}</td>
                        <td class="table-cell">
                            <span class="badge {{ $statusStyles[$course->status] ?? 'bg-slate-50 text-slate-700 ring-slate-200' }}">{{ \App\Models\Course::STATUSES[$course->status] ?? ucfirst($course->status) }}</span>
                        </td>
                        <td class="table-cell text-right whitespace-nowrap">
                            <a href="{{ route('courses.show', $course) }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">View</a>
                            @can('edit courses')
                                <a href="{{ route('courses.edit', $course) }}" class="ml-3 text-xs font-semibold text-slate-600 hover:text-slate-800">Edit</a>
                            @endcan
                            @can('delete courses')
                                <form action="{{ route('courses.destroy', $course) }}" method="POST" class="inline" onsubmit="return confirm('Delete this course? Enrollments and certificates will also be removed.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ml-3 text-xs font-semibold text-rose-600 hover:text-rose-700">Delete</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-slate-500">
                            <p class="text-lg font-medium text-slate-700">No courses found</p>
                            <p class="mt-1 text-sm">Create a course to get started.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($courses, 'links'))
        <div class="border-t border-slate-100 px-5 py-4">{{ $courses->links() }}</div>
    @endif
</div>
@endsection
