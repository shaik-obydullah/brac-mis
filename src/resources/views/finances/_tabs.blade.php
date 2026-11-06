@php
    $tabs = [
        ['route' => 'finances.index', 'label' => 'Journal Entries', 'active' => request()->routeIs('finances.index', 'finances.create', 'finances.show', 'finances.edit')],
        ['route' => 'finances.accounts.index', 'label' => 'Chart of Accounts', 'active' => request()->routeIs('finances.accounts.*')],
        ['route' => 'finances.trial-balance', 'label' => 'Trial Balance', 'active' => request()->routeIs('finances.trial-balance')],
        ['route' => 'finances.income-statement', 'label' => 'Income Statement', 'active' => request()->routeIs('finances.income-statement')],
        ['route' => 'finances.balance-sheet', 'label' => 'Balance Sheet', 'active' => request()->routeIs('finances.balance-sheet')],
    ];
@endphp

<nav class="mb-6 flex flex-wrap gap-1 border-b border-slate-200">
    @foreach($tabs as $tab)
        <a href="{{ route($tab['route']) }}"
           class="-mb-px border-b-2 px-4 py-2.5 text-sm font-medium transition {{ $tab['active'] ? 'border-brand-600 text-brand-700' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' }}">
            {{ $tab['label'] }}
        </a>
    @endforeach
</nav>
