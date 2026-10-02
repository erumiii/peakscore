<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PeakScore')</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}?v=2">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style type="text/tailwindcss">
        @theme {
            --color-canvas: #F7F5F2;
            --color-ink: #1C1C1A;
            --color-line: #E7E2DC;
            --color-muted: #8A857C;
        }
    </style>
    <style>
        [x-cloak] { display: none !important; }
        dialog::backdrop { background: rgb(28 28 26 / 0.4); }
        /* Tailwind v4 preflight me-reset margin semua elemen - kembalikan centering bawaan <dialog> */
        dialog { margin: auto; }
    </style>
</head>
<body class="bg-canvas text-ink min-h-screen font-sans antialiased">
<div class="flex min-h-screen">
    <aside class="sticky top-0 h-screen w-60 shrink-0 self-start overflow-y-auto bg-white border-r border-canvas p-4 flex flex-col gap-6">
        <div class="flex items-center gap-2.5 px-2 pt-2">
            <div class="w-8 h-8 rounded-lg bg-ink text-white grid place-items-center text-sm font-semibold">P</div>
            <div>
                <p class="text-sm font-semibold leading-tight">PeakScore</p>
                <p class="text-[11px] text-muted leading-tight">Participant Portal</p>
            </div>
        </div>

        <nav class="space-y-1 text-sm">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-2.5 rounded-lg px-3 py-2 {{ request()->routeIs('dashboard') ? 'bg-ink text-white' : 'text-ink/70 hover:bg-black/5' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                Test Schedules
            </a>
            <a href="{{ route('peserta.results') }}"
               class="flex items-center gap-2.5 rounded-lg px-3 py-2 {{ request()->routeIs('peserta.results') ? 'bg-ink text-white' : 'text-ink/70 hover:bg-black/5' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                My Results
            </a>
        </nav>

        <div class="mt-auto flex items-center gap-2.5 rounded-xl border border-line bg-white p-3">
            <div class="w-8 h-8 rounded-full bg-ink/10 grid place-items-center text-xs font-semibold">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-medium leading-tight">{{ auth()->user()->name }}</p>
                <p class="text-[11px] text-muted leading-tight capitalize">{{ auth()->user()->role }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Sign out" class="rounded-lg p-2 text-muted hover:bg-black/5 hover:text-ink">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 max-w-5xl mx-auto p-8">
        @yield('content')
    </main>
</div>
@yield('scripts')
</body>
</html>
