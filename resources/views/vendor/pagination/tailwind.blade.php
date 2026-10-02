@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination Navigation" class="inline-flex items-center gap-1.5">
    @php($box = 'grid h-9 w-9 place-items-center rounded-lg border text-sm transition select-none')

    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <span class="{{ $box }} border-line text-muted/50 cursor-not-allowed" aria-hidden="true">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
        </span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous page"
           class="{{ $box }} border-line text-ink/70 hover:bg-black/5">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
        </a>
    @endif

    {{-- Page numbers --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="grid h-9 w-6 place-items-center text-sm text-muted select-none">{{ $element }}</span>
        @elseif (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span aria-current="page" class="{{ $box }} border-ink bg-ink font-medium text-white">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" aria-label="Page {{ $page }}"
                       class="{{ $box }} border-line text-ink/70 hover:bg-black/5">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page"
           class="{{ $box }} border-line text-ink/70 hover:bg-black/5">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        </a>
    @else
        <span class="{{ $box }} border-line text-muted/50 cursor-not-allowed" aria-hidden="true">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        </span>
    @endif
</nav>
@endif
