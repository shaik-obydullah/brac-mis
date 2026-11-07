@extends('layouts.app')

@section('title', 'Journal Entry '.$entry->entry_no)

@section('content')
<div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">{{ $entry->entry_no }}</h1>
            <span class="badge {{ $entry->status === \App\Models\JournalEntry::STATUS_POSTED ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-amber-50 text-amber-700 ring-amber-200' }}">
                {{ $entry->status_label }}
            </span>
        </div>
        <p class="mt-1 text-sm text-slate-500">{{ $entry->entry_date?->format('d M Y') }} · {{ $entry->description ?? 'No description' }}</p>
    </div>
    <div class="flex items-center gap-2">
        @can('edit finances')
            @if($entry->status !== \App\Models\JournalEntry::STATUS_POSTED || auth()->user()?->isSuperAdmin())
                <a href="{{ route('finances.edit', $entry) }}" class="btn-primary">Edit entry</a>
            @endif
        @endcan
        <a href="{{ route('finances.index') }}" class="btn-secondary">Back to journal</a>
    </div>
</div>

@include('finances._tabs')

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="card p-6 lg:col-span-2">
        <h2 class="mb-4 text-base font-semibold text-slate-900">Journal lines</h2>

        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="table-head px-4 py-3 text-left">Account</th>
                        <th class="table-head px-4 py-3 text-left">Description</th>
                        <th class="table-head px-4 py-3 text-right">Debit</th>
                        <th class="table-head px-4 py-3 text-right">Credit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach($entry->lines as $line)
                        <tr>
                            <td class="table-cell px-4 py-3">
                                @if($line->account)
                                    <a href="{{ route('finances.ledger', $line->account) }}" class="font-semibold text-brand-700 hover:underline">
                                        {{ $line->account->code }} · {{ $line->account->name }}
                                    </a>
                                    <p class="text-xs text-slate-500">{{ $line->account->type_label }}</p>
                                @else
                                    <span class="text-slate-400">Deleted account</span>
                                @endif
                            </td>
                            <td class="table-cell px-4 py-3">{{ $line->description ?? '—' }}</td>
                            <td class="table-cell px-4 py-3 text-right font-medium text-slate-900">
                                {{ (float) $line->debit > 0 ? '৳ '.number_format((float) $line->debit, 2) : '—' }}
                            </td>
                            <td class="table-cell px-4 py-3 text-right font-medium text-slate-900">
                                {{ (float) $line->credit > 0 ? '৳ '.number_format((float) $line->credit, 2) : '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50">
                    <tr>
                        <td class="px-4 py-3 text-right text-sm font-semibold text-slate-700" colspan="2">Totals</td>
                        <td class="px-4 py-3 text-right text-sm font-bold text-brand-700">৳ {{ number_format($entry->total_debit, 2) }}</td>
                        <td class="px-4 py-3 text-right text-sm font-bold text-sky-700">৳ {{ number_format($entry->total_credit, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-4 flex items-center justify-between rounded-lg px-4 py-3 text-sm {{ $entry->is_balanced ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
            <span class="font-semibold">{{ $entry->is_balanced ? 'Entry is balanced' : 'Entry is out of balance' }}</span>
            <span>Difference: ৳ {{ number_format(abs($entry->total_debit - $entry->total_credit), 2) }}</span>
        </div>
    </div>

    <div class="space-y-6">
        <div class="card p-6">
            <h2 class="mb-4 text-base font-semibold text-slate-900">Entry details</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                    <dt class="text-slate-500">Entry no</dt>
                    <dd class="font-semibold text-slate-900">{{ $entry->entry_no }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                    <dt class="text-slate-500">Date</dt>
                    <dd class="font-medium text-slate-900">{{ $entry->entry_date?->format('d M Y') }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                    <dt class="text-slate-500">Reference</dt>
                    <dd class="font-medium text-slate-900">{{ $entry->reference ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                    <dt class="text-slate-500">Branch</dt>
                    <dd class="font-medium text-slate-900">{{ $entry->branch?->name ?? 'Organisation-wide' }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                    <dt class="text-slate-500">Status</dt>
                    <dd class="font-medium text-slate-900">{{ $entry->status_label }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                    <dt class="text-slate-500">Created by</dt>
                    <dd class="font-medium text-slate-900">{{ $entry->createdBy?->name ?? 'System' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Posted at</dt>
                    <dd class="font-medium text-slate-900">{{ $entry->posted_at?->format('d M Y H:i') ?? 'Not posted' }}</dd>
                </div>
            </dl>
        </div>

        @if($entry->description)
            <div class="card p-6">
                <h2 class="mb-2 text-base font-semibold text-slate-900">Description</h2>
                <p class="whitespace-pre-line text-sm text-slate-600">{{ $entry->description }}</p>
            </div>
        @endif

        @if($entry->status !== \App\Models\JournalEntry::STATUS_POSTED || auth()->user()?->isSuperAdmin())
            @can('delete finances')
                <div class="card border border-rose-200 p-6">
                    <h2 class="mb-2 text-base font-semibold text-rose-700">Delete entry</h2>
                    <p class="mb-4 text-sm text-slate-600">This permanently removes the journal entry and all of its lines.</p>
                    <form action="{{ route('finances.destroy', $entry) }}" method="POST" onsubmit="return confirm('Delete journal entry {{ $entry->entry_no }}? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">Delete entry</button>
                    </form>
                </div>
            @endcan
        @endif
    </div>
</div>
@endsection
