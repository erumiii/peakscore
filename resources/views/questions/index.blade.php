@extends('layouts.admin')

@section('title', 'Question Bank — PeakScore')

@section('content')
<div class="space-y-6">
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Question Bank</h1>
            <p class="mt-1 text-sm text-muted">{{ $total }} questions registered</p>
        </div>
        <a href="{{ route('questions.create') }}"
           class="inline-flex items-center gap-1.5 rounded-lg bg-ink px-4 py-2 text-sm font-medium text-white hover:opacity-90">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add Question
        </a>
    </div>

    <div id="questions-results" class="space-y-4">
        <div class="flex items-center justify-between gap-3">
            <form method="GET" action="{{ route('questions.index') }}">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <div class="relative">
                    <select name="category" onchange="this.form.submit()"
                        class="appearance-none rounded-lg border border-canvas bg-white pl-3 pr-9 py-2 text-sm text-ink/80 focus:outline-none focus:ring-2 focus:ring-ink/10">
                        <option value="">All Category</option>
                        <option value="Numeric" {{ request('category') == 'Numeric' ? 'selected' : '' }}>Numeric ({{ $kategoriCount->get('Numeric', 0) }})</option>
                        <option value="Spatial" {{ request('category') == 'Spatial' ? 'selected' : '' }}>Spatial ({{ $kategoriCount->get('Spatial', 0) }})</option>
                        <option value="Logic"   {{ request('category') == 'Logic'   ? 'selected' : '' }}>Logic ({{ $kategoriCount->get('Logic', 0) }})</option>
                        <option value="Verbal"  {{ request('category') == 'Verbal'  ? 'selected' : '' }}>Verbal ({{ $kategoriCount->get('Verbal', 0) }})</option>
                    </select>
                    <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-ink/60">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </span>
                </div>
            </form>

            <form id="questions-search-form" method="GET" action="{{ route('questions.index') }}" class="flex items-center gap-2">
                <input type="hidden" name="category" value="{{ request('category') }}">
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-muted">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                        </svg>
                    </span>
                    <input id="searchQuery" type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search question..."
                        oninput="document.getElementById('searchClearBtn').style.display = this.value.trim() ? 'flex' : 'none'"
                        class="w-64 rounded-lg border border-canvas bg-white py-2 pl-9 pr-10 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-ink/10">
                    <button id="searchClearBtn" type="button"
                        onclick="clearSearchInput()"
                        class="absolute inset-y-0 right-2 flex items-center justify-center text-muted hover:text-ink"
                        style="display: {{ request('search') ? 'flex' : 'none' }};">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18" />
                            <line x1="6" y1="6" x2="18" y2="18" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-xl border border-canvas bg-white">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-line text-left text-[11px] uppercase tracking-wider text-muted">
                        <th class="px-5 py-3 font-medium">No</th>
                        <th class="px-5 py-3 font-medium">Question</th>
                        <th class="px-5 py-3 font-medium">Category</th>
                        <th class="px-5 py-3 text-right font-medium">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($soal as $item)
                        <tr class="border-b border-line last:border-0 hover:bg-canvas/50">
                            <td class="px-5 py-3.5 text-muted">{{ str_pad($loop->index + 1 + ($soal->currentPage() - 1) * $soal->perPage(), 3, '0', STR_PAD_LEFT) }}.</td>
                            <td class="px-5 py-3.5">{{ $item->isiSoal }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center rounded-full bg-black/5 px-2.5 py-1 text-xs font-medium text-ink/70">{{ $item->kategori }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('questions.edit', $item->soalId) }}" title="Edit"
                                       class="rounded-lg p-2 text-muted hover:bg-black/5 hover:text-ink">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 3.487a2.25 2.25 0 1 1 3.182 3.182L7.5 19.213l-4.5 1 1-4.5 12.862-12.726z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('questions.destroy', $item->soalId) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this question?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Delete"
                                            class="rounded-lg p-2 text-muted hover:bg-red-50 hover:text-red-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center text-sm text-muted">No questions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <span class="text-sm text-muted">
                Showing {{ $soal->firstItem() }}-{{ $soal->lastItem() }} of {{ $soal->total() }} questions
            </span>
            <div class="w-full md:w-auto">
                {{ $soal->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    async function clearSearchInput() {
        const input = document.getElementById('searchQuery');
        const clearBtn = document.getElementById('searchClearBtn');
        const category = document.querySelector('#questions-search-form input[name="category"]').value;

        input.value = '';
        clearBtn.style.display = 'none';
        input.dispatchEvent(new Event('input'));

        const url = new URL(window.location.href);
        url.searchParams.delete('search');
        url.searchParams.delete('page');
        if (category) {
            url.searchParams.set('category', category);
        } else {
            url.searchParams.delete('category');
        }

        try {
            const response = await fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) {
                return;
            }
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newResults = doc.querySelector('#questions-results');
            if (newResults) {
                document.getElementById('questions-results').innerHTML = newResults.innerHTML;
                window.history.replaceState({}, '', url.toString());
            }
        } catch (error) {
            console.error('Clear search failed', error);
        }
    }
</script>
@endsection
