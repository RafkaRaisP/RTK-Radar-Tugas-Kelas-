@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-center gap-1">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="flex items-center justify-center w-11 h-11 border-2 border-hitam-pekat dark:border-merah-elegan font-mono text-xs text-zinc-400 cursor-default">
                &larr;
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                class="flex items-center justify-center w-11 h-11 border-2 border-hitam-pekat dark:border-merah-elegan font-mono text-xs hover:bg-merah-elegan hover:border-merah-elegan hover:text-putih-kertas transition-colors"
                aria-label="Sebelumnya">
                &larr;
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="flex items-center justify-center w-11 h-11 border-2 border-hitam-pekat dark:border-merah-elegan font-mono text-xs text-zinc-400 cursor-default">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page"
                            class="flex items-center justify-center w-11 h-11 border-2 border-hitam-pekat dark:border-merah-elegan bg-merah-elegan text-putih-kertas font-mono text-xs cursor-default">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                            class="flex items-center justify-center w-11 h-11 border-2 border-hitam-pekat dark:border-merah-elegan font-mono text-xs hover:bg-hitam-pekat dark:hover:bg-merah-elegan hover:text-putih-kertas transition-colors"
                            aria-label="Halaman {{ $page }}">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                class="flex items-center justify-center w-11 h-11 border-2 border-hitam-pekat dark:border-merah-elegan font-mono text-xs hover:bg-merah-elegan hover:border-merah-elegan hover:text-putih-kertas transition-colors"
                aria-label="Berikutnya">
                &rarr;
            </a>
        @else
            <span class="flex items-center justify-center w-11 h-11 border-2 border-hitam-pekat dark:border-merah-elegan font-mono text-xs text-zinc-400 cursor-default">
                &rarr;
            </span>
        @endif
    </nav>
@endif
