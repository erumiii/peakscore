@extends('layouts.admin')

@section('title', 'Dashboard — PeakScore')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight">Dashboard</h1>
        <p class="mt-1 text-sm text-muted">Overview of test administration.</p>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <a href="{{ route('schedules.index') }}"
           class="rounded-xl border border-canvas bg-white p-5 transition hover:border-ink/30">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-medium uppercase tracking-wider text-muted">Test Schedules</p>
                <svg class="h-4 w-4 text-muted" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
            </div>
            <p class="mt-2 text-3xl font-semibold">{{ $totalJadwal }}</p>
        </a>

        <a href="{{ route('questions.index') }}"
           class="rounded-xl border border-canvas bg-white p-5 transition hover:border-ink/30">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-medium uppercase tracking-wider text-muted">Total Questions</p>
                <svg class="h-4 w-4 text-muted" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
            </div>
            <p class="mt-2 text-3xl font-semibold">{{ $totalSoal }}</p>
        </a>

        <div class="rounded-xl border border-canvas bg-white p-5">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-medium uppercase tracking-wider text-muted">Total Participants</p>
                <svg class="h-4 w-4 text-muted" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
            </div>
            <p class="mt-2 text-3xl font-semibold">{{ $totalPeserta }}</p>
        </div>

        <a href="{{ route('results.index') }}"
           class="rounded-xl border border-canvas bg-white p-5 transition hover:border-ink/30">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-medium uppercase tracking-wider text-muted">Test Results</p>
                <svg class="h-4 w-4 text-muted" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
            </div>
            <p class="mt-2 text-3xl font-semibold">{{ $totalHasil }}</p>
            <p class="mt-1 text-xs text-muted">{{ $passRate }}% passed</p>
        </a>
    </div>

    <div class="grid gap-4 lg:grid-cols-5">
        <div class="rounded-xl border border-canvas bg-white lg:col-span-2">
            <div class="border-b border-line px-5 py-3.5">
                <p class="text-sm font-semibold">Questions by Category</p>
            </div>
            <div class="space-y-4 px-5 py-4">
                @if($soalPerKategori->sum() === 0)
                    <p class="py-6 text-center text-sm text-muted">No questions yet.</p>
                @else
                    @php($maxKategori = max(1, $soalPerKategori->max()))
                    @foreach(['Verbal', 'Numeric', 'Logic', 'Spatial'] as $kategori)
                        @php($jumlah = $soalPerKategori->get($kategori, 0))
                        <div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-medium">{{ $kategori }}</span>
                                <span class="text-muted">{{ $jumlah }}</span>
                            </div>
                            <div class="mt-1.5 h-1.5 rounded-full bg-canvas">
                                <div class="h-1.5 rounded-full bg-ink" style="width: {{ round($jumlah / $maxKategori * 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="rounded-xl border border-canvas bg-white lg:col-span-3">
            <div class="flex items-center justify-between border-b border-line px-5 py-3.5">
                <p class="text-sm font-semibold">Recent Test Activity</p>
                @if($aktivitas->isNotEmpty())
                    <a href="{{ route('results.index') }}" class="text-xs font-medium text-muted hover:text-ink">View all</a>
                @endif
            </div>
            @if($aktivitas->isEmpty())
                <div class="px-6 py-14 text-center">
                    <p class="text-sm font-medium">No activity yet</p>
                    <p class="mt-1 text-sm text-muted">Participant activity will appear here once tests are taken.</p>
                </div>
            @else
                <ul class="divide-y divide-line">
                    @foreach($aktivitas as $item)
                        <li class="flex items-center justify-between px-5 py-3.5">
                            <div>
                                <p class="text-sm font-medium">{{ $item->user->name }} <span class="font-normal text-muted">took</span> {{ $item->jadwal->judul }}</p>
                                <p class="mt-0.5 text-xs text-muted">{{ $item->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            @if($item->diterbitkanPada)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    Transcript published
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-neutral-100 px-2.5 py-1 text-xs font-medium text-neutral-500">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    Awaiting transcript
                                </span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection
