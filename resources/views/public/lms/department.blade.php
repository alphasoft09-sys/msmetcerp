<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $department->department_name }} - {{ env('PROJECT_NAME', 'MSME Technology Center') }}</title>
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

        .department-header {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .department-title {
            font-size: 2.5rem;
            font-weight: 300;
            color: #dc2626;
            margin-bottom: 1rem;
        }

        .department-description {
            color: #6b7280;
            font-size: 1.1rem;
            line-height: 1.6;
            max-width: 600px;
            margin: 0 auto;
        }

        .sites-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .site-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .site-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .site-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .site-description {
            color: #6b7280;
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 1rem;
        }

        .site-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            color: #6b7280;
        }

        .faculty-name {
            font-weight: 500;
            color: #dc2626;
        }

        .site-date {
            color: #9ca3af;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            width: 100%;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(220, 38, 38, 0.4);
            color: white;
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

        .no-sites {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .no-sites i {
            font-size: 4rem;
            color: #dc2626;
            margin-bottom: 1rem;
        }

        .no-sites h3 {
            color: #374151;
            margin-bottom: 1rem;
        }

        .no-sites p {
            color: #6b7280;
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .department-title {
                font-size: 2rem;
            }

            .sites-grid {
                grid-template-columns: 1fr;
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
                {{ $department->department_name }}
            </div>
        </div>
    </header>

    <div class="container">
        <a href="{{ route('public.lms.index') }}" class="back-btn">
            <i class="bi bi-arrow-left"></i>
            Back to LMS
        </a>

        <!-- Department Header -->
        <div class="department-header">
            <h1 class="department-title">{{ $department->department_name }}</h1>
            @if($department->description)
                <div class="department-description">
                    {{ $department->description }}
                </div>
            @endif
        </div>

        <!-- Sites Grid -->
        @if($lmsSites->count() > 0)
            <div class="sites-grid">
                @foreach($lmsSites as $site)
                    <div class="site-card">
                        <h3 class="site-title">{{ $site->site_title }}</h3>
                        
                        @if($site->site_description)
                            <div class="site-description">
                                {{ Str::limit($site->site_description, 120) }}
                            </div>
                        @endif

                        <div class="site-meta">
                            <span class="faculty-name">{{ $site->faculty->name }}</span>
                            <span class="site-date">{{ $site->created_at->format('M d, Y') }}</span>
                        </div>

                        <a href="{{ route('public.lms.show', [$department->department_slug, $site->site_url]) }}" 
                           class="btn btn-primary">
                            <i class="bi bi-arrow-right me-2"></i>
                            View Site
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $lmsSites->links() }}
            </div>
        @else
            <div class="no-sites">
                <i class="bi bi-file-text"></i>
                <h3>No Sites Available</h3>
                <p>There are currently no published sites in this department.</p>
                <a href="{{ route('public.lms.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left me-2"></i>
                    Back to LMS
                </a>
            </div>
        @endif
    </div>
</body>
</html>
