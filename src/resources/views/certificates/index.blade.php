@extends('layouts.app')

@section('title', 'Certificates')

@section('content')
@php
    $statusStyles = [
        'valid' => 'bg-brand-50 text-brand-700 ring-brand-200',
        'revoked' => 'bg-rose-50 text-rose-700 ring-rose-200',
    ];
@endphp

<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Certificates</h1>
        <p class="mt-1 text-sm text-slate-500">Create, manage and validate course completion certificates.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('certificates.validate') }}" target="_blank" rel="noopener" class="btn-secondary">Public Validation</a>
        @can('create certificates')
            <a href="{{ route('certificates.create') }}" class="btn-primary">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Issue Certificate
            </a>
        @endcan
    </div>
</div>

<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Total certificates</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totals['certificates']) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Valid</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-brand-600">{{ number_format($totals['valid']) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Revoked</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-rose-600">{{ number_format($totals['revoked']) }}</p>
    </div>
</div>

<div class="card mb-6 p-4">
    <form method="GET" action="{{ route('certificates.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="md:col-span-2">
            <label class="field-label" for="search">Search</label>
            <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Certificate #, code or recipient..." class="field">
        </div>
        <div>
            <label class="field-label" for="course_id">Course</label>
            <select id="course_id" name="course_id" class="field">
                <option value="">All courses</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ (string) request('course_id') === (string) $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="field-label" for="status">Status</label>
            <select id="status" name="status" class="field">
                <option value="">All statuses</option>
                @foreach(\App\Models\Certificate::STATUSES as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2 md:col-span-4">
            <button type="submit" class="btn-primary">Filter</button>
            <a href="{{ route('certificates.index') }}" class="btn-secondary">Clear</a>
        </div>
    </form>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="table-head px-5 py-3">Certificate #</th>
                    <th class="table-head px-5 py-3">Recipient</th>
                    <th class="table-head px-5 py-3">Course</th>
                    <th class="table-head px-5 py-3">Issued</th>
                    <th class="table-head px-5 py-3">Status</th>
                    <th class="table-head px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($certificates as $certificate)
                    <tr class="hover:bg-slate-50">
                        <td class="table-cell">
                            <a href="{{ route('certificates.show', $certificate) }}" class="font-mono text-xs font-semibold text-brand-600 hover:text-brand-700">{{ $certificate->certificate_number }}</a>
                            <p class="text-xs text-slate-500">Code: {{ $certificate->verification_code }}</p>
                        </td>
                        <td class="table-cell font-medium text-slate-900">{{ $certificate->recipient_name }}</td>
                        <td class="table-cell">
                            <p class="text-slate-800">{{ $certificate->course->title ?? 'N/A' }}</p>
                            <p class="text-xs text-slate-500">{{ $certificate->course->code ?? '' }}</p>
                        </td>
                        <td class="table-cell whitespace-nowrap">{{ optional($certificate->issued_date)->format('d M Y') }}</td>
                        <td class="table-cell">
                            <span class="badge {{ $statusStyles[$certificate->status] ?? 'bg-slate-50 text-slate-700 ring-slate-200' }}">{{ ucfirst($certificate->status) }}</span>
                            @if($certificate->is_expired && $certificate->status === 'valid')
                                <span class="badge ml-1 bg-amber-50 text-amber-700 ring-amber-200">Expired</span>
                            @endif
                        </td>
                        <td class="table-cell text-right whitespace-nowrap">
                            <a href="{{ route('certificates.show', $certificate) }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">View</a>
                            @can('edit certificates')
                                <a href="{{ route('certificates.edit', $certificate) }}" class="ml-3 text-xs font-semibold text-slate-600 hover:text-slate-800">Edit</a>
                            @endcan
                            @can('delete certificates')
                                <form action="{{ route('certificates.destroy', $certificate) }}" method="POST" class="inline" onsubmit="return confirm('Delete this certificate?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ml-3 text-xs font-semibold text-rose-600 hover:text-rose-700">Delete</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-slate-500">
                            <p class="text-lg font-medium text-slate-700">No certificates found</p>
                            <p class="mt-1 text-sm">Issue a certificate to get started.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($certificates, 'links'))
        <div class="border-t border-slate-100 px-5 py-4">{{ $certificates->links() }}</div>
    @endif
</div>
@endsection
