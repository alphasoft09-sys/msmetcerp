@extends('admin.layout')

@section('title', 'Create Admin')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-plus me-2"></i>
                        Create New Admin
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Error Alert -->
                    <div id="errorAlert" class="alert alert-danger d-none" role="alert">
                        <div id="errorMessage"></div>
                    </div>

                    <!-- Success Alert -->
                    <div id="successAlert" class="alert alert-success d-none" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        <div id="successMessage"></div>
                    </div>

                    <form id="createAdminForm" method="POST" action="{{ route('admin.management.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" 
                                       id="name" name="name" value="{{ old('name') }}" required
                                       placeholder="Enter full name">
                                <div class="invalid-feedback" id="nameError"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" 
                                       id="email" name="email" value="{{ old('email') }}" required
                                       placeholder="Enter email address">
                                <div class="invalid-feedback" id="emailError"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" 
                                       id="password" name="password" required
                                       placeholder="Enter password (min 8 characters)">
                                <div class="invalid-feedback" id="passwordError"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" required
                                       placeholder="Confirm password">
                                <div class="invalid-feedback" id="password_confirmationError"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="user_role" class="form-label">Role</label>
                                <select class="form-select" id="user_role" name="user_role" required>
                                    <option value="">Select Role</option>
                                    <option value="2" {{ old('user_role') == '2' ? 'selected' : '' }}>TC Head</option>
                                    <option value="3" {{ old('user_role') == '3' ? 'selected' : '' }}>Exam Cell</option>
                                    <option value="4" {{ old('user_role') == '4' ? 'selected' : '' }}>Assessment Agency</option>
                                    <option value="5" {{ old('user_role') == '5' ? 'selected' : '' }}>TC Faculty</option>
                                </select>
                                <div class="invalid-feedback" id="user_roleError"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="from_tc" class="form-label">TC Code</label>
                                <input type="text" class="form-control" 
                                       id="from_tc" name="from_tc" value="{{ $user->from_tc }}" 
                                       readonly
                                       placeholder="TC code will be automatically set">
                                <small class="form-text text-muted">TC code is automatically set to your training center</small>
                                <div class="invalid-feedback" id="from_tcError"></div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.management.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>
                                Back to List
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span id="submitText">Create Admin</span>
                                <span id="submitLoader" class="spinner-border spinner-border-sm d-none ms-2" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('createAdminForm');
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
            submitText.textContent = 'Creating...';
            submitLoader.classList.remove('d-none');
            submitBtn.classList.add('btn-loading');
        } else {
            submitBtn.disabled = false;
            submitText.textContent = 'Create Admin';
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
@endsection 