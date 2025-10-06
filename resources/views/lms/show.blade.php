@extends('layouts.goi-meta')

@php
    $pageTitle = ($lmsSite->seo_title ?: $lmsSite->site_title) . ' | Educational LMS | Government of India';
    $metaDescription = $lmsSite->seo_description ?: $lmsSite->site_description;
    $metaKeywords = $lmsSite->seo_keywords;
@endphp

@push('styles')
    <style>
        .lms-content {
            line-height: 1.8;
            font-size: 16px;
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .lms-content h1, .lms-content h2, .lms-content h3, 
        .lms-content h4, .lms-content h5, .lms-content h6 {
            margin-top: 2rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        
        .lms-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 1rem 0;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .lms-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5rem 0;
        }
        
        .lms-content table, .lms-content th, .lms-content td {
            border: 1px solid #dee2e6;
        }
        
        .lms-content th, .lms-content td {
            padding: 12px;
            text-align: left;
        }
        
        .lms-content th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        .breadcrumb {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
        }
        
        .content-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 0;
        }
        
        .seo-meta {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }
        
        .qr-code-container {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 1rem;
            margin: 1rem 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 200px;
        }
        
        .qr-code-display {
            width: 200px;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            margin: 0 auto;
            overflow: hidden;
            position: relative;
        }
        
        .qr-code-display img {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
            border-radius: 4px;
            display: block;
        }
        
        .qr-code-display canvas {
            border-radius: 4px;
            max-width: 100%;
            max-height: 100%;
        }
        
        .qr-code-display .text-muted {
            text-align: center;
            padding: 20px;
        }
        
        .qr-code-display .text-danger {
            text-align: center;
            padding: 20px;
        }
        
        .share-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
        }
        
        .copy-success {
            background: #d1edff;
            color: #0c5460;
            border: 1px solid #bee5eb;
            border-radius: 4px;
            padding: 0.5rem;
            margin-top: 0.5rem;
            font-size: 0.85rem;
            display: none;
        }
        
        .copy-success.show {
            display: block;
        }
        
        /* Sticky Sidebar */
        .sidebar-sticky {
            position: sticky;
            top: 2rem;
            max-height: calc(100vh - 4rem);
            overflow-y: auto;
            z-index: 10;
        }
        
        .sidebar-sticky::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar-sticky::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }
        
        .sidebar-sticky::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        .sidebar-sticky::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Mobile responsive for sticky sidebar */
        @media (max-width: 991px) {
            .sidebar-sticky {
                position: static;
                max-height: none;
                overflow-y: visible;
            }
        }
        
        /* Ensure main content area has proper background */
        #main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
            padding: 0;
        }
        
        /* Content wrapper styling */
        .content-wrapper {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-top: 0;
        }
    </style>
@endpush

@section('content')

    <!-- Breadcrumb -->
    <!-- <div class="container mt-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('public.lms.index') }}">LMS Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('public.lms.department', Str::slug($lmsSite->site_department)) }}">{{ $lmsSite->site_department }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $lmsSite->site_title }}</li>
            </ol>
        </nav>
    </div> -->

    <!-- Content Header -->
    <div class="content-header">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">{{ $lmsSite->site_title }}</h1>
                    <p class="lead mb-3">{{ $lmsSite->site_description }}</p>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-light text-dark fs-6">
                            <i class="fas fa-building me-1"></i>
                            {{ $lmsSite->site_department }}
                        </span>
                        <span class="badge bg-light text-dark fs-6">
                            <i class="fas fa-chalkboard-teacher me-1"></i>
                            {{ $lmsSite->faculty->name }}
                        </span>
                        <span class="badge bg-light text-dark fs-6">
                            <i class="fas fa-calendar me-1"></i>
                            {{ $lmsSite->created_at->format('M d, Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main id="main-content">
        <div class="container py-4">
            <div class="content-wrapper">
                <div class="row">
                    <div class="col-lg-8">
                        <article class="lms-content">
                            @if($lmsSite->site_contents && !empty(trim($lmsSite->site_contents)))
                                {!! $lmsSite->site_contents !!}
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-file-text display-1 text-muted"></i>
                                    <h3 class="mt-3 text-muted">Content Coming Soon</h3>
                                    <p class="text-muted">This educational content is being prepared and will be available soon.</p>
                                </div>
                            @endif
                        </article>
                    </div>
                
                <div class="col-lg-4">
                    <div class="sidebar-sticky p-3">
                        <!-- SEO Meta Information -->
                    <div class="seo-meta">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Course Information
                        </h6>
                        <div class="mb-2">
                            <strong>Department:</strong> {{ $lmsSite->site_department }}
                        </div>
                        <div class="mb-2">
                            <strong>Instructor:</strong> {{ $lmsSite->faculty->name }}
                        </div>
                        <div class="mb-2">
                            <strong>Created:</strong> {{ $lmsSite->created_at->format('F d, Y') }}
                        </div>
                        <div class="mb-2">
                            <strong>Last Updated:</strong> {{ $lmsSite->updated_at->format('F d, Y') }}
                        </div>
                        @if($lmsSite->seo_keywords)
                            <div class="mt-3">
                                <strong>Keywords:</strong>
                                <div class="mt-1">
                                    @foreach(explode(',', $lmsSite->seo_keywords) as $keyword)
                                        <span class="badge bg-secondary me-1 mb-1">{{ trim($keyword) }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Related Content -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-list me-2"></i>
                                Related Content
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">More educational content from {{ $lmsSite->site_department }} department will be available soon.</p>
                            <a href="{{ route('public.lms.department', Str::slug($lmsSite->site_department)) }}" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>
                                View All {{ $lmsSite->site_department }} Content
                            </a>
                        </div>
                    </div>

                    <!-- Share & QR Code -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-share-alt me-2"></i>
                                Share This Content
                            </h6>
                        </div>
                        <div class="card-body">
                            <!-- Copy Link Section -->
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Share Link:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="shareUrl" value="{{ url()->current() }}" readonly>
                                    <button class="btn btn-outline-primary btn-sm" type="button" id="copyLinkBtn">
                                        <i class="fas fa-copy me-1"></i>Copy
                                    </button>
                                </div>
                                <div class="form-text">Click copy to share this page with others</div>
                            </div>

                            <!-- QR Code Section -->
                            <div class="text-center">
                                <label class="form-label small fw-bold">QR Code:</label>
                                <div class="qr-code-container">
                                    <div class="qr-code-display">
                                        <img src="https://quickchart.io/qr?text={{ urlencode(request()->url()) }}&size=200&format=png&margin=1" 
                                             alt="QR Code for {{ $lmsSite->site_title }}" 
                                             class="qr-code-image"
                                             style="max-width: 100%; max-height: 100%; width: auto; height: auto; object-fit: contain; border-radius: 4px; display: block;">
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    </div> <!-- End sidebar-sticky -->
                </div>
                </div> <!-- End row -->
            </div> <!-- End content-wrapper -->
        </div> <!-- End container -->
    </main>
@endsection

@push('scripts')
<!-- QR Code generation using QuickChart.io API -->

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

    // Copy Link Functionality
    document.getElementById('copyLinkBtn').addEventListener('click', function() {
        const shareUrl = document.getElementById('shareUrl');
        const button = this;
        
        // Copy to clipboard
        shareUrl.select();
        shareUrl.setSelectionRange(0, 99999); // For mobile devices
        
        try {
            document.execCommand('copy');
            
            // Show success feedback
            button.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
            button.classList.remove('btn-outline-primary');
            button.classList.add('btn-success');
            
            // Reset button after 2 seconds
            setTimeout(() => {
                button.innerHTML = '<i class="fas fa-copy me-1"></i>Copy';
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-primary');
            }, 2000);
            
        } catch (err) {
            // Fallback for modern browsers
            navigator.clipboard.writeText(shareUrl.value).then(() => {
                button.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
                button.classList.remove('btn-outline-primary');
                button.classList.add('btn-success');
                
                setTimeout(() => {
                    button.innerHTML = '<i class="fas fa-copy me-1"></i>Copy';
                    button.classList.remove('btn-success');
                    button.classList.add('btn-outline-primary');
                }, 2000);
            }).catch(() => {
                alert('Failed to copy link. Please select and copy manually.');
            });
        }
    });

    // QR Code is now generated server-side using direct image tag
    // No JavaScript generation needed

    // Download QR Code
    document.getElementById('downloadQRBtn').addEventListener('click', function() {
        const qrContainer = document.querySelector('.qr-code-display');
        const img = qrContainer.querySelector('img');
        
        if (!img) {
            alert('QR Code not available.');
            return;
        }
        
        // Show loading state
        const button = this;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Preparing...';
        button.disabled = true;
        
        // Open QR code in new tab for user to save manually
        window.open(img.src, '_blank');
        
        // Show success feedback
        button.innerHTML = '<i class="fas fa-external-link-alt me-1"></i>Opened in new tab';
        button.classList.remove('btn-success');
        button.classList.add('btn-primary');
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('btn-primary');
            button.classList.add('btn-success');
            button.disabled = false;
        }, 2000);
    });

    // QR Code is now generated server-side using direct image tag
    // No JavaScript generation or fallback needed

</script>
@endpush
