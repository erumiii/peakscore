@extends('layouts.peserta')

@section('title', 'My Results — PeakScore')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight">My Results</h1>
        <p class="mt-1 text-sm text-muted">Scores appear after the organizer publishes the transcript.</p>
    </div>

    @if($hasil->isNotEmpty())
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-canvas bg-white p-5">
                <p class="text-[11px] font-medium uppercase tracking-wider text-muted">Tests Taken</p>
                <p class="mt-2 text-3xl font-semibold">{{ $taken }}</p>
            </div>
            <div class="rounded-xl border border-canvas bg-white p-5">
                <p class="text-[11px] font-medium uppercase tracking-wider text-muted">Best Score</p>
                <p class="mt-2 text-3xl font-semibold">
                    @if($best !== null)
                        {{ $best }}<span class="text-sm font-normal text-muted"> / 1000</span>
                    @else
                        <span class="text-ink/40">—</span>
                    @endif
                </p>
            </div>
            @if($best !== null)
                <div class="rounded-xl border border-canvas bg-white p-5">
                    <p class="text-[11px] font-medium uppercase tracking-wider text-muted">Passed</p>
                    <p class="mt-2 text-3xl font-semibold">{{ $passed }}</p>
                </div>
            @endif
        </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-canvas bg-white">
        @if($hasil->isEmpty())
            <div class="px-6 py-16 text-center">
                <p class="text-sm font-medium">No test results yet</p>
                <p class="mt-1 text-sm text-muted">Take a test from the available schedules.</p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-line text-left text-[11px] uppercase tracking-wider text-muted">
                        <th class="px-5 py-3 font-medium">Schedule</th>
                        <th class="px-5 py-3 font-medium">Test Date</th>
                        <th class="px-5 py-3 text-center font-medium">Verbal</th>
                        <th class="px-5 py-3 text-center font-medium">Numeric</th>
                        <th class="px-5 py-3 text-center font-medium">Logic</th>
                        <th class="px-5 py-3 text-center font-medium">Spatial</th>
                        <th class="px-5 py-3 text-center font-medium">Total Score</th>
                        <th class="px-5 py-3 font-medium">Result</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($hasil as $item)
                        @php($total = collect([$item->skorVerbal, $item->skorNumerik, $item->skorLogika, $item->skorSpasial])->filter()->sum())
                        <tr class="border-b border-line last:border-0 hover:bg-canvas/50">
                            <td class="px-5 py-3.5">
                                <p class="font-medium">{{ $item->jadwal->judul }}</p>
                            </td>
                            <td class="whitespace-nowrap px-5 py-3.5 text-ink/70">{{ $item->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-5 py-3.5 text-center">@if($item->diterbitkanPada) {{ $item->skorVerbal ?? '—' }} @else <span class="text-ink/40">—</span> @endif</td>
                            <td class="px-5 py-3.5 text-center">@if($item->diterbitkanPada) {{ $item->skorNumerik ?? '—' }} @else <span class="text-ink/40">—</span> @endif</td>
                            <td class="px-5 py-3.5 text-center">@if($item->diterbitkanPada) {{ $item->skorLogika ?? '—' }} @else <span class="text-ink/40">—</span> @endif</td>
                            <td class="px-5 py-3.5 text-center">@if($item->diterbitkanPada) {{ $item->skorSpasial ?? '—' }} @else <span class="text-ink/40">—</span> @endif</td>
                            <td class="px-5 py-3.5 text-center">
                                @if($item->diterbitkanPada)
                                    <span class="text-lg font-semibold">{{ $total }}</span>
                                    <span class="text-xs text-muted">/ 1000</span>
                                @else
                                    <span class="text-ink/40">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                @if($item->diterbitkanPada)
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium
                                        {{ $total >= 700 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600' }}">
                                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                        {{ $total >= 700 ? 'Passed' : 'Not Passed' }}
                                    </span>
                                    <span class="mt-1 block text-[11px] text-muted">Published {{ $item->diterbitkanPada->format('d M Y') }}</span>
                                    <a href="{{ route('results.transcript', $item->id) }}"
                                        class="mt-2 inline-block text-[11px] font-medium underline underline-offset-2 hover:text-ink">
                                        View Transcript
                                    </a>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-neutral-100 px-2.5 py-1 text-xs font-medium text-neutral-500">
                                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                        Awaiting transcript
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
