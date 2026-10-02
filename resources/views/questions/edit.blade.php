@extends('layouts.admin')

@section('title', 'Edit Question — PeakScore')

@section('content')
<div class="max-w-3xl space-y-6">
    <div>
        <div class="mb-2 flex items-center text-sm">
            <a href="{{ route('questions.index') }}" class="mr-1 text-muted hover:text-ink">Questions</a>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mt-0.5 mr-1 size-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
            <p class="font-medium">Edit Question</p>
        </div>
        <h1 class="text-2xl font-semibold tracking-tight">Edit Question</h1>
        <p class="mt-1 text-sm text-muted">Make sure to input the data correctly.</p>
    </div>

    <div class="rounded-xl border border-canvas bg-white p-8">
        <form action="{{ route('questions.update', $question->soalId) }}" method="POST" enctype="multipart/form-data" class="w-full space-y-5">
            @csrf
            @method('PUT')

            <div class="flex gap-x-5">
                <div class="flex-1">
                    <label class="mb-1.5 block text-sm font-medium">Category</label>
                    <select name="kategori"
                        class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ink/10 {{ $errors->has('kategori') ? 'border-red-400' : 'border-line' }}">
                        <option value="" disabled hidden>Choose a category</option>
                        @foreach(['Verbal', 'Numeric', 'Logic', 'Spatial'] as $cat)
                            <option value="{{ $cat }}" {{ old('kategori', $question->kategori) == $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                    <div class="mt-1 min-h-5">
                        @error('kategori')
                            <p class="text-xs text-red-500">The category field is required.</p>
                        @enderror
                    </div>
                </div>

                <div class="flex-1">
                    <label class="mb-1.5 block text-sm font-medium">Correct Answer</label>
                    <select name="jawabanBenar"
                        class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ink/10 {{ $errors->has('jawabanBenar') ? 'border-red-400' : 'border-line' }}">
                        <option value="" disabled hidden>Choose an answer</option>
                        @foreach(['A', 'B', 'C', 'D'] as $ans)
                            <option value="{{ $ans }}" {{ old('jawabanBenar', $question->jawabanBenar) == $ans ? 'selected' : '' }}>
                                {{ $ans }}
                            </option>
                        @endforeach
                    </select>
                    <div class="mt-1 min-h-5">
                        @error('jawabanBenar')
                            <p class="text-xs text-red-500">The correct answer field is required.</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium">Question Text</label>
                <textarea name="isiSoal" placeholder="Enter question instruction in here" rows="4"
                    class="w-full resize-none rounded-lg border px-3 py-2 text-left align-top text-sm focus:outline-none focus:ring-2 focus:ring-ink/10 {{ $errors->has('isiSoal') ? 'border-red-400' : 'border-line' }}">{{ old('isiSoal', $question->isiSoal) }}</textarea>
                <div class="mt-1 min-h-5">
                    @error('isiSoal')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mt-3">
                    <label class="mb-1.5 block text-sm font-medium">Question Image <span class="font-normal text-muted">(optional, max 2 MB)</span></label>
                    @if($question->gambarSoal && !$errors->has('gambarSoal'))
                        <img src="{{ asset($question->gambarSoal) }}" alt="Current question image" class="mb-2 max-h-40 rounded-lg border border-line">
                        <p class="mb-1 text-xs text-muted">Leave empty to keep the image above.</p>
                    @endif
                    <input type="file" name="gambarSoal" accept=".jpg,.jpeg,.png,.webp"
                        class="w-full rounded-lg border px-3 py-2 text-sm {{ $errors->has('gambarSoal') ? 'border-red-400' : 'border-line' }}">
                    <div class="mt-1 min-h-5">
                        @error('gambarSoal')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div>
                <p class="mb-3 text-sm font-medium">Answer Option</p>
                @foreach(['A','B','C','D'] as $opt)
                <div class="mb-2">
                    <div class="flex items-center">
                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-ink text-sm font-medium text-white">{{ $opt }}</span>
                        <input type="text" name="opsi{{ $opt }}"
                            value="{{ old('opsi'.$opt, $question->{'opsi'.$opt}) }}"
                            placeholder="Insert {{ $opt }} text answer"
                            class="ml-2 w-full rounded-lg border px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ink/10 {{ $errors->has('opsi'.$opt) ? 'border-red-400' : 'border-line' }}">
                    </div>
                    <div class="mt-1 ml-12 min-h-5">
                        @error('opsi'.$opt)
                            <p class="text-xs text-red-500">The option {{ $opt }} field is required.</p>
                        @enderror
                    </div>
                    <div class="mt-1 ml-12 flex items-center gap-2">
                        <label class="text-xs text-muted">atau gambar:</label>
                        @if($question->{'gambarOpsi'.$opt})
                            <img src="{{ asset($question->{'gambarOpsi'.$opt}) }}" alt="Current option {{ $opt }} image" class="max-h-16 rounded border border-line">
                        @endif
                        <input type="file" name="gambarOpsi{{ $opt }}" accept=".jpg,.jpeg,.png,.webp"
                            class="text-xs {{ $errors->has('gambarOpsi'.$opt) ? 'text-red-500' : '' }}">
                    </div>
                    <div class="mt-1 ml-12 min-h-5">
                        @error('gambarOpsi'.$opt)
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                @endforeach
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <a href="{{ route('questions.index') }}"
                    class="rounded-lg border border-line px-4 py-2 text-sm font-medium hover:bg-black/5">
                    Cancel
                </a>
                <button type="submit"
                    class="rounded-lg bg-ink px-4 py-2 text-sm font-medium text-white hover:opacity-90">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
