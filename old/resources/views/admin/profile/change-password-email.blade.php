@extends('admin.layout')

@section('title', 'Change Password & Email')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Change Password & Email</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-shield-lock me-2"></i>
                    Secure Profile Update
                </h5>
            </div>
            <div class="card-body">
                <!-- Security Notice -->
                <div class="alert alert-info" role="alert">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Security Notice:</strong> Changing your email or password requires OTP verification sent to your current email address.
                </div>

                <!-- Error Alert -->
                <div id="errorAlert" class="alert alert-danger d-none" role="alert">
                    <div id="errorMessage"></div>
                </div>

                <!-- Success Alert -->
                <div id="successAlert" class="alert alert-success d-none" role="alert">
                    <div id="successMessage"></div>
                </div>

                <form id="profileChangeForm">
                    @csrf
                    
                    <!-- Current User Info -->
                    <div class="mb-4">
                        <h6 class="text-muted">Current Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Current Email</label>
                                <input type="email" class="form-control" value="{{ $user->email }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">User Role</label>
                                <input type="text" class="form-control" value="{{ ucfirst(str_replace('-', ' ', $user->user_role == 1 ? 'TC Admin' : ($user->user_role == 2 ? 'TC Head' : ($user->user_role == 3 ? 'Exam Cell' : ($user->user_role == 4 ? 'Assessment Agency' : 'TC Faculty'))))) }}" readonly>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- New Information -->
                    <div class="mb-4">
                        <h6 class="text-muted">New Information</h6>
                        
                        <!-- New Email -->
                        <div class="mb-3">
                            <label for="new_email" class="form-label">New Email Address (Optional)</label>
                            <input type="email" class="form-control" id="new_email" name="new_email" 
                                   placeholder="Enter new email address">
                            <div class="invalid-feedback" id="new_emailError"></div>
                            <small class="form-text text-muted">
                                Leave blank if you only want to change password
                            </small>
                        </div>

                        <!-- New Password -->
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password (Optional)</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" 
                                   placeholder="Enter new password (min 8 characters)">
                            <div class="invalid-feedback" id="new_passwordError"></div>
                            <small class="form-text text-muted">
                                Minimum 8 characters. Leave blank if you only want to change email
                            </small>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="new_password_confirmation" 
                                   name="new_password_confirmation" placeholder="Confirm new password">
                            <div class="invalid-feedback" id="new_password_confirmationError"></div>
                        </div>
                    </div>

                    <!-- OTP Section -->
                    <div class="mb-4" id="otpSection" style="display: none;">
                        <h6 class="text-muted">OTP Verification</h6>
                        
                        <div class="mb-3">
                            <label for="otp" class="form-label">Verification Code</label>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-lg text-center" 
                                       id="otp" name="otp" maxlength="6" placeholder="000000" 
                                       style="letter-spacing: 0.5em; font-size: 1.5rem; font-weight: bold;">
                                <button type="button" class="btn btn-outline-secondary" id="resendOtpBtn">
                                    <span id="resendOtpText">Resend</span>
                                    <span id="resendOtpLoader" class="spinner-border spinner-border-sm d-none ms-2" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </span>
                                </button>
                            </div>
                            <div class="invalid-feedback" id="otpError"></div>
                            <small class="form-text text-muted">
                                Enter the 6-digit code sent to your email
                            </small>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary btn-lg" id="sendOtpBtn">
                            <span id="sendOtpText">Send Verification Code</span>
                            <span id="sendOtpLoader" class="spinner-border spinner-border-sm d-none ms-2" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </span>
                        </button>

                        <button type="submit" class="btn btn-success btn-lg" id="updateBtn" style="display: none;">
                            <span id="updateText">Update Profile</span>
                            <span id="updateLoader" class="spinner-border spinner-border-sm d-none ms-2" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </span>
                        </button>
                    </div>
                </form>

                <!-- Security Tips -->
                <div class="mt-4 p-3 bg-light rounded">
                    <h6 class="text-muted">Security Tips</h6>
                    <ul class="mb-0 small">
                        <li>Use a strong password with at least 8 characters</li>
                        <li>Include uppercase, lowercase, numbers, and symbols</li>
                        <li>Never share your verification code with anyone</li>
                        <li>The verification code expires in 5 minutes</li>
                        <li>You will be logged out after successful update</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('profileChangeForm');
    const newEmailInput = document.getElementById('new_email');
    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('new_password_confirmation');
    const otpInput = document.getElementById('otp');
    const otpSection = document.getElementById('otpSection');
    const sendOtpBtn = document.getElementById('sendOtpBtn');
    const sendOtpText = document.getElementById('sendOtpText');
    const sendOtpLoader = document.getElementById('sendOtpLoader');
    const resendOtpBtn = document.getElementById('resendOtpBtn');
    const resendOtpText = document.getElementById('resendOtpText');
    const resendOtpLoader = document.getElementById('resendOtpLoader');
    const updateBtn = document.getElementById('updateBtn');
    const updateText = document.getElementById('updateText');
    const updateLoader = document.getElementById('updateLoader');
    const errorAlert = document.getElementById('errorAlert');
    const errorMessage = document.getElementById('errorMessage');
    const successAlert = document.getElementById('successAlert');
    const successMessage = document.getElementById('successMessage');

    // Admin routes
    const sendOtpRoute = '{{ route("admin.profile.send-otp") }}';
    const verifyUpdateRoute = '{{ route("admin.profile.verify-update") }}';

    // Auto-focus and format OTP input
    otpInput.addEventListener('input', function(e) {
        // Remove non-numeric characters
        this.value = this.value.replace(/[^0-9]/g, '');
        
        // Limit to 6 digits
        if (this.value.length > 6) {
            this.value = this.value.slice(0, 6);
        }
    });

    // Handle Send OTP button
    sendOtpBtn.addEventListener('click', function() {
        // Reset previous errors
        clearErrors();
        hideAlerts();

        // Validate that at least one field is filled
        if (!newEmailInput.value && !newPasswordInput.value) {
            showError('Please provide either a new email or password to change.');
            return;
        }

        // Validate password confirmation if password is provided
        if (newPasswordInput.value && newPasswordInput.value !== confirmPasswordInput.value) {
            showFieldError('new_password_confirmation', 'Password confirmation does not match.');
            return;
        }

        setSendOtpLoading(true);

        // Prepare form data
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        if (newEmailInput.value) formData.append('new_email', newEmailInput.value);
        if (newPasswordInput.value) {
            formData.append('new_password', newPasswordInput.value);
            formData.append('new_password_confirmation', confirmPasswordInput.value);
        }

        fetch(sendOtpRoute, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            setSendOtpLoading(false);
            
            if (data.success) {
                showSuccess(data.message);
                otpSection.style.display = 'block';
                sendOtpBtn.style.display = 'none';
                updateBtn.style.display = 'block';
                otpInput.focus();
            } else {
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        showFieldError(field, data.errors[field][0]);
                    });
                } else {
                    showError(data.message);
                }
            }
        })
        .catch(error => {
            setSendOtpLoading(false);
            console.error('Error:', error);
            showError('An error occurred. Please try again.');
        });
    });

    // Handle Resend OTP button
    resendOtpBtn.addEventListener('click', function() {
        setResendOtpLoading(true);
        
        fetch('{{ route("admin.resend-otp") }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            setResendOtpLoading(false);
            
            if (data.success) {
                showSuccess(data.message);
            } else {
                showError(data.message);
            }
        })
        .catch(error => {
            setResendOtpLoading(false);
            console.error('Error:', error);
            showError('An error occurred. Please try again.');
        });
    });

    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Reset previous errors
        clearErrors();
        hideAlerts();

        // Validate OTP
        if (!otpInput.value || otpInput.value.length !== 6) {
            showFieldError('otp', 'Please enter a valid 6-digit OTP.');
            return;
        }

        setUpdateLoading(true);

        // Prepare form data
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('otp', otpInput.value);
        if (newEmailInput.value) formData.append('new_email', newEmailInput.value);
        if (newPasswordInput.value) {
            formData.append('new_password', newPasswordInput.value);
            formData.append('new_password_confirmation', confirmPasswordInput.value);
        }

        fetch(verifyUpdateRoute, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            setUpdateLoading(false);
            
            if (data.success) {
                showSuccess(data.message);
                
                if (data.logout_required) {
                    setTimeout(() => {
                        window.location.href = '{{ route("admin.login") }}';
                    }, 2000);
                }
            } else {
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        showFieldError(field, data.errors[field][0]);
                    });
                } else {
                    showError(data.message);
                }
            }
        })
        .catch(error => {
            setUpdateLoading(false);
            console.error('Error:', error);
            showError('An error occurred. Please try again.');
        });
    });

    // Helper functions
    function setSendOtpLoading(loading) {
        sendOtpBtn.disabled = loading;
        if (loading) {
            sendOtpText.textContent = 'Sending...';
            sendOtpLoader.classList.remove('d-none');
        } else {
            sendOtpText.textContent = 'Send Verification Code';
            sendOtpLoader.classList.add('d-none');
        }
    }

    function setResendOtpLoading(loading) {
        resendOtpBtn.disabled = loading;
        if (loading) {
            resendOtpText.textContent = 'Sending...';
            resendOtpLoader.classList.remove('d-none');
        } else {
            resendOtpText.textContent = 'Resend';
            resendOtpLoader.classList.add('d-none');
        }
    }

    function setUpdateLoading(loading) {
        updateBtn.disabled = loading;
        if (loading) {
            updateText.textContent = 'Updating...';
            updateLoader.classList.remove('d-none');
        } else {
            updateText.textContent = 'Update Profile';
            updateLoader.classList.add('d-none');
        }
    }

    function showError(message) {
        errorMessage.textContent = message;
        errorAlert.classList.remove('d-none');
        errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function showSuccess(message) {
        successMessage.textContent = message;
        successAlert.classList.remove('d-none');
        successAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function hideAlerts() {
        errorAlert.classList.add('d-none');
        successAlert.classList.add('d-none');
    }

    function showFieldError(field, message) {
        const fieldElement = document.getElementById(field);
        const errorElement = document.getElementById(field + 'Error');
        
        if (fieldElement && errorElement) {
            fieldElement.classList.add('is-invalid');
            errorElement.textContent = message;
        }
    }

    function clearErrors() {
        const invalidFields = form.querySelectorAll('.is-invalid');
        invalidFields.forEach(field => {
            field.classList.remove('is-invalid');
        });
    }
});
</script>
@endsection 