<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>OTP Verification - {{ env('PROJECT_NAME', 'MSME Technology Center') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="card-body">
                <div class="login-header">
                    <img src="{{ asset('msme_logo/favicon-96x96.png') }}" alt="MSME Logo" class="logo">
                    <h1>{{ env('PROJECT_NAME', 'MSME Technology Center') }}</h1>
                    <p>Two-Factor Authentication</p>
                </div>

                <!-- Success Alert -->
                @if(session('success'))
                <div class="alert alert-success fade-in-up" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
                @endif

                <!-- Error Alert -->
                <div id="errorAlert" class="alert alert-danger d-none fade-in-up" role="alert">
                    <div id="errorMessage"></div>
                </div>

                <!-- Success Alert -->
                <div id="successAlert" class="alert alert-success d-none fade-in-up" role="alert">
                    <div id="successMessage"></div>
                </div>

                <div class="text-center mb-4">
                    <div class="mb-3">
                        <div class="stats-icon mx-auto" style="background: var(--primary-color);">
                            <i class="bi bi-shield-check text-white"></i>
                        </div>
                    </div>
                    <h4 class="text-primary">Enter Verification Code</h4>
                    <p class="text-muted">
                        We've sent a 6-digit verification code to your email address.<br>
                        Please enter it below to complete your login.
                    </p>
                </div>

                <form id="otpForm" method="POST" action="{{ route('admin.verify-otp.post') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="otp" class="form-label">Verification Code</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-key"></i>
                            </span>
                            <input type="text" class="form-control otp-input" 
                                   id="otp" name="otp" maxlength="6" required autofocus
                                   placeholder="000000">
                        </div>
                        <div class="invalid-feedback" id="otpError"></div>
                        <small class="form-text text-muted">
                            Enter the 6-digit code sent to your email
                        </small>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                            <span id="submitText">Verify & Login</span>
                            <span id="submitLoader" class="spinner-border spinner-border-sm d-none ms-2" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </span>
                        </button>
                    </div>

                    <div class="text-center">
                        <button type="button" class="btn-link" id="resendBtn">
                            <span id="resendText">Resend Code</span>
                            <span id="resendLoader" class="spinner-border spinner-border-sm d-none ms-2" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </span>
                        </button>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('admin.login') }}" class="btn-link">
                            <i class="bi bi-arrow-left me-1"></i>
                            Back to Login
                        </a>
                    </div>
                </form>

                <!-- Security Notice -->
                <div class="test-credentials">
                    <small>
                        <strong>Security Notice:</strong><br>
                        • This code will expire in 5 minutes<br>
                        • Never share this code with anyone<br>
                        • If you didn't request this code, please ignore this email
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('otpForm');
        const otpInput = document.getElementById('otp');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        const submitLoader = document.getElementById('submitLoader');
        const resendBtn = document.getElementById('resendBtn');
        const resendText = document.getElementById('resendText');
        const resendLoader = document.getElementById('resendLoader');
        const errorAlert = document.getElementById('errorAlert');
        const errorMessage = document.getElementById('errorMessage');
        const successAlert = document.getElementById('successAlert');
        const successMessage = document.getElementById('successMessage');

        // Auto-focus and format OTP input
        otpInput.addEventListener('input', function(e) {
            // Remove non-numeric characters
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Limit to 6 digits
            if (this.value.length > 6) {
                this.value = this.value.slice(0, 6);
            }
        });

        // Handle form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Reset previous errors
            resetErrors();
            
            // Validate OTP
            if (otpInput.value.length !== 6) {
                showError('Please enter a 6-digit verification code.');
                return;
            }
            
            // Show loader
            setSubmitLoading(true);
            
            // Get form data
            const formData = new FormData(form);
            
            // Make AJAX request
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
                setSubmitLoading(false);
                
                if (data.success) {
                    // Show success message
                    showSuccess(data.message);
                    
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
                setSubmitLoading(false);
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
                            showError(serverMessage);
                        } else if (status === '422') {
                            showError('Invalid OTP. Please check and try again.');
                        } else if (status === '500') {
                            showError('Server error. Please try again later.');
                        } else if (status === '404') {
                            showError('OTP verification endpoint not found. Please contact administrator.');
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

        // Handle resend OTP
        resendBtn.addEventListener('click', function() {
            setResendLoading(true);
            
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
                setResendLoading(false);
                
                if (data.success) {
                    showSuccess(data.message);
                    // Clear OTP input
                    otpInput.value = '';
                    otpInput.focus();
                } else {
                    showError(data.message);
                }
            })
            .catch(error => {
                setResendLoading(false);
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
                            showError(serverMessage);
                        } else if (status === '500') {
                            showError('Server error. Please try again later.');
                        } else if (status === '404') {
                            showError('Resend OTP endpoint not found. Please contact administrator.');
                        } else {
                            showError(`Server error (${status}). Please try again later.`);
                        }
                    } else {
                        showError('Server error. Please try again later.');
                    }
                } else if (error.message) {
                    showError(error.message);
                } else {
                    showError('Failed to resend OTP. Please try again.');
                }
            });
        });

        function setSubmitLoading(loading) {
            if (loading) {
                submitBtn.disabled = true;
                submitText.textContent = 'Verifying...';
                submitLoader.classList.remove('d-none');
                submitBtn.classList.add('btn-loading');
            } else {
                submitBtn.disabled = false;
                submitText.textContent = 'Verify & Login';
                submitLoader.classList.add('d-none');
                submitBtn.classList.remove('btn-loading');
            }
        }

        function setResendLoading(loading) {
            if (loading) {
                resendBtn.disabled = true;
                resendText.textContent = 'Sending...';
                resendLoader.classList.remove('d-none');
            } else {
                resendBtn.disabled = false;
                resendText.textContent = 'Resend Code';
                resendLoader.classList.add('d-none');
            }
        }

        function showError(message) {
            errorMessage.textContent = message;
            errorAlert.classList.remove('d-none');
            successAlert.classList.add('d-none');
            
            // Auto-hide error after 5 seconds
            setTimeout(() => {
                errorAlert.classList.add('d-none');
            }, 5000);
        }

        function showSuccess(message) {
            successMessage.textContent = message;
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
        }
    });
    </script>
</body>
</html> 