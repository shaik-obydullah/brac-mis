@extends('layouts.app')

@section('title', 'User Roles')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">User Roles</h1>
        <p class="text-gray-600 mt-1">Assign roles to system users</p>
    </div>
    <a href="{{ route('roles.index') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
        Back to Roles
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</th>
                    <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $user->id }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-gray-900">{{ $user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @foreach($user->roles as $userRole)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-1">{{ $userRole->name }}</span>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 text-right">
                            <details class="inline-block relative">
                                <summary class="cursor-pointer text-purple-600 hover:text-purple-800 text-sm font-medium">Manage</summary>
                                <div class="absolute right-0 mt-2 w-72 bg-white border border-gray-200 rounded-xl shadow-lg p-4 z-10 text-left">
                                    <form action="{{ route('roles.users.assign', $user) }}" method="POST">
                                        @csrf
                                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Assign roles</p>
                                        <div class="space-y-1.5">
                                            @foreach($roles as $role)
                                                <label class="flex items-start cursor-pointer">
                                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                                           class="mt-0.5 rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                                                           {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                                                    <span class="ml-2 text-sm text-gray-700">{{ $role->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        <button type="submit" class="mt-3 w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Save</button>
                                    </form>
                                </div>
                            </details>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <p class="text-lg font-medium">No users found</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($users, 'links'))
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
