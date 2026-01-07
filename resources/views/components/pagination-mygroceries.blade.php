@props(['paginator'])

@if ($paginator->hasPages())
    <nav role="navigation" class="flex items-center justify-end gap-1 text-[11px] sm:text-xs">

        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg
                         bg-slate-100 text-slate-300 cursor-default select-none">
                <i class="fa fa-chevron-left text-[9px]"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               class="inline-flex h-7 w-7 items-center justify-center rounded-lg
                      bg-slate-50 text-slate-500 border border-slate-200
                      hover:bg-slate-100 hover:text-slate-700 transition">
                <i class="fa fa-chevron-left text-[9px]"></i>
            </a>
        @endif

        {{-- Page numbers --}}
        @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
            @if ($page == $paginator->currentPage())
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg
                             bg-slate-200 text-slate-800 font-semibold shadow-sm">
                    {{ $page }}
                </span>
            @else
                <a href="{{ $url }}"
                   class="inline-flex h-7 w-7 items-center justify-center rounded-lg
                          bg-white text-slate-500 border border-slate-200
                          hover:bg-slate-100 hover:text-slate-800 transition">
                    {{ $page }}
                </a>
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               class="inline-flex h-7 w-7 items-center justify-center rounded-lg
                      bg-slate-50 text-slate-500 border border-slate-200
                      hover:bg-slate-100 hover:text-slate-700 transition">
                <i class="fa fa-chevron-right text-[9px]"></i>
            </a>
        @else
            <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg
                         bg-slate-100 text-slate-300 cursor-default select-none">
                <i class="fa fa-chevron-right text-[9px]"></i>
            </span>
        @endif
    </nav>
@endif
