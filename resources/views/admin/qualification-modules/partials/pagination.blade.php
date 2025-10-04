<div class="pagination-with-results">
    <div class="d-flex justify-content-between align-items-center">
        <div class="pagination-stats">
            <i class="bi bi-info-circle me-2"></i>
            Showing <strong>{{ $modules->firstItem() ?? 0 }}</strong> to <strong>{{ $modules->lastItem() ?? 0 }}</strong> 
            of <strong>{{ $modules->total() }}</strong> modules
        </div>
        <div class="pagination-wrapper">
            @if($modules->hasPages())
                <nav aria-label="Modules pagination">
                    <ul class="pagination pagination-sm mb-0">
                        {{-- Previous Page Link --}}
                        @if($modules->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="bi bi-chevron-left"></i>
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="javascript:void(0)" data-page="{{ $modules->currentPage() - 1 }}" aria-label="Previous">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach($modules->getUrlRange(1, $modules->lastPage()) as $page => $url)
                            @if($page == $modules->currentPage())
                                <li class="page-item active">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="javascript:void(0)" data-page="{{ $page }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if($modules->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="javascript:void(0)" data-page="{{ $modules->currentPage() + 1 }}" aria-label="Next">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="bi bi-chevron-right"></i>
                                </span>
                            </li>
                        @endif
                    </ul>
                </nav>
            @endif
        </div>
    </div>
</div> 