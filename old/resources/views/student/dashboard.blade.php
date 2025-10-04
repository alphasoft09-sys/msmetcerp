<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - {{ env('PROJECT_NAME', 'MSME Technology Center') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse" id="sidebar">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white fw-bold">Student Portal</h4>
                        <p class="text-white-50 small">{{ $student->name }}</p>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">
                                <i class="bi bi-speedometer2 me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-file-text me-2"></i>
                                My Results
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-calendar-check me-2"></i>
                                Exam Schedule
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-person-circle me-2"></i>
                                Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('student.profile.change') }}">
                                <i class="bi bi-shield-lock me-2"></i>
                                Change Password & Email
                            </a>
                        </li>
                        
                        <li class="nav-item mt-3">
                            <button type="button" class="nav-link border-0 bg-transparent text-white-50" id="logoutBtn">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Logout
                            </button>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <!-- Top navbar -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
                    <div class="container-fluid">
                        <button class="navbar-toggler d-md-none" type="button" id="sidebarToggle">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        
                        <div class="navbar-nav ms-auto">
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    {{ $student->name }}
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Profile</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <button type="button" class="dropdown-item" id="logoutBtnDropdown">Logout</button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Page content -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Student Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary">Download Certificate</button>
                        </div>
                    </div>
                </div>

                <!-- Student Info Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stats-card h-100">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-white-50 text-uppercase mb-1">
                                            TC Code
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-white">{{ $student->tc_code }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-building-fill fa-2x text-white-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Class
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $student->class ?? 'N/A' }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-mortarboard-fill fa-2x text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Roll Number
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $student->roll_number ?? 'N/A' }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-123 fa-2x text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Average Score
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">85%</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-graph-up fa-2x text-info"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row mb-4">
                    <div class="col-xl-8 col-lg-7">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Performance Overview</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="performanceChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-5">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Subject Performance</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="subjectPieChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Results Table -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Recent Exam Results</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Exam Name</th>
                                        <th>Subject</th>
                                        <th>Date</th>
                                        <th>Score</th>
                                        <th>Grade</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Mid-Term Assessment</td>
                                        <td>Mathematics</td>
                                        <td>2024-01-15</td>
                                        <td>92%</td>
                                        <td>A</td>
                                        <td><span class="badge bg-success">Passed</span></td>
                                    </tr>
                                    <tr>
                                        <td>Practical Test</td>
                                        <td>Computer Science</td>
                                        <td>2024-01-20</td>
                                        <td>88%</td>
                                        <td>A-</td>
                                        <td><span class="badge bg-success">Passed</span></td>
                                    </tr>
                                    <tr>
                                        <td>Final Examination</td>
                                        <td>Physics</td>
                                        <td>2024-01-25</td>
                                        <td>85%</td>
                                        <td>B+</td>
                                        <td><span class="badge bg-success">Passed</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Personal Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> {{ $student->name }}</p>
                                <p><strong>Email:</strong> {{ $student->email }}</p>
                                <p><strong>Phone:</strong> {{ $student->phone ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>TC Code:</strong> {{ $student->tc_code }}</p>
                                <p><strong>Class:</strong> {{ $student->class ?? 'N/A' }}</p>
                                <p><strong>Roll Number:</strong> {{ $student->roll_number ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

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
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Performance Chart
        const performanceCtx = document.getElementById('performanceChart').getContext('2d');
        new Chart(performanceCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Performance Score',
                    data: [82, 85, 88, 86, 90, 92],
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });

        // Subject Performance Pie Chart
        const subjectPieCtx = document.getElementById('subjectPieChart').getContext('2d');
        new Chart(subjectPieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Mathematics', 'Physics', 'Computer Science', 'English'],
                datasets: [{
                    data: [92, 85, 88, 90],
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        const logoutBtn = document.getElementById('logoutBtn');
        const logoutBtnDropdown = document.getElementById('logoutBtnDropdown');
        const confirmLogoutBtn = document.getElementById('confirmLogout');
        const logoutText = document.getElementById('logoutText');
        const logoutLoader = document.getElementById('logoutLoader');
        const logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));
        const logoutForm = document.getElementById('logoutForm');

        if (logoutBtn) {
            logoutBtn.addEventListener('click', function() {
                logoutModal.show();
            });
        }
        if (logoutBtnDropdown) {
            logoutBtnDropdown.addEventListener('click', function() {
                logoutModal.show();
            });
        }
        confirmLogoutBtn.addEventListener('click', function() {
            setLogoutLoading(true);
            
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            console.log('CSRF Token:', csrfToken);
            console.log('Logout URL:', '{{ route("student.logout") }}');
            
            fetch('{{ route("student.logout") }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Logout response:', data);
                if (data.success) {
                    window.location.href = data.redirect_url;
                } else {
                    setLogoutLoading(false);
                    logoutModal.hide();
                    alert('Logout failed. Please try again.');
                }
            })
            .catch(error => {
                console.error('Logout error:', error);
                setLogoutLoading(false);
                logoutModal.hide();
                // Fallback to form submission
                console.log('Falling back to form submission');
                logoutForm.submit();
            });
        });



        function setLogoutLoading(loading) {
            if (loading) {
                confirmLogoutBtn.disabled = true;
                logoutText.textContent = 'Logging out...';
                logoutLoader.classList.remove('d-none');
            } else {
                confirmLogoutBtn.disabled = false;
                logoutText.textContent = 'Logout';
                logoutLoader.classList.add('d-none');
            }
        }
    });
    </script>
</body>
</html> 