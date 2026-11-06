@extends('layouts.app')

@section('title', 'Certificate ' . $certificate->certificate_number)

@section('content')
@php
    $state = $certificate->status === 'revoked' ? 'revoked' : ($certificate->is_expired ? 'expired' : 'valid');
    $stateStyles = [
        'valid' => ['label' => 'Valid certificate', 'wrap' => 'border-brand-200 bg-brand-50', 'text' => 'text-brand-800', 'badge' => 'bg-brand-100 text-brand-700 ring-brand-200'],
        'expired' => ['label' => 'Expired certificate', 'wrap' => 'border-amber-200 bg-amber-50', 'text' => 'text-amber-800', 'badge' => 'bg-amber-100 text-amber-700 ring-amber-200'],
        'revoked' => ['label' => 'Revoked certificate', 'wrap' => 'border-rose-200 bg-rose-50', 'text' => 'text-rose-800', 'badge' => 'bg-rose-100 text-rose-700 ring-rose-200'],
    ];
    $stateStyle = $stateStyles[$state];
@endphp

<div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end print:hidden">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Certificate</h1>
        <p class="mt-1 font-mono text-sm text-slate-500">{{ $certificate->certificate_number }}</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <button type="button" onclick="window.print()" class="btn-primary">Print / Save PDF</button>
        <a href="{{ route('certificates.verify', $certificate->verification_code) }}" target="_blank" rel="noopener" class="btn-secondary">Public Page</a>
        @can('edit certificates')
            <a href="{{ route('certificates.edit', $certificate) }}" class="btn-secondary">Edit</a>
        @endcan
        <a href="{{ route('certificates.index') }}" class="btn-secondary">Back</a>
    </div>
</div>

<div class="mb-6 rounded-2xl border p-4 {{ $stateStyle['wrap'] }} print:hidden">
    <div class="flex items-center justify-between gap-3">
        <p class="font-semibold {{ $stateStyle['text'] }}">{{ $stateStyle['label'] }}</p>
        <span class="badge {{ $stateStyle['badge'] }}">{{ ucfirst($state) }}</span>
    </div>
</div>

<div class="card mb-6 overflow-hidden">
    <div class="border-[10px] border-double border-brand-600 bg-white p-8 text-center sm:p-14">
        <p class="text-xs font-black uppercase tracking-[0.45em] text-brand-600">BRAC</p>
        <p class="mt-1 text-[11px] font-medium uppercase tracking-[0.2em] text-slate-500">Migration &amp; Reintegration Management</p>

        <h2 class="mt-8 font-serif text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Certificate of Completion</h2>

        <p class="mt-8 text-sm text-slate-500">This is to certify that</p>
        <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">{{ $certificate->recipient_name }}</p>

        <p class="mt-6 text-sm text-slate-500">has successfully completed the course</p>
        <p class="mt-2 text-xl font-semibold text-slate-800">{{ $certificate->course->title ?? 'N/A' }}</p>
        @if($certificate->course?->code)
            <p class="mt-1 text-xs font-medium uppercase tracking-wide text-slate-500">{{ $certificate->course->code }}</p>
        @endif

        <div class="mx-auto mt-10 grid max-w-lg grid-cols-2 gap-6 border-t border-slate-200 pt-6 text-left text-xs">
            <div>
                <p class="font-semibold uppercase tracking-wide text-slate-400">Issued on</p>
                <p class="mt-1 font-medium text-slate-800">{{ optional($certificate->issued_date)->format('d M Y') }}</p>
            </div>
            <div>
                <p class="font-semibold uppercase tracking-wide text-slate-400">Expires on</p>
                <p class="mt-1 font-medium text-slate-800">{{ optional($certificate->expiry_date)->format('d M Y') ?? 'No expiry' }}</p>
            </div>
            <div>
                <p class="font-semibold uppercase tracking-wide text-slate-400">Certificate no.</p>
                <p class="mt-1 font-mono font-medium text-slate-800">{{ $certificate->certificate_number }}</p>
            </div>
            <div>
                <p class="font-semibold uppercase tracking-wide text-slate-400">Verification code</p>
                <p class="mt-1 font-mono font-medium text-slate-800">{{ $certificate->verification_code }}</p>
            </div>
        </div>

        <p class="mt-8 text-[11px] text-slate-400">Validate this certificate at {{ route('certificates.validate') }} using the number or verification code above.</p>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2 print:hidden">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Recipient</h2>
        </div>
        <dl class="divide-y divide-slate-100">
            <div class="flex justify-between gap-4 px-5 py-3">
                <dt class="text-sm text-slate-500">Name</dt>
                <dd class="text-sm font-medium text-slate-900">{{ $certificate->recipient_name }}</dd>
            </div>
            <div class="flex justify-between gap-4 px-5 py-3">
                <dt class="text-sm text-slate-500">Type</dt>
                <dd class="text-sm font-medium text-slate-900">{{ $certificate->recipient_type ? ucfirst($certificate->recipient_type) : 'Manual' }}</dd>
            </div>
            <div class="flex justify-between gap-4 px-5 py-3">
                <dt class="text-sm text-slate-500">Enrollment</dt>
                <dd class="text-sm font-medium text-slate-900">{{ $certificate->enrollment ? '#' . $certificate->enrollment->id : '—' }}</dd>
            </div>
            <div class="flex justify-between gap-4 px-5 py-3">
                <dt class="text-sm text-slate-500">Issued by</dt>
                <dd class="text-sm font-medium text-slate-900">{{ $certificate->issuedBy->name ?? 'N/A' }}</dd>
            </div>
        </dl>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Certificate details</h2>
        </div>
        <dl class="divide-y divide-slate-100">
            <div class="flex justify-between gap-4 px-5 py-3">
                <dt class="text-sm text-slate-500">Course</dt>
                <dd class="text-sm font-medium text-slate-900">{{ $certificate->course->title ?? 'N/A' }}</dd>
            </div>
            <div class="flex justify-between gap-4 px-5 py-3">
                <dt class="text-sm text-slate-500">Issued date</dt>
                <dd class="text-sm font-medium text-slate-900">{{ optional($certificate->issued_date)->format('d M Y') }}</dd>
            </div>
            <div class="flex justify-between gap-4 px-5 py-3">
                <dt class="text-sm text-slate-500">Expiry date</dt>
                <dd class="text-sm font-medium text-slate-900">{{ optional($certificate->expiry_date)->format('d M Y') ?? 'No expiry' }}</dd>
            </div>
            <div class="flex justify-between gap-4 px-5 py-3">
                <dt class="text-sm text-slate-500">Status</dt>
                <dd class="text-sm font-medium text-slate-900">{{ ucfirst($state) }}</dd>
            </div>
            @if($certificate->remarks)
                <div class="px-5 py-3">
                    <dt class="text-sm text-slate-500">Remarks</dt>
                    <dd class="mt-1 text-sm text-slate-800">{{ $certificate->remarks }}</dd>
                </div>
            @endif
        </dl>
    </div>
</div>
@endsection
