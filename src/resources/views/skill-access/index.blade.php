@extends('layouts.app')

@section('title', 'Skill Access')

@section('content')
@php
    $proficiencyStyles = [
        'beginner' => 'bg-slate-50 text-slate-700 ring-slate-200',
        'intermediate' => 'bg-sky-50 text-sky-700 ring-sky-200',
        'advanced' => 'bg-brand-50 text-brand-700 ring-brand-200',
        'expert' => 'bg-violet-50 text-violet-700 ring-violet-200',
    ];
@endphp

<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Skill Access</h1>
        <p class="mt-1 text-sm text-slate-500">Select a user from the user module and manage their skills.</p>
    </div>
    <a href="{{ route('users.index') }}" class="btn-secondary">Go to Users</a>
</div>

<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Total users</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totals['users']) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Users with skills</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-brand-600">{{ number_format($totals['with_skills']) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Total skills</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totals['skills']) }}</p>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="card lg:col-span-1">
        <div class="card-header">
            <div>
                <h2 class="card-title">Select user</h2>
                <p class="mt-0.5 text-xs text-slate-500">Choose a user to manage their skills</p>
            </div>
        </div>
        <div class="border-b border-slate-100 p-4">
            <form method="GET" action="{{ route('skill-access.index') }}">
                @if(request('user_id'))
                    <input type="hidden" name="user_id" value="{{ request('user_id') }}">
                @endif
                <div class="flex gap-2">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search name, email, phone..." class="field">
                    <button type="submit" class="btn-primary shrink-0">Search</button>
                </div>
            </form>
        </div>
        <div class="max-h-[32rem] divide-y divide-slate-100 overflow-y-auto">
            @forelse($users as $user)
                @php($isSelected = $selectedUser && $selectedUser->id === $user->id)
                <a href="{{ route('skill-access.index', array_filter(['user_id' => $user->id, 'search' => $search])) }}"
                   class="flex items-center gap-3 px-4 py-3 transition {{ $isSelected ? 'bg-brand-50' : 'hover:bg-slate-50' }}">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-xs font-bold {{ $isSelected ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-600' }}">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-semibold {{ $isSelected ? 'text-brand-700' : 'text-slate-900' }}">{{ $user->name }}</span>
                        <span class="block truncate text-xs text-slate-500">{{ $user->email }}</span>
                    </span>
                    <span class="badge {{ $isSelected ? 'bg-brand-100 text-brand-700 ring-brand-200' : 'bg-slate-50 text-slate-600 ring-slate-200' }}">
                        {{ $user->skills_count }} {{ \Illuminate\Support\Str::plural('skill', $user->skills_count) }}
                    </span>
                </a>
            @empty
                <p class="px-4 py-10 text-center text-sm text-slate-500">No users match your search.</p>
            @endforelse
        </div>
    </div>

    <div class="space-y-6 lg:col-span-2">
        @if($selectedUser)
            <div class="card p-5">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                    <div class="flex items-center gap-4">
                        <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-brand-600 text-lg font-bold text-white">
                            {{ strtoupper(substr($selectedUser->name, 0, 2)) }}
                        </span>
                        <div>
                            <h2 class="text-xl font-bold tracking-tight text-slate-900">{{ $selectedUser->name }}</h2>
                            <p class="text-sm text-slate-500">{{ $selectedUser->email }}</p>
                            <p class="text-xs text-slate-500">{{ $selectedUser->phone ?? 'No phone' }} &middot; {{ $selectedUser->branch->name ?? 'No branch' }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-1">
                        @forelse($selectedUser->roles as $role)
                            <span class="badge bg-slate-50 text-slate-700 ring-slate-200">{{ $role->name }}</span>
                        @empty
                            <span class="text-xs text-slate-500">No roles assigned</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Add skill</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Assign a new skill to {{ $selectedUser->name }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('skill-access.skills.store', $selectedUser) }}" class="p-5">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="field-label" for="skill_name">Skill name</label>
                            <input id="skill_name" type="text" name="skill_name" value="{{ old('skill_name') }}" class="field" required>
                            @error('skill_name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="field-label" for="proficiency">Proficiency</label>
                            <select id="proficiency" name="proficiency" class="field" required>
                                @foreach(['beginner', 'intermediate', 'advanced', 'expert'] as $level)
                                    <option value="{{ $level }}" {{ old('proficiency') === $level ? 'selected' : '' }}>{{ ucfirst($level) }}</option>
                                @endforeach
                            </select>
                            @error('proficiency')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="field-label" for="years_of_experience">Years of experience</label>
                            <input id="years_of_experience" type="number" name="years_of_experience" value="{{ old('years_of_experience') }}" min="0" max="60" class="field">
                            @error('years_of_experience')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="field-label" for="certification">Certification</label>
                            <input id="certification" type="text" name="certification" value="{{ old('certification') }}" class="field">
                            @error('certification')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="mt-5 flex items-center gap-3">
                        <button type="submit" class="btn-primary">Add Skill</button>
                        <span class="text-xs text-slate-500">{{ $selectedUser->skills->count() }} {{ \Illuminate\Support\Str::plural('skill', $selectedUser->skills->count()) }} currently assigned</span>
                    </div>
                </form>
            </div>

            <div class="card overflow-hidden">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Assigned skills</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Skills currently linked to {{ $selectedUser->name }}</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="table-head px-5 py-3">Skill</th>
                                <th class="table-head px-5 py-3">Proficiency</th>
                                <th class="table-head px-5 py-3">Experience</th>
                                <th class="table-head px-5 py-3">Certification</th>
                                <th class="table-head px-5 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($selectedUser->skills as $skill)
                                <tr class="hover:bg-slate-50">
                                    <td class="table-cell font-medium text-slate-900">{{ $skill->skill_name }}</td>
                                    <td class="table-cell">
                                        <span class="badge {{ $proficiencyStyles[$skill->proficiency] ?? 'bg-slate-50 text-slate-700 ring-slate-200' }}">
                                            {{ ucfirst($skill->proficiency) }}
                                        </span>
                                    </td>
                                    <td class="table-cell">{{ $skill->years_of_experience !== null ? $skill->years_of_experience . ' years' : 'N/A' }}</td>
                                    <td class="table-cell">{{ $skill->certification ?? 'N/A' }}</td>
                                    <td class="table-cell text-right">
                                        <form method="POST" action="{{ route('skill-access.skills.destroy', [$selectedUser, $skill]) }}" onsubmit="return confirm('Remove this skill?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-700">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center text-slate-500">
                                        <p class="text-lg font-medium text-slate-700">No skills assigned</p>
                                        <p class="mt-1 text-sm">Use the form above to add the first skill.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="card flex min-h-[24rem] flex-col items-center justify-center p-10 text-center">
                <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9.663 17h4.674M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                </span>
                <h2 class="mt-4 text-lg font-semibold text-slate-800">Select a user</h2>
                <p class="mt-1 max-w-sm text-sm text-slate-500">Pick a user from the list on the left to view and manage their skills.</p>
            </div>
        @endif
    </div>
</div>
@endsection
