@extends('layouts.app')

@section('title', 'User Roles')

@section('content')
<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">User Roles</h1>
        <p class="mt-1 text-sm text-slate-500">Assign one or more roles to each system user.</p>
    </div>
    <a href="{{ route('roles.index') }}" class="btn-secondary">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
        Back to roles
    </a>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="table-head px-5 py-3">User</th>
                    <th class="table-head px-5 py-3">Roles</th>
                    <th class="table-head px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($users as $user)
                    <tr class="hover:bg-slate-50">
                        <td class="table-cell">
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-50 text-xs font-bold text-brand-700 ring-1 ring-inset ring-brand-200">
                                    {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->name, 0, 1)) }}
                                </span>
                                <div class="min-w-0">
                                    <div class="font-semibold text-slate-900">{{ $user->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="table-cell">
                            <div class="flex flex-wrap gap-1.5">
                                @forelse($user->roles as $userRole)
                                    <span class="badge {{ $userRole->name === 'super-admin' ? 'bg-violet-50 text-violet-700 ring-violet-200' : 'bg-brand-50 text-brand-700 ring-brand-200' }}">
                                        {{ $userRole->name }}
                                    </span>
                                @empty
                                    <span class="text-xs font-medium text-slate-400">No roles assigned</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="table-cell">
                            <div class="flex items-center justify-end gap-2">
                                @can('manage users')
                                    <a href="{{ route('users.show', $user) }}"
                                       class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50">
                                        Profile
                                    </a>
                                @endcan
                                <button type="button" data-modal-target="assign-roles-{{ $user->id }}"
                                        class="inline-flex items-center gap-1 rounded-lg bg-brand-600 px-2.5 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-brand-700">
                                    Manage roles
                                </button>
                            </div>

                            <dialog id="assign-roles-{{ $user->id }}" class="m-auto w-96 max-w-[calc(100vw-2rem)] rounded-2xl border border-slate-200 p-0 shadow-xl backdrop:bg-slate-950/50">
                                <form action="{{ route('roles.users.assign', $user) }}" method="POST">
                                    @csrf
                                    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">Assign roles</p>
                                            <p class="mt-0.5 text-xs text-slate-500">{{ $user->name }}</p>
                                        </div>
                                        <button type="button" data-modal-close class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600" aria-label="Close">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>

                                    <div class="max-h-72 overflow-y-auto px-5 py-4">
                                        <div class="space-y-1.5">
                                            @foreach($roles as $role)
                                                <label class="flex cursor-pointer items-start gap-2 rounded-lg px-2 py-1.5 hover:bg-slate-50">
                                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                                           class="mt-0.5 rounded border-slate-300 text-brand-600 focus:ring-brand-500"
                                                           {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                                                    <span class="text-sm text-slate-700">{{ $role->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-end gap-2 border-t border-slate-100 px-5 py-4">
                                        <button type="button" data-modal-close class="btn-secondary">Cancel</button>
                                        <button type="submit" class="btn-primary">Save roles</button>
                                    </div>
                                </form>
                            </dialog>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-5 py-14 text-center">
                            <p class="text-base font-semibold text-slate-700">No users found</p>
                            <p class="mt-1 text-sm text-slate-500">Users will appear here once they are created.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="border-t border-slate-100 px-5 py-4">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('[data-modal-target]').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById(button.dataset.modalTarget).showModal();
        });
    });
    document.querySelectorAll('[data-modal-close]').forEach(function (button) {
        button.addEventListener('click', function () {
            button.closest('dialog').close();
        });
    });
    document.querySelectorAll('dialog').forEach(function (dialog) {
        dialog.addEventListener('click', function (event) {
            if (event.target === dialog) {
                dialog.close();
            }
        });
    });
</script>
@endpush
