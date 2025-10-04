<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - {{ env('PROJECT_NAME', 'MSME Technology Center') }}</title>
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
                    <p>Admin Password Reset</p>
                </div>

                <!-- Error Alert -->
                <div id="errorAlert" class="alert alert-danger d-none fade-in-up" role="alert">
                    <div id="errorMessage"></div>
                </div>

                <!-- Success Alert -->
                <div id="successAlert" class="alert alert-success d-none fade-in-up" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    <div id="successMessage"></div>
                </div>

                <form id="resetPasswordForm" method="POST" action="{{ route('admin.password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    
                    <div class="mb-4">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email" class="form-control" 
                                   id="email" name="email" value="{{ old('email') }}" required autofocus
                                   placeholder="Enter your email address">
                        </div>
                        <div class="invalid-feedback" id="emailError"></div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">New Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" class="form-control" 
                                   id="password" name="password" required
                                   placeholder="Enter new password (min 8 characters)">
                        </div>
                        <div class="invalid-feedback" id="passwordError"></div>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" class="form-control" 
                                   id="password_confirmation" name="password_confirmation" required
                                   placeholder="Confirm new password">
                        </div>
                        <div class="invalid-feedback" id="password_confirmationError"></div>
                    </div>

                    <div class="d-grid mb-4">
                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                            <span id="submitText">Reset Password</span>
                            <span id="submitLoader" class="spinner-border spinner-border-sm d-none ms-2" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </span>
                        </button>
                    </div>
                </form>

                <div class="text-center">
                    <a href="{{ route('admin.login') }}" class="btn-link">
                        <i class="bi bi-arrow-left me-1"></i>
                        Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('resetPasswordForm');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        const submitLoader = document.getElementById('submitLoader');
        const errorAlert = document.getElementById('errorAlert');
        const errorMessage = document.getElementById('errorMessage');
        const successAlert = document.getElementById('successAlert');
        const successMessage = document.getElementById('successMessage');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Reset previous errors
            resetErrors();
            
            // Show loader
            setLoading(true);
            
            // Get form data
            const formData = new FormData(form);
            
            // Make AJAX request
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                setLoading(false);
                
                if (data.success) {
                    // Show success message
                    showSuccess(data.message);
                    
                    // Redirect after a short delay
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 2000);
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
                
                if (error.name === 'TypeError' && error.message.includes('JSON')) {
                    showError('Server error. Please try again later.');
                } else {
                    showError('Network error. Please check your connection and try again.');
                }
            });
        });

        function setLoading(loading) {
            if (loading) {
                submitBtn.disabled = true;
                submitText.textContent = 'Resetting...';
                submitLoader.classList.remove('d-none');
                submitBtn.classList.add('btn-loading');
            } else {
                submitBtn.disabled = false;
                submitText.textContent = 'Reset Password';
                submitLoader.classList.add('d-none');
                submitBtn.classList.remove('btn-loading');
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