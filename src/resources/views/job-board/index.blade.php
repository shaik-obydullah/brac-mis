<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Board - BRAC MIS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased">
    <header class="bg-pink-900 text-white">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="{{ route('job-board.index') }}" class="flex items-center space-x-2">
                <span class="text-2xl font-bold">BRAC Job Board</span>
            </a>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-pink-200 hidden sm:block">Find skilled workers from BRAC programs</span>
                <a href="{{ url('/login') }}" class="text-sm bg-pink-700 hover:bg-pink-800 px-4 py-2 rounded-lg">Staff Login</a>
            </div>
        </div>
    </header>

    <section class="bg-gradient-to-br from-pink-800 to-pink-950 text-white">
        <div class="max-w-7xl mx-auto px-4 py-16 text-center">
            <h1 class="text-4xl font-bold mb-3">Hire Verified Job Seekers</h1>
            <p class="text-pink-200 text-lg max-w-2xl mx-auto">Search skilled beneficiaries and returnee migrants registered with BRAC across Bangladesh.</p>
            <form method="GET" action="{{ route('job-board.index') }}" class="mt-8 max-w-2xl mx-auto flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, occupation, skill, phone..." class="w-full pl-12 pr-4 py-3 rounded-lg bg-white/10 text-white placeholder-white/70 border border-white/20 focus:outline-none focus:ring-2 focus:ring-pink-400">
                </div>
                <button type="submit" class="bg-white hover:bg-pink-100 text-pink-900 font-semibold px-8 py-3 rounded-lg transition">Search</button>
            </form>
            <p class="text-pink-300 text-sm mt-4">{{ $seekers->total() }} job seekers available</p>
        </div>
    </section>

    <main class="max-w-7xl mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <aside class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 sticky top-6">
                    <h2 class="font-semibold text-gray-800 mb-4">Filters</h2>
                    <form method="GET" action="{{ route('job-board.index') }}" class="space-y-4">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select name="category" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-pink-500 focus:border-pink-500">
                                <option value="">All Candidates</option>
                                <option value="beneficiary" {{ request('category') == 'beneficiary' ? 'selected' : '' }}>Beneficiaries</option>
                                <option value="returnee" {{ request('category') == 'returnee' ? 'selected' : '' }}>Returnees</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                            <select name="gender" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-pink-500 focus:border-pink-500">
                                <option value="">All Genders</option>
                                <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">District</label>
                            <select name="district" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-pink-500 focus:border-pink-500">
                                <option value="">All Districts</option>
                                @foreach($districts as $d)
                                    <option value="{{ $d }}" {{ request('district') == $d ? 'selected' : '' }}>{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Occupation / Skill</label>
                            <input type="text" name="occupation" value="{{ request('occupation') }}" placeholder="e.g. Driver, Tailoring..." class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-pink-500 focus:border-pink-500">
                        </div>
                        <div class="pt-2 border-t border-gray-100 flex gap-2">
                            <button type="submit" class="flex-1 bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Apply Filters</button>
                            <a href="{{ route('job-board.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Clear</a>
                        </div>
                    </form>
                </div>
            </aside>

            <div class="lg:col-span-3">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">Available Job Seekers</h2>
                    <span class="text-sm text-gray-500">Showing {{ $seekers->firstItem() ?? 0 }}-{{ $seekers->lastItem() ?? 0 }} of {{ $seekers->total() }}</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                    @forelse($seekers as $seeker)
                        <a href="{{ route('job-board.show', [$seeker['type'], $seeker['id']]) }}" class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md hover:border-pink-300 transition p-5 group">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-full bg-pink-100 text-pink-700 flex items-center justify-center font-bold text-lg">{{ strtoupper(substr($seeker['name'], 0, 1)) }}</div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900 group-hover:text-pink-700">{{ $seeker['name'] }}</h3>
                                        <span class="text-xs {{ $seeker['type'] === 'returnee' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }} px-2 py-0.5 rounded-full">{{ $seeker['type'] === 'returnee' ? 'Returnee' : 'Beneficiary' }}</span>
                                    </div>
                                </div>
                            </div>
                            @if($seeker['occupation'])
                                <p class="text-sm text-gray-700 mb-1">{{ $seeker['occupation'] }}</p>
                            @endif
                            @if(count($seeker['skills']) > 0)
                                <div class="flex flex-wrap gap-1 mb-2">
                                    @foreach($seeker['skills']->take(3) as $skill)
                                        <span class="text-xs bg-pink-50 text-pink-700 border border-pink-200 px-2 py-0.5 rounded-full">{{ $skill }}</span>
                                    @endforeach
                                    @if(count($seeker['skills']) > 3)
                                        <span class="text-xs text-gray-500 px-1 py-0.5">+{{ count($seeker['skills']) - 3 }} more</span>
                                    @endif
                                </div>
                            @endif
                            <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-500 mt-3 border-t border-gray-100 pt-3">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    {{ ucfirst($seeker['gender']) }}
                                </span>
                                @if($seeker['age'])
                                    <span>{{ $seeker['age'] }} yrs</span>
                                @endif
                                @if($seeker['location'])
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ $seeker['location'] }}
                                    </span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                            <svg class="w-14 h-14 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <p class="text-lg font-medium text-gray-700">No job seekers found</p>
                            <p class="text-sm text-gray-500 mt-1">Try adjusting your search or clearing the filters.</p>
                        </div>
                    @endforelse
                </div>

                @if($seekers->hasPages())
                    <div class="mt-8">
                        {{ $seekers->links() }}
                    </div>
                @endif
            </div>
        </div>
    </main>

    <footer class="bg-pink-900 text-pink-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 py-8 text-center">
            <p class="font-semibold text-white">BRAC Job Board</p>
            <p class="text-sm mt-1">Part of BRAC Migration Information System - connecting skilled workers with employers</p>
            <p class="text-xs mt-3 text-pink-400">&copy; {{ date('Y') }} BRAC. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
