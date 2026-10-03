@extends('layouts.admin')

@section('title', 'Test Results — PeakScore')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight">Test Results</h1>
        <p class="mt-1 text-sm text-muted">{{ $hasil->total() }} test results recorded</p>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if($totalHasil > 0)
        <div class="rounded-xl border border-canvas bg-white p-5">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <p class="text-[11px] font-medium uppercase tracking-wider text-muted">Total Results</p>
                    <p class="mt-1 text-2xl font-semibold">{{ $totalHasil }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-medium uppercase tracking-wider text-muted">Passed</p>
                    <p class="mt-1 text-2xl font-semibold">{{ $lulus }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-medium uppercase tracking-wider text-muted">Pass Rate</p>
                    <p class="mt-1 text-2xl font-semibold">{{ $passRate }}%</p>
                </div>
            </div>
            <div class="mt-4 h-2 rounded-full bg-canvas">
                <div class="h-2 rounded-full bg-emerald-500" style="width: {{ $passRate }}%"></div>
            </div>
            <div class="mt-2 flex justify-between text-xs text-muted">
                <span>Passed</span>
                <span>Not Passed</span>
            </div>
        </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-canvas bg-white">
        @if($hasil->isEmpty())
            <div class="px-6 py-16 text-center">
                <p class="text-sm font-medium">No test results yet</p>
                <p class="mt-1 text-sm text-muted">Results will appear after participants take a test.</p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-line text-left text-[11px] uppercase tracking-wider text-muted">
                        <th class="px-5 py-3 font-medium">Participant</th>
                        <th class="px-5 py-3 font-medium">Schedule</th>
                        <th class="px-5 py-3 text-center font-medium">Verbal</th>
                        <th class="px-5 py-3 text-center font-medium">Numeric</th>
                        <th class="px-5 py-3 text-center font-medium">Logic</th>
                        <th class="px-5 py-3 text-center font-medium">Spatial</th>
                        <th class="px-5 py-3 text-center font-medium">Total</th>
                        <th class="px-5 py-3 font-medium">Transcript</th>
                        <th class="px-5 py-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($hasil as $item)
                        @php($total = collect([$item->skorVerbal, $item->skorNumerik, $item->skorLogika, $item->skorSpasial])->filter()->sum())
                        <tr class="border-b border-line last:border-0 hover:bg-canvas/50">
                            <td class="px-5 py-3.5">
                                <p class="font-medium">{{ $item->user->name }}</p>
                                <p class="text-xs text-muted">{{ $item->user->username }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-ink/70">{{ $item->jadwal->judul }}</td>
                            <td class="px-5 py-3.5 text-center">{{ $item->skorVerbal ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-center">{{ $item->skorNumerik ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-center">{{ $item->skorLogika ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-center">{{ $item->skorSpasial ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-center font-semibold">{{ $total > 0 ? $total : '—' }}</td>
                            <td class="px-5 py-3.5">
                                @if($item->diterbitkanPada)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                        Published
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-neutral-100 px-2.5 py-1 text-xs font-medium text-neutral-500">
                                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('results.show', $item->id) }}" title="Answer details"
                                       class="rounded-lg p-2 text-muted hover:bg-black/5 hover:text-ink">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </a>
                                    @if($item->diterbitkanPada)
                                        <a href="{{ route('results.transcript', $item->id) }}" title="Transcript"
                                       class="rounded-lg p-2 text-muted hover:bg-black/5 hover:text-ink">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 12h5.25m-5.25 3h5.25M6.75 21h10.5a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0017.25 4.5h-5.25a2.25 2.25 0 00-2.25 2.25v12a2.25 2.25 0 002.25 2.25z"/></svg>
                                    </a>
                                    @endif
                                    @if(!$item->diterbitkanPada)
                                        <form method="POST" action="{{ route('results.publish', $item->id) }}"
                                              data-confirm="Publish transcript for {{ $item->user->name }}?">
                                            @csrf
                                            <button type="submit"
                                                    class="rounded-lg border border-line px-3 py-1.5 text-xs font-medium hover:bg-black/5">
                                                Publish
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{ $hasil->links() }}
</div>
@endsection
