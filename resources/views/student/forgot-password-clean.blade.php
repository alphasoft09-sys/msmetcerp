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
        
        /* Password Recovery Steps */
        .goi-steps-container {
            margin: 1.5rem 0;
        }
        
        .goi-step {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1rem;
            padding: 1rem;
            background: var(--gray-50);
            border-radius: 8px;
            border: 1px solid var(--gray-200);
        }
        
        .goi-step-number {
            width: 32px;
            height: 32px;
            background: var(--primary-red);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
            margin-right: 1rem;
            flex-shrink: 0;
        }
        
        .goi-step-content {
            flex: 1;
        }
        
        .goi-step-title {
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 0.25rem;
            font-size: 0.9rem;
        }
        
        .goi-step-description {
            color: var(--gray-600);
            font-size: 0.8rem;
            line-height: 1.4;
        }
        
        .goi-help-contact {
            text-align: center;
            margin-top: 1.5rem;
            padding: 1rem;
            background: var(--gray-50);
            border-radius: 8px;
            border: 1px solid var(--gray-200);
        }
        
        .goi-help-contact p {
            margin: 0;
            font-size: 0.8rem;
            color: var(--gray-600);
        }
        
        .goi-help-contact a {
            color: var(--primary-red);
            text-decoration: none;
            font-weight: 500;
        }
        
        .goi-help-contact a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Skip to Content (Accessibility) -->
    <a href="#main-content" class="skip-to-content">Skip to main content</a>
    
    <!-- Government Header -->
    <header class="goi-header-unique">
        <div class="goi-header-content-unique">
            <div class="goi-emblem-section-unique">
                <img src="{{ asset('msme_logo/msme_logo.png') }}" alt="MSME Technology Centre" class="goi-emblem-unique">
                <div class="goi-title-section-unique">
                    <h1 class="goi-hindi-title-unique">एमएसएमई प्रौद्योगिकी केंद्र द्वैध (एबी-एए)</h1>
                    <h1 class="goi-title-unique">MSME Technology Centre Dual (AB-AA)</h1>
                    <p class="goi-subtitle-unique">Government of India | भारत सरकार</p>
                </div>
            </div>
            <div class="goi-language-switcher-unique">
                <button class="goi-language-btn-unique active">English</button>
                <button class="goi-language-btn-unique">हिन्दी</button>
                <button class="goi-language-btn-unique">A+</button>
                <button class="goi-language-btn-unique">A-</button>
            </div>
        </div>
    </header>
    
    @include('layouts.goi-navigation')
    
    <!-- Student Banner -->
    <div class="student-banner">
        <i class="fas fa-graduation-cap"></i> STUDENT PASSWORD RECOVERY
        <span class="student-badge">LEARNER ACCESS</span>
    </div>
    
    <!-- Main Content -->
    <main id="main-content" class="page-transition">
        <x-clean-form-layout 
            title="Forgot Password" 
            subtitle="Reset your student account password"
            :formAction="route('student.password.email')"
            :showBackLink="true"
            :backLinkUrl="route('student.login')"
            backLinkText="Back to Login"
        >
            <!-- Password Recovery Steps -->
            <div class="goi-steps-container">
                <div class="goi-step">
                    <div class="goi-step-number">1</div>
                    <div class="goi-step-content">
                        <div class="goi-step-title">Enter Your Email or Roll Number</div>
                        <div class="goi-step-description">Provide the email address or roll number associated with your student account.</div>
                    </div>
                </div>
                <div class="goi-step">
                    <div class="goi-step-number">2</div>
                    <div class="goi-step-content">
                        <div class="goi-step-title">Check Your Email</div>
                        <div class="goi-step-description">We'll send a password reset link to your registered email address.</div>
                    </div>
                </div>
                <div class="goi-step">
                    <div class="goi-step-number">3</div>
                    <div class="goi-step-content">
                        <div class="goi-step-title">Reset Your Password</div>
                        <div class="goi-step-description">Click the link in the email and create a new secure password.</div>
                    </div>
                </div>
            </div>
            
            <!-- Email/Roll Number Field -->
            <div class="goi-form-group-clean">
                <label for="email" class="goi-form-label-clean">
                    Email Address or Roll Number
                </label>
                <input 
                    type="text" 
                    id="email" 
                    name="email" 
                    class="goi-form-input-clean @error('email') error @enderror"
                    placeholder="Enter your registered email or roll number"
                    value="{{ old('email') }}"
                    required
                    autocomplete="username"
                >
                @error('email')
                    <span class="error-text-clean">{{ $message }}</span>
                @enderror
            </div>
            
            <!-- Security Verification -->
            <div class="goi-captcha-section">
                <div class="goi-captcha-container">
                    <i class="fas fa-shield-alt"></i>
                    <p>Security verification required</p>
                    <div id="captcha-badge">
                        <div class="captcha-status">
                            <i class="fas fa-check-circle text-success"></i>
                            <span>Security verification enabled</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" id="submitBtn" class="goi-btn-clean">
                <span id="submitText">Send Password Reset Link</span>
                <span id="submitLoader" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i> Sending...
                </span>
            </button>
            
            <!-- Help Contact -->
            <div class="goi-help-contact">
                <p>Need help? Contact <a href="mailto:student.support@msme.gov.in">student.support@msme.gov.in</a></p>
            </div>
        </x-clean-form-layout>
    </main>
    
    <!-- Footer -->
    <footer class="goi-footer">
        <div class="goi-footer-content">
            <div class="goi-footer-copyright">
                <p>&copy; 2024 Ministry of MSME, Government of India</p>
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
            const form = document.querySelector('form');
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitLoader = document.getElementById('submitLoader');
            
            // Form submission
            form.addEventListener('submit', function(e) {
                const email = document.getElementById('email').value.trim();
                
                if (!email) {
                    e.preventDefault();
                    showError('Please enter your email address or roll number');
                    return;
                }
                
                // Show loading state
                submitBtn.disabled = true;
                submitText.style.display = 'none';
                submitLoader.style.display = 'inline-flex';
            });
            
            function showError(message) {
                // Create or update error message
                let errorElement = document.querySelector('.error-text-clean');
                if (!errorElement) {
                    errorElement = document.createElement('span');
                    errorElement.className = 'error-text-clean';
                    document.querySelector('.goi-form-group-clean').appendChild(errorElement);
                }
                errorElement.textContent = message;
                errorElement.style.display = 'block';
            }
        });
    </script>
    
    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
