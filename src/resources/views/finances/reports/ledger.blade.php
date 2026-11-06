@extends('layouts.app')

@section('title', 'Ledger · '.$account->name)

@section('content')
<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">General Ledger</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $account->code }} · {{ $account->name }} ({{ $account->type_label }})</p>
    </div>
    <button type="button" onclick="window.print()" class="btn-secondary">Print</button>
</div>

@include('finances._tabs')

<div class="card mb-6 p-4">
    <form method="GET" action="{{ route('finances.ledger', $account) }}" class="grid grid-cols-1 gap-4 md:grid-cols-5">
        <div class="md:col-span-2">
            <label class="field-label" for="account">Account</label>
            <select id="account" name="account" class="field" onchange="window.location.href='{{ url('/finances/ledger') }}/' + this.value">
                @foreach($accounts as $option)
                    <option value="{{ $option->id }}" {{ $option->id === $account->id ? 'selected' : '' }}>{{ $option->code }} · {{ $option->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="field-label" for="from">From</label>
            <input id="from" type="date" name="from" value="{{ $from }}" class="field">
        </div>
        <div>
            <label class="field-label" for="to">To</label>
            <input id="to" type="date" name="to" value="{{ $to }}" class="field">
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="btn-primary">Apply</button>
            <a href="{{ route('finances.accounts.index') }}" class="btn-secondary">Accounts</a>
        </div>
    </form>
</div>

<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Opening balance</p>
        <p class="mt-1 text-2xl font-bold tracking-tight text-slate-900">৳ {{ number_format($opening, 2) }}</p>
        <p class="text-xs text-slate-500">As at {{ \Illuminate\Support\Carbon::parse($from)->format('d M Y') }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Period movement</p>
        <p class="mt-1 text-2xl font-bold tracking-tight {{ $closing >= $opening ? 'text-brand-600' : 'text-rose-600' }}">৳ {{ number_format($closing - $opening, 2) }}</p>
        <p class="text-xs text-slate-500">Debit ৳ {{ number_format($totalDebit, 2) }} · Credit ৳ {{ number_format($totalCredit, 2) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Closing balance</p>
        <p class="mt-1 text-2xl font-bold tracking-tight text-slate-900">৳ {{ number_format($closing, 2) }}</p>
        <p class="text-xs text-slate-500">As at {{ \Illuminate\Support\Carbon::parse($to)->format('d M Y') }}</p>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="table-head px-5 py-3 text-left">Date</th>
                    <th class="table-head px-5 py-3 text-left">Entry no</th>
                    <th class="table-head px-5 py-3 text-left">Description</th>
                    <th class="table-head px-5 py-3 text-left">Reference</th>
                    <th class="table-head px-5 py-3 text-right">Debit</th>
                    <th class="table-head px-5 py-3 text-right">Credit</th>
                    <th class="table-head px-5 py-3 text-right">Balance</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($rows as $row)
                    <tr class="hover:bg-slate-50">
                        <td class="table-cell whitespace-nowrap">{{ $row['line']->entry->entry_date?->format('d M Y') }}</td>
                        <td class="table-cell whitespace-nowrap">
                            <a href="{{ route('finances.show', $row['line']->entry) }}" class="font-semibold text-brand-700 hover:underline">{{ $row['line']->entry->entry_no }}</a>
                        </td>
                        <td class="table-cell">{{ $row['line']->description ?: ($row['line']->entry->description ?? '—') }}</td>
                        <td class="table-cell">{{ $row['line']->entry->reference ?? '—' }}</td>
                        <td class="table-cell text-right">{{ (float) $row['line']->debit > 0 ? '৳ '.number_format((float) $row['line']->debit, 2) : '—' }}</td>
                        <td class="table-cell text-right">{{ (float) $row['line']->credit > 0 ? '৳ '.number_format((float) $row['line']->credit, 2) : '—' }}</td>
                        <td class="table-cell text-right font-semibold text-slate-900">৳ {{ number_format($row['balance'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-slate-500">
                            <p class="text-lg font-medium text-slate-700">No posted activity</p>
                            <p class="mt-1 text-sm">There are no posted journal lines for this account in the selected period.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-slate-50">
                <tr>
                    <td class="px-5 py-3 text-right text-sm font-semibold text-slate-700" colspan="4">Period totals</td>
                    <td class="px-5 py-3 text-right text-sm font-bold text-brand-700">৳ {{ number_format($totalDebit, 2) }}</td>
                    <td class="px-5 py-3 text-right text-sm font-bold text-sky-700">৳ {{ number_format($totalCredit, 2) }}</td>
                    <td class="px-5 py-3 text-right text-sm font-bold text-slate-900">৳ {{ number_format($closing, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
