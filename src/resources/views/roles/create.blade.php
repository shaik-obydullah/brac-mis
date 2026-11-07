@extends('layouts.app')

@section('title', 'Create Role')

@section('content')
<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Create Role</h1>
        <p class="mt-1 text-sm text-slate-500">Define a new role and choose exactly which permissions it should have.</p>
    </div>
    <a href="{{ route('roles.index') }}" class="btn-secondary">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
        Back to roles
    </a>
</div>

<form action="{{ route('roles.store') }}" method="POST" class="space-y-6">
    @csrf

    <div class="card p-5 sm:p-6">
        <div class="max-w-xl">
            <label class="field-label" for="name">Role name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                   placeholder="e.g. data-entry-officer" class="field">
            <p class="mt-2 text-xs text-slate-500">Use a short, unique name. Spaces are converted to hyphens automatically.</p>
            @error('name')
                <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="card p-5 sm:p-6">
        <div class="mb-5">
            <h2 class="text-base font-semibold text-slate-900">Permissions</h2>
            <p class="mt-1 text-sm text-slate-500">
                Permissions are grouped by module. All {{ count($permissionGroups) }} modules and {{ \App\Support\PermissionCatalog::total() }} permissions are listed below.
            </p>
        </div>

        @error('permissions')
            <p class="mb-4 rounded-xl bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">{{ $message }}</p>
        @enderror

        @include('roles._permissions', [
            'permissionGroups' => $permissionGroups,
            'selectedPermissions' => $selectedPermissions,
        ])
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <button type="submit" class="btn-primary">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Create role
        </button>
        <a href="{{ route('roles.index') }}" class="btn-secondary">Cancel</a>
    </div>
</form>
@endsection
