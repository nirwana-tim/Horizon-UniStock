@if ($paginator->hasPages())
    <nav class="flex items-center gap-1" role="navigation" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1.5 text-sm text-gray-400 bg-gray-50 rounded-lg border border-gray-200 cursor-not-allowed">&laquo;</span>
        @else
            <button data-page="{{ $paginator->currentPage() - 1 }}" class="px-3 py-1.5 text-sm text-gray-700 bg-white rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">&laquo;</button>
        @endif

        @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
            <button data-page="{{ $page }}"
                class="px-3 py-1.5 text-sm rounded-lg border transition-colors
                {{ $page == $paginator->currentPage()
                    ? 'bg-primary-700 text-white border-primary-700 font-medium'
                    : 'text-gray-700 bg-white border-gray-200 hover:bg-gray-50' }}">
                {{ $page }}
            </button>
        @endforeach

        @if ($paginator->hasMorePages())
            <button data-page="{{ $paginator->currentPage() + 1 }}" class="px-3 py-1.5 text-sm text-gray-700 bg-white rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">&raquo;</button>
        @else
            <span class="px-3 py-1.5 text-sm text-gray-400 bg-gray-50 rounded-lg border border-gray-200 cursor-not-allowed">&raquo;</span>
        @endif
    </nav>
@endif
