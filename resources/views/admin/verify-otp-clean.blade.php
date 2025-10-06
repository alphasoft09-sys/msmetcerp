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
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
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
        <i class="fas fa-shield-alt"></i> TWO-FACTOR AUTHENTICATION
        <span class="admin-badge">SECURE ACCESS</span>
    </div>
    
    <!-- Main Content -->
    <main id="main-content" class="page-transition">
        <x-clean-form-layout 
            title="OTP Verification" 
            subtitle="Enter the code sent to your device"
            :formAction="route('admin.verify-otp.post')"
            :showBackLink="true"
            :backLinkUrl="route('admin.login')"
            backLinkText="Back to Login"
        >
            <!-- OTP Info -->
            <div class="goi-otp-info">
                <p>We have sent a 6-digit OTP to your registered</p>
                @if(session('otp_sent_to'))
                    <p class="goi-masked-contact">{{ session('otp_sent_to') }}</p>
                @else
                    <p class="goi-masked-contact">Email: ****@****.com</p>
                @endif
                <p>Please enter the OTP below to proceed</p>
            </div>
            
            <!-- OTP Input Boxes -->
            <div class="goi-form-group-clean">
                <label class="goi-form-label-clean" style="text-align: center; display: block;">
                    Enter 6-Digit OTP
                </label>
                <div class="goi-otp-input-container">
                    <input type="text" class="goi-otp-single-input" maxlength="1" pattern="[0-9]" inputmode="numeric" aria-label="OTP digit 1">
                    <input type="text" class="goi-otp-single-input" maxlength="1" pattern="[0-9]" inputmode="numeric" aria-label="OTP digit 2">
                    <input type="text" class="goi-otp-single-input" maxlength="1" pattern="[0-9]" inputmode="numeric" aria-label="OTP digit 3">
                    <input type="text" class="goi-otp-single-input" maxlength="1" pattern="[0-9]" inputmode="numeric" aria-label="OTP digit 4">
                    <input type="text" class="goi-otp-single-input" maxlength="1" pattern="[0-9]" inputmode="numeric" aria-label="OTP digit 5">
                    <input type="text" class="goi-otp-single-input" maxlength="1" pattern="[0-9]" inputmode="numeric" aria-label="OTP digit 6">
                </div>
                <input type="hidden" name="otp" id="otp-input">
                <span id="otpError" class="error-text-clean" style="display: none;"></span>
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
                <span id="submitText">Verify & Login</span>
                <span id="submitLoader" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i> Verifying...
                </span>
            </button>
            
            <!-- Resend OTP -->
            <div class="goi-resend-otp">
                <p>Resend OTP in <span id="countdown">30</span> seconds.</p>
                <a href="#" id="resendLink" style="display: none;">Resend OTP</a>
            </div>
            
            <!-- Security Tips -->
            <div class="goi-security-tips">
                <h6>Security Tips:</h6>
                <ul>
                    <li>Never share your OTP with anyone</li>
                    <li>OTP is valid for 5 minutes only</li>
                    <li>Government officials will never ask for your OTP</li>
                </ul>
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
            const otpInputs = document.querySelectorAll('.goi-otp-single-input');
            const otpHiddenInput = document.getElementById('otp-input');
            const form = document.getElementById('otpForm');
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitLoader = document.getElementById('submitLoader');
            const otpError = document.getElementById('otpError');
            const countdownElement = document.getElementById('countdown');
            const resendLink = document.getElementById('resendLink');
            
            let countdown = 30;
            let countdownInterval;
            
            // OTP Input handling
            otpInputs.forEach((input, index) => {
                input.addEventListener('input', function(e) {
                    const value = e.target.value;
                    
                    // Only allow numbers
                    if (!/^\d$/.test(value)) {
                        e.target.value = '';
                        return;
                    }
                    
                    // Move to next input
                    if (value && index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                    
                    updateOTP();
                });
                
                input.addEventListener('keydown', function(e) {
                    // Handle backspace
                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        otpInputs[index - 1].focus();
                    }
                });
                
                input.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pastedData = e.clipboardData.getData('text');
                    const digits = pastedData.replace(/\D/g, '').slice(0, 6);
                    
                    digits.split('').forEach((digit, i) => {
                        if (otpInputs[i]) {
                            otpInputs[i].value = digit;
                        }
                    });
                    
                    updateOTP();
                    
                    // Focus last filled input
                    const lastFilledIndex = Math.min(digits.length - 1, otpInputs.length - 1);
                    otpInputs[lastFilledIndex].focus();
                });
            });
            
            function updateOTP() {
                const otp = Array.from(otpInputs).map(input => input.value).join('');
                otpHiddenInput.value = otp;
                
                // Clear error when user types
                if (otpError) {
                    otpError.style.display = 'none';
                }
            }
            
            // Form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const otp = otpHiddenInput.value;
                
                if (otp.length !== 6) {
                    showError('Please enter a complete 6-digit OTP');
                    return;
                }
                
                // Show loading state
                submitBtn.disabled = true;
                submitText.style.display = 'none';
                submitLoader.style.display = 'inline-flex';
                
                // Submit form
                form.submit();
            });
            
            function showError(message) {
                if (otpError) {
                    otpError.textContent = message;
                    otpError.style.display = 'block';
                }
            }
            
            // Countdown timer
            function startCountdown() {
                countdown = 30;
                countdownElement.textContent = countdown;
                resendLink.style.display = 'none';
                
                countdownInterval = setInterval(() => {
                    countdown--;
                    countdownElement.textContent = countdown;
                    
                    if (countdown <= 0) {
                        clearInterval(countdownInterval);
                        resendLink.style.display = 'inline';
                    }
                }, 1000);
            }
            
            // Start countdown
            startCountdown();
            
            // Resend OTP
            resendLink.addEventListener('click', function(e) {
                e.preventDefault();
                // Add resend logic here
                startCountdown();
            });
        });
    </script>
    
    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
