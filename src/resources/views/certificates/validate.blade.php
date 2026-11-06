<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f172a">
    <title>Validate Certificate · BRAC MIS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-slate-50 font-sans text-slate-900 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-4xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-500 text-sm font-black text-slate-950">B</span>
                <span class="text-sm font-bold tracking-tight text-slate-900">BRAC MIS</span>
            </a>
            <a href="{{ url('/login') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">Staff Login</a>
        </div>
    </header>

    <main class="mx-auto max-w-4xl px-4 py-10 sm:px-6 sm:py-14">
        <div class="text-center">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-brand-600">Certificate Validation</p>
            <h1 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Verify a BRAC certificate</h1>
            <p class="mx-auto mt-3 max-w-2xl text-sm text-slate-500">
                Enter the certificate number or the verification code printed on the certificate to confirm its authenticity.
            </p>
        </div>

        <div class="card mx-auto mt-8 max-w-2xl p-5 sm:p-6">
            <form method="POST" action="{{ route('certificates.validate.submit') }}" class="flex flex-col gap-3 sm:flex-row">
                @csrf
                <div class="flex-1">
                    <label class="sr-only" for="query">Certificate number or verification code</label>
                    <input id="query" type="text" name="query" value="{{ old('query', request('query')) }}" class="field" placeholder="e.g. BRAC-CERT-2026-00001" required autofocus>
                </div>
                <button type="submit" class="btn-primary shrink-0">Validate</button>
            </form>

            @if(session('error'))
                <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
                    {{ session('error') }}
                </div>
            @endif
            @error('query')
                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        @isset($certificate)
            @php
                $state = $certificate->status === 'revoked' ? 'revoked' : ($certificate->is_expired ? 'expired' : 'valid');
                $stateStyles = [
                    'valid' => ['title' => 'Certificate is valid', 'icon' => 'M5 13l4 4L19 7', 'wrap' => 'border-brand-200 bg-brand-50', 'text' => 'text-brand-800', 'badge' => 'bg-brand-100 text-brand-700 ring-brand-200', 'iconWrap' => 'bg-brand-600 text-white'],
                    'expired' => ['title' => 'Certificate has expired', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'wrap' => 'border-amber-200 bg-amber-50', 'text' => 'text-amber-800', 'badge' => 'bg-amber-100 text-amber-700 ring-amber-200', 'iconWrap' => 'bg-amber-500 text-white'],
                    'revoked' => ['title' => 'Certificate has been revoked', 'icon' => 'M6 18L18 6M6 6l12 12', 'wrap' => 'border-rose-200 bg-rose-50', 'text' => 'text-rose-800', 'badge' => 'bg-rose-100 text-rose-700 ring-rose-200', 'iconWrap' => 'bg-rose-600 text-white'],
                ];
                $style = $stateStyles[$state];
            @endphp

            <div class="card mx-auto mt-6 max-w-2xl overflow-hidden">
                <div class="flex items-center gap-4 border-b p-5 {{ $style['wrap'] }}">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full {{ $style['iconWrap'] }}">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $style['icon'] }}"/></svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold {{ $style['text'] }}">{{ $style['title'] }}</p>
                        <p class="text-xs {{ $style['text'] }} opacity-80">Checked against the official BRAC certificate registry.</p>
                    </div>
                    <span class="badge {{ $style['badge'] }}">{{ ucfirst($state) }}</span>
                </div>

                <dl class="divide-y divide-slate-100">
                    <div class="flex flex-col gap-1 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <dt class="text-sm text-slate-500">Recipient</dt>
                        <dd class="font-semibold text-slate-900">{{ $certificate->recipient_name }}</dd>
                    </div>
                    <div class="flex flex-col gap-1 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <dt class="text-sm text-slate-500">Course</dt>
                        <dd class="font-medium text-slate-900">{{ $certificate->course->title ?? 'N/A' }}</dd>
                    </div>
                    <div class="flex flex-col gap-1 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <dt class="text-sm text-slate-500">Certificate number</dt>
                        <dd class="font-mono text-sm font-medium text-slate-900">{{ $certificate->certificate_number }}</dd>
                    </div>
                    <div class="flex flex-col gap-1 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <dt class="text-sm text-slate-500">Verification code</dt>
                        <dd class="font-mono text-sm font-medium text-slate-900">{{ $certificate->verification_code }}</dd>
                    </div>
                    <div class="flex flex-col gap-1 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <dt class="text-sm text-slate-500">Issued on</dt>
                        <dd class="font-medium text-slate-900">{{ optional($certificate->issued_date)->format('d M Y') }}</dd>
                    </div>
                    <div class="flex flex-col gap-1 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <dt class="text-sm text-slate-500">Expires on</dt>
                        <dd class="font-medium text-slate-900">{{ optional($certificate->expiry_date)->format('d M Y') ?? 'No expiry' }}</dd>
                    </div>
                    <div class="flex flex-col gap-1 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <dt class="text-sm text-slate-500">Issued by</dt>
                        <dd class="font-medium text-slate-900">{{ $certificate->issuedBy->name ?? 'BRAC' }}</dd>
                    </div>
                </dl>
            </div>
        @endisset

        <p class="mt-8 text-center text-xs text-slate-400">
            Certificate data is provided by BRAC Migration &amp; Reintegration Management. Contact your branch if details are incorrect.
        </p>
    </main>
</body>
</html>
