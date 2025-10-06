<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Student Dashboard') - {{ env('PROJECT_NAME', 'MSME Technology Center') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    @include('layouts.goi-header')
    @include('layouts.goi-navigation')
    
    <!-- Mobile Toggle Button -->
    <button class="mobile-toggle" type="button" id="sidebarToggle">
        <i class="bi bi-list" style="font-size: 1.25rem;"></i>
    </button>

    <!-- Sidebar Backdrop for Mobile -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('msme_logo/favicon-96x96.png') }}" alt="MSME Logo" class="logo">
            <h3>{{ env('PROJECT_NAME', 'MSME Technology Center') }}</h3>
            <p>Student Portal</p>
        </div>
        
        <div class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" href="{{ route('student.dashboard') }}">
                        <i class="bi bi-speedometer2"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-file-text"></i>
                        My Results
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-calendar-check"></i>
                        Exam Schedule
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-person-circle"></i>
                        Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.profile.change') ? 'active' : '' }}" href="{{ route('student.profile.change') }}">
                        <i class="bi bi-shield-lock"></i>
                        Change Password & Email
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="sidebar-footer">
            <div class="user-info">
                Logged in as: <strong>{{ $student->name ?? $user->name ?? Auth::guard('student')->user()->name }}</strong>
            </div>
            <button type="button" class="btn btn-outline-light btn-sm w-100" id="logoutBtn">
                <i class="bi bi-box-arrow-right me-2"></i>
                Logout
            </button>
        </div>
    </nav>

    <!-- Main content -->
    <main class="main-content">
        <!-- Success Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show fade-in-up" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Error Messages -->
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show fade-in-up" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Logout Confirmation Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to logout?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmLogout">
                        <span id="logoutText">Logout</span>
                        <span id="logoutLoader" class="spinner-border spinner-border-sm d-none ms-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden logout form as backup -->
    <form id="logoutForm" method="POST" action="{{ route('student.logout') }}" style="display: none;">
        @csrf
    </form>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if this is a fresh login (from session)
        @if(session('success') && str_contains(session('success'), 'Login successful'))
            // Force a page reload to ensure fresh data
            if (!sessionStorage.getItem('loginReloaded')) {
                sessionStorage.setItem('loginReloaded', 'true');
                window.location.reload();
            }
        @endif
        
        // Clear login reload flag when navigating away
        window.addEventListener('beforeunload', function() {
            sessionStorage.removeItem('loginReloaded');
        });
        
        // Sidebar toggle functionality
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
                if (sidebar.classList.contains('show')) {
                    sidebar.style.transform = 'translateX(0)';
                    sidebarBackdrop.style.opacity = '1';
                    sidebarBackdrop.style.visibility = 'visible';
                } else {
                    sidebar.style.transform = 'translateX(-100%)';
                    sidebarBackdrop.style.opacity = '0';
                    sidebarBackdrop.style.visibility = 'hidden';
                }
            });
            
            sidebarBackdrop.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebar.style.transform = 'translateX(-100%)';
                sidebarBackdrop.style.opacity = '0';
                sidebarBackdrop.style.visibility = 'hidden';
            });
        }
        
        // Mobile responsive sidebar
        function handleResize() {
            if (window.innerWidth <= 768) {
                sidebarToggle.style.display = 'flex';
                if (!sidebar.classList.contains('show')) {
                    sidebar.style.transform = 'translateX(-100%)';
                }
            } else {
                sidebarToggle.style.display = 'none';
                sidebar.style.transform = 'translateX(0)';
                sidebar.classList.remove('show');
                sidebarBackdrop.style.opacity = '0';
                sidebarBackdrop.style.visibility = 'hidden';
            }
        }
        
        window.addEventListener('resize', handleResize);
        handleResize(); // Initial call
        
        // Logout Modal Functionality
        const logoutBtn = document.getElementById('logoutBtn');
        const logoutModal = document.getElementById('logoutModal');
        const confirmLogoutBtn = document.getElementById('confirmLogout');
        const logoutText = document.getElementById('logoutText');
        const logoutLoader = document.getElementById('logoutLoader');
        const logoutForm = document.getElementById('logoutForm');
        
        // Initialize Bootstrap Modal
        let logoutModalInstance;
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            logoutModalInstance = new bootstrap.Modal(logoutModal);
        }
        
        // Logout button click handler
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Logout button clicked');
                
                if (logoutModalInstance) {
                    logoutModalInstance.show();
                } else {
                    // Fallback if Bootstrap Modal is not available
                    logoutModal.style.display = 'block';
                    logoutModal.classList.add('show');
                    logoutModal.setAttribute('aria-hidden', 'false');
                }
            });
        }
        
        // Confirm logout button click handler
        if (confirmLogoutBtn) {
            confirmLogoutBtn.addEventListener('click', function() {
                console.log('Confirm logout clicked');
                
                // Show loading state
                logoutText.textContent = 'Logging out...';
                logoutLoader.classList.remove('d-none');
                confirmLogoutBtn.disabled = true;
                
                // Submit the logout form
                if (logoutForm) {
                    logoutForm.submit();
                } else {
                    // Fallback: redirect to logout route
                    window.location.href = '{{ route("student.logout") }}';
                }
            });
        }
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                } else {
                    alert.style.display = 'none';
                }
            });
        }, 5000);
    });
    </script>
</body>
</html>