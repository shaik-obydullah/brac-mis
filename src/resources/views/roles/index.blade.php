@extends('layouts.app')

@section('title', 'Roles & Permissions')

@section('content')
<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Roles &amp; Permissions</h1>
        <p class="mt-1 text-sm text-slate-500">Manage system roles and the permissions assigned to each one.</p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('roles.users') }}" class="btn-secondary">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            User roles
        </a>
        <a href="{{ route('roles.create') }}" class="btn-primary">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create role
        </a>
    </div>
</div>

<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
    <div class="card p-5">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Roles</p>
        <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">{{ $stats['roles'] }}</p>
        <p class="mt-1 text-xs text-slate-500">Defined access levels</p>
    </div>
    <div class="card p-5">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Permissions</p>
        <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">{{ $stats['permissions'] }}</p>
        <p class="mt-1 text-xs text-slate-500">Available across all modules</p>
    </div>
    <div class="card p-5">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Assigned users</p>
        <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">{{ $stats['assigned_users'] }}</p>
        <p class="mt-1 text-xs text-slate-500">Users with at least one role</p>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="table-head px-5 py-3">Role</th>
                    <th class="table-head px-5 py-3">Permissions</th>
                    <th class="table-head px-5 py-3">Users</th>
                    <th class="table-head px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($roles as $role)
                    <tr class="hover:bg-slate-50">
                        <td class="table-cell">
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-sm font-bold text-brand-700 ring-1 ring-inset ring-brand-200">
                                    {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($role->name, 0, 1)) }}
                                </span>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="font-semibold text-slate-900">{{ $role->name }}</span>
                                        @if($role->name === 'super-admin')
                                            <span class="badge bg-violet-50 text-violet-700 ring-violet-200">Super admin</span>
                                        @elseif($role->name === 'admin')
                                            <span class="badge bg-sky-50 text-sky-700 ring-sky-200">System role</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="table-cell">
                            <span class="font-semibold text-slate-900">{{ $role->permissions_count }}</span>
                            <span class="text-slate-500">{{ \Illuminate\Support\Str::plural('permission', $role->permissions_count) }}</span>
                        </td>
                        <td class="table-cell">
                            <span class="font-semibold text-slate-900">{{ $role->users_count }}</span>
                            <span class="text-slate-500">{{ \Illuminate\Support\Str::plural('user', $role->users_count) }}</span>
                        </td>
                        <td class="table-cell">
                            <div class="flex items-center justify-end gap-2">
                                @if($role->name === 'super-admin')
                                    <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-1.5 text-xs font-semibold text-slate-500">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        Locked
                                    </span>
                                @else
                                    <a href="{{ route('roles.edit', $role) }}"
                                       class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50">
                                        Edit
                                    </a>
                                    @if(! in_array($role->name, ['super-admin', 'admin'], true) && $role->users_count === 0)
                                        <form action="{{ route('roles.destroy', $role) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Delete the {{ $role->name }} role?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 rounded-lg border border-rose-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-rose-600 shadow-sm transition hover:bg-rose-50">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-14 text-center">
                            <p class="text-base font-semibold text-slate-700">No roles found</p>
                            <p class="mt-1 text-sm text-slate-500">Create your first role to start assigning permissions.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($roles->hasPages())
        <div class="border-t border-slate-100 px-5 py-4">
            {{ $roles->links() }}
        </div>
    @endif
</div>
@endsection
