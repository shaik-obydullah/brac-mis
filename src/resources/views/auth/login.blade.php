<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BRAC MIS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-green-800 to-green-950 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white">BRAC MIS</h1>
            <p class="text-green-200 mt-2">Migration Information System</p>
        </div>
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Sign In</h2>
            @if($errors->any())
                <div class="bg-red-50 text-red-600 px-4 py-3 rounded-lg mb-4 text-sm">
                    {{ $errors->first('email') }}
                </div>
            @endif
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center text-sm text-gray-600">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <span class="ml-2">Remember me</span>
                    </label>
                </div>
                <button type="submit" class="w-full bg-green-700 hover:bg-green-800 text-white font-medium py-2.5 px-4 rounded-lg transition">
                    Sign In
                </button>
            </form>
            <a href="{{ url('/job-board') }}" class="block text-center mt-4 text-sm text-green-700 hover:text-green-900 font-medium">
                Browse Public Job Board →
            </a>
            <p class="text-xs text-gray-400 text-center mt-6">BRAC Migration Information System v1.0</p>
        </div>
    </div>
</body>
</html>
