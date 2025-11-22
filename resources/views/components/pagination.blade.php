@props(['paginator', 'showInfo' => true])

@if ($paginator->hasPages())
    <nav class="mt-8" aria-label="Pagination">
        <ul class="flex justify-center items-center space-x-2 text-sm">
            {{-- Previous Button --}}
            <li>
                @if ($paginator->onFirstPage())
                    <span
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-gray-200 text-gray-400 cursor-not-allowed">
                        <span class="sr-only">Previous</span>
                        <x-heroicon-s-chevron-left class="w-4 h-4" />
                    </span>
                @else
                    <button type="button" wire:click="previousPage"
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white hover:bg-gray-100 text-gray-700 transition-colors">
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
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white hover:bg-gray-100 text-gray-700 transition-colors">1</button>
                </li>
                @if ($start > 2)
                    <li>
                        <span
                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white text-gray-700">...</span>
                    </li>
                @endif
            @endif

            {{-- Page numbers around current page --}}
            @for ($page = $start; $page <= $end; $page++)
                <li>
                    @if ($page == $currentPage)
                        <span aria-current="page"
                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-gsi-red text-white font-semibold">{{ $page }}</span>
                    @else
                        <button type="button" wire:click="gotoPage({{ $page }})"
                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white hover:bg-gray-100 text-gray-700 transition-colors">{{ $page }}</button>
                    @endif
                </li>
            @endfor

            {{-- Last page --}}
            @if ($end < $lastPage)
                @if ($end < $lastPage - 1)
                    <li>
                        <span
                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white text-gray-700">...</span>
                    </li>
                @endif
                <li>
                    <button type="button" wire:click="gotoPage({{ $lastPage }})"
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white hover:bg-gray-100 text-gray-700 transition-colors">{{ $lastPage }}</button>
                </li>
            @endif

            {{-- Next Button --}}
            <li>
                @if ($paginator->hasMorePages())
                    <button type="button" wire:click="nextPage"
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white hover:bg-gray-100 text-gray-700 transition-colors">
                        <span class="sr-only">Next</span>
                        <x-heroicon-s-chevron-right class="w-4 h-4" />
                    </button>
                @else
                    <span
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-gray-200 text-gray-400 cursor-not-allowed">
                        <span class="sr-only">Next</span>
                        <x-heroicon-s-chevron-right class="w-4 h-4" />
                    </span>
                @endif
            </li>
        </ul>
    </nav>
@endif
