@extends('layouts.app')

@section('title', 'Awareness Program Details')

@section('content')
@php
    $participantsTested = $program->outcomes_count;
    $understoodParticipants = $program->understood_outcomes_count;
    $understoodPercentage = $participantsTested > 0
        ? (int) round(($understoodParticipants / $participantsTested) * 100)
        : 0;
@endphp

<div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">{{ $program->program_name }}</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $program->upazila }}, {{ $program->district }}, {{ $program->division }}</p>
    </div>
    <div class="flex flex-wrap items-center gap-3">
        @can('edit awareness programs')
            <a href="{{ route('awareness-programs.edit', $program) }}" class="btn-primary">Edit</a>
        @endcan
        <a href="{{ route('awareness-programs.index') }}" class="btn-secondary">Back to List</a>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="card p-6 lg:col-span-2">
        <h2 class="card-title mb-4">Program Information</h2>
        <dl class="space-y-3">
            <div class="flex justify-between border-b border-slate-100 py-2">
                <dt class="text-sm text-slate-500">Program name</dt>
                <dd class="text-sm font-medium text-slate-900">{{ $program->program_name }}</dd>
            </div>
            <div class="flex justify-between border-b border-slate-100 py-2">
                <dt class="text-sm text-slate-500">Division</dt>
                <dd class="text-sm font-medium text-slate-900">{{ $program->division }}</dd>
            </div>
            <div class="flex justify-between border-b border-slate-100 py-2">
                <dt class="text-sm text-slate-500">District</dt>
                <dd class="text-sm font-medium text-slate-900">{{ $program->district }}</dd>
            </div>
            <div class="flex justify-between border-b border-slate-100 py-2">
                <dt class="text-sm text-slate-500">Upazila</dt>
                <dd class="text-sm font-medium text-slate-900">{{ $program->upazila }}</dd>
            </div>
            <div class="flex justify-between border-b border-slate-100 py-2">
                <dt class="text-sm text-slate-500">Address</dt>
                <dd class="text-sm font-medium text-slate-900">{{ $program->address ?? 'N/A' }}</dd>
            </div>
            <div class="flex justify-between border-b border-slate-100 py-2">
                <dt class="text-sm text-slate-500">Created by</dt>
                <dd class="text-sm font-medium text-slate-900">{{ $program->createdBy?->name ?? 'N/A' }}</dd>
            </div>
            <div class="flex justify-between py-2">
                <dt class="text-sm text-slate-500">Created at</dt>
                <dd class="text-sm font-medium text-slate-900">{{ $program->created_at?->format('d M Y, h:i A') }}</dd>
            </div>
        </dl>
    </div>

    <div class="card p-6">
        <h2 class="card-title mb-4">Testing Summary</h2>
        <p class="text-sm font-medium text-slate-500">Participants tested</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($participantsTested) }}</p>

        <div class="mt-6 flex items-end justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Understood the message</p>
                <p class="mt-1 text-3xl font-bold tracking-tight text-emerald-600">{{ $understoodPercentage }}%</p>
            </div>
            <p class="text-xs text-slate-500">{{ number_format($understoodParticipants) }} of {{ number_format($participantsTested) }}</p>
        </div>
        <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-slate-100">
            <div class="h-full rounded-full bg-emerald-500" style="width: {{ $understoodPercentage }}%"></div>
        </div>

        <div class="mt-6 flex items-center justify-between rounded-xl bg-rose-50 px-4 py-3">
            <span class="text-sm font-medium text-rose-700">Did not understand</span>
            <span class="text-lg font-bold text-rose-700">{{ number_format($participantsTested - $understoodParticipants) }}</span>
        </div>
    </div>
</div>

<div class="card mt-6 overflow-hidden">
    <div class="card-header">
        <div>
            <h2 class="card-title">Participant Outcomes</h2>
            <p class="mt-0.5 text-xs text-slate-500">Add each participant and record whether they understood the awareness message.</p>
        </div>
        <span class="badge bg-slate-50 text-slate-700 ring-slate-200">{{ number_format($participantsTested) }} tested</span>
    </div>

    @can('edit awareness programs')
        <form method="POST" action="{{ route('awareness-programs.outcomes.store', $program) }}" class="border-b border-slate-100 p-5">
            @csrf
            <div class="grid grid-cols-1 gap-4 md:grid-cols-12">
                <div class="md:col-span-4">
                    <label class="field-label" for="participant_name">Participant name <span class="text-rose-500">*</span></label>
                    <input id="participant_name" type="text" name="participant_name" value="{{ old('participant_name') }}" required class="field" placeholder="e.g. Rahim Uddin">
                    @error('participant_name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-3">
                    <label class="field-label" for="participant_phone">Phone</label>
                    <input id="participant_phone" type="text" name="participant_phone" value="{{ old('participant_phone') }}" class="field" placeholder="01XXXXXXXXX">
                    @error('participant_phone')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-5">
                    <label class="field-label" for="notes">Notes</label>
                    <input id="notes" type="text" name="notes" value="{{ old('notes') }}" class="field" placeholder="Optional observations">
                    @error('notes')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-8">
                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-4 transition hover:border-emerald-200 hover:bg-emerald-50/40">
                        <input type="checkbox" name="understood" value="1" {{ old('understood') ? 'checked' : '' }} class="mt-0.5 h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <span>
                            <span class="block text-sm font-semibold text-slate-800">Participant understood the awareness message</span>
                            <span class="mt-0.5 block text-xs text-slate-500">Leave unchecked when the participant did not understand the message.</span>
                        </span>
                    </label>
                    @error('understood')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div class="flex items-end justify-end md:col-span-4">
                    <button type="submit" class="btn-primary">Add participant outcome</button>
                </div>
            </div>
        </form>
    @endcan

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="table-head px-5 py-3">Participant</th>
                    <th class="table-head px-5 py-3">Phone</th>
                    <th class="table-head px-5 py-3">Result</th>
                    <th class="table-head px-5 py-3">Notes</th>
                    <th class="table-head px-5 py-3">Recorded</th>
                    @can('edit awareness programs')
                        <th class="table-head px-5 py-3 text-right">Actions</th>
                    @endcan
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($program->outcomes as $outcome)
                    <tr class="hover:bg-slate-50">
                        <td class="table-cell font-medium text-slate-900">{{ $outcome->participant_name }}</td>
                        <td class="table-cell">{{ $outcome->participant_phone ?? '—' }}</td>
                        <td class="table-cell">
                            @if($outcome->understood)
                                <span class="badge bg-emerald-50 text-emerald-700 ring-emerald-200">Understood</span>
                            @else
                                <span class="badge bg-rose-50 text-rose-700 ring-rose-200">Not understood</span>
                            @endif
                        </td>
                        <td class="table-cell max-w-xs truncate">{{ $outcome->notes ?? '—' }}</td>
                        <td class="table-cell">{{ $outcome->created_at?->format('d M Y') }}</td>
                        @can('edit awareness programs')
                            <td class="table-cell text-right">
                                <form method="POST" action="{{ route('awareness-programs.outcomes.update', [$program, $outcome]) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="participant_name" value="{{ $outcome->participant_name }}">
                                    <input type="hidden" name="participant_phone" value="{{ $outcome->participant_phone }}">
                                    <input type="hidden" name="notes" value="{{ $outcome->notes }}">
                                    <input type="hidden" name="understood" value="{{ $outcome->understood ? 0 : 1 }}">
                                    <button type="submit" class="text-xs font-semibold text-brand-600 hover:text-brand-700">
                                        {{ $outcome->understood ? 'Mark not understood' : 'Mark understood' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('awareness-programs.outcomes.destroy', [$program, $outcome]) }}" class="ml-3 inline" onsubmit="return confirm('Remove this participant outcome?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-700">Remove</button>
                                </form>
                            </td>
                        @endcan
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()?->can('edit awareness programs') ? 6 : 5 }}" class="px-5 py-12 text-center text-slate-500">
                            <p class="text-lg font-medium text-slate-700">No participant outcomes yet</p>
                            <p class="mt-1 text-sm">Add a participant above to record whether they understood the message.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
