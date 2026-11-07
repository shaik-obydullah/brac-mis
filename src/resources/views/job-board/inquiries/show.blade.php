@extends('layouts.app')

@section('title', 'Job Board Inquiry')

@section('content')
@php
    $statusStyles = [
        'new' => 'bg-sky-50 text-sky-700 ring-sky-200',
        'contacted' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'shared' => 'bg-brand-50 text-brand-700 ring-brand-200',
        'closed' => 'bg-slate-100 text-slate-700 ring-slate-200',
    ];
    $candidate = $inquiry->candidate;
@endphp

<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <a href="{{ route('job-board.inquiries.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-slate-500 hover:text-slate-800">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to inquiries
        </a>
        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Inquiry from {{ $inquiry->employer_name }}</h1>
        <p class="mt-1 text-sm text-slate-500">Received {{ $inquiry->created_at?->format('d M Y, H:i') }}</p>
    </div>
    <span class="badge self-start {{ $statusStyles[$inquiry->status] ?? 'bg-slate-50 text-slate-700 ring-slate-200' }}">{{ $inquiry->status_label }}</span>
</div>

<div class="mb-6 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
    <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
    <p><strong>Confidential.</strong> Share candidate contact details only after confirming the candidate's consent and verifying the employer's request. Do not copy this information into public channels.</p>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        <div class="card overflow-hidden">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Employer request</h2>
                    <p class="mt-0.5 text-xs text-slate-500">Details submitted through the public job board</p>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-5 px-5 py-5 sm:grid-cols-2">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Contact name</p>
                    <p class="mt-1 font-medium text-slate-900">{{ $inquiry->employer_name }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Organisation</p>
                    <p class="mt-1 font-medium text-slate-900">{{ $inquiry->employer_organization ?: 'Not provided' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Email</p>
                    <p class="mt-1"><a href="mailto:{{ $inquiry->employer_email }}" class="font-medium text-brand-600 hover:text-brand-700">{{ $inquiry->employer_email }}</a></p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Phone</p>
                    <p class="mt-1 font-medium text-slate-900">
                        @if($inquiry->employer_phone)
                            <a href="tel:{{ $inquiry->employer_phone }}" class="text-brand-600 hover:text-brand-700">{{ $inquiry->employer_phone }}</a>
                        @else
                            Not provided
                        @endif
                    </p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Role they are hiring for</p>
                    <p class="mt-1 font-medium text-slate-900">{{ $inquiry->job_title ?: 'Not specified' }}</p>
                </div>
            </div>
            <div class="border-t border-slate-100 px-5 py-5">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Message</p>
                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $inquiry->message }}</p>
            </div>
        </div>

        @if($candidate)
            <div class="card overflow-hidden">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Candidate</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Contact details are visible to authorised staff only</p>
                    </div>
                    @can('manage users')
                        <a href="{{ route('users.show', $candidate) }}" class="btn-secondary">Open full profile</a>
                    @endcan
                </div>
                <div class="grid grid-cols-1 gap-5 px-5 py-5 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Name</p>
                        <p class="mt-1 font-medium text-slate-900">{{ $candidate->name }}</p>
                        <span class="badge mt-1 bg-pink-50 text-pink-700 ring-pink-200">{{ $candidate->profile_type_label }}</span>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Location</p>
                        <p class="mt-1 font-medium text-slate-900">{{ $candidate->branch?->district ?? 'N/A' }}</p>
                        <p class="text-xs text-slate-500">{{ $candidate->branch?->division ?? '' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Email</p>
                        <p class="mt-1 font-medium text-slate-900 break-all">{{ $candidate->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Phone</p>
                        <p class="mt-1 font-medium text-slate-900">{{ $candidate->phone ?? 'Not recorded' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Skills</p>
                        @if($candidate->skills->isNotEmpty())
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach($candidate->skills as $skill)
                                    <span class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-medium text-slate-700">
                                        {{ $skill->skill_name }}
                                        <span class="text-slate-500">{{ ucfirst($skill->proficiency) }}</span>
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="mt-1 text-sm text-slate-500">No skills recorded.</p>
                        @endif
                    </div>
                </div>

                <div class="border-t border-slate-100 px-5 py-5">
                    <h3 class="text-sm font-semibold text-slate-900">Migration history</h3>
                    @if($candidate->migrationHistories->isNotEmpty())
                        <div class="mt-3 space-y-3">
                            @foreach($candidate->migrationHistories->sortByDesc('start_date') as $history)
                                <div class="rounded-lg border border-slate-200 p-3">
                                    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                        <p class="font-medium text-slate-900">
                                            {{ $history->job_title ?? 'Worker' }}
                                            @if($history->employer_name)
                                                <span class="font-normal text-slate-500">at {{ $history->employer_name }}</span>
                                            @endif
                                        </p>
                                        <span class="badge self-start bg-slate-50 text-slate-700 ring-slate-200">{{ ucfirst($history->status) }}</span>
                                    </div>
                                    <p class="mt-1 text-sm text-slate-600">
                                        {{ $history->country?->name ?? 'Unknown country' }}
                                        @if($history->visa_type)
                                            &middot; {{ $history->visa_type }} visa
                                        @endif
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $history->start_date?->format('M Y') ?? 'N/A' }}
                                        &ndash;
                                        {{ $history->end_date?->format('M Y') ?? 'Present' }}
                                    </p>
                                    @if($history->notes)
                                        <p class="mt-2 text-xs text-slate-600">{{ $history->notes }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-2 text-sm text-slate-500">No migration history recorded.</p>
                    @endif
                </div>
            </div>
        @else
            <div class="card p-6 text-sm text-slate-500">This candidate profile has been removed.</div>
        @endif
    </div>

    <div class="space-y-6">
        <div class="card p-5">
            <h2 class="card-title">Update status</h2>
            <p class="mt-1 text-xs text-slate-500">Keep the inquiry pipeline accurate so other staff know what has been done.</p>
            <form method="POST" action="{{ route('job-board.inquiries.update', $inquiry) }}" class="mt-4 space-y-3">
                @csrf
                @method('PATCH')
                <div>
                    <label class="field-label" for="status">Status</label>
                    <select id="status" name="status" class="field">
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" {{ $inquiry->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="btn-primary w-full justify-center">Save status</button>
            </form>
        </div>

        <div class="card p-5 text-sm">
            <h2 class="card-title">Handling</h2>
            <dl class="mt-4 space-y-3">
                <div class="flex items-start justify-between gap-4">
                    <dt class="text-slate-500">Handled by</dt>
                    <dd class="text-right font-medium text-slate-900">{{ $inquiry->handledBy?->name ?? 'Not yet handled' }}</dd>
                </div>
                <div class="flex items-start justify-between gap-4">
                    <dt class="text-slate-500">Handled at</dt>
                    <dd class="text-right font-medium text-slate-900">{{ $inquiry->handled_at?->format('d M Y, H:i') ?? '—' }}</dd>
                </div>
                <div class="flex items-start justify-between gap-4">
                    <dt class="text-slate-500">Submitted from IP</dt>
                    <dd class="text-right font-medium text-slate-900">{{ $inquiry->ip_address ?? 'Unknown' }}</dd>
                </div>
            </dl>
        </div>

        <div class="card p-5 text-sm text-slate-600">
            <h2 class="card-title">Follow-up checklist</h2>
            <ul class="mt-3 space-y-2">
                <li class="flex gap-2"><span class="text-brand-500">&bull;</span> Confirm the employer and role are genuine.</li>
                <li class="flex gap-2"><span class="text-brand-500">&bull;</span> Contact the candidate and confirm they want to be put forward.</li>
                <li class="flex gap-2"><span class="text-brand-500">&bull;</span> Record that consent before sharing contact details.</li>
                <li class="flex gap-2"><span class="text-brand-500">&bull;</span> Update the status to <strong>Contact shared</strong> or <strong>Closed</strong>.</li>
            </ul>
        </div>
    </div>
</div>
@endsection
