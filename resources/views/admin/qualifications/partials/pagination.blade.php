<div class="pagination-with-results">
    <div class="d-flex justify-content-between align-items-center">
        <div class="pagination-stats">
            <i class="bi bi-info-circle me-2"></i>
            Showing <strong>{{ $qualifications->firstItem() ?? 0 }}</strong> to <strong>{{ $qualifications->lastItem() ?? 0 }}</strong> 
            of <strong>{{ $qualifications->total() }}</strong> qualifications
        </div>
        <div class="pagination-wrapper">
            @if($qualifications->hasPages())
                <nav aria-label="Qualifications pagination">
                    <ul class="pagination pagination-sm mb-0">
                        {{-- Previous Page Link --}}
                        @if($qualifications->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="bi bi-chevron-left"></i>
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="javascript:void(0)" data-page="{{ $qualifications->currentPage() - 1 }}" aria-label="Previous">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach($qualifications->getUrlRange(1, $qualifications->lastPage()) as $page => $url)
                            @if($page == $qualifications->currentPage())
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
                        @if($qualifications->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="javascript:void(0)" data-page="{{ $qualifications->currentPage() + 1 }}" aria-label="Next">
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