<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Student Forgot Password - MSME Technology Centre, Government of India">
    <meta name="keywords" content="MSME, Government of India, Student Password Reset, Technology Centre">
    
    <!-- Security Headers -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <title>Student Forgot Password | MSME Technology Centre | Government of India</title>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Government of India Theme CSS -->
    <link href="{{ asset('css/goi-theme.css') }}" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&family=Noto+Sans+Devanagari:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
        
        /* Forgot Password Specific Styles */
        .steps-container {
            margin: 1.5rem 0;
        }
        
        .step {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        
        .step-number {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--primary-red);
            color: white;
            font-weight: 600;
            margin-right: 0.75rem;
            flex-shrink: 0;
            font-size: 0.75rem;
        }
        
        .step-content {
            flex: 1;
        }
        
        .step-title {
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 0.25rem;
            font-size: 0.875rem;
        }
        
        .step-description {
            font-size: 0.75rem;
            color: var(--gray-600);
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
                <button class="goi-language-btn active">English</button>
                <button class="goi-language-btn">हिन्दी</button>
                <button class="goi-language-btn">A+</button>
                <button class="goi-language-btn">A-</button>
            </div>
        </div>
    </header>
    
    <!-- Student Banner -->
    <div class="student-banner">
        <i class="fas fa-graduation-cap"></i> STUDENT PASSWORD RECOVERY
        <span class="student-badge">LEARNER ACCESS</span>
    </div>
    
    <!-- Main Content -->
    <main id="main-content" class="page-transition">
        <div class="goi-login-container" style="flex: 1 0 auto;">
            <!-- Left Side - Form -->
            <div class="goi-login-left">
                <div>
                    <!-- Header -->
                    <div class="goi-login-header">
                        <h2><i class="fas fa-key"></i> Forgot Password</h2>
                        <p>Reset your student account password</p>
                    </div>
                    
                    <!-- Alert Messages -->
                    @include('partials.notification')
                </div>
                
                <!-- Password Recovery Steps -->
                <div class="steps-container">
                    <div class="step">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <div class="step-title">Enter Your Email or Roll Number</div>
                            <div class="step-description">Provide the email address or roll number associated with your student account.</div>
                        </div>
                    </div>
                    <div class="step">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <div class="step-title">Check Your Email</div>
                            <div class="step-description">We'll send a password reset link to your registered email address.</div>
                        </div>
                    </div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <div class="step-title">Reset Your Password</div>
                            <div class="step-description">Click the link in the email and create a new secure password.</div>
                        </div>
                    </div>
                </div>
                
                <!-- Forgot Password Form -->
                <form id="forgotPasswordForm" action="{{ route('student.password.email') }}" method="POST">
                    @csrf
                    
                    <!-- Email/Roll Number Field -->
                    <div class="goi-form-group">
                        <label for="email" class="goi-form-label">
                            Email Address or Roll Number
                        </label>
                        <input 
                            type="text" 
                            id="email" 
                            name="email" 
                            class="goi-form-input @error('email') error @enderror"
                            placeholder="Enter your registered email or roll number"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            aria-label="Email Address or Roll Number"
                            aria-required="true"
                        >
                        <span id="emailError" class="error-text" style="color: var(--error); font-size: 0.875rem; display: none;"></span>
                        @error('email')
                            <span class="error-text" style="color: var(--error); font-size: 0.875rem;">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" id="submitBtn" class="goi-btn goi-btn-primary goi-btn-block">
                        <span id="submitText">Send Password Reset Link</span>
                        <span id="submitLoader" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i> Sending...
                        </span>
                    </button>
                    
                    <!-- Back to Login -->
                    <div style="text-align: center; margin-top: 1.5rem;">
                        <a href="{{ route('student.login') }}" style="color: var(--primary-red); text-decoration: none; font-size: 0.875rem;">
                            <i class="fas fa-arrow-left"></i> Back to Login
                        </a>
                    </div>
                </form>
                
                <!-- Footer Links -->
                <div style="margin-top: 1.5rem; text-align: center;">
                    <p style="font-size: 0.75rem; color: var(--gray-600);">
                        Need help? Contact <a href="mailto:student.support@msme.gov.in" style="color: var(--primary-red);">student.support@msme.gov.in</a>
                    </p>
                </div>
            </div>
            
            <!-- Right Side - Image and Info -->
            <div class="goi-login-right">
                <img src="https://images.unsplash.com/photo-1523240795132-0a3bf5ae7cca?w=600&h=400&fit=crop&crop=center" alt="Student Password Recovery">
                
                <!-- Chat Bubble -->
                <div class="chat-bubble">
                    <strong>Forgot password?</strong>
                    <p style="margin-top: 0.25rem;">Follow the steps to reset your student account access.</p>
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
            const form = document.getElementById('forgotPasswordForm');
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitLoader = document.getElementById('submitLoader');
            
            // Form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Reset errors
                document.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
                document.querySelectorAll('.error-text').forEach(el => el.style.display = 'none');
                
                // Show loading
                submitBtn.disabled = true;
                submitText.style.display = 'none';
                submitLoader.style.display = 'inline-block';
                
                // Get form data
                const formData = new FormData(form);
                
                // Submit form
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
                    
                    if (data.status) {
                        showSuccess(data.status);
                        form.reset();
                    } else {
                        if (data.errors) {
                            showFieldErrors(data.errors);
                        } else if (data.message) {
                            showError(data.message);
                        } else {
                            showSuccess('Password reset link sent successfully!');
                            form.reset();
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
                                showError(serverMessage);
                            } else if (status === '422') {
                                showError('Please enter a valid email address or roll number.');
                            } else if (status === '500') {
                                showError('Server error. Please try again later.');
                            } else if (status === '404') {
                                showError('Account not found. Please check your email or roll number.');
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
            
            function resetButton() {
                submitBtn.disabled = false;
                submitText.style.display = 'inline-block';
                submitLoader.style.display = 'none';
            }
            
            function showError(message) {
                console.error('Error:', message);
                showNotification(message, 'error');
            }
            
            function showSuccess(message) {
                showNotification(message, 'success');
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