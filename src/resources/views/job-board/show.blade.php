<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Seeker Profile - BRAC MIS</title>
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

        @if($type === 'beneficiary')
            @php
                $name = $beneficiary->name;
                $gender = $beneficiary->gender ?? 'other';
                $age = $beneficiary->date_of_birth ? \Carbon\Carbon::parse($beneficiary->date_of_birth)->age : null;
                $phone = $beneficiary->phone;
                $location = $beneficiary->branch->district ?? $beneficiary->address_line_1;
                $occupation = $beneficiary->occupation;
                $skills = $beneficiary->interventions->where('type', 'Skill Training')->pluck('type')->unique();
                $bracId = $beneficiary->brac_id ?? $beneficiary->id;
            @endphp
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-pink-700 to-pink-900 px-8 py-8 text-white">
                    <div class="flex items-center gap-5">
                        <div class="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center text-3xl font-bold">{{ strtoupper(substr($name, 0, 1)) }}</div>
                        <div>
                            <h1 class="text-3xl font-bold">{{ $name }}</h1>
                            <p class="text-pink-200 mt-1">
                                <span class="bg-blue-500/30 px-2 py-0.5 rounded-full text-sm">Beneficiary</span>
                                <span class="ml-2 text-sm">{{ $occupation ?? 'Seeking Employment' }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 px-8 py-6 border-b border-gray-100">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Gender</p>
                        <p class="font-medium text-gray-800">{{ ucfirst($gender) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Age</p>
                        <p class="font-medium text-gray-800">{{ $age ? $age . ' years' : 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">District</p>
                        <p class="font-medium text-gray-800">{{ $location ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Status</p>
                        <p class="font-medium text-gray-800">{{ ucfirst($beneficiary->status ?? 'Active') }}</p>
                    </div>
                </div>

                <div class="px-8 py-6">
                    <h2 class="font-semibold text-gray-900 mb-4">Profile Summary</h2>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                        <div class="flex justify-between gap-4 border-b border-gray-100 pb-3">
                            <dt class="text-gray-500">BRAC ID</dt>
                            <dd class="font-medium text-gray-900">{{ $bracId }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 border-b border-gray-100 pb-3">
                            <dt class="text-gray-500">Phone</dt>
                            <dd class="font-medium text-gray-900">{{ $phone ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 border-b border-gray-100 pb-3">
                            <dt class="text-gray-500">Father's Name</dt>
                            <dd class="font-medium text-gray-900">{{ $beneficiary->father_name ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 border-b border-gray-100 pb-3">
                            <dt class="text-gray-500">Mother's Name</dt>
                            <dd class="font-medium text-gray-900">{{ $beneficiary->mother_name ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 border-b border-gray-100 pb-3">
                            <dt class="text-gray-500">Monthly Income</dt>
                            <dd class="font-medium text-gray-900">{{ $beneficiary->monthly_income ? 'Tk ' . number_format($beneficiary->monthly_income) : 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 border-b border-gray-100 pb-3">
                            <dt class="text-gray-500">Family Size</dt>
                            <dd class="font-medium text-gray-900">{{ $beneficiary->family_size ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="px-8 pb-6">
                    <h2 class="font-semibold text-gray-900 mb-3">Address</h2>
                    <p class="text-sm text-gray-700">{{ $beneficiary->address_line_1 ?? 'N/A' }} {{ $beneficiary->address_line_2 ? ', ' . $beneficiary->address_line_2 : '' }}</p>
                </div>

                @if($beneficiary->migrants->isNotEmpty())
                    <div class="px-8 pb-6">
                        <h2 class="font-semibold text-gray-900 mb-3">Migration Experience</h2>
                        <div class="space-y-3">
                            @foreach($beneficiary->migrants as $migrant)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <p class="font-medium text-gray-900">{{ $migrant->occupation ?? 'Worker' }}</p>
                                        <span class="text-xs bg-pink-100 text-pink-700 px-2 py-0.5 rounded-full">{{ ucwords(str_replace('_', ' ', $migrant->status)) }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">{{ $migrant->destinationCountry->name ?? 'N/A' }} - {{ $migrant->destination_city ?? '' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @else
            @php
                $source = $returnee->migrant ?? $returnee->beneficiary;
                $name = $source?->name ?? $returnee->beneficiary?->name ?? 'Returnee #' . $returnee->id;
                $gender = $source?->gender ?? $returnee->beneficiary?->gender ?? 'other';
                $age = $source?->date_of_birth ? \Carbon\Carbon::parse($source->date_of_birth)->age : ($returnee->beneficiary?->date_of_birth ? \Carbon\Carbon::parse($returnee->beneficiary->date_of_birth)->age : null);
                $phone = $source?->phone ?? $returnee->beneficiary?->phone;
                $location = $returnee->beneficiary?->branch?->district ?? $returnee->beneficiary?->address_line_1;
                $occupation = $source?->occupation ?? $returnee->beneficiary?->occupation;
                $skills = $returnee->skillAssessments;
                $bracId = $source?->brac_id ?? $returnee->id;
            @endphp
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-700 to-pink-900 px-8 py-8 text-white">
                    <div class="flex items-center gap-5">
                        <div class="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center text-3xl font-bold">{{ strtoupper(substr($name, 0, 1)) }}</div>
                        <div>
                            <h1 class="text-3xl font-bold">{{ $name }}</h1>
                            <p class="text-pink-200 mt-1">
                                <span class="bg-purple-500/30 px-2 py-0.5 rounded-full text-sm">Returnee Migrant</span>
                                <span class="ml-2 text-sm">{{ $occupation ?? 'Seeking Employment' }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 px-8 py-6 border-b border-gray-100">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Gender</p>
                        <p class="font-medium text-gray-800">{{ ucfirst($gender) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Age</p>
                        <p class="font-medium text-gray-800">{{ $age ? $age . ' years' : 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">District</p>
                        <p class="font-medium text-gray-800">{{ $location ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Status</p>
                        <p class="font-medium text-gray-800">{{ ucwords(str_replace('_', ' ', $returnee->current_status ?? 'Active')) }}</p>
                    </div>
                </div>

                <div class="px-8 py-6">
                    <h2 class="font-semibold text-gray-900 mb-4">Profile Summary</h2>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                        <div class="flex justify-between gap-4 border-b border-gray-100 pb-3">
                            <dt class="text-gray-500">BRAC ID</dt>
                            <dd class="font-medium text-gray-900">{{ $bracId }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 border-b border-gray-100 pb-3">
                            <dt class="text-gray-500">Phone</dt>
                            <dd class="font-medium text-gray-900">{{ $phone ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 border-b border-gray-100 pb-3">
                            <dt class="text-gray-500">Worked In</dt>
                            <dd class="font-medium text-gray-900">{{ $returnee->originCountry?->name ?? ($returnee->migrant?->destinationCountry?->name ?? 'N/A') }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 border-b border-gray-100 pb-3">
                            <dt class="text-gray-500">Return Date</dt>
                            <dd class="font-medium text-gray-900">{{ $returnee->return_date ? \Carbon\Carbon::parse($returnee->return_date)->format('Y-m-d') : 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 border-b border-gray-100 pb-3">
                            <dt class="text-gray-500">Return Reason</dt>
                            <dd class="font-medium text-gray-900 text-right">{{ $returnee->return_reason ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="px-8 pb-6">
                    <h2 class="font-semibold text-gray-900 mb-3">Skills</h2>
                    @if($skills->isNotEmpty())
                        <div class="flex flex-wrap gap-2">
                            @foreach($skills as $skill)
                                <span class="inline-flex items-center gap-2 bg-pink-50 border border-pink-200 text-pink-700 px-3 py-1 rounded-full text-sm">
                                    {{ $skill->skill_name }}
                                    <span class="text-xs text-pink-600">{{ ucfirst($skill->proficiency_level) }}{{ $skill->certification ? ' - Certified' : '' }}</span>
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No formal skill assessment recorded yet.</p>
                    @endif
                </div>

                @if($returnee->migrant)
                    <div class="px-8 pb-6">
                        <h2 class="font-semibold text-gray-900 mb-3">Overseas Work Experience</h2>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <p class="font-medium text-gray-900">{{ $returnee->migrant->occupation ?? 'Worker' }}</p>
                                <span class="text-xs bg-pink-100 text-pink-700 px-2 py-0.5 rounded-full">{{ $returnee->migrant->skill_level ? ucfirst($returnee->migrant->skill_level) . ' Skill' : '' }}</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">{{ $returnee->migrant->destinationCountry?->name ?? 'N/A' }} - {{ $returnee->migrant->destination_city ?? '' }}</p>
                            <p class="text-xs text-gray-500 mt-1">Education: {{ ucfirst($returnee->migrant->education_level ?? 'N/A') }}</p>
                        </div>
                    </div>
                @endif

                @if($returnee->reintegrationPlans->isNotEmpty())
                    <div class="px-8 pb-6">
                        <h2 class="font-semibold text-gray-900 mb-3">Reintegration Plan</h2>
                        @foreach($returnee->reintegrationPlans as $plan)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <p class="font-medium text-gray-900">{{ $plan->goal }}</p>
                                    <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">{{ ucfirst($plan->status) }}</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">{{ $plan->activities }}</p>
                                @if($plan->timeline)
                                    <p class="text-xs text-gray-500 mt-1">Timeline: {{ $plan->timeline }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

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
