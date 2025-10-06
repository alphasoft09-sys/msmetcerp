<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Admin Reset Password - MSME Technology Centre, Government of India">
    <meta name="keywords" content="MSME, Government of India, Admin Password Reset, Technology Centre">
    
    <!-- Security Headers -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <title>Admin Reset Password | MSME Technology Centre | Government of India</title>
    
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
        /* Admin Specific Styles */
        .admin-banner {
            background: linear-gradient(135deg, #B91C1C 0%, #DC2626 100%);
            padding: 0.5rem;
            text-align: center;
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        .admin-badge {
            display: inline-block;
            background: #FF9933;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.75rem;
            margin-left: 0.5rem;
        }
        
        /* Password Requirements */
        .goi-password-requirements {
            margin: 1rem 0;
            padding: 1rem;
            background: var(--gray-50);
            border-radius: 8px;
            border: 1px solid var(--gray-200);
        }
        
        .goi-password-requirements h6 {
            margin-bottom: 0.5rem;
            color: var(--gray-800);
            font-size: 0.9rem;
        }
        
        .goi-password-requirements ul {
            margin: 0;
            padding-left: 1.2rem;
            font-size: 0.8rem;
            color: var(--gray-600);
        }
        
        .goi-password-requirements li {
            margin-bottom: 0.25rem;
        }
        
        /* Password Strength Indicator */
        .goi-password-strength {
            margin-top: 0.5rem;
        }
        
        .goi-strength-bar {
            height: 4px;
            background: var(--gray-200);
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 0.5rem;
        }
        
        .goi-strength-fill {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }
        
        .goi-strength-weak .goi-strength-fill {
            background: #DC2626;
            width: 25%;
        }
        
        .goi-strength-fair .goi-strength-fill {
            background: #F59E0B;
            width: 50%;
        }
        
        .goi-strength-good .goi-strength-fill {
            background: #10B981;
            width: 75%;
        }
        
        .goi-strength-strong .goi-strength-fill {
            background: #059669;
            width: 100%;
        }
        
        .goi-strength-text {
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .goi-strength-weak .goi-strength-text {
            color: #DC2626;
        }
        
        .goi-strength-fair .goi-strength-text {
            color: #F59E0B;
        }
        
        .goi-strength-good .goi-strength-text {
            color: #10B981;
        }
        
        .goi-strength-strong .goi-strength-text {
            color: #059669;
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
    
    <!-- Admin Banner -->
    <div class="admin-banner">
        <i class="fas fa-shield-alt"></i> ADMIN PASSWORD RESET
        <span class="admin-badge">SECURE ACCESS</span>
    </div>
    
    <!-- Main Content -->
    <main id="main-content" class="page-transition">
        <x-clean-form-layout 
            title="Reset Password" 
            subtitle="Create a new secure password for your admin account"
            :formAction="route('admin.password.update')"
            :showBackLink="true"
            :backLinkUrl="route('admin.login')"
            backLinkText="Back to Login"
            submitButtonText="Reset Password"
            loadingText="Resetting..."
            icon="fas fa-key"
        >
            <!-- Hidden Token Field -->
            <input type="hidden" name="token" value="{{ $token }}">
            
            <!-- Email Field -->
            <div class="goi-form-group-clean">
                <label for="email" class="goi-form-label-clean">
                    Email Address
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="goi-form-input-clean @error('email') error @enderror"
                    placeholder="Enter your registered email address"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                >
                @error('email')
                    <span class="error-text-clean">{{ $message }}</span>
                @enderror
            </div>
            
            <!-- New Password Field -->
            <div class="goi-form-group-clean">
                <label for="password" class="goi-form-label-clean">
                    New Password
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="goi-form-input-clean @error('password') error @enderror"
                    placeholder="Enter new password (min 8 characters)"
                    required
                    autocomplete="new-password"
                >
                @error('password')
                    <span class="error-text-clean">{{ $message }}</span>
                @enderror
                
                <!-- Password Strength Indicator -->
                <div class="goi-password-strength" id="passwordStrength" style="display: none;">
                    <div class="goi-strength-bar">
                        <div class="goi-strength-fill"></div>
                    </div>
                    <div class="goi-strength-text"></div>
                </div>
            </div>
            
            <!-- Confirm Password Field -->
            <div class="goi-form-group-clean">
                <label for="password_confirmation" class="goi-form-label-clean">
                    Confirm New Password
                </label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    class="goi-form-input-clean @error('password_confirmation') error @enderror"
                    placeholder="Confirm your new password"
                    required
                    autocomplete="new-password"
                >
                @error('password_confirmation')
                    <span class="error-text-clean">{{ $message }}</span>
                @enderror
            </div>
            
            <!-- Password Requirements -->
            <div class="goi-password-requirements">
                <h6>Password Requirements:</h6>
                <ul>
                    <li>At least 8 characters long</li>
                    <li>Contains uppercase and lowercase letters</li>
                    <li>Contains at least one number</li>
                    <li>Contains at least one special character</li>
                </ul>
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
                <span id="submitText">Reset Password</span>
                <span id="submitLoader" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i> Resetting...
                </span>
            </button>
            
            <!-- Help Contact -->
            <div class="goi-help-contact">
                <p>Need help? Contact <a href="mailto:admin.support@msme.gov.in">admin.support@msme.gov.in</a></p>
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
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');
            const passwordStrength = document.getElementById('passwordStrength');
            
            // Password strength checker
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                if (password.length > 0) {
                    passwordStrength.style.display = 'block';
                    updatePasswordStrength(password);
                } else {
                    passwordStrength.style.display = 'none';
                }
            });
            
            // Password confirmation checker
            confirmPasswordInput.addEventListener('input', function() {
                const password = passwordInput.value;
                const confirmPassword = this.value;
                
                if (confirmPassword.length > 0) {
                    if (password === confirmPassword) {
                        this.classList.remove('error');
                        this.classList.add('success');
                    } else {
                        this.classList.remove('success');
                        this.classList.add('error');
                    }
                } else {
                    this.classList.remove('success', 'error');
                }
            });
            
            // Form submission
            form.addEventListener('submit', function(e) {
                const email = document.getElementById('email').value.trim();
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;
                
                if (!email) {
                    e.preventDefault();
                    showError('Please enter your email address');
                    return;
                }
                
                if (!password) {
                    e.preventDefault();
                    showError('Please enter a new password');
                    return;
                }
                
                if (password.length < 8) {
                    e.preventDefault();
                    showError('Password must be at least 8 characters long');
                    return;
                }
                
                if (password !== confirmPassword) {
                    e.preventDefault();
                    showError('Passwords do not match');
                    return;
                }
                
                // Show loading state
                submitBtn.disabled = true;
                submitText.style.display = 'none';
                submitLoader.style.display = 'inline-flex';
            });
            
            function updatePasswordStrength(password) {
                const strengthBar = passwordStrength.querySelector('.goi-strength-fill');
                const strengthText = passwordStrength.querySelector('.goi-strength-text');
                
                let strength = 0;
                let strengthClass = '';
                let strengthLabel = '';
                
                // Length check
                if (password.length >= 8) strength++;
                if (password.length >= 12) strength++;
                
                // Character variety checks
                if (/[a-z]/.test(password)) strength++;
                if (/[A-Z]/.test(password)) strength++;
                if (/[0-9]/.test(password)) strength++;
                if (/[^A-Za-z0-9]/.test(password)) strength++;
                
                // Determine strength level
                if (strength < 3) {
                    strengthClass = 'goi-strength-weak';
                    strengthLabel = 'Weak';
                } else if (strength < 5) {
                    strengthClass = 'goi-strength-fair';
                    strengthLabel = 'Fair';
                } else if (strength < 7) {
                    strengthClass = 'goi-strength-good';
                    strengthLabel = 'Good';
                } else {
                    strengthClass = 'goi-strength-strong';
                    strengthLabel = 'Strong';
                }
                
                // Update UI
                passwordStrength.className = `goi-password-strength ${strengthClass}`;
                strengthText.textContent = strengthLabel;
            }
            
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
