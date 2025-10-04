<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - {{ env('PROJECT_NAME', 'MSME Technology Center') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Mobile Toggle Button -->
    <button class="mobile-toggle" type="button" id="sidebarToggle" style="position: fixed; top: 1rem; left: 1rem; z-index: 1040; background: white; border: 1px solid #ddd; border-radius: 4px; padding: 0.75rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: none; color: #333; cursor: pointer; min-width: 44px; min-height: 44px; align-items: center; justify-content: center;">
        <i class="bi bi-list" style="font-size: 1.25rem;"></i>
    </button>

    <!-- Sidebar Backdrop for Mobile -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar" style="position: fixed; top: 0; left: 0; height: 100vh; width: 280px; background: white; box-shadow: 2px 0 10px rgba(0,0,0,0.1); z-index: 1000; overflow-y: auto; overflow-x: hidden; border-right: 1px solid #ddd;">
        <div class="sidebar-header">
            <img src="{{ asset('msme_logo/favicon-96x96.png') }}" alt="MSME Logo" class="logo">
            <h3>{{ env('PROJECT_NAME', 'MSME Technology Center') }}</h3>
            <p>
                @php
                    $userRole = Auth::user()->user_role;
                    $roleName = match($userRole) {
                        1 => 'TC Admin',
                        2 => 'TC Head',
                        3 => 'Exam Cell',
                        4 => 'Assessment Agency',
                        5 => 'TC Faculty',
                        default => 'Admin'
                    };
                @endphp
                {{ $roleName }} Panel
            </p>
        </div>
        
        <div class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    @php
                        $userRole = Auth::user()->user_role;
                        $dashboardRoute = \App\Helpers\DashboardHelper::getDashboardRoute($userRole);
                    @endphp
                    <a class="nav-link {{ request()->routeIs($dashboardRoute) ? 'active' : '' }}" 
                       href="{{ route($dashboardRoute) }}">
                        <i class="bi bi-speedometer2"></i>
                        Dashboard
                    </a>
                </li>
                @if(in_array(Auth::user()->user_role, [1, 2])) {{-- Only show for TC Admin and TC Head --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.management.*') ? 'active' : '' }}" 
                       href="{{ route('admin.management.index') }}">
                        <i class="bi bi-people-fill"></i>
                        Admin Management
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.centres.*') ? 'active' : '' }}" 
                       href="{{ route('admin.centres.index') }}">
                        <i class="bi bi-building"></i>
                        Add Centres
                    </a>
                </li>
                @endif
                @if(Auth::user()->user_role === 4) {{-- Only show for Assessment Agency --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.tc-management.*') ? 'active' : '' }}" 
                       href="{{ route('admin.tc-management.index') }}">
                        <i class="bi bi-building-add"></i>
                        Add TC
                    </a>
                </li>
                @endif
                
                @if(in_array(Auth::user()->user_role, [1, 2, 3, 4, 5])) {{-- Show for TC Admin, TC Head, Exam Cell, Assessment Agency, and TC Faculty --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.qualifications.*') ? 'active' : '' }}" 
                       href="{{ route('admin.qualifications.index') }}">
                        <i class="bi bi-award"></i>
                        Qualification List
                    </a>
                </li>
                @endif
                
                @if(Auth::user()->user_role === 4) {{-- Only show for Assessment Agency --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.qualification-modules.*') ? 'active' : '' }}" 
                       href="{{ route('admin.qualification-modules.index') }}">
                        <i class="bi bi-puzzle"></i>
                        Modules
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.tc-header-layouts.*') ? 'active' : '' }}" 
                       href="{{ route('admin.tc-header-layouts.index') }}">
                        <i class="bi bi-image"></i>
                        Header Layout
                    </a>
                </li>
                @endif
                @if(in_array(Auth::user()->user_role, [1, 2, 3, 5])) {{-- Only show for TC Admin, TC Head, Exam Cell, and TC Faculty --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}" 
                       href="{{ route('admin.students.index') }}">
                        <i class="bi bi-people"></i>
                        Students
                    </a>
                </li>
                @endif
                @if(Auth::user()->user_role === 5) {{-- Only show for TC Faculty --}}
                
                
                <!-- <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.faculty.attendance.*') ? 'active' : '' }}" 
                       href="#">
                        <i class="bi bi-check2-square"></i>
                        Attendance
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.faculty.progress.*') ? 'active' : '' }}" 
                       href="#">
                        <i class="bi bi-graph-up"></i>
                        Student Progress
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.faculty.messages.*') ? 'active' : '' }}" 
                       href="#">
                        <i class="bi bi-chat-dots"></i>
                        Messages
                    </a>
                </li> -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.faculty.exam-schedules.*') ? 'active' : '' }}" 
                       href="{{ route('admin.faculty.exam-schedules.index') }}">
                        <i class="bi bi-calendar-event"></i>
                        Exam Schedules
                    </a>
                </li>
                @endif
                
                @if(Auth::user()->user_role === 3) {{-- Only show for Exam Cell --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.exam-cell.exam-schedules.*') ? 'active' : '' }}" 
                       href="{{ route('admin.exam-cell.exam-schedules.index') }}">
                        <i class="bi bi-calendar-event"></i>
                        Exam Schedules
                    </a>
                </li>
                @endif
                @if(Auth::user()->user_role === 1) {{-- Only show for TC Admin --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.tc-admin.exam-schedules.*') ? 'active' : '' }}" 
                       href="{{ route('admin.tc-admin.exam-schedules.index') }}">
                        <i class="bi bi-calendar-event"></i>
                        Exam Schedules
                    </a>
                </li>
                @endif
                @if(Auth::user()->user_role === 2) {{-- Only show for TC Head --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.tc-head.exam-schedules.*') ? 'active' : '' }}" 
                       href="{{ route('admin.tc-head.exam-schedules.index') }}">
                        <i class="bi bi-calendar-event"></i>
                        Exam Schedules
                    </a>
                </li>
                @endif
                @if(Auth::user()->user_role === 4) {{-- Only show for Assessment Agency --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.aa.exam-schedules.*') ? 'active' : '' }}" 
                       href="{{ route('admin.aa.exam-schedules.index') }}">
                        <i class="bi bi-calendar-event"></i>
                        Exam Schedules
                    </a>
                </li>
                @endif
                
                <!-- <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-file-earmark-text"></i>
                        Reports
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-gear"></i>
                        Settings
                    </a>
                </li> -->
                
                {{-- LMS Management Links --}}
                @if(Auth::user()->user_role == 4) {{-- Assessment Agency --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.lms-departments.*') ? 'active' : '' }}" 
                       href="{{ route('admin.lms-departments.index') }}">
                        <i class="bi bi-building"></i>
                        LMS Departments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.tc-lms.admin-*') ? 'active' : '' }}" 
                       href="{{ route('admin.tc-lms.admin-index') }}">
                        <i class="bi bi-globe"></i>
                        LMS Approval
                    </a>
                </li>
                @endif
                
                @if(Auth::user()->user_role == 5) {{-- Faculty --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.tc-lms.*') && !request()->routeIs('admin.tc-lms.admin-*') ? 'active' : '' }}" 
                       href="{{ route('admin.tc-lms.index') }}">
                        <i class="bi bi-globe"></i>
                        My LMS Sites
                    </a>
                </li>
                @endif
                
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.profile.change') }}">
                        <i class="bi bi-shield-lock"></i>
                        Change Password & Email
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}" 
                       href="{{ route('admin.profile.view') }}">
                        <i class="bi bi-person-circle"></i>
                        My Profile
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="sidebar-footer">
            <div class="user-info">
                Logged in as: <strong>{{ Auth::user()->name }}</strong>
            </div>
            <button type="button" class="btn btn-outline-light btn-sm w-100 text-dark" id="logoutBtn">
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
    <form id="logoutForm" method="POST" action="{{ route('admin.logout') }}" style="display: none;">
        @csrf
    </form>

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
        
        if (sidebarToggle && sidebar && sidebarBackdrop) {
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
                    window.location.href = '{{ route("admin.logout") }}';
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
    
    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>