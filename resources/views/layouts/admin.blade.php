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

        /* Motion: hover halus di seluruh elemen interaktif */
        a, button, input, select, textarea, tr {
            transition: background-color .15s ease, border-color .15s ease, color .15s ease, opacity .15s ease;
        }

        /* Modal <dialog>: backdrop fade + box scale-up saat dibuka */
        dialog[open] { animation: dialogIn .18s ease-out; }
        dialog[open]::backdrop { animation: backdropIn .18s ease-out; }
        @keyframes dialogIn {
            from { opacity: 0; transform: translateY(6px) scale(.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes backdropIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>
<body class="bg-canvas text-ink min-h-screen font-sans antialiased">
<div class="flex min-h-screen">
    <aside class="sticky top-0 h-screen w-60 shrink-0 self-start overflow-y-auto bg-white border-r border-canvas p-4 flex flex-col gap-6">
        <div class="flex items-center gap-2.5 px-2 pt-2">
            <img src="{{ asset('logo.svg') }}" alt="PeakScore logo" class="w-8 h-8 rounded-lg object-cover">
            <div>
                <p class="text-sm font-semibold leading-tight">PeakScore</p>
                <p class="text-[11px] text-muted leading-tight">Organizer Portal</p>
            </div>
        </div>

        <nav class="space-y-6">
            <div>
                <p class="px-3 pb-2 text-[11px] font-medium uppercase tracking-wider text-muted">Workspace</p>
                <ul class="space-y-1 text-sm">
                    <li>
                        <a href="{{ route('dashboard') }}"
                           class="flex items-center gap-2.5 rounded-lg px-3 py-2 {{ request()->routeIs('dashboard') ? 'bg-ink text-white' : 'text-ink/70 hover:bg-black/5' }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('schedules.index') }}"
                           class="flex items-center gap-2.5 rounded-lg px-3 py-2 {{ request()->routeIs('schedules.*') ? 'bg-ink text-white' : 'text-ink/70 hover:bg-black/5' }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                            Test Schedules
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('questions.index') }}"
                           class="flex items-center gap-2.5 rounded-lg px-3 py-2 {{ request()->routeIs('questions.*') ? 'bg-ink text-white' : 'text-ink/70 hover:bg-black/5' }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/></svg>
                            Question Bank
                        </a>
                    </li>
                </ul>
            </div>
            <div>
                <p class="px-3 pb-2 text-[11px] font-medium uppercase tracking-wider text-muted">Management</p>
                <ul class="space-y-1 text-sm">
                    <li>
                        <a href="{{ route('participants.index') }}"
                           class="flex items-center gap-2.5 rounded-lg px-3 py-2 {{ request()->routeIs('participants.*') ? 'bg-ink text-white' : 'text-ink/70 hover:bg-black/5' }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                            Participants
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('results.index') }}"
                           class="flex items-center gap-2.5 rounded-lg px-3 py-2 {{ request()->routeIs('results.*') ? 'bg-ink text-white' : 'text-ink/70 hover:bg-black/5' }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                            Test Results
                        </a>
                    </li>
                </ul>
            </div>
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
@include('partials.confirm-dialog')
</body>
</html>
