@extends('layouts.app')

@section('title', 'Designations')

@section('content')
<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Designations</h1>
        <p class="mt-1 text-sm text-slate-500">Manage the job titles used across the organisation.</p>
    </div>
    @can('create designations')
        <a href="{{ route('designations.create') }}" class="btn-primary">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New designation
        </a>
    @endcan
</div>

<div class="card mb-6 p-4">
    <form method="GET" action="{{ route('designations.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="md:col-span-2">
            <label class="field-label" for="name">Search</label>
            <input id="name" type="text" name="name" value="{{ request('name') }}" placeholder="Search by designation name..." class="field">
        </div>
        <div>
            <label class="field-label" for="status">Status</label>
            <select id="status" name="status" class="field">
                <option value="">All statuses</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="btn-primary">Search</button>
            <a href="{{ route('designations.index') }}" class="btn-secondary">Clear</a>
        </div>
    </form>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="table-head px-5 py-3">Name</th>
                    <th class="table-head px-5 py-3">Status</th>
                    <th class="table-head px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($designations as $designation)
                    <tr class="hover:bg-slate-50">
                        <td class="table-cell font-medium text-slate-900">{{ $designation->name }}</td>
                        <td class="table-cell">
                            <span class="badge {{ $designation->status ? 'bg-brand-50 text-brand-700 ring-brand-200' : 'bg-slate-100 text-slate-600 ring-slate-200' }}">
                                {{ $designation->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="table-cell text-right">
                            @can('edit designations')
                                <a href="{{ route('designations.edit', $designation) }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">Edit</a>
                            @endcan
                            @can('delete designations')
                                <form action="{{ route('designations.destroy', $designation) }}" method="POST" class="inline" onsubmit="return confirm('Delete this designation?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ml-3 text-xs font-semibold text-rose-600 hover:text-rose-700">Delete</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-5 py-12 text-center text-slate-500">
                            <p class="text-lg font-medium text-slate-700">No designations found</p>
                            <p class="mt-1 text-sm">Get started by creating a new designation.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($designations, 'links'))
        <div class="border-t border-slate-100 px-5 py-4">
            {{ $designations->links() }}
        </div>
    @endif
</div>
@endsection
