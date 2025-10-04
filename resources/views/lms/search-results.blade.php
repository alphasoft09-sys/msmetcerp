@extends('layouts.goi-meta')

@php
    $pageTitle = 'Search Results | Educational LMS | Government of India';
    $metaDescription = 'Search Results - Educational LMS System, Government of India';
    $metaKeywords = 'LMS, search, educational content, Government of India, MSME';
@endphp

@push('styles')
    <style>
        .search-results-header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #06b6d4 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        
        .search-box {
            background: white;
            border-radius: 50px;
            padding: 0.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .search-input {
            border: none;
            outline: none;
            padding: 0.75rem 1rem;
            width: 100%;
            font-size: 1.1rem;
        }
        
        .search-btn {
            background: #dc2626;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
        }
        
        .result-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        
        .result-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        .result-highlight {
            background-color: #fef3cd;
            padding: 0.1rem 0.3rem;
            border-radius: 3px;
        }
    </style>
@endpush

@section('content')

    <!-- Search Results Header -->
    <section class="search-results-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-5 fw-bold mb-3">
                        <i class="fas fa-search me-3"></i>
                        Search Results
                    </h1>
                    @if(request('q'))
                        <p class="lead mb-4">
                            Showing results for: <strong>"{{ request('q') }}"</strong>
                        </p>
                    @endif
                </div>
                <div class="col-lg-4">
                    <form method="GET" action="{{ route('public.lms.search') }}" class="search-box">
                        <div class="input-group">
                            <input type="text" name="q" class="search-input" 
                                   placeholder="Search again..."
                                   value="{{ request('q') }}">
                            <button type="submit" class="search-btn">
                                <i class="fas fa-search me-2"></i>Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main id="main-content">
        <div class="container">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('public.lms.index') }}">LMS Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Search Results</li>
                </ol>
            </nav>

            @if($results->count() > 0)
                <div class="row">
                    <div class="col-12">
                        <h4 class="mb-4">
                            Found {{ $results->total() }} result(s)
                        </h4>
                    </div>
                </div>

                <div class="row">
                    @foreach($results as $result)
                        <div class="col-lg-6 mb-4">
                            <div class="card result-card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="{{ route('public.lms.show', ['departmentSlug' => Str::slug($result->site_department), 'siteUrl' => $result->site_url]) }}" 
                                           class="text-decoration-none text-dark">
                                            {{ $result->site_title }}
                                        </a>
                                    </h5>
                                    <p class="card-text text-muted">
                                        @if(request('q'))
                                            {!! Str::limit(str_ireplace(request('q'), '<span class="result-highlight">' . request('q') . '</span>', $result->site_description), 150) !!}
                                        @else
                                            {{ Str::limit($result->site_description, 150) }}
                                        @endif
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-info me-2">{{ $result->site_department }}</span>
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>
                                                {{ $result->faculty->name }}
                                            </small>
                                        </div>
                                        <small class="text-muted">
                                            {{ $result->created_at->format('M d, Y') }}
                                        </small>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <a href="{{ route('public.lms.show', ['departmentSlug' => Str::slug($result->site_department), 'siteUrl' => $result->site_url]) }}" 
                                       class="btn btn-danger btn-sm">
                                        <i class="fas fa-eye me-2"></i>View Content
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-5">
                    {{ $results->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-search display-1 text-muted mb-3"></i>
                    <h4 class="text-muted">No Results Found</h4>
                    <p class="text-muted">
                        @if(request('q'))
                            No content found for "{{ request('q') }}". Try different keywords or browse all content.
                        @else
                            Please enter a search term to find educational content.
                        @endif
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('public.lms.index') }}" class="btn btn-danger me-3">
                            <i class="fas fa-home me-2"></i>Browse All Content
                        </a>
                        <button onclick="document.querySelector('.search-input').focus()" class="btn btn-outline-danger">
                            <i class="fas fa-search me-2"></i>Try Another Search
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </main>
@endsection

@push('scripts')
<script>
    // Font size adjustment
    document.querySelectorAll('[data-lang="font-size"]').forEach(button => {
        button.addEventListener('click', function() {
            const action = this.getAttribute('data-action');
            const body = document.body;
            const currentSize = parseFloat(getComputedStyle(body).fontSize);
            
            if (action === 'increase') {
                body.style.fontSize = (currentSize + 2) + 'px';
            } else if (action === 'decrease') {
                body.style.fontSize = (currentSize - 2) + 'px';
            }
        });
    });

    // Search form enhancement
    document.querySelector('.search-input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            this.closest('form').submit();
        }
    });
</script>
@endpush
