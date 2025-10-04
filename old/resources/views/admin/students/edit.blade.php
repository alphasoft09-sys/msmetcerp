@extends('admin.layout')

@section('title', 'Edit Student')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="bi bi-pencil-square me-2"></i>
                        Edit Student
                    </h1>
                    <p class="text-muted">Edit student information for TC: <strong>{{ $tcCode }}</strong></p>
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
                    <form id="studentForm" method="POST" action="{{ route('admin.students.update', $student->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-person me-2"></i>
                                    Basic Information
                                </h6>
                                
                                <div class="mb-3">
                                    <label for="ProgName" class="form-label">Program No <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="ProgName" name="ProgName" 
                                           value="{{ old('ProgName', $student->ProgName) }}" required>
                                    <div class="invalid-feedback" id="ProgNameError"></div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="RefNo" class="form-label">Reference Number <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="RefNo" name="RefNo" 
                                                   value="{{ old('RefNo', $student->RefNo) }}" required>
                                            <div class="invalid-feedback" id="RefNoError"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="RollNo" class="form-label">Roll Number <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="RollNo" name="RollNo" 
                                                   value="{{ old('RollNo', $student->RollNo) }}" required>
                                            <div class="invalid-feedback" id="RollNoError"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="Name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="Name" name="Name" 
                                           value="{{ old('Name', $student->Name) }}" required>
                                    <div class="invalid-feedback" id="NameError"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="FatherName" class="form-label">Father's Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="FatherName" name="FatherName" 
                                           value="{{ old('FatherName', $student->FatherName) }}" required>
                                    <div class="invalid-feedback" id="FatherNameError"></div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="DOB" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="DOB" name="DOB" 
                                                   value="{{ old('DOB', $student->DOB) }}" required>
                                            <div class="invalid-feedback" id="DOBError"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="Gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                            <select class="form-select" id="Gender" name="Gender" required>
                                                <option value="">Select Gender</option>
                                                <option value="Male" {{ old('Gender', $student->Gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                                <option value="Female" {{ old('Gender', $student->Gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                                <option value="Other" {{ old('Gender', $student->Gender) == 'Other' ? 'selected' : '' }}>Other</option>
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
                                        <option value="General" {{ old('Category', $student->Category) == 'General' ? 'selected' : '' }}>General</option>
                                        <option value="OBC" {{ old('Category', $student->Category) == 'OBC' ? 'selected' : '' }}>OBC</option>
                                        <option value="SC" {{ old('Category', $student->Category) == 'SC' ? 'selected' : '' }}>SC</option>
                                        <option value="ST" {{ old('Category', $student->Category) == 'ST' ? 'selected' : '' }}>ST</option>
                                        <option value="EWS" {{ old('Category', $student->Category) == 'EWS' ? 'selected' : '' }}>EWS</option>
                                    </select>
                                    <div class="invalid-feedback" id="CategoryError"></div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="Minority" class="form-label">Minority</label>
                                            <select class="form-select" id="Minority" name="Minority">
                                                <option value="0" {{ old('Minority', $student->Minority) == 0 ? 'selected' : '' }}>No</option>
                                                <option value="1" {{ old('Minority', $student->Minority) == 1 ? 'selected' : '' }}>Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="MinorityType" class="form-label">Minority Type</label>
                                            <input type="text" class="form-control" id="MinorityType" name="MinorityType" 
                                                   value="{{ old('MinorityType', $student->MinorityType) }}" placeholder="If applicable">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="EducationName" class="form-label">Education Qualification <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="EducationName" name="EducationName" 
                                           value="{{ old('EducationName', $student->EducationName) }}" required 
                                           placeholder="e.g., Graduate(Tech) (Pursuing)">
                                    <div class="invalid-feedback" id="EducationNameError"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="TraineeFee" class="form-label">Trainee Fee</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" class="form-control" id="TraineeFee" name="TraineeFee" 
                                               value="{{ old('TraineeFee', $student->TraineeFee) }}" step="0.01" min="0">
                                    </div>
                                    <div class="invalid-feedback" id="TraineeFeeError"></div>
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
                                
                                <!-- Current Photo Display -->
                                <div class="mb-3">
                                    <label class="form-label">Current Photo</label>
                                    <div class="text-center">
                                        @if($student->Photo)
                                            <img src="{{ route('images.students', ['filename' => basename($student->Photo)]) }}" 
                                                 alt="Current Student Photo" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 200px; max-height: 200px;">
                                            <p class="text-muted mt-2">Current photo</p>
                                        @else
                                            <div class="bg-light border rounded d-flex align-items-center justify-content-center" 
                                                 style="width: 200px; height: 200px; margin: 0 auto;">
                                                <i class="bi bi-person text-muted" style="font-size: 3rem;"></i>
                                            </div>
                                            <p class="text-muted mt-2">No photo uploaded</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Photo Upload -->
                                <div class="mb-3">
                                    <label for="Photo" class="form-label">Upload New Photo</label>
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
                                        <p class="text-muted mt-2">New photo preview</p>
                                    </div>
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
                                    <textarea class="form-control" id="Address" name="Address" rows="3" required>{{ old('Address', $student->Address) }}</textarea>
                                    <div class="invalid-feedback" id="AddressError"></div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="City" class="form-label">City <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="City" name="City" 
                                                   value="{{ old('City', $student->City) }}" required>
                                            <div class="invalid-feedback" id="CityError"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="State" class="form-label">State <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="State" name="State" 
                                                   value="{{ old('State', $student->State) }}" required>
                                            <div class="invalid-feedback" id="StateError"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="District" class="form-label">District <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="District" name="District" 
                                                   value="{{ old('District', $student->District) }}" required>
                                            <div class="invalid-feedback" id="DistrictError"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="Country" class="form-label">Country <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="Country" name="Country" 
                                                   value="{{ old('Country', $student->Country) }}" required>
                                            <div class="invalid-feedback" id="CountryError"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="Pincode" class="form-label">Pincode <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="Pincode" name="Pincode" 
                                           value="{{ old('Pincode', $student->Pincode) }}" required maxlength="10">
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
                                           value="{{ old('MobileNo', $student->MobileNo) }}" required maxlength="15">
                                    <div class="invalid-feedback" id="MobileNoError"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="PhoneNo" class="form-label">Alternate Phone Number</label>
                                    <input type="text" class="form-control" id="PhoneNo" name="PhoneNo" 
                                           value="{{ old('PhoneNo', $student->PhoneNo) }}" maxlength="15">
                                    <div class="invalid-feedback" id="PhoneNoError"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="Email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="Email" name="Email" 
                                           value="{{ old('Email', $student->Email) }}">
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

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>
                                Cancel
                            </a>
                            
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-check-circle me-2"></i>
                                <span id="submitText">Update Student</span>
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
    const form = document.getElementById('studentForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitLoader = document.getElementById('submitLoader');
    
    // Form validation and submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Reset validation
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');
        
        // Show loading
        submitBtn.disabled = true;
        submitText.textContent = 'Updating Student...';
        submitLoader.classList.remove('d-none');
        
        // Get form data
        const formData = new FormData(form);
        
        console.log('Form submission started');
        console.log('Form action:', form.action);
        console.log('Form data:', Object.fromEntries(formData));
        
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                showAlert('success', 'Student updated successfully!');
                // Stay on the same page instead of redirecting
                setTimeout(() => {
                    // Optionally refresh the form data or show updated info
                    console.log('Student updated successfully');
                }, 1500);
            } else {
                // Handle validation errors
                if (data.errors) {
                    // Show field-specific errors
                    Object.keys(data.errors).forEach(field => {
                        const input = document.querySelector(`[name="${field}"]`);
                        const errorDiv = document.getElementById(`${field}Error`);
                        
                        if (input && errorDiv) {
                            input.classList.add('is-invalid');
                            errorDiv.textContent = data.errors[field][0];
                            errorDiv.style.display = 'block';
                        }
                    });
                    showAlert('error', 'Please correct the errors below');
                } else {
                    showAlert('error', data.message || 'Failed to update student');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Failed to update student. Please try again.');
        })
        .finally(() => {
            // Reset button
            submitBtn.disabled = false;
            submitText.textContent = 'Update Student';
            submitLoader.classList.add('d-none');
        });
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
    
    // Remove existing alerts
    document.querySelectorAll('.alert').forEach(alert => alert.remove());
    
    // Add new alert at the top
    const container = document.querySelector('.container-fluid');
    container.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        const newAlert = document.querySelector('.alert');
        if (newAlert) {
            newAlert.style.transition = 'opacity 0.5s';
            newAlert.style.opacity = '0';
            setTimeout(() => newAlert.remove(), 500);
        }
    }, 5000);
}

function testJavaScript() {
    console.log('JavaScript is working!');
    showAlert('success', 'JavaScript is working correctly!');
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