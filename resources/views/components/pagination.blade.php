@props(['paginator', 'showInfo' => true])

@if ($paginator->hasPages())
    <nav aria-label="Pagination">
        <ul class="flex justify-center items-center gap-1 sm:gap-2">
            {{-- Previous Button --}}
            <li>
                @if ($paginator->onFirstPage())
                    <span
                        class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-lg border-2 border-gray-300 text-gray-400 cursor-not-allowed bg-gray-100">
                        <span class="sr-only">Previous</span>
                        <x-heroicon-s-chevron-left class="w-4 h-4" />
                    </span>
                @else
                    <button type="button" wire:click="previousPage"
                        class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-lg border-2 border-gray-300 hover:border-gsi-red hover:text-gsi-red text-gray-700 transition-all bg-white shadow-sm">
                        <span class="sr-only">Previous</span>
                        <x-heroicon-s-chevron-left class="w-4 h-4" />
                    </button>
                @endif
            </li>

            {{-- Page Numbers --}}
            @php
                $currentPage = $paginator->currentPage();
                $lastPage = $paginator->lastPage();
                $start = max(1, $currentPage - 2);
                $end = min($lastPage, $currentPage + 2);
            @endphp

            {{-- First page --}}
            @if ($start > 1)
                <li>
                    <button type="button" wire:click="gotoPage(1)"
                        class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-lg border-2 border-gray-300 hover:border-gsi-red hover:text-gsi-red text-gray-700 transition-all bg-white shadow-sm text-sm font-medium">1</button>
                </li>
                @if ($start > 2)
                    <li class="hidden sm:inline-flex">
                        <span
                            class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 text-gray-500">...</span>
                    </li>
                @endif
            @endif

            {{-- Page numbers around current page --}}
            @for ($page = $start; $page <= $end; $page++)
                <li>
                    @if ($page == $currentPage)
                        <span aria-current="page"
                            class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-gsi-red border-2 border-gsi-red text-white font-semibold text-sm shadow-md">{{ $page }}</span>
                    @else
                        <button type="button" wire:click="gotoPage({{ $page }})"
                            class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-lg border-2 border-gray-300 hover:border-gsi-red hover:text-gsi-red text-gray-700 transition-all bg-white shadow-sm text-sm font-medium">{{ $page }}</button>
                    @endif
                </li>
            @endfor

            {{-- Last page --}}
            @if ($end < $lastPage)
                @if ($end < $lastPage - 1)
                    <li class="hidden sm:inline-flex">
                        <span
                            class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 text-gray-500">...</span>
                    </li>
                @endif
                <li>
                    <button type="button" wire:click="gotoPage({{ $lastPage }})"
                        class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-lg border-2 border-gray-300 hover:border-gsi-red hover:text-gsi-red text-gray-700 transition-all bg-white shadow-sm text-sm font-medium">{{ $lastPage }}</button>
                </li>
            @endif

            {{-- Next Button --}}
            <li>
                @if ($paginator->hasMorePages())
                    <button type="button" wire:click="nextPage"
                        class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-lg border-2 border-gray-300 hover:border-gsi-red hover:text-gsi-red text-gray-700 transition-all bg-white shadow-sm">
                        <span class="sr-only">Next</span>
                        <x-heroicon-s-chevron-right class="w-4 h-4" />
                    </button>
                @else
                    <span
                        class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-lg border-2 border-gray-300 text-gray-400 cursor-not-allowed bg-gray-100">
                        <span class="sr-only">Next</span>
                        <x-heroicon-s-chevron-right class="w-4 h-4" />
                    </span>
                @endif
            </li>
        </ul>
    </nav>
@endif

