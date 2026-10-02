@extends('layouts.admin')

@section('title', 'Answer Details — PeakScore')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div>
        <div class="mb-2 flex items-center text-sm">
            <a href="{{ route('results.index') }}" class="mr-1 text-muted hover:text-ink">Test Results</a>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mt-0.5 mr-1 size-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
            <p class="font-medium">Answer Details</p>
        </div>
        <h1 class="text-2xl font-semibold tracking-tight">{{ $hasil->user->name }}</h1>
        <p class="mt-1 text-sm text-muted">{{ $hasil->jadwal->judul }} · taken on {{ $hasil->created_at->format('d M Y, H:i') }}</p>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        @php($total = collect([$hasil->skorVerbal, $hasil->skorNumerik, $hasil->skorLogika, $hasil->skorSpasial])->filter()->sum())
        <div class="rounded-xl border border-canvas bg-white p-4 text-center">
            <p class="text-[11px] font-medium uppercase tracking-wider text-muted">Total Score</p>
            <p class="mt-1 text-2xl font-semibold">{{ $total }} <span class="text-sm font-normal text-muted">/ 1000</span></p>
        </div>
        <div class="rounded-xl border border-canvas bg-white p-4 text-center">
            <p class="text-[11px] font-medium uppercase tracking-wider text-muted">Transcript Status</p>
            <p class="mt-2">
                @if($hasil->diterbitkanPada)
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
            </p>
        </div>
        <div class="rounded-xl border border-canvas bg-white p-4 text-center">
            <p class="text-[11px] font-medium uppercase tracking-wider text-muted">Recorded Answers</p>
            <p class="mt-1 text-2xl font-semibold">{{ $jawaban->count() }}</p>
        </div>
    </div>

    <div class="space-y-3">
        @if($jawaban->isEmpty())
            <div class="rounded-xl border border-canvas bg-white px-6 py-16 text-center">
                <p class="text-sm font-medium">No recorded answers</p>
                <p class="mt-1 text-sm text-muted">This attempt happened before answer recording was introduced.</p>
            </div>
        @else
            @foreach($jawaban as $j)
                @php($benar = $j->opsiDipilih === $j->soal->jawabanBenar)
                <div class="rounded-xl border border-canvas bg-white p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            @if($j->soal->gambarSoal)
                                <img src="{{ asset($j->soal->gambarSoal) }}" alt="Question image" class="mb-2 max-h-40 rounded-lg border border-line">
                            @endif
                            @if($j->soal->isiSoal)
                                <p class="font-medium">{{ $j->soal->isiSoal }}</p>
                            @endif
                        </div>
                        <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium
                            {{ $benar ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600' }}">
                            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                            {{ $benar ? 'Correct' : 'Incorrect' }}
                        </span>
                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-lg border px-3 py-2 {{ $benar ? 'border-emerald-200 bg-emerald-50' : 'border-red-200 bg-red-50' }}">
                            <p class="text-[11px] uppercase tracking-wider text-muted">Participant Answer</p>
                            @if($j->soal->{'gambarOpsi'.$j->opsiDipilih})
                                <img src="{{ asset($j->soal->{'gambarOpsi'.$j->opsiDipilih}) }}" alt="Option {{ $j->opsiDipilih }}" class="mt-1 max-h-24 rounded border border-line">
                            @else
                                <p class="mt-0.5 font-medium">{{ $j->opsiDipilih }}. {{ $j->soal->{'opsi'.$j->opsiDipilih} }}</p>
                            @endif
                        </div>
                        <div class="rounded-lg border border-line bg-canvas px-3 py-2">
                            <p class="text-[11px] uppercase tracking-wider text-muted">Correct Answer</p>
                            @if($j->soal->{'gambarOpsi'.$j->soal->jawabanBenar})
                                <img src="{{ asset($j->soal->{'gambarOpsi'.$j->soal->jawabanBenar}) }}" alt="Correct option {{ $j->soal->jawabanBenar }}" class="mt-1 max-h-24 rounded border border-line">
                            @else
                                <p class="mt-0.5 font-medium">{{ $j->soal->jawabanBenar }}. {{ $j->soal->{'opsi'.$j->soal->jawabanBenar} }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
