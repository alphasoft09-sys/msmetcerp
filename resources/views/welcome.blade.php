<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('PROJECT_NAME', 'MSME Technology Center') }}</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }

        .container {
            max-width: 1200px;
            width: 100%;
            padding: 2rem;
        }

        .hero {
            text-align: center;
            margin-bottom: 4rem;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 300;
            color: white;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
        }

        .hero p {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 300;
            max-width: 600px;
            margin: 0 auto;
        }

        .portals {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .portal-card {
            background: white;
            border-radius: 16px;
            padding: 3rem 2rem;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .portal-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
        }

        .portal-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: white;
        }

        .admin-icon {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
        }

        .student-icon {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
        }

        .portal-card h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #333;
        }

        .portal-card p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 2rem;
            font-size: 0.95rem;
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

        .btn-success {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(5, 150, 105, 0.4);
            color: white;
        }

        .footer {
            text-align: center;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .hero p {
                font-size: 1.1rem;
            }
            
            .portals {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .portal-card {
                padding: 2rem 1.5rem;
            }
            
            .container {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Hero Section -->
        <div class="hero">
            <div class="logo mb-4">
                <img src="{{ asset('msme_logo/favicon.svg') }}" alt="MSME Logo" style="width: 120px; height: 120px; filter: brightness(0) invert(1);">
            </div>
            <h1>{{ env('PROJECT_NAME', 'MSME Technology Center') }}</h1>
            <p>Empowering education through technology. Choose your portal to get started.</p>
        </div>

        <!-- Portal Cards -->
        <div class="portals">
            <!-- Admin Portal -->
            <div class="portal-card">
                <div class="portal-icon admin-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h3>Admin Portal</h3>
                <p>Access for Technology Centre administrators, heads, exam cell, and assessment agency. Manage students, schedules, and system operations.</p>
                <a href="{{ route('admin.login') }}" class="btn btn-primary">Admin Login</a>
            </div>

            <!-- Student Portal -->
            <div class="portal-card">
                <div class="portal-icon student-icon">
                    <i class="bi bi-mortarboard"></i>
                </div>
                <h3>Student Portal</h3>
                <p>Access for students to view their academic records, performance, and course information. Track your progress and achievements.</p>
                <a href="{{ route('student.login') }}" class="btn btn-success">Student Login</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ env('PROJECT_NAME', 'MSME Technology Center') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
