{{--
    The one pagination look for the whole admin (host + packages).
    Every paginator view name — links(), links('pagination'), links('pagination::bootstrap-4'),
    links('pagination::bootstrap-5'), simple paginators — renders this file
    (see resources/views/vendor/pagination/* and AppServiceProvider::boot).
    Styling: .ui-pager in public/admin/assets/css/ui.css.
--}}
@if ($paginator->hasPages())
    <nav class="ui-pager" role="navigation" aria-label="Pagination">
        @if (method_exists($paginator, 'total') && $paginator->firstItem())
            <div class="ui-pager-info">
                Showing <b>{{ number_format($paginator->firstItem()) }}</b>–<b>{{ number_format($paginator->lastItem()) }}</b>
                of <b>{{ number_format($paginator->total()) }}</b>
            </div>
        @else
            <div class="ui-pager-info">Page <b>{{ $paginator->currentPage() }}</b></div>
        @endif

        <ul class="pagination">
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true"><span class="page-link" aria-label="Previous"><i class="fa-solid fa-chevron-left"></i></span></li>
            @else
                <li class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous"><i class="fa-solid fa-chevron-left"></i></a></li>
            @endif

            @isset($elements)
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            @endisset

            @if ($paginator->hasMorePages())
                <li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next"><i class="fa-solid fa-chevron-right"></i></a></li>
            @else
                <li class="page-item disabled" aria-disabled="true"><span class="page-link" aria-label="Next"><i class="fa-solid fa-chevron-right"></i></span></li>
            @endif
        </ul>
    </nav>
@endif
