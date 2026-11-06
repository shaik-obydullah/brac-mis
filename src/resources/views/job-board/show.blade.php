<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $user->name }} - BRAC Job Board</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased">
    <header class="bg-pink-900 text-white">
        <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="{{ route('job-board.index') }}" class="flex items-center space-x-2">
                <span class="text-xl font-bold">BRAC Job Board</span>
            </a>
            <a href="{{ url('/login') }}" class="text-sm bg-pink-700 hover:bg-pink-800 px-4 py-2 rounded-lg">Staff Login</a>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-8">
        <a href="{{ route('job-board.index') }}" class="inline-flex items-center text-sm text-pink-700 hover:text-pink-900 mb-6">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Job Board
        </a>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-pink-700 to-pink-900 px-8 py-8 text-white">
                <div class="flex items-center gap-5">
                    <div class="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center text-3xl font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                    <div>
                        <h1 class="text-3xl font-bold">{{ $user->name }}</h1>
                        <p class="text-pink-200 mt-1">
                            <span class="bg-white/20 px-2 py-0.5 rounded-full text-sm">Potential Customer</span>
                            <span class="ml-2 text-sm">{{ $user->skills->first()->skill_name ?? 'Seeking Employment' }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 px-8 py-6 border-b border-gray-100">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Phone</p>
                    <p class="font-medium text-gray-800">{{ $user->phone ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Email</p>
                    <p class="font-medium text-gray-800 break-all">{{ $user->email }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">District</p>
                    <p class="font-medium text-gray-800">{{ $user->branch?->district ?? $user->branch?->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Branch</p>
                    <p class="font-medium text-gray-800">{{ $user->branch?->name ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="px-8 py-6">
                <h2 class="font-semibold text-gray-900 mb-4">Profile Summary</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                    <div class="flex justify-between gap-4 border-b border-gray-100 pb-3">
                        <dt class="text-gray-500">Profile Type</dt>
                        <dd class="font-medium text-gray-900">{{ $user->profile_type_label }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-gray-100 pb-3">
                        <dt class="text-gray-500">Phone</dt>
                        <dd class="font-medium text-gray-900">{{ $user->phone ?? 'N/A' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-gray-100 pb-3">
                        <dt class="text-gray-500">Division</dt>
                        <dd class="font-medium text-gray-900">{{ $user->branch?->division ?? 'N/A' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-gray-100 pb-3">
                        <dt class="text-gray-500">Upazila</dt>
                        <dd class="font-medium text-gray-900">{{ $user->branch?->upazila ?? 'N/A' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="px-8 pb-6">
                <h2 class="font-semibold text-gray-900 mb-3">Skills</h2>
                @if($user->skills->isNotEmpty())
                    <div class="flex flex-wrap gap-2">
                        @foreach($user->skills as $skill)
                            <span class="inline-flex items-center gap-2 bg-pink-50 border border-pink-200 text-pink-700 px-3 py-1 rounded-full text-sm">
                                {{ $skill->skill_name }}
                                <span class="text-xs text-pink-600">
                                    {{ ucfirst($skill->proficiency) }}
                                    @if($skill->years_of_experience !== null)
                                        &middot; {{ $skill->years_of_experience }} yrs
                                    @endif
                                    @if($skill->certification)
                                        &middot; Certified
                                    @endif
                                </span>
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No skills recorded yet.</p>
                @endif
            </div>

            <div class="px-8 pb-8">
                <h2 class="font-semibold text-gray-900 mb-3">Migration History</h2>
                @if($user->migrationHistories->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($user->migrationHistories->sortByDesc('start_date') as $history)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                    <p class="font-medium text-gray-900">
                                        {{ $history->job_title ?? 'Worker' }}
                                        @if($history->employer_name)
                                            <span class="text-gray-500">at {{ $history->employer_name }}</span>
                                        @endif
                                    </p>
                                    <span class="text-xs bg-pink-100 text-pink-700 px-2 py-0.5 rounded-full self-start">{{ ucfirst($history->status) }}</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $history->country?->name ?? 'Unknown country' }}
                                    @if($history->visa_type)
                                        &middot; {{ $history->visa_type }} visa
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $history->start_date?->format('M Y') ?? 'N/A' }}
                                    &ndash;
                                    {{ $history->end_date?->format('M Y') ?? 'Present' }}
                                </p>
                                @if($history->notes)
                                    <p class="text-xs text-gray-600 mt-2">{{ $history->notes }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No migration history recorded yet.</p>
                @endif
            </div>
        </div>

        <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-semibold text-gray-900">Interested in hiring this candidate?</h3>
                <p class="text-sm text-gray-500 mt-1">Contact the local BRAC office for verification and further details.</p>
            </div>
            <a href="{{ route('job-board.index') }}" class="bg-pink-600 hover:bg-pink-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium whitespace-nowrap">Browse More Candidates</a>
        </div>
    </main>

    <footer class="bg-pink-900 text-pink-200 mt-12">
        <div class="max-w-5xl mx-auto px-4 py-8 text-center">
            <p class="font-semibold text-white">BRAC Job Board</p>
            <p class="text-xs mt-1 text-pink-400">&copy; {{ date('Y') }} BRAC. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
