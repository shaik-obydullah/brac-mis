@extends('layouts.app')

@section('title', 'Awareness Programs')

@section('content')
@php
    $divisions = config('locations.divisions', []);
@endphp

<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Awareness Programs</h1>
        <p class="mt-1 text-sm text-slate-500">Manage awareness programs and their branch locations.</p>
    </div>
    @can('create awareness programs')
        <a href="{{ route('awareness-programs.create') }}" class="btn-primary">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New program
        </a>
    @endcan
</div>

<div class="card mb-6 p-4">
    <form method="GET" action="{{ route('awareness-programs.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-5">
        <div class="md:col-span-2">
            <label class="field-label" for="search">Search</label>
            <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Program name or address..." class="field">
        </div>
        <div>
            <label class="field-label" for="filter_division">Division</label>
            <select id="filter_division" name="division" class="field">
                <option value="">All divisions</option>
                @foreach($divisions as $division => $districts)
                    <option value="{{ $division }}" {{ request('division') === $division ? 'selected' : '' }}>{{ $division }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="field-label" for="filter_district">District</label>
            <select id="filter_district" name="district" class="field">
                <option value="">All districts</option>
                @foreach($divisions as $division => $districts)
                    @foreach($districts as $district => $upazilas)
                        <option value="{{ $district }}" data-division="{{ $division }}" {{ request('district') === $district ? 'selected' : '' }}>{{ $district }}</option>
                    @endforeach
                @endforeach
            </select>
        </div>
        <div>
            <label class="field-label" for="filter_upazila">Upazila</label>
            <select id="filter_upazila" name="upazila" class="field">
                <option value="">All upazilas</option>
                @foreach($divisions as $division => $districts)
                    @foreach($districts as $district => $upazilas)
                        @foreach($upazilas as $upazila)
                            <option value="{{ $upazila }}" data-district="{{ $district }}" {{ request('upazila') === $upazila ? 'selected' : '' }}>{{ $upazila }}</option>
                        @endforeach
                    @endforeach
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2 md:col-span-5">
            <button type="submit" class="btn-primary">Search</button>
            <a href="{{ route('awareness-programs.index') }}" class="btn-secondary">Clear</a>
        </div>
    </form>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="table-head px-5 py-3">Program</th>
                    <th class="table-head px-5 py-3">Division</th>
                    <th class="table-head px-5 py-3">District</th>
                    <th class="table-head px-5 py-3">Upazila</th>
                    <th class="table-head px-5 py-3">Address</th>
                    <th class="table-head px-5 py-3">Created By</th>
                    <th class="table-head px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($programs as $program)
                    <tr class="hover:bg-slate-50">
                        <td class="table-cell font-medium text-slate-900">{{ $program->program_name }}</td>
                        <td class="table-cell">{{ $program->division }}</td>
                        <td class="table-cell">{{ $program->district }}</td>
                        <td class="table-cell">{{ $program->upazila }}</td>
                        <td class="table-cell max-w-xs truncate">{{ $program->address ?? '—' }}</td>
                        <td class="table-cell">{{ $program->createdBy?->name ?? 'N/A' }}</td>
                        <td class="table-cell text-right">
                            <a href="{{ route('awareness-programs.show', $program) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">View</a>
                            @can('edit awareness programs')
                                <a href="{{ route('awareness-programs.edit', $program) }}" class="ml-3 text-xs font-semibold text-brand-600 hover:text-brand-700">Edit</a>
                            @endcan
                            @can('delete awareness programs')
                                <form action="{{ route('awareness-programs.destroy', $program) }}" method="POST" class="inline" onsubmit="return confirm('Delete this awareness program?')">
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
                            <p class="text-lg font-medium text-slate-700">No awareness programs found</p>
                            <p class="mt-1 text-sm">Get started by creating a new program.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($programs, 'links'))
        <div class="border-t border-slate-100 px-5 py-4">
            {{ $programs->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const division = document.getElementById('filter_division');
        const district = document.getElementById('filter_district');
        const upazila = document.getElementById('filter_upazila');
        if (!division || !district || !upazila) return;

        const filterUpazilas = () => {
            const selected = district.value;
            let currentIsValid = false;

            Array.from(upazila.options).forEach((option) => {
                if (!option.value) return;
                const matches = !selected || option.dataset.district === selected;
                option.hidden = !matches;
                option.disabled = !matches;
                if (matches && option.value === upazila.value) currentIsValid = true;
            });

            if (!currentIsValid) upazila.value = '';
        };

        const filterDistricts = () => {
            const selected = division.value;
            let currentIsValid = false;

            Array.from(district.options).forEach((option) => {
                if (!option.value) return;
                const matches = !selected || option.dataset.division === selected;
                option.hidden = !matches;
                option.disabled = !matches;
                if (matches && option.value === district.value) currentIsValid = true;
            });

            if (!currentIsValid) district.value = '';
            filterUpazilas();
        };

        division.addEventListener('change', filterDistricts);
        district.addEventListener('change', filterUpazilas);
        filterDistricts();
    })();
</script>
@endpush
