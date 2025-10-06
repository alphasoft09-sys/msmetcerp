<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="OTP Verification - MSME Technology Centre, Government of India">
    <meta name="keywords" content="MSME, Government of India, OTP Verification, Security">
    
    <!-- Security Headers -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <title>OTP Verification | MSME Technology Centre | Government of India</title>
    
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
        
        /* OTP Specific Styles */
        .otp-info {
            background: var(--gray-50);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
            border: 1px solid var(--gray-200);
        }
        
        .otp-info p {
            font-size: 0.875rem;
            color: var(--gray-700);
            margin: 0.25rem 0;
        }
        
        .otp-info .masked-contact {
            font-weight: 600;
            color: var(--primary-red);
        }
        
        .otp-input-container {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            margin: 1.5rem 0;
        }
        
        .otp-single-input {
            width: 40px;
            height: 40px;
            text-align: center;
            font-size: 1.1rem;
            font-weight: 600;
            border: 1px solid var(--gray-300);
            border-radius: 4px;
            transition: all 0.3s ease;
            background: var(--gray-50);
        }
        
        .otp-single-input:focus {
            outline: none;
            border-color: var(--light-red);
            box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.1);
            background: var(--white);
        }
        
        .otp-single-input.filled {
            border-color: var(--success);
            background: #F0FDF4;
        }
        
        .resend-section {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .resend-timer {
            font-size: 0.875rem;
            color: var(--gray-600);
            margin-bottom: 1rem;
        }
        
        .resend-timer .timer-count {
            font-weight: 600;
            color: var(--primary-red);
            font-size: 1rem;
        }
        
        .security-tips {
            background: #FEF3C7;
            border-left: 3px solid var(--warning);
            padding: 0.5rem 0.75rem;
            border-radius: 4px;
            margin-top: 1rem;
        }
        
        .security-tips h4 {
            color: var(--gray-800);
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .security-tips ul {
            margin: 0;
            padding-left: 1.25rem;
            font-size: 0.7rem;
            color: var(--gray-700);
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
    
    @include('layouts.goi-navigation')
    
    <!-- Admin Banner -->
    <div class="admin-banner">
        <i class="fas fa-shield-alt"></i> TWO-FACTOR AUTHENTICATION
        <span class="admin-badge">SECURE ACCESS</span>
    </div>
    
    <!-- Main Content -->
    <main id="main-content" class="page-transition">
        <div class="goi-login-container" style="flex: 1 0 auto;">
            <!-- Left Side - OTP Form -->
            <div class="goi-login-left">
                <div>
                    <!-- OTP Header -->
                    <div class="goi-login-header">
                        <h2><i class="fas fa-mobile-alt"></i> OTP Verification</h2>
                        <p>Enter the code sent to your device</p>
                    </div>
                    
                    <!-- Alert Messages -->
                    @include('partials.notification')
                </div>
                
                <!-- OTP Info -->
                <div class="otp-info">
                    <p>We have sent a 6-digit OTP to your registered</p>
                    @if(session('otp_sent_to'))
                        <p class="masked-contact">{{ session('otp_sent_to') }}</p>
                    @else
                        <p class="masked-contact">Email: ****@****.com</p>
                    @endif
                    <p style="margin-top: 0.5rem; font-size: 0.75rem;">Please enter the OTP below to proceed</p>
                </div>
                
                <!-- OTP Form -->
                <form id="otpForm" action="{{ route('admin.verify-otp.post') }}" method="POST">
                    @csrf
                    
                    <!-- OTP Input Boxes -->
                    <div class="goi-form-group">
                        <label class="goi-form-label" style="text-align: center; display: block;">
                            Enter 6-Digit OTP
                        </label>
                        <div class="otp-input-container">
                            <input type="text" class="otp-single-input" maxlength="1" pattern="[0-9]" inputmode="numeric" aria-label="OTP digit 1">
                            <input type="text" class="otp-single-input" maxlength="1" pattern="[0-9]" inputmode="numeric" aria-label="OTP digit 2">
                            <input type="text" class="otp-single-input" maxlength="1" pattern="[0-9]" inputmode="numeric" aria-label="OTP digit 3">
                            <input type="text" class="otp-single-input" maxlength="1" pattern="[0-9]" inputmode="numeric" aria-label="OTP digit 4">
                            <input type="text" class="otp-single-input" maxlength="1" pattern="[0-9]" inputmode="numeric" aria-label="OTP digit 5">
                            <input type="text" class="otp-single-input" maxlength="1" pattern="[0-9]" inputmode="numeric" aria-label="OTP digit 6">
                        </div>
                        <input type="hidden" name="otp" id="otpValue">
                        <span id="otpError" class="error-text" style="color: var(--error); font-size: 0.875rem; text-align: center; display: none;"></span>
                        @error('otp')
                            <span class="error-text" style="color: var(--error); font-size: 0.875rem; text-align: center; display: block;">{{ $message }}</span>
                        @enderror
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
                        <span id="submitText">Verify & Login</span>
                        <span id="submitLoader" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i> Verifying...
                        </span>
                    </button>
                </form>
                
                <!-- Resend Section -->
                <div class="resend-section">
                    <div class="resend-timer" id="resendTimer">
                        Resend OTP in <span class="timer-count" id="timerCount">30</span> seconds
                    </div>
                    <button type="button" id="resendBtn" class="goi-btn goi-btn-secondary" style="display: none;">
                        <span id="resendText">Resend OTP</span>
                        <span id="resendLoader" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i> Sending...
                        </span>
                    </button>
                </div>
                
                <!-- Security Tips -->
                <div class="security-tips">
                    <h4><i class="fas fa-info-circle"></i> Security Tips</h4>
                    <ul>
                        <li>Never share your OTP with anyone</li>
                        <li>OTP is valid for 5 minutes only</li>
                        <li>Government officials will never ask for your OTP</li>
                    </ul>
                </div>
                
                <!-- Back to Login -->
                <div style="text-align: center; margin-top: 1.5rem;">
                    <a href="{{ route('admin.login') }}" style="color: var(--primary-red); text-decoration: none; font-size: 0.875rem;">
                        <i class="fas fa-arrow-left"></i> Back to Login
                    </a>
                </div>
            </div>
            
            <!-- Right Side - Image and Info -->
            <div class="goi-login-right">
                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=600&h=400&fit=crop&crop=center" alt="OTP Verification">
                
                <!-- Chat Bubble -->
                <div class="chat-bubble">
                    <strong>Security Check</strong>
                    <p style="margin-top: 0.25rem;">Enter the OTP sent to your device to verify your identity.</p>
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
            const otpInputs = document.querySelectorAll('.otp-single-input');
            const otpValue = document.getElementById('otpValue');
            const form = document.getElementById('otpForm');
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitLoader = document.getElementById('submitLoader');
            const resendBtn = document.getElementById('resendBtn');
            const resendText = document.getElementById('resendText');
            const resendLoader = document.getElementById('resendLoader');
            const resendTimer = document.getElementById('resendTimer');
            const timerCount = document.getElementById('timerCount');
            // Notification system handles alerts
            
            let timerInterval;
            let timeLeft = 30;
            
            // Start timer
            function startTimer() {
                timeLeft = 30;
                resendBtn.style.display = 'none';
                resendTimer.style.display = 'block';
                
                timerInterval = setInterval(() => {
                    timeLeft--;
                    timerCount.textContent = timeLeft;
                    
                    if (timeLeft <= 0) {
                        clearInterval(timerInterval);
                        resendTimer.style.display = 'none';
                        resendBtn.style.display = 'inline-block';
                    }
                }, 1000);
            }
            
            startTimer();
            
            // OTP Input handling
            otpInputs.forEach((input, index) => {
                input.addEventListener('input', function(e) {
                    const value = e.target.value;
                    
                    // Only allow numbers
                    if (!/^\d*$/.test(value)) {
                        e.target.value = '';
                        return;
                    }
                    
                    // Mark as filled
                    if (value) {
                        input.classList.add('filled');
                    } else {
                        input.classList.remove('filled');
                    }
                    
                    // Auto-focus next input
                    if (value && index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                    
                    // Update hidden input
                    updateOtpValue();
                });
                
                input.addEventListener('keydown', function(e) {
                    // Handle backspace
                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        otpInputs[index - 1].focus();
                    }
                    
                    // Handle paste
                    if (e.key === 'v' && (e.ctrlKey || e.metaKey)) {
                        e.preventDefault();
                        navigator.clipboard.readText().then(text => {
                            const digits = text.replace(/\D/g, '').slice(0, 6);
                            digits.split('').forEach((digit, i) => {
                                if (otpInputs[i]) {
                                    otpInputs[i].value = digit;
                                    otpInputs[i].classList.add('filled');
                                }
                            });
                            updateOtpValue();
                            if (digits.length === 6) {
                                otpInputs[5].focus();
                            }
                        });
                    }
                });
            });
            
            function updateOtpValue() {
                const otp = Array.from(otpInputs).map(input => input.value).join('');
                otpValue.value = otp;
            }
            
            // Form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validate OTP
                if (otpValue.value.length !== 6) {
                    showError('Please enter complete 6-digit OTP');
                    return;
                }
                
                // Reset errors handled by notification system
                
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
                    
                    if (data.success) {
                        showNotification(data.message || 'OTP verified successfully! Redirecting...', 'success');
                        setTimeout(() => {
                            if (data.redirect_url) {
                                window.location.href = data.redirect_url;
                            } else {
                                window.location.reload();
                            }
                        }, 1000);
                    } else {
                        if (data.message) {
                            showNotification(data.message, 'error');
                        }
                        // Clear OTP inputs on error
                        otpInputs.forEach(input => {
                            input.value = '';
                            input.classList.remove('filled');
                        });
                        otpInputs[0].focus();
                    }
                })
                .catch(error => {
                    resetButton();
                    console.error('Error:', error);
                    showError('An error occurred. Please try again.');
                });
            });
            
            // Resend OTP
            resendBtn.addEventListener('click', function() {
                resendBtn.disabled = true;
                resendText.style.display = 'none';
                resendLoader.style.display = 'inline-block';
                
                fetch('{{ route("admin.resend-otp") }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to resend OTP');
                    }
                    return response.json();
                })
                .then(data => {
                    resendBtn.disabled = false;
                    resendText.style.display = 'inline-block';
                    resendLoader.style.display = 'none';
                    
                    if (data.success) {
                        showSuccess(data.message || 'OTP resent successfully');
                        // Clear OTP inputs
                        otpInputs.forEach(input => {
                            input.value = '';
                            input.classList.remove('filled');
                        });
                        otpInputs[0].focus();
                        // Restart timer
                        startTimer();
                    } else {
                        showError(data.message || 'Failed to resend OTP');
                    }
                })
                .catch(error => {
                    resendBtn.disabled = false;
                    resendText.style.display = 'inline-block';
                    resendLoader.style.display = 'none';
                    showError('Failed to resend OTP. Please try again.');
                });
            });
            
            function resetButton() {
                submitBtn.disabled = false;
                submitText.style.display = 'inline-block';
                submitLoader.style.display = 'none';
            }
            
            function showError(message) {
                console.error('OTP Error:', message);
                showNotification(message, 'error');
            }
            
            function showSuccess(message) {
                showNotification(message, 'success');
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