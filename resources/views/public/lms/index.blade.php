<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learning Management System - {{ env('PROJECT_NAME', 'MSME Technology Center') }}</title>
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
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            min-height: 100vh;
            color: #333;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 0;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
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
            width: 50px;
            height: 50px;
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 600;
            color: #dc2626;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #dc2626;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .hero {
            text-align: center;
            margin-bottom: 4rem;
            color: white;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: 300;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
        }

        .hero p {
            font-size: 1.25rem;
            opacity: 0.9;
            font-weight: 300;
            max-width: 600px;
            margin: 0 auto;
        }

        .search-section {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 3rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .search-form {
            display: flex;
            gap: 1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .search-input {
            flex: 1;
            padding: 0.875rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 50px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #dc2626;
        }

        .search-btn {
            padding: 0.875rem 2rem;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .search-btn:hover {
            transform: translateY(-2px);
        }

        .departments {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .department-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .department-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
        }

        .department-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: white;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
        }

        .department-card h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #333;
        }

        .department-card p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }

        .site-count {
            background: #f3f4f6;
            color: #6b7280;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            display: inline-block;
        }

        .btn {
            display: inline-block;
            padding: 0.875rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
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

        .footer {
            text-align: center;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            margin-top: 4rem;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .hero p {
                font-size: 1.1rem;
            }
            
            .departments {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .department-card {
                padding: 1.5rem;
            }
            
            .container {
                padding: 1rem;
            }

            .search-form {
                flex-direction: column;
            }

            .nav-links {
                display: none;
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
            <nav>
                <ul class="nav-links">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('public.lms.index') }}">LMS</a></li>
                    <li><a href="{{ route('admin.login') }}">Admin Login</a></li>
                    <li><a href="{{ route('student.login') }}">Student Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <!-- Hero Section -->
        <div class="hero">
            <h1>Learning Management System</h1>
            <p>Explore educational content and resources from our training centers across various departments.</p>
        </div>

        <!-- Search Section -->
        <div class="search-section">
            <form method="GET" action="{{ route('public.lms.search') }}" class="search-form">
                <input type="text" name="q" class="search-input" 
                       placeholder="Search for courses, topics, or faculty..." 
                       value="{{ request('q') }}">
                <button type="submit" class="search-btn">
                    <i class="bi bi-search me-2"></i>Search
                </button>
            </form>
        </div>

        <!-- Departments Section -->
        <div class="departments">
            @forelse($departments as $department)
                <div class="department-card">
                    <div class="department-icon">
                        <i class="bi bi-book"></i>
                    </div>
                    <h3>{{ $department->department_name }}</h3>
                    <p>{{ $department->description ?: 'Explore educational content in this department.' }}</p>
                    <div class="site-count">
                        {{ $department->lms_sites_count }} {{ Str::plural('site', $department->lms_sites_count) }}
                    </div>
                    <a href="{{ route('public.lms.department', $department->department_slug) }}" class="btn btn-primary">
                        Explore Department
                    </a>
                </div>
            @empty
                <div class="department-card" style="grid-column: 1 / -1;">
                    <div class="department-icon">
                        <i class="bi bi-info-circle"></i>
                    </div>
                    <h3>No Departments Available</h3>
                    <p>There are currently no departments with published content.</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ env('PROJECT_NAME', 'MSME Technology Center') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
