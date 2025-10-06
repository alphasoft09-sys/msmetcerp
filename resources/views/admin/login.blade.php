<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Admin Login - {{ env('PROJECT_NAME', 'MSME Technology Center') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    @include('layouts.goi-header')
    @include('layouts.goi-navigation')
    <div class="login-container">
        <div class="login-card">
            <div class="card-body">
                <div class="login-header">
                    <img src="{{ asset('msme_logo/favicon-96x96.png') }}" alt="MSME Logo" class="logo">
                    <h1>{{ env('PROJECT_NAME', 'MSME Technology Center') }}</h1>
                    <p>Admin Login</p>
                </div>

                <!-- Navigation Links -->
                <div class="navigation-links mb-4">
                    <p class="text-center text-muted mb-2">Quick Access:</p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="{{ route('home') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-house me-1"></i>Home
                        </a>
                        <a href="{{ route('public.lms.index') }}" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-book me-1"></i>LMS
                        </a>
                        <a href="{{ route('public.exam-schedules') }}" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-calendar-event me-1"></i>Exam Schedules
                        </a>
                        <a href="{{ route('student.login') }}" class="btn btn-outline-warning btn-sm">
                            <i class="bi bi-mortarboard me-1"></i>Student Login
                        </a>
                    </div>
                </div>

                <!-- Error Alert -->
                <div id="errorAlert" class="alert alert-danger d-none fade-in-up" role="alert">
                    <div id="errorMessage"></div>
                </div>

                <!-- Success Alert -->
                <div id="successAlert" class="alert alert-success d-none fade-in-up" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    Login successful! Redirecting...
                </div>

                <!-- Logout Message -->
                @if(session('message'))
                <div class="alert alert-info fade-in-up" role="alert" id="logoutMessage">
                    <i class="bi bi-info-circle me-2"></i>
                    {{ session('message') }}
                </div>
                @endif

                <!-- 404 Error Message -->
                @if(session('error'))
                <div class="alert alert-warning fade-in-up" role="alert" id="errorMessage">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                </div>
                @endif

                <form id="adminLoginForm" method="POST" action="{{ route('admin.login') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email" class="form-control" 
                                   id="email" name="email" value="{{ old('email') }}" required autofocus
                                   placeholder="Enter your email">
                        </div>
                        <div class="invalid-feedback" id="emailError"></div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" class="form-control" 
                                   id="password" name="password" required
                                   placeholder="Enter your password">
                        </div>
                        <div class="invalid-feedback" id="passwordError"></div>
                    </div>

                    <div class="d-grid mb-4">
                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                            <span id="submitText">Sign In</span>
                            <span id="submitLoader" class="spinner-border spinner-border-sm d-none ms-2" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </span>
                        </button>
                    </div>
                </form>

                <div class="text-center mb-3">
                    <a href="{{ route('admin.password.request') }}" class="btn-link">
                        <i class="bi bi-question-circle me-1"></i>
                        Forgot Password?
                    </a>
                </div>

                <div class="text-center">
                    <a href="{{ route('student.login') }}" class="btn-link">
                        <i class="bi bi-arrow-right me-1"></i>
                        Student Login
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('adminLoginForm');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        const submitLoader = document.getElementById('submitLoader');
        const errorAlert = document.getElementById('errorAlert');
        const errorMessage = document.getElementById('errorMessage');
        const successAlert = document.getElementById('successAlert');
        const logoutMessage = document.getElementById('logoutMessage');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Reset previous errors
            resetErrors();
            
            // Show loader
            setLoading(true);
            
            // Get form data
            const formData = new FormData(form);
            
            // Validate CSRF token before making request
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            if (!csrfToken || csrfToken.length < 10) {
                console.log('Invalid CSRF token detected, refreshing...');
                refreshCsrfToken();
                showError('Session issue detected. Refreshing token... Please try again in a moment.');
                return;
            }
            
            // Make AJAX request
            console.log('Submitting login form to:', form.action);
            console.log('Form data:', Object.fromEntries(formData));
            console.log('CSRF token:', csrfToken.substring(0, 20) + '...');
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    // Try to get error message from response
                    return response.text().then(text => {
                        let errorData = null;
                        try {
                            errorData = JSON.parse(text);
                        } catch (e) {
                            // If not JSON, create a generic error
                            errorData = { message: `HTTP error! status: ${response.status}` };
                        }
                        throw new Error(`HTTP error! status: ${response.status}, message: ${errorData.message || 'Unknown error'}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                setLoading(false);
                
                if (data.success) {
                    // Show success message
                    showSuccess();
                    
                    // Redirect after a short delay with page reload
                    setTimeout(() => {
                        if (data.redirect_url) {
                            // Navigate to the redirect URL
                            window.location.href = data.redirect_url;
                        } else {
                            // If no redirect URL, reload the current page
                            window.location.reload();
                        }
                    }, 1000);
                } else {
                    // Show errors
                    if (data.errors) {
                        showFieldErrors(data.errors);
                    } else if (data.message) {
                        showError(data.message);
                    }
                }
            })
            .catch(error => {
                setLoading(false);
                console.error('Error:', error);
                
                // Try to get more specific error information
                if (error.name === 'TypeError' && error.message.includes('JSON')) {
                    showError('Server error. Please try again later.');
                } else if (error.message && error.message.includes('HTTP error')) {
                    // Extract status code and message from error message
                    const statusMatch = error.message.match(/status: (\d+)/);
                    const messageMatch = error.message.match(/message: (.+)$/);
                    
                    if (statusMatch) {
                        const status = statusMatch[1];
                        const serverMessage = messageMatch ? messageMatch[1] : null;
                        
                        if (serverMessage && serverMessage !== 'Unknown error') {
                            // Check for CSRF token mismatch
                            if (serverMessage.includes('CSRF token') || serverMessage.includes('419')) {
                                console.log('CSRF token mismatch detected, attempting to refresh...');
                                // Try to refresh the CSRF token first
                                refreshCsrfToken();
                                showError('Session issue detected. Refreshing token... Please try again in a moment.');
                            } else {
                                showError(serverMessage);
                            }
                        } else if (status === '419') {
                            console.log('419 status detected, attempting to refresh...');
                            // Try to refresh the CSRF token first
                            refreshCsrfToken();
                            showError('Session issue detected. Refreshing token... Please try again in a moment.');
                        } else if (status === '422') {
                            showError('Invalid credentials. Please check your email and password.');
                        } else if (status === '500') {
                            showError('Server error. Please try again later.');
                        } else if (status === '404') {
                            showError('Login endpoint not found. Please contact administrator.');
                        } else {
                            showError(`Server error (${status}). Please try again later.`);
                        }
                    } else {
                        showError('Server error. Please try again later.');
                    }
                } else if (error.message) {
                    showError(error.message);
                } else {
                    showError('Network error. Please check your connection and try again.');
                }
            });
        });

        function setLoading(loading) {
            if (loading) {
                submitBtn.disabled = true;
                submitText.textContent = 'Signing In...';
                submitLoader.classList.remove('d-none');
                submitBtn.classList.add('btn-loading');
            } else {
                submitBtn.disabled = false;
                submitText.textContent = 'Sign In';
                submitLoader.classList.add('d-none');
                submitBtn.classList.remove('btn-loading');
            }
        }

        function showError(message) {
            errorMessage.textContent = message;
            errorAlert.classList.remove('d-none');
            successAlert.classList.add('d-none');
            
            // Log error for debugging
            console.error('Login Error:', message);
            
            // Auto-hide error after 5 seconds
            setTimeout(() => {
                errorAlert.classList.add('d-none');
            }, 5000);
        }

        function showSuccess() {
            successAlert.classList.remove('d-none');
            errorAlert.classList.add('d-none');
        }

        function showFieldErrors(errors) {
            Object.keys(errors).forEach(field => {
                const input = document.getElementById(field);
                const errorDiv = document.getElementById(field + 'Error');
                
                if (input && errorDiv) {
                    input.classList.add('is-invalid');
                    errorDiv.textContent = errors[field][0];
                }
            });
        }

        function resetErrors() {
            // Remove all invalid classes
            form.querySelectorAll('.is-invalid').forEach(input => {
                input.classList.remove('is-invalid');
            });
            
            // Hide alerts
            errorAlert.classList.add('d-none');
            successAlert.classList.add('d-none');
            if (logoutMessage) {
                logoutMessage.classList.add('d-none'); // Hide logout message
            }
        }

        // Auto-hide logout message after 5 seconds
        if (logoutMessage) {
            setTimeout(() => {
                logoutMessage.classList.add('d-none');
            }, 5000);
        }

        // Function to refresh CSRF token
        function refreshCsrfToken() {
            fetch('/csrf-token', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to refresh CSRF token');
                }
                return response.json();
            })
            .then(data => {
                if (data.token) {
                    document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.token);
                    console.log('CSRF token refreshed successfully');
                }
            })
            .catch(error => {
                console.error('Failed to refresh CSRF token:', error);
                // If refresh fails, reload the page to get a fresh session
                setTimeout(() => {
                    window.location.reload();
                }, 5000);
            });
        }

        // Refresh CSRF token every 15 minutes to prevent expiration
        setInterval(refreshCsrfToken, 15 * 60 * 1000);
        
        // Also refresh on page focus to ensure token is fresh
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                refreshCsrfToken();
            }
        });
    });
    </script>
</body>
</html> 