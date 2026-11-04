@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex flex-wrap items-center justify-center gap-1.5">
        @if ($paginator->onFirstPage())
            <span aria-disabled="true" aria-label="Sebelumnya"
                class="flex h-11 min-w-11 cursor-default items-center justify-center rounded-sm border border-stone-300 bg-[#eee5d6] px-3 font-mono text-xs txt-muted dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-600">
                &larr;
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Sebelumnya"
                class="flex h-11 min-w-11 items-center justify-center rounded-sm border border-stone-300 bg-[#fffdf7] px-3 font-mono text-xs font-bold text-stone-600 transition-colors hover:border-[#be1836] hover:bg-[#be1836] hover:text-white dark:border-white/15 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:border-rose-500 dark:hover:bg-rose-600 dark:hover:text-white">
                &larr;
            </a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span aria-hidden="true" class="flex h-11 min-w-8 items-center justify-center font-mono text-xs txt-muted">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" aria-label="Halaman {{ $page }}"
                            class="flex h-11 min-w-11 cursor-default items-center justify-center rounded-sm border border-[#760b20] bg-[#be1836] px-3 font-mono text-xs font-bold text-white shadow-[0_3px_0_#760b20]">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" aria-label="Halaman {{ $page }}"
                            class="flex h-11 min-w-11 items-center justify-center rounded-sm border border-stone-300 bg-[#fffdf7] px-3 font-mono text-xs font-bold text-stone-600 transition-colors hover:border-[#be1836] hover:text-[#be1836] dark:border-white/15 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:border-rose-400 dark:hover:text-rose-400">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Berikutnya"
                class="flex h-11 min-w-11 items-center justify-center rounded-sm border border-stone-300 bg-[#fffdf7] px-3 font-mono text-xs font-bold text-stone-600 transition-colors hover:border-[#be1836] hover:bg-[#be1836] hover:text-white dark:border-white/15 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:border-rose-500 dark:hover:bg-rose-600 dark:hover:text-white">
                &rarr;
            </a>
        @else
            <span aria-disabled="true" aria-label="Berikutnya"
                class="flex h-11 min-w-11 cursor-default items-center justify-center rounded-sm border border-stone-300 bg-[#eee5d6] px-3 font-mono text-xs txt-muted dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-600">
                &rarr;
            </span>
        @endif
    </nav>
@endif
