<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f172a">
    <title>Sign in · BRAC MIS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 font-sans text-slate-900 antialiased">
    <div class="flex min-h-full">
        <aside class="relative hidden w-1/2 flex-col justify-between overflow-hidden bg-slate-950 p-12 text-white lg:flex xl:w-[55%]">
            <div class="absolute inset-0 opacity-[0.07]" style="background-image: linear-gradient(to right, #fff 1px, transparent 1px), linear-gradient(to bottom, #fff 1px, transparent 1px); background-size: 48px 48px;"></div>
            <div class="absolute -left-24 top-1/4 h-96 w-96 rounded-full bg-brand-500/20 blur-3xl"></div>
            <div class="absolute -bottom-32 right-0 h-96 w-96 rounded-full bg-emerald-400/10 blur-3xl"></div>

            <div class="relative flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-500 text-base font-black text-slate-950 shadow-lg shadow-brand-500/30">B</span>
                <div>
                    <p class="text-base font-bold tracking-tight">BRAC <span class="text-brand-400">MIS</span></p>
                    <p class="text-xs text-slate-400">Migration &amp; Reintegration Management</p>
                </div>
            </div>

            <div class="relative max-w-lg">
                <p class="mb-5 inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-brand-300">
                    <span class="h-1.5 w-1.5 rounded-full bg-brand-400"></span>
                    One system, the full migration journey
                </p>
                <h1 class="text-4xl font-bold leading-[1.15] tracking-tight xl:text-5xl">
                    From risk to<br>reintegration,<br><span class="text-brand-400">all in one place.</span>
                </h1>
                <p class="mt-5 max-w-md text-base leading-relaxed text-slate-400">
                    Track beneficiaries, monitor deployments abroad, and support returnees through every stage of reintegration with a single source of truth.
                </p>

                <div class="mt-10 grid grid-cols-3 gap-4">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur">
                        <p class="text-2xl font-bold text-white">Lifecycle</p>
                        <p class="mt-1 text-xs text-slate-400">End-to-end tracking</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur">
                        <p class="text-2xl font-bold text-white">RBAC</p>
                        <p class="mt-1 text-xs text-slate-400">Role-based access</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur">
                        <p class="text-2xl font-bold text-white">Audit</p>
                        <p class="mt-1 text-xs text-slate-400">Every change logged</p>
                    </div>
                </div>
            </div>

            <p class="relative text-xs text-slate-500">BRAC Migration Information System · v1.0</p>
        </aside>

        <main class="flex w-full flex-col justify-center px-5 py-10 sm:px-10 lg:w-1/2 xl:w-[45%]">
            <div class="mx-auto w-full max-w-sm">
                <div class="mb-8 flex items-center gap-3 lg:hidden">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-600 text-base font-black text-white">B</span>
                    <div>
                        <p class="text-base font-bold tracking-tight text-slate-900">BRAC <span class="text-brand-600">MIS</span></p>
                        <p class="text-xs text-slate-500">Migration &amp; Reintegration</p>
                    </div>
                </div>

                <h2 class="text-2xl font-bold tracking-tight text-slate-900">Welcome back</h2>
                <p class="mt-1.5 text-sm text-slate-500">Sign in to continue to your workspace.</p>

                @if($errors->any())
                    <div class="mt-6 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 p-3.5 text-sm text-rose-700">
                        <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
                        <span>{{ $errors->first('email') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="field-label">Email address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                            class="field" placeholder="you@bracmis.org">
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <label for="password" class="field-label">Password</label>
                            <span class="mb-1.5 text-xs font-medium text-slate-400">Contact your admin to reset</span>
                        </div>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="field" placeholder="••••••••">
                    </div>

                    <label class="flex items-center gap-2.5 text-sm text-slate-600">
                        <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        Keep me signed in
                    </label>

                    <button type="submit" class="btn-primary w-full !py-3">
                        Sign in
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </button>
                </form>

                <div class="my-6 flex items-center gap-3">
                    <span class="h-px flex-1 bg-slate-200"></span>
                    <span class="text-xs font-medium uppercase tracking-wider text-slate-400">or</span>
                    <span class="h-px flex-1 bg-slate-200"></span>
                </div>

                <a href="{{ url('/job-board') }}" class="btn-secondary w-full">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Browse the public job board
                </a>

                <div class="mt-8 rounded-xl border border-slate-200 bg-white p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Demo credentials</p>
                    <div class="mt-2 flex items-center justify-between gap-3 text-sm">
                        <span class="font-mono text-slate-700">admin@bracmis.org</span>
                        <span class="font-mono text-slate-700">password</span>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
