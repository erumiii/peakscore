@extends('layouts.peserta')

@section('title', 'Test — PeakScore')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight">{{ $jadwal->judul }}</h1>
        <p class="mt-1 text-sm text-muted">
            Session ends {{ $jadwal->selesai->format('d M Y, H:i') }} — answer all questions then submit.
        </p>
    </div>

    @if($errors->any() || session('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-sm text-red-600">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
            {{ session('error') }}
        </div>
    @endif

    @if($soal->count())
        <div class="rounded-xl border border-canvas bg-white p-4">
            <div class="flex items-center justify-between text-sm">
                <span class="font-medium">Progress</span>
                <span class="text-muted"><span id="answeredCount">0</span> / {{ $soal->count() }} answered</span>
            </div>
            <div class="mt-2 h-2 rounded-full bg-canvas">
                <div id="progressFill" class="h-2 w-0 rounded-full bg-ink"></div>
            </div>
        </div>
    @endif

    <form id="testForm" method="POST" action="{{ route('peserta.test.submit', $jadwal->id) }}" class="space-y-4">
        @csrf

        @php($kategoriSekarang = null)
        @foreach($soal as $s)
            @if($kategoriSekarang !== $s->kategori)
                @php($kategoriSekarang = $s->kategori)
                <p class="pt-2 text-[11px] font-medium uppercase tracking-wider text-muted">{{ $s->kategori }}</p>
            @endif

            <div class="rounded-xl border border-canvas bg-white p-5">
                @if($s->gambarSoal)
                    <img src="{{ asset($s->gambarSoal) }}" alt="Question image" class="mb-3 max-h-64 rounded-lg border border-line">
                @endif
                @if($s->isiSoal)
                    <p class="font-medium">{{ $s->isiSoal }}</p>
                @endif
                <div class="mt-3 space-y-2">
                    @foreach(['A', 'B', 'C', 'D'] as $opt)
                        <label class="flex items-center gap-2.5 rounded-lg border border-line px-3 py-2 text-sm has-[:checked]:border-ink has-[:checked]:bg-canvas">
                            <input type="radio" name="jawaban[{{ $s->soalId }}]" value="{{ $opt }}"
                                   @checked(old('jawaban.'.$s->soalId) === $opt) required>
                            @if($s->{'gambarOpsi'.$opt})
                                <img src="{{ asset($s->{'gambarOpsi'.$opt}) }}" alt="Option {{ $opt }}" class="max-h-24 rounded border border-line">
                            @else
                                <span>{{ $s->{'opsi'.$opt} }}</span>
                            @endif
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="flex justify-end">
            <button type="submit"
                    class="rounded-lg bg-ink px-5 py-2.5 text-sm font-medium text-white hover:opacity-90"
                    onclick="return confirm('Submit answers? Answers cannot be changed after submission.')">
                Submit Answers
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
    @if($soal->count())
        <script>
            const testForm = document.getElementById('testForm');
            const updateProgress = () => {
                const answered = new Set(
                    Array.from(testForm.querySelectorAll('input[type="radio"]:checked')).map((input) => input.name)
                );
                const pct = Math.round((answered.size / {{ $soal->count() }}) * 100);
                document.getElementById('answeredCount').textContent = answered.size;
                document.getElementById('progressFill').style.width = pct + '%';
            };
            testForm.addEventListener('change', updateProgress);
            updateProgress();
        </script>
    @endif
@endsection
