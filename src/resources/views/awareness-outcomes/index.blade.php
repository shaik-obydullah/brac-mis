@extends('layouts.app')

@section('title', 'Awareness Outcomes')

@section('content')
<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Awareness Outcomes</h1>
        <p class="mt-1 text-sm text-slate-500">Every participant tested across awareness programs and whether they understood the message.</p>
    </div>
    <a href="{{ route('awareness-programs.index') }}" class="btn-secondary">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Awareness programs
    </a>
</div>

<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Participants tested</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totals['total']) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Understood the message</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-emerald-600">{{ $totals['understood_percentage'] }}%</p>
        <p class="mt-1 text-xs text-slate-500">{{ number_format($totals['understood']) }} of {{ number_format($totals['total']) }} participants understood</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Understood</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-emerald-600">{{ number_format($totals['understood']) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Did not understand</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-rose-500">{{ number_format($totals['not_understood']) }}</p>
    </div>
</div>

<div class="card mb-6 p-4">
    <form method="GET" action="{{ route('awareness-outcomes.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="md:col-span-2">
            <label class="field-label" for="search">Search</label>
            <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Participant, phone, notes or program..." class="field">
        </div>
        <div>
            <label class="field-label" for="filter_program">Program</label>
            <select id="filter_program" name="program" class="field">
                <option value="">All programs</option>
                @foreach($programs as $program)
                    <option value="{{ $program->id }}" {{ (string) request('program') === (string) $program->id ? 'selected' : '' }}>{{ $program->program_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="field-label" for="filter_understood">Result</label>
            <select id="filter_understood" name="understood" class="field">
                <option value="">All results</option>
                <option value="1" {{ request('understood') === '1' ? 'selected' : '' }}>Understood</option>
                <option value="0" {{ request('understood') === '0' ? 'selected' : '' }}>Not understood</option>
            </select>
        </div>
        <div class="flex items-end gap-2 md:col-span-4">
            <button type="submit" class="btn-primary">Search</button>
            <a href="{{ route('awareness-outcomes.index') }}" class="btn-secondary">Clear</a>
        </div>
    </form>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="table-head px-5 py-3">Participant</th>
                    <th class="table-head px-5 py-3">Phone</th>
                    <th class="table-head px-5 py-3">Awareness Program</th>
                    <th class="table-head px-5 py-3">Result</th>
                    <th class="table-head px-5 py-3">Notes</th>
                    <th class="table-head px-5 py-3">Recorded</th>
                    <th class="table-head px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($outcomes as $outcome)
                    <tr class="hover:bg-slate-50">
                        <td class="table-cell font-medium text-slate-900">{{ $outcome->participant_name }}</td>
                        <td class="table-cell">{{ $outcome->participant_phone ?? '—' }}</td>
                        <td class="table-cell">
                            @if($outcome->awarenessProgram)
                                <a href="{{ route('awareness-programs.show', $outcome->awarenessProgram) }}" class="font-medium text-brand-600 hover:text-brand-700">{{ $outcome->awarenessProgram->program_name }}</a>
                            @else
                                <span class="text-slate-400">Program removed</span>
                            @endif
                        </td>
                        <td class="table-cell">
                            @if($outcome->understood)
                                <span class="badge bg-emerald-50 text-emerald-700 ring-emerald-200">Understood</span>
                            @else
                                <span class="badge bg-rose-50 text-rose-700 ring-rose-200">Not understood</span>
                            @endif
                        </td>
                        <td class="table-cell max-w-xs truncate">{{ $outcome->notes ?? '—' }}</td>
                        <td class="table-cell">{{ $outcome->created_at?->format('d M Y') }}</td>
                        <td class="table-cell text-right">
                            @if($outcome->awarenessProgram)
                                <a href="{{ route('awareness-programs.show', $outcome->awarenessProgram) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">View program</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-slate-500">
                            <p class="text-lg font-medium text-slate-700">No participant outcomes found</p>
                            <p class="mt-1 text-sm">Open an awareness program and add participants to record their outcomes.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($outcomes, 'links'))
        <div class="border-t border-slate-100 px-5 py-4">
            {{ $outcomes->links() }}
        </div>
    @endif
</div>
@endsection
