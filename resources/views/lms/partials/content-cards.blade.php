@if($lmsSites->count() > 0)
    <div class="row g-4">
        @foreach($lmsSites as $site)
            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                <div class="simple-content-card">
                    <!-- Card Header -->
                    <div class="card-header-simple">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="department-tag">{{ $site->site_department }}</span>
                        </div>
                        <div class="card-date-simple">
                            <i class="fas fa-calendar me-1"></i>{{ $site->created_at->format('M d, Y') }}
                        </div>
                    </div>
                    
                    <!-- Card Body -->
                    <div class="card-body-simple">
                        <h5 class="card-title-simple">
                            {{ $site->site_title }}
                        </h5>
                        
                        <div class="card-description-simple">
                            @if($site->site_description && !empty(trim($site->site_description)))
                                <p class="description-simple">
                                    {{ Str::limit($site->site_description, 120) }}
                                </p>
                            @else
                                <p class="description-simple text-muted">
                                    <em>No description available</em>
                                </p>
                            @endif
                        </div>
                        
                        <!-- Faculty Info -->
                        <div class="faculty-section-simple">
                            <div class="faculty-info-simple">
                                <div class="faculty-avatar-simple">
                                    {{ $site->faculty ? substr($site->faculty->name, 0, 1) : 'F' }}
                                </div>
                                <div class="faculty-details-simple">
                                    <div class="faculty-label-simple">Faculty</div>
                                    <div class="faculty-name-simple">{{ $site->faculty->name ?? 'Unknown Faculty' }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Button -->
                        <div class="card-action-simple">
                            <a href="{{ route('public.lms.show', ['departmentSlug' => Str::slug($site->site_department), 'siteUrl' => $site->site_url]) }}" 
                               class="btn-simple">
                                <i class="fas fa-eye me-2"></i>View Course
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="empty-state">
        <div class="empty-state-content">
            <div class="empty-state-icon">
                <i class="fas fa-search-minus"></i>
            </div>
            <h3 class="mb-3 text-dark">No Content Found</h3>
            <p class="lead text-muted mb-4">
                No educational content matches your current filter criteria. 
                Try adjusting your filters or search terms.
            </p>
            <button onclick="clearAllFilters()" class="btn btn-primary btn-lg">
                <i class="fas fa-times me-2"></i>Clear All Filters
            </button>
        </div>
    </div>
@endif
