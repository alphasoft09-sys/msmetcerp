<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tcLm->seo_title ?: $tcLm->site_title }} - Educational LMS</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="{{ $tcLm->seo_description ?: $tcLm->site_description }}">
    <meta name="keywords" content="{{ $tcLm->seo_keywords }}">
    <meta name="author" content="{{ $tcLm->faculty->name }}">
    <meta name="robots" content="index, follow">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="{{ $tcLm->seo_title ?: $tcLm->site_title }}">
    <meta property="og:description" content="{{ $tcLm->seo_description ?: $tcLm->site_description }}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="Educational LMS System">
    <meta property="og:locale" content="en_US">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $tcLm->seo_title ?: $tcLm->site_title }}">
    <meta name="twitter:description" content="{{ $tcLm->seo_description ?: $tcLm->site_description }}">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">
    
    <!-- Structured Data -->
    @if($tcLm->structured_data)
        <script type="application/ld+json">
            {!! $tcLm->structured_data !!}
        </script>
    @endif
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        .lms-content {
            line-height: 1.8;
            font-size: 16px;
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
            margin-bottom: 2rem;
        }
        
        .seo-meta {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="bi bi-book me-2"></i>
                Educational LMS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/courses">Courses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about">About</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb -->
    <div class="container mt-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/courses">Courses</a></li>
                <li class="breadcrumb-item"><a href="/courses/{{ $tcLm->site_department }}">{{ $tcLm->site_department }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $tcLm->site_title }}</li>
            </ol>
        </nav>
    </div>

    <!-- Content Header -->
    <div class="content-header">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">{{ $tcLm->site_title }}</h1>
                    <p class="lead mb-3">{{ $tcLm->site_description }}</p>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-light text-dark fs-6">
                            <i class="bi bi-building me-1"></i>
                            {{ $tcLm->site_department }}
                        </span>
                        <span class="badge bg-light text-dark fs-6">
                            <i class="bi bi-person me-1"></i>
                            {{ $tcLm->faculty->name }}
                        </span>
                        <span class="badge bg-light text-dark fs-6">
                            <i class="bi bi-calendar me-1"></i>
                            {{ $tcLm->created_at->format('M d, Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <article class="lms-content">
                    @if($tcLm->site_contents && !empty(trim($tcLm->site_contents)))
                        {!! $tcLm->site_contents !!}
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-file-text display-1 text-muted"></i>
                            <h3 class="mt-3 text-muted">Content Coming Soon</h3>
                            <p class="text-muted">This educational content is being prepared and will be available soon.</p>
                        </div>
                    @endif
                </article>
            </div>
            
            <div class="col-lg-4">
                <!-- SEO Meta Information -->
                <div class="seo-meta">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-search me-2"></i>
                        Course Information
                    </h6>
                    <div class="mb-2">
                        <strong>Department:</strong> {{ $tcLm->site_department }}
                    </div>
                    <div class="mb-2">
                        <strong>Instructor:</strong> {{ $tcLm->faculty->name }}
                    </div>
                    <div class="mb-2">
                        <strong>Created:</strong> {{ $tcLm->created_at->format('F d, Y') }}
                    </div>
                    <div class="mb-2">
                        <strong>Last Updated:</strong> {{ $tcLm->updated_at->format('F d, Y') }}
                    </div>
                    @if($tcLm->seo_keywords)
                        <div class="mt-3">
                            <strong>Keywords:</strong>
                            <div class="mt-1">
                                @foreach(explode(',', $tcLm->seo_keywords) as $keyword)
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
                            <i class="bi bi-collection me-2"></i>
                            Related Content
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">More educational content from {{ $tcLm->site_department }} department will be available soon.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h6>Educational LMS System</h6>
                    <p class="text-muted">Providing quality educational content following Government of India policies.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted mb-0">
                        &copy; {{ date('Y') }} Educational LMS System. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
