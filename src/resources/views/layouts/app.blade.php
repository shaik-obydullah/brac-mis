<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f172a">
    <title>@yield('title', 'BRAC MIS') · BRAC MIS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 font-sans text-slate-900 antialiased">
    <div id="sidebar-overlay" class="fixed inset-0 z-40 hidden bg-slate-950/60 backdrop-blur-sm lg:hidden"></div>

    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col bg-slate-950 transition-transform duration-300 ease-in-out lg:translate-x-0">
        <div class="flex h-16 shrink-0 items-center gap-3 border-b border-white/10 px-5">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-500 text-sm font-black text-slate-950 shadow-lg shadow-brand-500/20">B</span>
            <div class="min-w-0">
                <p class="truncate text-sm font-bold tracking-tight text-white">BRAC <span class="text-brand-400">MIS</span></p>
                <p class="truncate text-[11px] text-slate-400">Migration &amp; Reintegration</p>
            </div>
            <button type="button" data-sidebar-close class="ml-auto rounded-lg p-1.5 text-slate-400 hover:bg-white/5 hover:text-white lg:hidden" aria-label="Close navigation">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <nav class="scrollbar-thin flex-1 overflow-y-auto px-3 pb-6">
            <p class="nav-section">Overview</p>
            <a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'nav-link-active' : '' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Dashboard
            </a>

            <p class="nav-section">Programs</p>
            <a href="{{ url('/beneficiaries') }}" class="nav-link {{ request()->is('beneficiaries*') ? 'nav-link-active' : '' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Beneficiaries
            </a>
            <a href="{{ url('/migrants') }}" class="nav-link {{ request()->is('migrants*') ? 'nav-link-active' : '' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                Migrants
            </a>
            <a href="{{ url('/returnees') }}" class="nav-link {{ request()->is('returnees*') ? 'nav-link-active' : '' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Returnees
            </a>

            @can('view immediate support')
                @php
                    $supportLinks = [
                        'mental-psychological' => [
                            'label' => 'Mental / Psychological Support',
                            'icon' => 'M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                        ],
                        'physical' => [
                            'label' => 'Physical Support',
                            'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
                        ],
                        'financial' => [
                            'label' => 'Financial Support',
                            'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
                        ],
                    ];
                @endphp
                <p class="nav-section">Support</p>
                <a href="{{ url('/support/immediate') }}" class="nav-link {{ request()->is('support/immediate*') ? 'nav-link-active' : '' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    Immediate Support
                </a>
                @foreach($supportLinks as $category => $supportLink)
                    <a href="{{ url('/support/' . $category) }}"
                       class="nav-link {{ request()->is('support/' . $category . '*') ? 'nav-link-active' : '' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="{{ $supportLink['icon'] }}"/></svg>
                        {{ $supportLink['label'] }}
                    </a>
                @endforeach
                <a href="{{ url('/support/history-analysis') }}" class="nav-link {{ request()->is('support/history-analysis*') ? 'nav-link-active' : '' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    History Analysis
                </a>
                <a href="{{ url('/support-follow-ups') }}" class="nav-link {{ request()->is('support-follow-ups*') ? 'nav-link-active' : '' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    Support Follow-ups
                </a>
            @endcan

            <p class="nav-section">Finance</p>
            <a href="{{ url('/finances') }}" class="nav-link {{ request()->is('finances*') ? 'nav-link-active' : '' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Finance
            </a>

            <p class="nav-section">Organization</p>
            <a href="{{ url('/branches') }}" class="nav-link {{ request()->is('branches*') ? 'nav-link-active' : '' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Branches
            </a>
            @can('view awareness programs')
                <a href="{{ url('/awareness-programs') }}" class="nav-link {{ request()->is('awareness-programs*') ? 'nav-link-active' : '' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    Awareness Program
                </a>
            @endcan
            @canany(['view staff', 'view designations', 'view staff incentives'])
                <p class="nav-section">HRM</p>
                @can('view staff')
                    <a href="{{ url('/hrm') }}" class="nav-link {{ request()->is('hrm*') ? 'nav-link-active' : '' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                        HRM Overview
                    </a>
                    <a href="{{ url('/staff') }}" class="nav-link {{ request()->is('staff*') ? 'nav-link-active' : '' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Staff
                    </a>
                @endcan
                @can('view designations')
                    <a href="{{ url('/designations') }}" class="nav-link {{ request()->is('designations*') ? 'nav-link-active' : '' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Designations
                    </a>
                @endcan
                @can('view staff incentives')
                    <a href="{{ url('/staff-incentives') }}" class="nav-link {{ request()->is('staff-incentives*') ? 'nav-link-active' : '' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Staff Incentives
                    </a>
                @endcan
            @endcanany

            <p class="nav-section">Insights</p>
            <a href="{{ url('/reports') }}" class="nav-link {{ request()->is('reports*') ? 'nav-link-active' : '' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Reports
            </a>

            @canany(['manage users', 'manage settings', 'view audit logs', 'manage roles', 'manage permissions'])
                <p class="nav-section">Administrator</p>
                @can('manage users')
                    <a href="{{ url('/users') }}" class="nav-link {{ request()->is('users*') ? 'nav-link-active' : '' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Users
                    </a>
                @endcan
                @can('manage settings')
                    <a href="{{ url('/settings') }}" class="nav-link {{ request()->is('settings*') ? 'nav-link-active' : '' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/></svg>
                        Settings
                    </a>
                @endcan
                @can('view audit logs')
                    <a href="{{ url('/audit-logs') }}" class="nav-link {{ request()->is('audit-logs*') ? 'nav-link-active' : '' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 13h4l3-8 4 16 3-8h4"/></svg>
                        System Activity Logger
                    </a>
                @endcan
                @canany(['manage roles', 'manage permissions'])
                    <a href="{{ url('/roles') }}" class="nav-link {{ request()->is('roles*') ? 'nav-link-active' : '' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Roles &amp; Permissions
                    </a>
                @endcanany
            @endcanany

            <p class="nav-section">Public</p>
            <a href="{{ url('/job-board') }}" target="_blank" rel="noopener" class="nav-link">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Job Board
                <svg class="ml-auto h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        </nav>

        <div class="border-t border-white/10 p-3">
            <div class="flex items-center gap-3 rounded-xl bg-white/5 px-3 py-2.5">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-500/15 text-xs font-bold text-brand-300 ring-1 ring-inset ring-brand-500/30">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                </span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-white">{{ auth()->user()->name ?? 'User' }}</p>
                    <p class="truncate text-[11px] text-slate-400">{{ auth()->user()->email ?? '' }}</p>
                </div>
                <form method="POST" action="{{ url('/logout') }}">
                    @csrf
                    <button type="submit" class="rounded-lg p-2 text-slate-400 transition hover:bg-white/5 hover:text-rose-300" title="Sign out">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="lg:pl-72">
        <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/85 backdrop-blur-md">
            <div class="flex h-16 items-center gap-3 px-4 sm:px-6 lg:px-8">
                <button type="button" data-sidebar-toggle class="rounded-xl border border-slate-200 bg-white p-2 text-slate-600 shadow-sm hover:bg-slate-50 lg:hidden" aria-label="Open navigation">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <form method="GET" action="{{ url('/beneficiaries') }}" class="hidden min-w-0 flex-1 sm:block">
                    <label for="global-search" class="sr-only">Search</label>
                    <div class="relative max-w-md">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input id="global-search" name="name" value="{{ request()->is('beneficiaries*') ? request('name') : '' }}" type="search" placeholder="Search beneficiaries by name…" class="field !py-2 !pl-9">
                    </div>
                </form>

                <div class="ml-auto flex items-center gap-2">
                    <a href="{{ url('/job-board') }}" target="_blank" rel="noopener" class="hidden items-center gap-2 rounded-xl px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 md:inline-flex">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Job Board
                    </a>
                    <span class="hidden h-8 w-px bg-slate-200 sm:block"></span>
                    <div class="flex items-center gap-2.5 rounded-xl px-1 py-1">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-900 text-[11px] font-bold text-white">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}</span>
                        <div class="hidden leading-tight sm:block">
                            <p class="max-w-[10rem] truncate text-sm font-semibold text-slate-800">{{ auth()->user()->name ?? 'User' }}</p>
                            <p class="text-[11px] text-slate-500">{{ auth()->user()->getRoleNames()->first() ?? 'Member' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="mx-auto w-full max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
            @yield('content')
        </main>
    </div>

    <div class="pointer-events-none fixed inset-x-0 top-4 z-[60] flex flex-col items-center gap-2 px-4 sm:items-end">
        @if(session('success'))
            <div class="pointer-events-auto flex w-full max-w-md items-start gap-3 rounded-xl border border-brand-200 bg-white p-4 shadow-lg" role="alert">
                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-brand-100 text-brand-600">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </span>
                <p class="flex-1 text-sm font-medium text-slate-700">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="pointer-events-auto flex w-full max-w-md items-start gap-3 rounded-xl border border-rose-200 bg-white p-4 shadow-lg" role="alert">
                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-600">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                </span>
                <p class="flex-1 text-sm font-medium text-slate-700">{{ session('error') }}</p>
            </div>
        @endif
    </div>

    <script>
        (function () {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            if (!sidebar || !overlay) return;

            const open = () => {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
            };
            const close = () => {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden', 'lg:overflow-auto');
            };

            document.querySelectorAll('[data-sidebar-toggle]').forEach((btn) => btn.addEventListener('click', open));
            document.querySelectorAll('[data-sidebar-close]').forEach((btn) => btn.addEventListener('click', close));
            overlay.addEventListener('click', close);
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
        })();
    </script>
    @stack('scripts')
</body>
</html>
