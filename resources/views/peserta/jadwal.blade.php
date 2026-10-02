@extends('layouts.peserta')

@section('title', 'Test Schedules — PeakScore')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight">Test Schedules</h1>
        <p class="mt-1 text-sm text-muted">Tests can only be taken during their scheduled window.</p>
    </div>

    @if($aktif)
        <div class="flex flex-col gap-4 rounded-xl border border-canvas bg-white p-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    Open Now
                </span>
                <p class="mt-2 text-lg font-semibold">{{ $aktif->judul }}</p>
                <p class="mt-0.5 text-sm text-muted">
                    Time left: {{ max(0, (int) now()->diffInMinutes($aktif->selesai)) }} minutes —
                    closes {{ $aktif->selesai->format('d M Y, H:i') }}
                </p>
            </div>
            <a href="{{ route('peserta.test.show', $aktif->id) }}"
               class="rounded-lg bg-ink px-5 py-2.5 text-center text-sm font-medium text-white hover:opacity-90">
                Start Test
            </a>
        </div>
    @endif

    @if(session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-sm text-red-600">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-canvas bg-white">
        @if($jadwal->isEmpty())
            <div class="px-6 py-16 text-center">
                <p class="text-sm font-medium">No test schedules yet</p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-line text-left text-[11px] uppercase tracking-wider text-muted">
                        <th class="px-5 py-3 font-medium">Schedule</th>
                        <th class="px-5 py-3 font-medium">Starts</th>
                        <th class="px-5 py-3 font-medium">Ends</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwal as $item)
                        @php($status = $item->status())
                        @php($sudah = $dikerjakan->contains($item->id))
                        <tr class="border-b border-line last:border-0 {{ $status === 'Ongoing' && !$sudah ? 'bg-emerald-50/40' : 'hover:bg-canvas/50' }}">
                            <td class="px-5 py-3.5">
                                <p class="font-medium">{{ $item->judul }}</p>
                                @if($item->deskripsi)
                                    <p class="mt-0.5 text-xs text-muted">{{ $item->deskripsi }}</p>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-3.5 text-ink/70">{{ $item->mulai->format('d M Y, H:i') }}</td>
                            <td class="whitespace-nowrap px-5 py-3.5 text-ink/70">{{ $item->selesai->format('d M Y, H:i') }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium
                                    {{ match($status) {
                                        'Scheduled' => 'bg-blue-50 text-blue-700',
                                        'Ongoing' => 'bg-emerald-50 text-emerald-700',
                                        default => 'bg-neutral-100 text-neutral-500',
                                    } }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    {{ $sudah ? 'Completed' : $status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex justify-end">
                                    @if($sudah)
                                        <span class="text-xs text-muted">Completed</span>
                                    @elseif($status === 'Ongoing')
                                        <a href="{{ route('peserta.test.show', $item->id) }}"
                                           class="rounded-lg bg-ink px-4 py-2 text-sm font-medium text-white hover:opacity-90">
                                            Start Test
                                        </a>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-xs text-muted" title="Locked outside the schedule window">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                                            Locked
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
