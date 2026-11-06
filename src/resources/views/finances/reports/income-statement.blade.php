@extends('layouts.app')

@section('title', 'Income Statement')

@section('content')
<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Income Statement</h1>
        <p class="mt-1 text-sm text-slate-500">{{ \Illuminate\Support\Carbon::parse($from)->format('d M Y') }} to {{ \Illuminate\Support\Carbon::parse($to)->format('d M Y') }}</p>
    </div>
    <button type="button" onclick="window.print()" class="btn-secondary">Print</button>
</div>

@include('finances._tabs')

<div class="card mb-6 p-4">
    <form method="GET" action="{{ route('finances.income-statement') }}" class="flex flex-col gap-4 sm:flex-row sm:items-end">
        <div>
            <label class="field-label" for="from">From</label>
            <input id="from" type="date" name="from" value="{{ $from }}" class="field">
        </div>
        <div>
            <label class="field-label" for="to">To</label>
            <input id="to" type="date" name="to" value="{{ $to }}" class="field">
        </div>
        <button type="submit" class="btn-primary">Apply</button>
    </form>
</div>

<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Total income</p>
        <p class="mt-1 text-2xl font-bold tracking-tight text-brand-600">৳ {{ number_format($totalIncome, 2) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Total expenses</p>
        <p class="mt-1 text-2xl font-bold tracking-tight text-rose-600">৳ {{ number_format($totalExpense, 2) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Net {{ $netIncome >= 0 ? 'surplus' : 'deficit' }}</p>
        <p class="mt-1 text-2xl font-bold tracking-tight {{ $netIncome >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">৳ {{ number_format(abs($netIncome), 2) }}</p>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="table-head px-5 py-3 text-left">Code</th>
                    <th class="table-head px-5 py-3 text-left">Account</th>
                    <th class="table-head px-5 py-3 text-right">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr class="bg-slate-50">
                    <td class="px-5 py-2 text-xs font-bold uppercase tracking-wide text-slate-500" colspan="3">Income</td>
                </tr>
                @forelse($income as $row)
                    <tr class="hover:bg-slate-50">
                        <td class="table-cell whitespace-nowrap font-semibold text-slate-900">{{ $row['account']->code }}</td>
                        <td class="table-cell">
                            <a href="{{ route('finances.ledger', $row['account']) }}" class="font-medium text-brand-700 hover:underline">{{ $row['account']->name }}</a>
                        </td>
                        <td class="table-cell text-right font-semibold text-slate-900">৳ {{ number_format($row['amount'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-5 py-4 text-sm text-slate-500">No posted income in this period.</td>
                    </tr>
                @endforelse
                <tr class="bg-white">
                    <td class="px-5 py-3 text-right text-sm font-semibold text-slate-700" colspan="2">Total income</td>
                    <td class="px-5 py-3 text-right text-sm font-bold text-brand-700">৳ {{ number_format($totalIncome, 2) }}</td>
                </tr>

                <tr class="bg-slate-50">
                    <td class="px-5 py-2 text-xs font-bold uppercase tracking-wide text-slate-500" colspan="3">Expenses</td>
                </tr>
                @forelse($expenses as $row)
                    <tr class="hover:bg-slate-50">
                        <td class="table-cell whitespace-nowrap font-semibold text-slate-900">{{ $row['account']->code }}</td>
                        <td class="table-cell">
                            <a href="{{ route('finances.ledger', $row['account']) }}" class="font-medium text-brand-700 hover:underline">{{ $row['account']->name }}</a>
                        </td>
                        <td class="table-cell text-right font-semibold text-slate-900">৳ {{ number_format($row['amount'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-5 py-4 text-sm text-slate-500">No posted expenses in this period.</td>
                    </tr>
                @endforelse
                <tr class="bg-white">
                    <td class="px-5 py-3 text-right text-sm font-semibold text-slate-700" colspan="2">Total expenses</td>
                    <td class="px-5 py-3 text-right text-sm font-bold text-rose-700">৳ {{ number_format($totalExpense, 2) }}</td>
                </tr>
            </tbody>
            <tfoot class="bg-slate-50">
                <tr>
                    <td class="px-5 py-4 text-right text-sm font-bold text-slate-900" colspan="2">Net {{ $netIncome >= 0 ? 'surplus' : 'deficit' }}</td>
                    <td class="px-5 py-4 text-right text-sm font-bold {{ $netIncome >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">৳ {{ number_format(abs($netIncome), 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
