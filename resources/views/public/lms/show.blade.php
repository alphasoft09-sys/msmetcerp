<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $lmsSite->site_title }} - {{ env('PROJECT_NAME', 'MSME Technology Center') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        .header {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            color: white;
            padding: 1rem 0;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo img {
            width: 40px;
            height: 40px;
            filter: brightness(0) invert(1);
        }

        .logo-text {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .breadcrumb {
            background: rgba(255, 255, 255, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
        }

        .breadcrumb a {
            color: white;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .site-header {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .site-title {
            font-size: 2.5rem;
            font-weight: 300;
            color: #dc2626;
            margin-bottom: 1rem;
        }

        .site-meta {
            display: flex;
            gap: 2rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #6b7280;
            font-size: 0.875rem;
        }

        .meta-item i {
            color: #dc2626;
        }

        .site-description {
            color: #6b7280;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .site-content {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            min-height: 400px;
        }

        .content-placeholder {
            text-align: center;
            padding: 4rem 2rem;
            color: #6b7280;
        }

        .content-placeholder i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #dc2626;
        }

        .content-placeholder h3 {
            margin-bottom: 1rem;
            color: #374151;
        }

        .sidebar {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            height: fit-content;
        }

        .sidebar h4 {
            color: #dc2626;
            margin-bottom: 1rem;
            font-size: 1.25rem;
        }

        .faculty-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 12px;
            margin-bottom: 1rem;
        }

        .faculty-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .faculty-details h5 {
            margin-bottom: 0.25rem;
            color: #374151;
        }

        .faculty-details p {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .site-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .stat-item {
            text-align: center;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 12px;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 600;
            color: #dc2626;
        }

        .stat-label {
            font-size: 0.875rem;
            color: #6b7280;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #dc2626;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 2rem;
            transition: color 0.3s ease;
        }

        .back-btn:hover {
            color: #991b1b;
        }

        .loading {
            text-align: center;
            padding: 2rem;
            color: #6b7280;
        }

        .loading i {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .site-title {
                font-size: 2rem;
            }

            .site-meta {
                flex-direction: column;
                gap: 0.5rem;
            }

            .header-content {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <img src="{{ asset('msme_logo/favicon.svg') }}" alt="MSME Logo">
                <div class="logo-text">{{ env('PROJECT_NAME', 'MSME Technology Center') }}</div>
            </div>
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Home</a> / 
                <a href="{{ route('public.lms.index') }}">LMS</a> / 
                <a href="{{ route('public.lms.department', $department->department_slug) }}">{{ $department->department_name }}</a> / 
                {{ $lmsSite->site_title }}
            </div>
        </div>
    </header>

    <div class="container">
        <a href="{{ route('public.lms.department', $department->department_slug) }}" class="back-btn">
            <i class="bi bi-arrow-left"></i>
            Back to {{ $department->department_name }}
        </a>

        <div class="row">
            <div class="col-md-8">
                <!-- Site Header -->
                <div class="site-header">
                    <h1 class="site-title">{{ $lmsSite->site_title }}</h1>
                    
                    <div class="site-meta">
                        <div class="meta-item">
                            <i class="bi bi-building"></i>
                            <span>{{ $lmsSite->tc_code }}</span>
                        </div>
                        <div class="meta-item">
                            <i class="bi bi-calendar"></i>
                            <span>{{ $lmsSite->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="meta-item">
                            <i class="bi bi-check-circle"></i>
                            <span>Approved {{ $lmsSite->approved_at->format('M d, Y') }}</span>
                        </div>
                    </div>

                    @if($lmsSite->site_description)
                        <div class="site-description">
                            {{ $lmsSite->site_description }}
                        </div>
                    @endif
                </div>

                <!-- Site Content -->
                <div class="site-content" id="site-content">
                    <div class="loading">
                        <i class="bi bi-hourglass-split"></i>
                        <p>Loading content...</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Sidebar -->
                <div class="sidebar">
                    <h4>Faculty Information</h4>
                    
                    <div class="faculty-info">
                        <div class="faculty-avatar">
                            {{ substr($lmsSite->faculty->name, 0, 1) }}
                        </div>
                        <div class="faculty-details">
                            <h5>{{ $lmsSite->faculty->name }}</h5>
                            <p>{{ $lmsSite->faculty->email }}</p>
                        </div>
                    </div>

                    <div class="site-stats">
                        <div class="stat-item">
                            <div class="stat-number">{{ $department->lms_sites_count }}</div>
                            <div class="stat-label">Sites in Department</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">{{ $lmsSite->tc_code }}</div>
                            <div class="stat-label">Training Center</div>
                        </div>
                    </div>

                    <h4>Department</h4>
                    <p>{{ $department->description ?: 'Educational content in this department.' }}</p>
                    
                    <a href="{{ route('public.lms.department', $department->department_slug) }}" 
                       class="btn btn-primary w-100 mt-3">
                        <i class="bi bi-arrow-left me-2"></i>
                        Back to Department
                    </a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        loadSiteContent();
    });

    function loadSiteContent() {
        const contentContainer = document.getElementById('site-content');
        
        // Get site content via AJAX
        fetch('{{ route("public.lms.content", [$department->department_slug, $lmsSite->site_url]) }}')
            .then(response => response.json())
            .then(data => {
                if (data.content && Object.keys(data.content).length > 0) {
                    renderSiteContent(data.content);
                } else {
                    showNoContent();
                }
            })
            .catch(error => {
                console.error('Error loading content:', error);
                showNoContent();
            });
    }

    function renderSiteContent(content) {
        const contentContainer = document.getElementById('site-content');
        let html = '';

        Object.values(content).forEach(component => {
            switch(component.type) {
                case 'heading':
                    html += `<h2 class="mb-3">${component.content}</h2>`;
                    break;
                case 'paragraph':
                    html += `<p class="mb-3">${component.content}</p>`;
                    break;
                case 'button':
                    html += `<button class="btn btn-primary mb-3">${component.content}</button>`;
                    break;
                case 'card':
                    html += `
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Card Title</h5>
                                <p class="card-text">Card content</p>
                            </div>
                        </div>
                    `;
                    break;
                case 'spacer':
                    html += '<div style="height: 50px;"></div>';
                    break;
                case 'image':
                    html += `
                        <div class="text-center mb-3">
                            <i class="bi bi-image display-4 text-muted"></i>
                            <p class="text-muted">Image placeholder</p>
                        </div>
                    `;
                    break;
                case 'video':
                    html += `
                        <div class="text-center mb-3">
                            <i class="bi bi-play-circle display-4 text-muted"></i>
                            <p class="text-muted">Video placeholder</p>
                        </div>
                    `;
                    break;
                case 'list':
                    html += `
                        <ul class="mb-3">
                            <li>List item 1</li>
                            <li>List item 2</li>
                            <li>List item 3</li>
                        </ul>
                    `;
                    break;
            }
        });

        contentContainer.innerHTML = html;
    }

    function showNoContent() {
        const contentContainer = document.getElementById('site-content');
        contentContainer.innerHTML = `
            <div class="content-placeholder">
                <i class="bi bi-file-text"></i>
                <h3>No Content Available</h3>
                <p>This site doesn't have any content yet.</p>
            </div>
        `;
    }
    </script>
    @endpush
</body>
</html>
