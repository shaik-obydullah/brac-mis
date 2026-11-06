@extends('layouts.app')

@section('title', 'History Analysis')

@section('content')
@php
    $categoryStyles = [
        'immediate' => [
            'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
            'iconClass' => 'bg-brand-50 text-brand-600',
            'bar' => 'bg-brand-500',
            'badge' => 'bg-brand-50 text-brand-700 ring-brand-200',
        ],
        'mental-psychological' => [
            'icon' => 'M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'iconClass' => 'bg-violet-50 text-violet-600',
            'bar' => 'bg-violet-500',
            'badge' => 'bg-violet-50 text-violet-700 ring-violet-200',
        ],
        'physical' => [
            'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
            'iconClass' => 'bg-rose-50 text-rose-600',
            'bar' => 'bg-rose-500',
            'badge' => 'bg-rose-50 text-rose-700 ring-rose-200',
        ],
        'financial' => [
            'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
            'iconClass' => 'bg-blue-50 text-blue-600',
            'bar' => 'bg-blue-500',
            'badge' => 'bg-blue-50 text-blue-700 ring-blue-200',
        ],
    ];

    $typeBars = [
        'food' => 'bg-amber-500',
        'bus_ticket' => 'bg-sky-500',
        'shelter' => 'bg-emerald-500',
        'mental_psychological' => 'bg-violet-500',
        'physical' => 'bg-rose-500',
        'financial' => 'bg-blue-500',
    ];

    $maxMonth = max(1, (int) $months->max('total'));
    $maxType = max(1, (int) $typeRows->max('total'));
    $filterQuery = array_filter([
        'from' => request('from'),
        'to' => request('to'),
        'status' => request('status'),
    ], fn ($value) => $value !== null && $value !== '');
@endphp

<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">History Analysis</h1>
        <p class="mt-1 text-sm text-slate-500">Analyse the four support types across time, status and support category.</p>
    </div>
    <a href="{{ route('support.index', ['category' => 'immediate']) }}" class="btn-secondary">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Support records
    </a>
</div>

<div class="card mb-6 p-4">
    <form method="GET" action="{{ route('support.history') }}" class="grid grid-cols-1 gap-4 md:grid-cols-6">
        <div>
            <label class="field-label" for="category">Support type</label>
            <select id="category" name="category" class="field">
                <option value="">All support types</option>
                @foreach($categories as $value => $config)
                    <option value="{{ $value }}" {{ $selectedCategory === $value ? 'selected' : '' }}>{{ $config['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="field-label" for="type">Category</label>
            <select id="type" name="type" class="field">
                <option value="">All categories</option>
                @foreach($typeOptions as $value => $label)
                    <option value="{{ $value }}" {{ $selectedType === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="field-label" for="status">Status</label>
            <select id="status" name="status" class="field">
                <option value="">All statuses</option>
                @foreach($statuses as $value => $label)
                    <option value="{{ $value }}" {{ $selectedStatus === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="field-label" for="from">From</label>
            <input id="from" type="date" name="from" value="{{ request('from') }}" class="field">
        </div>
        <div>
            <label class="field-label" for="to">To</label>
            <input id="to" type="date" name="to" value="{{ request('to') }}" class="field">
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="btn-primary">Analyse</button>
            <a href="{{ route('support.history') }}" class="btn-secondary">Clear</a>
        </div>
    </form>
</div>

<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @foreach($categories as $key => $config)
        @php($style = $categoryStyles[$key])
        @php($summary = $categorySummary[$key])
        <a href="{{ route('support.history', array_merge($filterQuery, ['category' => $key])) }}"
           class="card p-5 transition hover:border-slate-300 hover:shadow-md {{ $selectedCategory === $key ? 'border-brand-400 ring-2 ring-brand-100' : '' }}">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-slate-500">{{ $config['label'] }}</p>
                    <p class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($summary['records']) }}</p>
                    <p class="mt-1 text-xs text-slate-500">৳ {{ number_format($summary['amount'], 2) }} total</p>
                </div>
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $style['iconClass'] }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="{{ $style['icon'] }}"/></svg>
                </span>
            </div>
            <div class="mt-4 flex items-center gap-3 text-xs">
                <span class="badge bg-brand-50 text-brand-700 ring-brand-200">{{ $summary['provided'] }} provided</span>
                <span class="badge bg-amber-50 text-amber-700 ring-amber-200">{{ $summary['pending'] }} pending</span>
            </div>
        </a>
    @endforeach
</div>

<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Filtered records</p>
        <p class="mt-1 text-2xl font-bold tracking-tight text-slate-900">{{ number_format($totals['records']) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Total support value</p>
        <p class="mt-1 text-2xl font-bold tracking-tight text-slate-900">৳ {{ number_format($totals['amount'], 2) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Provided</p>
        <p class="mt-1 text-2xl font-bold tracking-tight text-brand-600">{{ number_format($totals['provided']) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-sm font-medium text-slate-500">Pending / cancelled</p>
        <p class="mt-1 text-2xl font-bold tracking-tight text-amber-600">{{ number_format($totals['pending']) }} <span class="text-base font-semibold text-slate-400">/ {{ number_format($totals['cancelled']) }}</span></p>
    </div>
</div>

<div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="card lg:col-span-2">
        <div class="card-header">
            <div>
                <h2 class="card-title">Category breakdown</h2>
                <p class="mt-0.5 text-xs text-slate-500">Records and value by individual support category</p>
            </div>
        </div>
        <div class="space-y-5 p-5">
            @forelse($typeOptions as $value => $label)
                @php($row = $typeRows[$value] ?? null)
                @php($count = (int) ($row->total ?? 0))
                @php($amount = (float) ($row->amount ?? 0))
                <div>
                    <div class="flex items-center justify-between gap-3 text-sm">
                        <span class="font-medium text-slate-700">{{ $label }}</span>
                        <span class="text-slate-500">{{ $count }} {{ \Illuminate\Support\Str::plural('record', $count) }}</span>
                    </div>
                    <div class="mt-2 flex items-center gap-3">
                        <div class="h-2 flex-1 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full {{ $typeBars[$value] ?? 'bg-slate-400' }}" style="width: {{ ($count / $maxType) * 100 }}%"></div>
                        </div>
                        <span class="w-28 text-right text-xs font-semibold text-slate-700">৳ {{ number_format($amount, 2) }}</span>
                    </div>
                </div>
            @empty
                <p class="py-8 text-center text-sm text-slate-500">No support categories available.</p>
            @endforelse
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">Status breakdown</h2>
                <p class="mt-0.5 text-xs text-slate-500">Current state of filtered records</p>
            </div>
        </div>
        <div class="space-y-5 p-5">
            @foreach($statuses as $value => $label)
                @php($count = (int) ($statusRows[$value] ?? 0))
                @php($percent = $totals['records'] > 0 ? round(($count / $totals['records']) * 100) : 0)
                @php($statusBar = ['provided' => 'bg-brand-500', 'pending' => 'bg-amber-500', 'cancelled' => 'bg-rose-500'][$value] ?? 'bg-slate-400')
                <div>
                    <div class="flex items-center justify-between gap-3 text-sm">
                        <span class="font-medium text-slate-700">{{ $label }}</span>
                        <span class="text-slate-500">{{ $count }} · {{ $percent }}%</span>
                    </div>
                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full {{ $statusBar }}" style="width: {{ $percent }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="card lg:col-span-2">
        <div class="card-header">
            <div>
                <h2 class="card-title">Monthly history</h2>
                <p class="mt-0.5 text-xs text-slate-500">Support records by category over the last 12 months</p>
            </div>
        </div>
        <div class="p-5">
            <div class="mb-4 flex flex-wrap items-center gap-x-4 gap-y-2">
                @foreach($categories as $key => $config)
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-600">
                        <span class="h-2.5 w-2.5 rounded-full {{ $categoryStyles[$key]['bar'] }}"></span>
                        {{ $config['label'] }}
                    </span>
                @endforeach
            </div>
            <div class="space-y-3">
                @foreach($months as $month)
                    <div class="flex items-center gap-3">
                        <span class="w-16 shrink-0 text-xs font-medium text-slate-500">{{ $month['label'] }}</span>
                        <div class="flex h-2.5 flex-1 overflow-hidden rounded-full bg-slate-100">
                            @foreach($categories as $key => $config)
                                @php($count = (int) ($month['counts'][$key] ?? 0))
                                @if($count > 0)
                                    <div class="{{ $categoryStyles[$key]['bar'] }}" style="width: {{ ($count / $maxMonth) * 100 }}%" title="{{ $config['label'] }}: {{ $count }}"></div>
                                @endif
                            @endforeach
                        </div>
                        <span class="w-10 shrink-0 text-right text-xs font-semibold text-slate-700">{{ $month['total'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">Yearly history</h2>
                <p class="mt-0.5 text-xs text-slate-500">All-time totals by year</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="table-head px-5 py-3">Year</th>
                        <th class="table-head px-5 py-3 text-right">Records</th>
                        <th class="table-head px-5 py-3 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($yearlyHistory as $year)
                        <tr class="hover:bg-slate-50">
                            <td class="table-cell font-semibold text-slate-900">{{ $year->year }}</td>
                            <td class="table-cell text-right">{{ number_format($year->total) }}</td>
                            <td class="table-cell text-right">৳ {{ number_format((float) $year->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-10 text-center text-sm text-slate-500">No support history recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="card-header">
        <div>
            <h2 class="card-title">Support records</h2>
            <p class="mt-0.5 text-xs text-slate-500">History matching the selected filters</p>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="table-head px-5 py-3">Date</th>
                    <th class="table-head px-5 py-3">Support type</th>
                    <th class="table-head px-5 py-3">Category</th>
                    <th class="table-head px-5 py-3">Recipient</th>
                    <th class="table-head px-5 py-3 text-right">Amount</th>
                    <th class="table-head px-5 py-3">Status</th>
                    <th class="table-head px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($supports as $support)
                    @php($supportCategory = \App\Models\ImmediateSupport::categoryForType($support->support_type))
                    @php($categoryStyle = $categoryStyles[$supportCategory] ?? null)
                    <tr class="hover:bg-slate-50">
                        <td class="table-cell">{{ $support->support_date?->format('d M Y') }}</td>
                        <td class="table-cell">
                            <span class="inline-flex items-center gap-1.5 font-medium text-slate-900">
                                <span class="h-2 w-2 rounded-full {{ $typeBars[$support->support_type] ?? 'bg-slate-400' }}"></span>
                                {{ $support->type_label }}
                            </span>
                        </td>
                        <td class="table-cell">
                            @if($categoryStyle)
                                <span class="badge {{ $categoryStyle['badge'] }}">{{ \App\Models\ImmediateSupport::CATEGORIES[$supportCategory]['label'] }}</span>
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="table-cell">
                            @if($support->beneficiary)
                                <a href="{{ route('beneficiaries.show', $support->beneficiary) }}" class="font-medium text-slate-900 hover:text-brand-600">{{ $support->beneficiary->name }}</a>
                                <p class="text-xs text-slate-500">{{ $support->beneficiary->brac_id ?? $support->recipient_phone ?? 'N/A' }}</p>
                            @elseif($support->returnee)
                                <a href="{{ route('returnees.show', $support->returnee) }}" class="font-medium text-slate-900 hover:text-brand-600">Returnee #{{ $support->returnee->id }}{{ $support->returnee->beneficiary ? ' - ' . $support->returnee->beneficiary->name : '' }}</a>
                                <p class="text-xs text-slate-500">{{ $support->recipient_phone ?? 'N/A' }}</p>
                            @else
                                <span class="font-medium text-slate-900">{{ $support->recipient_name ?? 'N/A' }}</span>
                                <p class="text-xs text-slate-500">{{ $support->recipient_phone ?? 'N/A' }}</p>
                            @endif
                        </td>
                        <td class="table-cell text-right font-semibold text-slate-900">
                            {{ $support->amount !== null ? '৳ ' . number_format((float) $support->amount, 2) : 'N/A' }}
                        </td>
                        <td class="table-cell">
                            <span class="badge {{ $support->status === 'provided' ? 'bg-brand-50 text-brand-700 ring-brand-200' : ($support->status === 'cancelled' ? 'bg-rose-50 text-rose-700 ring-rose-200' : 'bg-amber-50 text-amber-700 ring-amber-200') }}">
                                {{ ucfirst($support->status) }}
                            </span>
                        </td>
                        <td class="table-cell text-right">
                            @if($supportCategory)
                                <a href="{{ route('support.show', ['category' => $supportCategory, 'immediateSupport' => $support]) }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">View</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-slate-500">
                            <p class="text-lg font-medium text-slate-700">No support history found</p>
                            <p class="mt-1 text-sm">Try adjusting the filters or record a support entry.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($supports, 'links'))
        <div class="border-t border-slate-100 px-5 py-4">
            {{ $supports->links() }}
        </div>
    @endif
</div>
@endsection
