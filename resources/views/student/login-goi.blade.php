<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Student Login - MSME Technology Centre, Government of India">
    <meta name="keywords" content="MSME, Government of India, Student Login, Technology Centre, Training">
    
    <!-- Security Headers -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <title>Student Login | MSME Technology Centre | Government of India</title>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Government of India Theme CSS -->
    <link href="{{ asset('css/goi-theme.css') }}" rel="stylesheet">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&family=Noto+Sans+Devanagari:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Translation Script -->
    <script src="{{ asset('js/translation.js') }}"></script>
    
    <!-- CAPTCHA Script -->
    <script src="{{ asset('js/captcha.js') }}"></script>
    
    <style>
        /* Student Specific Styles */
        .student-banner {
            background: linear-gradient(135deg, #B91C1C 0%, #DC2626 100%);
            padding: 0.5rem;
            text-align: center;
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        .student-badge {
            display: inline-block;
            background: #FF9933;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.75rem;
            margin-left: 0.5rem;
        }
        
        .chat-bubble {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #FBBF24;
            color: #1F2937;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            font-size: 0.8rem;
            max-width: 200px;
            box-shadow: var(--shadow-md);
            z-index: 10;
        }
        
        .chat-bubble:after {
            content: '';
            position: absolute;
            top: 50%;
            right: -8px;
            width: 0;
            height: 0;
            border: 8px solid transparent;
            border-left-color: #FBBF24;
            border-right: 0;
            margin-top: -8px;
        }
        
        .feature-list {
            margin-top: 1.5rem;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }
        
        .feature-icon {
            width: 24px;
            height: 24px;
            background: var(--red-bg);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-red);
            flex-shrink: 0;
        }
        
        .feature-text {
            font-size: 0.875rem;
            color: var(--gray-700);
        }
        
        /* Image styling for student login */
        .goi-login-right img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        /* Custom Navigation Button Styles */
        .navigation-links .btn {
            display: inline-block !important;
            padding: 8px 16px !important;
            margin: 4px !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            line-height: 1.5 !important;
            text-align: center !important;
            text-decoration: none !important;
            vertical-align: middle !important;
            cursor: pointer !important;
            border: 2px solid !important;
            border-radius: 20px !important;
            transition: all 0.3s ease !important;
            background-color: transparent !important;
        }
        
        .navigation-links .btn-outline-primary {
            color: #0d6efd !important;
            border-color: #0d6efd !important;
        }
        
        .navigation-links .btn-outline-primary:hover {
            color: #fff !important;
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
        }
        
        .navigation-links .btn-outline-info {
            color: #0dcaf0 !important;
            border-color: #0dcaf0 !important;
        }
        
        .navigation-links .btn-outline-info:hover {
            color: #fff !important;
            background-color: #0dcaf0 !important;
            border-color: #0dcaf0 !important;
        }
        
        .navigation-links .btn-outline-success {
            color: #198754 !important;
            border-color: #198754 !important;
        }
        
        .navigation-links .btn-outline-success:hover {
            color: #fff !important;
            background-color: #198754 !important;
            border-color: #198754 !important;
        }
        
        .navigation-links .btn-outline-warning {
            color: #ffc107 !important;
            border-color: #ffc107 !important;
        }
        
        .navigation-links .btn-outline-warning:hover {
            color: #000 !important;
            background-color: #ffc107 !important;
            border-color: #ffc107 !important;
        }
        
        .navigation-links .btn i {
            margin-right: 4px !important;
        }
    </style>
</head>
<body>
    <!-- Skip to Content (Accessibility) -->
    <a href="#main-content" class="skip-to-content">Skip to main content</a>
    
    <!-- Government Header -->
    <header class="goi-header">
        <div class="goi-header-content">
            <div class="goi-emblem-section">
                <img src="{{ asset('msme_logo/msme_logo.png') }}" alt="MSME Technology Centre" class="goi-emblem">
                <div class="goi-title-section">
                    <h1 class="goi-hindi-title">एमएसएमई प्रौद्योगिकी केंद्र द्वैध (एबी-एए)</h1>
                    <h1 class="goi-title">MSME Technology Centre Dual (AB-AA)</h1>
                    <p class="goi-subtitle">Government of India | भारत सरकार</p>
                </div>
            </div>
            <div class="goi-language-switcher">
                <button class="goi-language-btn active" data-lang="en" data-lang-code="en">English</button>
                <button class="goi-language-btn" data-lang="hi" data-lang-code="hi">हिन्दी</button>
                <button class="goi-language-btn" data-lang="font-size" data-action="increase">A+</button>
                <button class="goi-language-btn" data-lang="font-size" data-action="decrease">A-</button>
            </div>
        </div>
    </header>
    
    @include('layouts.goi-navigation')
    
    <!-- Student Banner -->
    <div class="student-banner">
        <i class="fas fa-graduation-cap"></i> STUDENT LOGIN PORTAL
        <span class="student-badge">LEARNER ACCESS</span>
    </div>
    
    <!-- Main Content -->
    <main id="main-content" class="page-transition">
        <div class="goi-login-container" style="flex: 1 0 auto;">
            <!-- Left Side - Login Form -->
            <div class="goi-login-left">
                <div>
                    <!-- Login Header -->
                    <div class="goi-login-header">
                        <h2><i class="fas fa-user-graduate"></i> Create an account</h2>
                        <p>Sign in and get started</p>
                    </div>

                    <!-- Navigation Links -->
                    <div class="navigation-links mb-4">
                        <p class="text-center text-muted mb-2">Quick Access:</p>
                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            <a href="{{ route('home') }}" class="btn btn-outline-primary">
                                <i class="fas fa-home me-1"></i>Home
                            </a>
                            <a href="{{ route('public.lms.index') }}" class="btn btn-outline-info">
                                <i class="fas fa-book me-1"></i>LMS
                            </a>
                            <a href="{{ route('public.exam-schedules') }}" class="btn btn-outline-success">
                                <i class="fas fa-calendar-alt me-1"></i>Exam Schedules
                            </a>
                            <a href="{{ route('admin.login') }}" class="btn btn-outline-warning">
                                <i class="fas fa-shield-alt me-1"></i>Admin Login
                            </a>
                        </div>
                    </div>
                    
                    <!-- Alert Messages -->
                    @include('partials.notification')
                </div>
                
                <!-- Login Form -->
                <form id="loginForm" action="{{ route('student.login') }}" method="POST">
                    @csrf
                    
                    <!-- Email/Roll Number Field -->
                    <div class="goi-form-group">
                        <label for="email" class="goi-form-label">
                            Email Address / Roll Number
                        </label>
                        <input 
                            type="text" 
                            id="email" 
                            name="email" 
                            class="goi-form-input @error('email') error @enderror"
                            placeholder="yourname@example.com or roll number"
                            value="{{ old('email') }}"
                            required
                            autocomplete="username"
                            aria-label="Email or Roll Number"
                            aria-required="true"
                        >
                        <span id="emailError" class="error-text" style="color: var(--error); font-size: 0.875rem; display: none;"></span>
                        @error('email')
                            <span class="error-text" style="color: var(--error); font-size: 0.875rem;">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <!-- Password Field -->
                    <div class="goi-form-group">
                        <label for="password" class="goi-form-label">
                            Password
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="goi-form-input @error('password') error @enderror"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                            aria-label="Password"
                            aria-required="true"
                        >
                        <span id="passwordError" class="error-text" style="color: var(--error); font-size: 0.875rem; display: none;"></span>
                        @error('password')
                            <span class="error-text" style="color: var(--error); font-size: 0.875rem;">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <!-- Remember Me & Forgot Password -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="remember" id="remember">
                            <span style="font-size: 0.875rem;">Remember me</span>
                        </label>
                        <a href="{{ route('student.password.request') }}" style="font-size: 0.875rem; color: var(--primary-red); text-decoration: none;">
                            Forgot Password?
                        </a>
                    </div>
                    
                    <!-- CAPTCHA Section -->
                    <div class="goi-form-group" style="margin-bottom: 1.5rem;">
                        <div class="captcha-container" style="text-align: center; padding: 1rem; background: #f8f9fa; border-radius: 8px; border: 1px solid #e9ecef;">
                            <i class="fas fa-shield-alt" style="color: var(--primary-red); font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
                            <p style="margin: 0; font-size: 0.875rem; color: var(--gray-600);" data-translate="Security verification required">Security verification required</p>
                            <div id="captcha-badge" style="margin-top: 0.5rem;"></div>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" id="submitBtn" class="goi-btn goi-btn-primary goi-btn-block">
                        <span id="submitText">Submit</span>
                        <span id="submitLoader" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i> Signing In...
                        </span>
                    </button>
                    
                    <!-- New Student Registration -->
                    <div style="text-align: center; margin-top: 1rem;">
                        <p style="font-size: 0.875rem; color: var(--gray-600);">
                            New Student? <a href="#" style="color: var(--primary-red); font-weight: 600;">Register Here</a>
                        </p>
                    </div>
                </form>
                
                <!-- Footer Links -->
                <div style="margin-top: 1.5rem; text-align: center;">
                    <p style="font-size: 0.75rem; color: var(--gray-600);">
                        By signing in, you agree to our <a href="#" style="color: var(--primary-red);">Terms & Conditions</a> and <a href="#" style="color: var(--primary-red);">Privacy Policy</a>
                    </p>
                </div>
            </div>
            
            <!-- Right Side - Image and Info -->
            <div class="goi-login-right">
                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=600&h=400&fit=crop&crop=center" 
                     alt="MSME Student Learning"
                     onerror="this.src='{{ asset('msme_logo/msme_logo.png') }}'; this.style.objectFit='contain'; this.style.padding='20px';">
                
                <!-- Chat Bubble -->
                <div class="chat-bubble">
                    <strong>Welcome!</strong>
                    <p style="margin-top: 0.25rem;">Sign in to access your courses and learning resources.</p>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="goi-footer">
        <div class="goi-footer-content">
            <div class="goi-footer-copyright">
                © 2024 Ministry of MSME, Government of India
            </div>
            <div class="goi-footer-links">
                <a href="#">Terms of Use</a>
                <a href="#">Privacy Policy</a>
                <a href="#">Contact Us</a>
                <a href="#">Help</a>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitLoader = document.getElementById('submitLoader');
            
            // Auto-hide messages handled by notification system
            
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
                    setTimeout(() => {
                        window.location.reload();
                    }, 5000);
                });
            }
            
            // Refresh CSRF token every 15 minutes
            setInterval(refreshCsrfToken, 15 * 60 * 1000);
            
            // Also refresh on page focus
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    refreshCsrfToken();
                }
            });
            
            // Initialize CAPTCHA for the form
            if (window.captchaManager) {
                window.captchaManager.addToAjaxForm(form, 'student_login');
            }
            
            // Form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Reset errors
                errorAlert.style.display = 'none';
                document.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
                document.querySelectorAll('.error-text').forEach(el => el.style.display = 'none');
                
                // Show loading
                submitBtn.disabled = true;
                submitText.style.display = 'none';
                submitLoader.style.display = 'inline-block';
                
                // Get form data
                const formData = new FormData(form);
                
                // Validate CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                if (!csrfToken || csrfToken.length < 10) {
                    console.log('Invalid CSRF token detected, refreshing...');
                    refreshCsrfToken();
                    showError('Session issue detected. Please try again.');
                    resetButton();
                    return;
                }
                
                // Submit form
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            let errorData = null;
                            try {
                                errorData = JSON.parse(text);
                            } catch (e) {
                                errorData = { message: `HTTP error! status: ${response.status}` };
                            }
                            throw new Error(`HTTP error! status: ${response.status}, message: ${errorData.message || 'Unknown error'}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    resetButton();
                    
                    if (data.success) {
                        showNotification(data.message || 'Login successful! Redirecting...', 'success');
                        setTimeout(() => {
                            if (data.redirect_url) {
                                window.location.href = data.redirect_url;
                            } else {
                                window.location.reload();
                            }
                        }, 1000);
                    } else {
                        if (data.errors) {
                            showFieldErrors(data.errors);
                        } else if (data.message) {
                            showNotification(data.message, 'error');
                        }
                    }
                })
                .catch(error => {
                    resetButton();
                    console.error('Error:', error);
                    
                    if (error.message && error.message.includes('HTTP error')) {
                        const statusMatch = error.message.match(/status: (\d+)/);
                        const messageMatch = error.message.match(/message: (.+)$/);
                        
                        if (statusMatch) {
                            const status = statusMatch[1];
                            const serverMessage = messageMatch ? messageMatch[1] : null;
                            
                                                            if (serverMessage && serverMessage !== 'Unknown error') {
                                    if (serverMessage.includes('CSRF token') || serverMessage.includes('419')) {
                                        console.log('CSRF token mismatch detected, attempting to refresh...');
                                        refreshCsrfToken();
                                        showNotification('Session issue detected. Please try again.', 'warning');
                                    } else {
                                        showNotification(serverMessage, 'error');
                                    }
                                } else if (status === '419') {
                                    console.log('419 status detected, attempting to refresh...');
                                    refreshCsrfToken();
                                    showNotification('Session issue detected. Please try again.', 'warning');
                                } else if (status === '422') {
                                    showNotification('Invalid credentials. Please check your login details.', 'error');
                                } else if (status === '500') {
                                    showNotification('Server error. Please try again later.', 'error');
                                } else if (status === '404') {
                                    showNotification('Login endpoint not found. Please contact administrator.', 'error');
                                } else {
                                    showNotification(`Server error (${status}). Please try again later.`, 'error');
                                }
                            } else {
                                showNotification('Server error. Please try again later.', 'error');
                            }
                        } else if (error.message) {
                            showNotification(error.message, 'error');
                        } else {
                            showNotification('Network error. Please check your connection and try again.', 'error');
                    }
                });
            });
            
            function resetButton() {
                submitBtn.disabled = false;
                submitText.style.display = 'inline-block';
                submitLoader.style.display = 'none';
            }
            
            function showError(message) {
                console.error('Login Error:', message);
                showNotification(message, 'error');
            }
            
            function showFieldErrors(errors) {
                // Create a list of all errors
                const errorList = Object.keys(errors).map(field => errors[field][0]);
                
                // Show notification with all errors
                if (errorList.length > 0) {
                    showNotification('Please correct the following errors:', 'error', 0);
                    
                    // Create a notification with the error list
                    const container = document.querySelector('.goi-notification-container');
                    if (container) {
                        const notification = document.createElement('div');
                        notification.className = 'goi-alert goi-alert-error persistent';
                        notification.setAttribute('role', 'alert');
                        
                        let errorContent = `
                            <i class="fas fa-exclamation-circle"></i>
                            <div class="notification-content">
                                <ul class="mt-2 ml-4" style="margin-top: 0.5rem; margin-left: 1.25rem; list-style-type: disc;">
                        `;
                        
                        errorList.forEach(error => {
                            errorContent += `<li style="margin-bottom: 0.25rem;">${error}</li>`;
                        });
                        
                        errorContent += `
                                </ul>
                            </div>
                            <button type="button" class="notification-close" onclick="this.parentElement.remove();" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        `;
                        
                        notification.innerHTML = errorContent;
                        container.appendChild(notification);
                    }
                }
                
                // Also mark fields as error in the form
                Object.keys(errors).forEach(field => {
                    const input = document.getElementById(field);
                    const errorSpan = document.getElementById(field + 'Error');
                    
                    if (input) {
                        input.classList.add('error');
                    }
                    
                    if (errorSpan) {
                        errorSpan.textContent = errors[field][0];
                        errorSpan.style.display = 'block';
                    }
                });
            }
            
            // Language switcher
            document.querySelectorAll('.goi-language-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.goi-language-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                });
            });
            
            // Font size adjustment
            const fontSizeButtons = document.querySelectorAll('.goi-language-btn');
            fontSizeButtons[2].addEventListener('click', function() {
                document.body.style.fontSize = '1.1rem';
            });
            fontSizeButtons[3].addEventListener('click', function() {
                document.body.style.fontSize = '1rem';
            });
        });
    </script>
</body>
</html>