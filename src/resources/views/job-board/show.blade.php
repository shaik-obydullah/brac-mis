<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
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

        @if(session('success'))
            <div class="mb-6 flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" role="alert">
                <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-pink-700 to-pink-900 px-8 py-8 text-white">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                    <div class="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center text-3xl font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                    <div>
                        <h1 class="text-3xl font-bold">{{ $user->name }}</h1>
                        <p class="text-pink-200 mt-1">
                            <span class="bg-white/20 px-2 py-0.5 rounded-full text-sm">Potential Customer</span>
                            <span class="ml-2 text-sm">{{ $user->skills->first()->skill_name ?? 'Seeking Employment' }}</span>
                        </p>
                    </div>
                    <a href="#contact" class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-pink-800 hover:bg-pink-50 sm:ml-auto">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Request Contact
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 border-b border-gray-100 px-8 py-6 md:grid-cols-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase">District</p>
                    <p class="font-medium text-gray-800">{{ $user->branch?->district ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Division</p>
                    <p class="font-medium text-gray-800">{{ $user->branch?->division ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Contact</p>
                    <p class="font-medium text-gray-800">Via BRAC only</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Skills recorded</p>
                    <p class="font-medium text-gray-800">{{ $user->skills->count() }}</p>
                </div>
            </div>

            <div class="px-8 py-6">
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
                <div class="mb-3 flex flex-col gap-1 sm:flex-row sm:items-baseline sm:justify-between">
                    <h2 class="font-semibold text-gray-900">Migration History</h2>
                    <p class="text-xs text-gray-500">Country, role and duration only. Employer names and private notes are withheld.</p>
                </div>
                @if($user->migrationHistories->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($user->migrationHistories->sortByDesc('start_date') as $history)
                            @php
                                $startYear = $history->start_date?->format('Y');
                                $endYear = $history->end_date?->format('Y');
                                $duration = $startYear
                                    ? $startYear.' – '.($endYear ?: 'Present')
                                    : 'Duration not recorded';
                            @endphp
                            <div class="border border-gray-200 rounded-lg p-4">
                                <p class="font-medium text-gray-900">{{ $history->job_title ?? 'Worker' }}</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $history->country?->name ?? 'Unknown country' }}
                                    <span class="text-gray-400">&middot;</span>
                                    {{ $duration }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No migration history recorded yet.</p>
                @endif
            </div>
        </div>

        <div id="contact" class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-100 px-6 py-5 sm:px-8">
                <h2 class="text-xl font-semibold text-gray-900">Request contact via BRAC</h2>
                <p class="mt-1 text-sm text-gray-500">Tell BRAC about your organisation and the role. BRAC staff will review the request and contact you before sharing any candidate details.</p>
            </div>

            <div class="grid grid-cols-1 gap-6 px-6 py-6 sm:px-8 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    @if($errors->any())
                        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            <p class="font-semibold">Please check the form below.</p>
                            <ul class="mt-1 list-inside list-disc space-y-0.5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('job-board.contact', $user) }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @csrf

                        <div class="hidden" aria-hidden="true">
                            <label for="company_website">Company website</label>
                            <input id="company_website" type="text" name="company_website" tabindex="-1" autocomplete="off">
                        </div>

                        <div>
                            <label for="employer_name" class="block text-sm font-medium text-gray-700 mb-1">Your name <span class="text-red-500">*</span></label>
                            <input id="employer_name" type="text" name="employer_name" value="{{ old('employer_name') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-pink-500 focus:ring-pink-500">
                        </div>
                        <div>
                            <label for="employer_organization" class="block text-sm font-medium text-gray-700 mb-1">Organisation</label>
                            <input id="employer_organization" type="text" name="employer_organization" value="{{ old('employer_organization') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-pink-500 focus:ring-pink-500">
                        </div>
                        <div>
                            <label for="employer_email" class="block text-sm font-medium text-gray-700 mb-1">Work email <span class="text-red-500">*</span></label>
                            <input id="employer_email" type="email" name="employer_email" value="{{ old('employer_email') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-pink-500 focus:ring-pink-500">
                        </div>
                        <div>
                            <label for="employer_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone (optional)</label>
                            <input id="employer_phone" type="text" name="employer_phone" value="{{ old('employer_phone') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-pink-500 focus:ring-pink-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label for="job_title" class="block text-sm font-medium text-gray-700 mb-1">Role you are hiring for</label>
                            <input id="job_title" type="text" name="job_title" value="{{ old('job_title') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-pink-500 focus:ring-pink-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message <span class="text-red-500">*</span></label>
                            <textarea id="message" name="message" rows="4" required minlength="20" maxlength="2000" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-pink-500 focus:ring-pink-500" placeholder="Describe the role, location, salary range and why this candidate is a good fit.">{{ old('message') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">At least 20 characters. Maximum 2,000 characters.</p>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="flex items-start gap-3 text-sm text-gray-600">
                                <input type="checkbox" name="consent" value="1" required @checked(old('consent')) class="mt-0.5 rounded border-gray-300 text-pink-600 focus:ring-pink-500">
                                <span>I agree that BRAC may contact me about this request and share my details with the relevant BRAC team. I will use any candidate information only for this hiring request.</span>
                            </label>
                        </div>
                        <div class="sm:col-span-2">
                            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-pink-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-pink-700">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                Send contact request to BRAC
                            </button>
                        </div>
                    </form>
                </div>

                <aside class="lg:col-span-1">
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                        <div class="flex items-center gap-2 font-semibold">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Privacy protected
                        </div>
                        <ul class="mt-2 space-y-2">
                            <li>Candidate phone numbers and emails are never shown publicly.</li>
                            <li>Employer names and private notes are withheld from the public board.</li>
                            <li>BRAC reviews every request before sharing candidate contact details.</li>
                            <li>Your request is visible only to authorised BRAC staff.</li>
                        </ul>
                    </div>

                    <div class="mt-4 rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-600">
                        <p class="font-medium text-gray-800">Prefer to visit a BRAC office?</p>
                        <p class="mt-1">Contact your nearest BRAC branch and mention the candidate's name and the job board.</p>
                        @if($user->branch)
                            <p class="mt-2 font-medium text-gray-800">{{ $user->branch->name }}</p>
                            <p>{{ $user->branch->district }}{{ $user->branch->division ? ', '.$user->branch->division : '' }}</p>
                        @endif
                    </div>
                </aside>
            </div>
        </div>

        <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-semibold text-gray-900">Looking for someone with different skills?</h3>
                <p class="text-sm text-gray-500 mt-1">Browse other potential customers or adjust your search.</p>
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
