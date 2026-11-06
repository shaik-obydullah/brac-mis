@extends('layouts.app')

@section('title', 'Chart of Accounts')

@section('content')
<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Chart of Accounts</h1>
        <p class="mt-1 text-sm text-slate-500">Define the accounts used across journal entries and reports.</p>
    </div>
    @can('create finances')
        <a href="{{ route('finances.accounts.create') }}" class="btn-primary">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New account
        </a>
    @endcan
</div>

@include('finances._tabs')

<div class="card mb-6 p-4">
    <form method="GET" action="{{ route('finances.accounts.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="md:col-span-2">
            <label class="field-label" for="search">Search</label>
            <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Code or account name..." class="field">
        </div>
        <div>
            <label class="field-label" for="type">Type</label>
            <select id="type" name="type" class="field">
                <option value="">All types</option>
                @foreach($types as $value => $label)
                    <option value="{{ $value }}" {{ request('type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="btn-primary">Filter</button>
            <a href="{{ route('finances.accounts.index') }}" class="btn-secondary">Clear</a>
        </div>
    </form>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="table-head px-5 py-3 text-left">Code</th>
                    <th class="table-head px-5 py-3 text-left">Account</th>
                    <th class="table-head px-5 py-3 text-left">Type</th>
                    <th class="table-head px-5 py-3 text-left">Normal balance</th>
                    <th class="table-head px-5 py-3 text-right">Current balance</th>
                    <th class="table-head px-5 py-3 text-center">Status</th>
                    <th class="table-head px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($accounts as $account)
                    <tr class="hover:bg-slate-50">
                        <td class="table-cell whitespace-nowrap font-semibold text-slate-900">{{ $account->code }}</td>
                        <td class="table-cell">
                            <a href="{{ route('finances.ledger', $account) }}" class="font-medium text-brand-700 hover:underline">{{ $account->name }}</a>
                            @if($account->description)
                                <p class="max-w-md truncate text-xs text-slate-500">{{ $account->description }}</p>
                            @endif
                        </td>
                        <td class="table-cell">{{ $account->type_label }}</td>
                        <td class="table-cell capitalize">{{ $account->normal_balance }}</td>
                        <td class="table-cell text-right font-semibold text-slate-900">৳ {{ number_format($account->balance, 2) }}</td>
                        <td class="table-cell text-center">
                            <span class="badge {{ $account->is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-slate-200' }}">
                                {{ $account->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="table-cell whitespace-nowrap text-right">
                            <a href="{{ route('finances.ledger', $account) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">Ledger</a>
                            @can('edit finances')
                                <a href="{{ route('finances.accounts.edit', $account) }}" class="ml-3 text-xs font-semibold text-brand-600 hover:text-brand-700">Edit</a>
                            @endcan
                            @can('delete finances')
                                <form action="{{ route('finances.accounts.destroy', $account) }}" method="POST" class="inline" onsubmit="return confirm('Delete account {{ $account->code }} · {{ $account->name }}?')">
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
                            <p class="text-lg font-medium text-slate-700">No accounts found</p>
                            <p class="mt-1 text-sm">Add an account to start recording journal entries.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($accounts, 'links'))
        <div class="border-t border-slate-100 px-5 py-4">
            {{ $accounts->links() }}
        </div>
    @endif
</div>
@endsection
