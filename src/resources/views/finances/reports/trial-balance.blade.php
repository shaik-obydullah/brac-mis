@extends('layouts.app')

@section('title', 'Trial Balance')

@section('content')
<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Trial Balance</h1>
        <p class="mt-1 text-sm text-slate-500">Closing balances for all accounts as at {{ \Illuminate\Support\Carbon::parse($asOf)->format('d M Y') }}.</p>
    </div>
    <button type="button" onclick="window.print()" class="btn-secondary">Print</button>
</div>

@include('finances._tabs')

<div class="card mb-6 p-4">
    <form method="GET" action="{{ route('finances.trial-balance') }}" class="flex flex-col gap-4 sm:flex-row sm:items-end">
        <div class="sm:w-64">
            <label class="field-label" for="as_of">As at</label>
            <input id="as_of" type="date" name="as_of" value="{{ $asOf }}" class="field">
        </div>
        <button type="submit" class="btn-primary">Apply</button>
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
                    <th class="table-head px-5 py-3 text-right">Debit balance</th>
                    <th class="table-head px-5 py-3 text-right">Credit balance</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($accounts as $row)
                    <tr class="hover:bg-slate-50">
                        <td class="table-cell whitespace-nowrap font-semibold text-slate-900">{{ $row['account']->code }}</td>
                        <td class="table-cell">
                            <a href="{{ route('finances.ledger', $row['account']) }}" class="font-medium text-brand-700 hover:underline">{{ $row['account']->name }}</a>
                        </td>
                        <td class="table-cell">{{ $row['account']->type_label }}</td>
                        <td class="table-cell text-right font-semibold text-slate-900">
                            {{ $row['debit_balance'] > 0 ? '৳ '.number_format($row['debit_balance'], 2) : '—' }}
                        </td>
                        <td class="table-cell text-right font-semibold text-slate-900">
                            {{ $row['credit_balance'] > 0 ? '৳ '.number_format($row['credit_balance'], 2) : '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-slate-500">
                            <p class="text-lg font-medium text-slate-700">No posted transactions</p>
                            <p class="mt-1 text-sm">Post journal entries before running the trial balance.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-slate-50">
                <tr>
                    <td class="px-5 py-4 text-right text-sm font-semibold text-slate-700" colspan="3">Totals</td>
                    <td class="px-5 py-4 text-right text-sm font-bold text-brand-700">৳ {{ number_format($totalDebit, 2) }}</td>
                    <td class="px-5 py-4 text-right text-sm font-bold text-sky-700">৳ {{ number_format($totalCredit, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="mt-4 flex items-center justify-between rounded-lg px-4 py-3 text-sm {{ abs($totalDebit - $totalCredit) < 0.005 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
    <span class="font-semibold">{{ abs($totalDebit - $totalCredit) < 0.005 ? 'Trial balance is balanced' : 'Trial balance is out of balance' }}</span>
    <span>Difference: ৳ {{ number_format(abs($totalDebit - $totalCredit), 2) }}</span>
</div>
@endsection
