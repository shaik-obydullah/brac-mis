@extends('layouts.app')

@section('title', 'Balance Sheet')

@section('content')
<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Balance Sheet</h1>
        <p class="mt-1 text-sm text-slate-500">Financial position as at {{ \Illuminate\Support\Carbon::parse($asOf)->format('d M Y') }}.</p>
    </div>
    <button type="button" onclick="window.print()" class="btn-secondary">Print</button>
</div>

@include('finances._tabs')

<div class="card mb-6 p-4">
    <form method="GET" action="{{ route('finances.balance-sheet') }}" class="flex flex-col gap-4 sm:flex-row sm:items-end">
        <div class="sm:w-64">
            <label class="field-label" for="as_of">As at</label>
            <input id="as_of" type="date" name="as_of" value="{{ $asOf }}" class="field">
        </div>
        <button type="submit" class="btn-primary">Apply</button>
    </form>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <div class="card overflow-hidden">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="text-base font-semibold text-slate-900">Assets</h2>
        </div>
        <table class="w-full">
            <tbody class="divide-y divide-slate-100">
                @forelse($assets as $row)
                    <tr class="hover:bg-slate-50">
                        <td class="table-cell px-5 py-3">
                            <span class="font-semibold text-slate-900">{{ $row['account']->code }}</span>
                            <a href="{{ route('finances.ledger', $row['account']) }}" class="ml-2 font-medium text-brand-700 hover:underline">{{ $row['account']->name }}</a>
                        </td>
                        <td class="table-cell px-5 py-3 text-right font-semibold text-slate-900">৳ {{ number_format($row['amount'], 2) }}</td>
                    </tr>
                @empty
                    <tr><td class="px-5 py-6 text-sm text-slate-500">No asset balances.</td></tr>
                @endforelse
            </tbody>
            <tfoot class="bg-slate-50">
                <tr>
                    <td class="px-5 py-3 text-sm font-bold text-slate-900">Total assets</td>
                    <td class="px-5 py-3 text-right text-sm font-bold text-brand-700">৳ {{ number_format($totalAssets, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="space-y-6">
        <div class="card overflow-hidden">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-base font-semibold text-slate-900">Liabilities</h2>
            </div>
            <table class="w-full">
                <tbody class="divide-y divide-slate-100">
                    @forelse($liabilities as $row)
                        <tr class="hover:bg-slate-50">
                            <td class="table-cell px-5 py-3">
                                <span class="font-semibold text-slate-900">{{ $row['account']->code }}</span>
                                <a href="{{ route('finances.ledger', $row['account']) }}" class="ml-2 font-medium text-brand-700 hover:underline">{{ $row['account']->name }}</a>
                            </td>
                            <td class="table-cell px-5 py-3 text-right font-semibold text-slate-900">৳ {{ number_format($row['amount'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td class="px-5 py-6 text-sm text-slate-500">No liability balances.</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-50">
                    <tr>
                        <td class="px-5 py-3 text-sm font-bold text-slate-900">Total liabilities</td>
                        <td class="px-5 py-3 text-right text-sm font-bold text-slate-900">৳ {{ number_format($totalLiabilities, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="card overflow-hidden">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-base font-semibold text-slate-900">Equity</h2>
            </div>
            <table class="w-full">
                <tbody class="divide-y divide-slate-100">
                    @foreach($equity as $row)
                        <tr class="hover:bg-slate-50">
                            <td class="table-cell px-5 py-3">
                                <span class="font-semibold text-slate-900">{{ $row['account']->code }}</span>
                                <a href="{{ route('finances.ledger', $row['account']) }}" class="ml-2 font-medium text-brand-700 hover:underline">{{ $row['account']->name }}</a>
                            </td>
                            <td class="table-cell px-5 py-3 text-right font-semibold text-slate-900">৳ {{ number_format($row['amount'], 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="hover:bg-slate-50">
                        <td class="table-cell px-5 py-3">
                            <span class="font-medium text-slate-700">Current period net {{ $netIncome >= 0 ? 'surplus' : 'deficit' }}</span>
                        </td>
                        <td class="table-cell px-5 py-3 text-right font-semibold {{ $netIncome >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">৳ {{ number_format(abs($netIncome), 2) }}</td>
                    </tr>
                </tbody>
                <tfoot class="bg-slate-50">
                    <tr>
                        <td class="px-5 py-3 text-sm font-bold text-slate-900">Total equity</td>
                        <td class="px-5 py-3 text-right text-sm font-bold text-slate-900">৳ {{ number_format($totalEquity + $netIncome, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<div class="card mt-6 overflow-hidden">
    <table class="w-full">
        <tbody class="divide-y divide-slate-100">
            <tr>
                <td class="px-5 py-4 text-sm font-semibold text-slate-700">Total liabilities and equity</td>
                <td class="px-5 py-4 text-right text-base font-bold text-slate-900">৳ {{ number_format($totalLiabilitiesAndEquity, 2) }}</td>
            </tr>
            <tr>
                <td class="px-5 py-4 text-sm font-semibold text-slate-700">Total assets</td>
                <td class="px-5 py-4 text-right text-base font-bold text-slate-900">৳ {{ number_format($totalAssets, 2) }}</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="mt-4 flex items-center justify-between rounded-lg px-4 py-3 text-sm {{ abs($totalAssets - $totalLiabilitiesAndEquity) < 0.005 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
    <span class="font-semibold">{{ abs($totalAssets - $totalLiabilitiesAndEquity) < 0.005 ? 'Balance sheet is balanced' : 'Balance sheet is out of balance' }}</span>
    <span>Difference: ৳ {{ number_format(abs($totalAssets - $totalLiabilitiesAndEquity), 2) }}</span>
</div>
@endsection
