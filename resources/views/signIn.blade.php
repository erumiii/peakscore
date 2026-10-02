<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — PeakScore</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}?v=2">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @theme {
            --color-canvas: #F7F5F2;
            --color-ink: #1C1C1A;
            --color-line: #E7E2DC;
            --color-muted: #8A857C;
        }
    </style>
</head>
<body class="bg-canvas text-ink min-h-screen font-sans antialiased">
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-6">
            <img src="{{ asset('/logo.svg') }}" alt="PeakScore logo" class="w-10 h-10 mx-auto object-cover">
            <h1 class="text-2xl font-semibold text-ink mt-2">PeakScore</h1>
            <p class="text-sm text-muted">Academic Potential Test</p>
        </div>
        <div class="rounded-xl border border-canvas bg-white p-8">
            @if ($errors->any())
                <p class="text-sm text-red-500 mb-4">{{ $errors->first() }}</p>
            @endif
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="username" class="mb-1.5 block text-sm font-medium">Username</label>
                    <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus
                        placeholder="Enter your username"
                        class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ink/10 {{ $errors->has('username') ? 'border-red-400' : 'border-line' }}">
                </div>
                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium">Password</label>
                    <input id="password" type="password" name="password" required
                        placeholder="Enter your password"
                        class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ink/10 {{ $errors->has('password') ? 'border-red-400' : 'border-line' }}">
                </div>
                <button type="submit"
                    class="w-full rounded-lg bg-ink px-4 py-2 text-sm font-medium text-white hover:opacity-90">
                    Sign In
                </button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
