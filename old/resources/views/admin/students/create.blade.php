@extends('admin.layout')

@section('title', 'Add New Student')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="bi bi-person-plus me-2"></i>
                        Add New Student
                    </h1>
                    <p class="text-muted">Add a new student to TC: <strong>{{ $tcCode }}</strong></p>
                </div>
                <div>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Back to Students
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-card-text me-2"></i>
                        Student Information
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Error/Success Messages -->
                    <div id="messageArea"></div>
                    
                    <form id="studentForm" method="POST" action="{{ route('admin.students.store') }}" enctype="multipart/form-data" onsubmit="return false;">
                        @csrf
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-person me-2"></i>
                                    Basic Information
                                </h6>
                                
                                <div class="mb-3">
                                    <label for="ProgName" class="form-label">Program Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="ProgName" name="ProgName" 
                                           value="{{ old('ProgName') }}" required>
                                    <div class="invalid-feedback" id="ProgNameError"></div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="RefNo" class="form-label">Reference Number <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="RefNo" name="RefNo" 
                                                   value="{{ old('RefNo') }}" required>
                                            <div class="invalid-feedback" id="RefNoError"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="RollNo" class="form-label">Roll Number <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="RollNo" name="RollNo" 
                                                   value="{{ old('RollNo') }}" required>
                                            <div class="invalid-feedback" id="RollNoError"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="Name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="Name" name="Name" 
                                           value="{{ old('Name') }}" required>
                                    <div class="invalid-feedback" id="NameError"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="FatherName" class="form-label">Father's Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="FatherName" name="FatherName" 
                                           value="{{ old('FatherName') }}" required>
                                    <div class="invalid-feedback" id="FatherNameError"></div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="DOB" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="DOB" name="DOB" 
                                                   value="{{ old('DOB') }}" required>
                                            <div class="invalid-feedback" id="DOBError"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="Gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                            <select class="form-select" id="Gender" name="Gender" required>
                                                <option value="">Select Gender</option>
                                                <option value="Male" {{ old('Gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                                <option value="Female" {{ old('Gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                                <option value="Other" {{ old('Gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                            <div class="invalid-feedback" id="GenderError"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Category and Education -->
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-mortarboard me-2"></i>
                                    Category & Education
                                </h6>
                                
                                <div class="mb-3">
                                    <label for="Category" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-select" id="Category" name="Category" required>
                                        <option value="">Select Category</option>
                                        <option value="General" {{ old('Category') == 'General' ? 'selected' : '' }}>General</option>
                                        <option value="OBC" {{ old('Category') == 'OBC' ? 'selected' : '' }}>OBC</option>
                                        <option value="SC" {{ old('Category') == 'SC' ? 'selected' : '' }}>SC</option>
                                        <option value="ST" {{ old('Category') == 'ST' ? 'selected' : '' }}>ST</option>
                                        <option value="EWS" {{ old('Category') == 'EWS' ? 'selected' : '' }}>EWS</option>
                                    </select>
                                    <div class="invalid-feedback" id="CategoryError"></div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="Minority" class="form-label">Minority</label>
                                            <select class="form-select" id="Minority" name="Minority">
                                                <option value="0" {{ old('Minority') == '0' ? 'selected' : '' }}>No</option>
                                                <option value="1" {{ old('Minority') == '1' ? 'selected' : '' }}>Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="MinorityType" class="form-label">Minority Type</label>
                                            <input type="text" class="form-control" id="MinorityType" name="MinorityType" 
                                                   value="{{ old('MinorityType') }}" placeholder="If applicable">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="EducationName" class="form-label">Education Qualification <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="EducationName" name="EducationName" 
                                           value="{{ old('EducationName') }}" required 
                                           placeholder="e.g., Graduate(Tech) (Pursuing)">
                                    <div class="invalid-feedback" id="EducationNameError"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="TraineeFee" class="form-label">Trainee Fee</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" class="form-control" id="TraineeFee" name="TraineeFee" 
                                               value="{{ old('TraineeFee', 0) }}" step="0.01" min="0">
                                    </div>
                                    <div class="invalid-feedback" id="TraineeFeeError"></div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <!-- Address Information -->
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-geo-alt me-2"></i>
                                    Address Information
                                </h6>
                                
                                <div class="mb-3">
                                    <label for="Address" class="form-label">Full Address <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="Address" name="Address" rows="3" required>{{ old('Address') }}</textarea>
                                    <div class="invalid-feedback" id="AddressError"></div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="City" class="form-label">City <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="City" name="City" 
                                                   value="{{ old('City') }}" required>
                                            <div class="invalid-feedback" id="CityError"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="State" class="form-label">State <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="State" name="State" 
                                                   value="{{ old('State') }}" required>
                                            <div class="invalid-feedback" id="StateError"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="District" class="form-label">District <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="District" name="District" 
                                                   value="{{ old('District') }}" required>
                                            <div class="invalid-feedback" id="DistrictError"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="Country" class="form-label">Country <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="Country" name="Country" 
                                                   value="{{ old('Country', 'India') }}" required>
                                            <div class="invalid-feedback" id="CountryError"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="Pincode" class="form-label">Pincode <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="Pincode" name="Pincode" 
                                           value="{{ old('Pincode') }}" required maxlength="10">
                                    <div class="invalid-feedback" id="PincodeError"></div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-telephone me-2"></i>
                                    Contact Information
                                </h6>
                                
                                <div class="mb-3">
                                    <label for="MobileNo" class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="MobileNo" name="MobileNo" 
                                           value="{{ old('MobileNo') }}" required maxlength="15">
                                    <div class="invalid-feedback" id="MobileNoError"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="PhoneNo" class="form-label">Alternate Phone Number</label>
                                    <input type="text" class="form-control" id="PhoneNo" name="PhoneNo" 
                                           value="{{ old('PhoneNo') }}" maxlength="15">
                                    <div class="invalid-feedback" id="PhoneNoError"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="Email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="Email" name="Email" 
                                           value="{{ old('Email') }}">
                                    <div class="form-text">If provided, login credentials will be created/updated automatically</div>
                                    <div class="invalid-feedback" id="EmailError"></div>
                                </div>

                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Note:</strong> If email is provided and login doesn't exist, the student will receive login credentials with default password: <code>password123</code>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Photo Upload Section -->
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-camera me-2"></i>
                                    Student Photo
                                </h6>
                                
                                <!-- Photo Upload -->
                                <div class="mb-3">
                                    <label for="Photo" class="form-label">Upload Photo</label>
                                    <input type="file" class="form-control" id="Photo" name="Photo" 
                                           accept="image/*" onchange="previewImage(this)">
                                    <div class="form-text">
                                        Supported formats: JPG, PNG, GIF. Maximum size: 50KB
                                    </div>
                                    <div class="invalid-feedback" id="PhotoError"></div>
                                </div>

                                <!-- Image Preview -->
                                <div id="imagePreview" class="mb-3 d-none">
                                    <label class="form-label">Preview</label>
                                    <div class="text-center">
                                        <img id="previewImg" src="" alt="Preview" 
                                             class="img-thumbnail" 
                                             style="max-width: 200px; max-height: 200px;">
                                        <p class="text-muted mt-2">Photo preview</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-check-circle me-2"></i>
                                <span id="submitText">Add Student</span>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    console.log('Form element:', document.getElementById('studentForm'));
    
    const form = document.getElementById('studentForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitLoader = document.getElementById('submitLoader');
    const messageArea = document.getElementById('messageArea');
    
    if (!form) {
        console.error('Form not found!');
        return;
    }
    
    // Form validation and submission
    form.addEventListener('submit', function(e) {
        console.log('Form submit event triggered');
        e.preventDefault();
        e.stopPropagation();
        
        // Reset validation
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');
        
        console.log('Form action:', form.action);
        
        // Show loading
        submitBtn.disabled = true;
        submitText.textContent = 'Adding Student...';
        submitLoader.classList.remove('d-none');
        
        // Get form data
        const formData = new FormData(form);
        const data = {};
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        
        console.log('Form data:', data);
        
        // Make AJAX request
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(response => {
            console.log('AJAX Success:', response);
            if (response.success) {
                showAlert('success', 'Student added successfully!');
                setTimeout(() => {
                    window.location.href = '{{ route("admin.students.index") }}';
                }, 1500);
            } else {
                // Handle validation errors
                if (response.errors) {
                    // Show field-specific errors
                    Object.keys(response.errors).forEach(field => {
                        const input = document.querySelector(`[name="${field}"]`);
                        const errorDiv = document.getElementById(`${field}Error`);
                        
                        if (input && errorDiv) {
                            input.classList.add('is-invalid');
                            errorDiv.textContent = response.errors[field][0];
                            errorDiv.style.display = 'block';
                        }
                    });
                    showAlert('error', 'Please correct the errors below');
                } else {
                    showAlert('error', response.message || 'Failed to add student');
                }
            }
        })
        .catch(error => {
            console.log('AJAX Error:', error);
            showAlert('error', 'Failed to add student. Please try again.');
        })
        .finally(() => {
            // Reset button
            submitBtn.disabled = false;
            submitText.textContent = 'Add Student';
            submitLoader.classList.add('d-none');
        });
        
        return false; // Prevent form submission
    });
    
    // Auto-format phone numbers
    document.getElementById('MobileNo').addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.length > 10) {
            value = value.substring(0, 10);
        }
        this.value = value;
    });
    
    document.getElementById('PhoneNo').addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.length > 10) {
            value = value.substring(0, 10);
        }
        this.value = value;
    });
    
    // Auto-format pincode
    document.getElementById('Pincode').addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.length > 6) {
            value = value.substring(0, 6);
        }
        this.value = value;
    });
    
    // Auto-generate reference number if empty
    document.getElementById('RefNo').addEventListener('blur', function() {
        if (!this.value) {
            const timestamp = Date.now().toString().slice(-6);
            const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            this.value = `T${timestamp}${random}`;
        }
    });
    
    // Auto-generate roll number if empty
    document.getElementById('RollNo').addEventListener('blur', function() {
        if (!this.value) {
            const timestamp = Date.now().toString().slice(-6);
            const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            this.value = `BBST${timestamp}${random}`;
        }
    });
    
    // Test button to verify JavaScript is working
    const testBtn = document.getElementById('testBtn');
    if (testBtn) {
        testBtn.addEventListener('click', function() {
            console.log('Test button clicked!');
            showAlert('success', 'JavaScript is working!');
        });
    }
    
    // Fallback: also handle submit button click
    if (submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            console.log('Submit button clicked!');
            // This will trigger the form submit event
        });
    }
});

function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="bi ${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Show alert in the message area
    const messageArea = document.getElementById('messageArea');
    if (messageArea) {
        messageArea.innerHTML = alertHtml;
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            const alert = messageArea.querySelector('.alert');
            if (alert) {
                alert.style.display = 'none';
            }
        }, 5000);
    }
}

function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const photoError = document.getElementById('PhotoError');
    
    // Reset error
    photoError.style.display = 'none';
    input.classList.remove('is-invalid');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate file size (50KB = 50 * 1024 bytes)
        const maxSize = 50 * 1024;
        if (file.size > maxSize) {
            photoError.textContent = 'File size must be less than 50KB';
            photoError.style.display = 'block';
            input.classList.add('is-invalid');
            input.value = '';
            preview.classList.add('d-none');
            return;
        }
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            photoError.textContent = 'Please select a valid image file (JPG, PNG, GIF)';
            photoError.style.display = 'block';
            input.classList.add('is-invalid');
            input.value = '';
            preview.classList.add('d-none');
            return;
        }
        
        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    } else {
        preview.classList.add('d-none');
    }
}
</script>
@endpush 