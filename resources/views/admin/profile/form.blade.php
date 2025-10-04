@extends('admin.layout')

@section('title', 'Edit Profile')

@section('content')
<div class="fade-in-up">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>Edit Profile</h1>
                <p>Update your profile information</p>
            </div>
            <div class="btn-group">
                @if($profile)
                <a href="{{ route('admin.profile.view') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>
                    Back to Profile
                </a>
                @endif
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if($profile && $profile->name === 'NA')
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Welcome!</strong> Please complete your profile information below. All fields marked with * are required.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form id="profileForm" enctype="multipart/form-data">
                @csrf
                
                <!-- Basic Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            Basic Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" value="{{ $user->email }}" readonly>
                                <div class="form-text">Email cannot be changed from this form</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="contact_no" class="form-label">Contact Number *</label>
                                <input type="text" class="form-control" id="contact_no" name="contact_no" 
                                       value="{{ $profile->contact_no ?? '' }}" required maxlength="15">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="dob" class="form-label">Date of Birth *</label>
                                <input type="date" class="form-control" id="dob" name="dob" 
                                       value="{{ $profile && $profile->dob && $profile->dob !== '1900-01-01' ? $profile->dob->format('Y-m-d') : '' }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="category" class="form-label">Category *</label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="GEN" {{ ($profile->category ?? '') === 'GEN' ? 'selected' : '' }}>GEN</option>
                                    <option value="SC" {{ ($profile->category ?? '') === 'SC' ? 'selected' : '' }}>SC</option>
                                    <option value="ST" {{ ($profile->category ?? '') === 'ST' ? 'selected' : '' }}>ST</option>
                                    <option value="OTHER" {{ ($profile->category ?? '') === 'OTHER' ? 'selected' : '' }}>OTHER</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="blood_group" class="form-label">Blood Group</label>
                                <input type="text" class="form-control" id="blood_group" name="blood_group" 
                                       value="{{ $profile->blood_group ?? '' }}" placeholder="e.g., A+, O-">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="mother_tongue" class="form-label">Mother Tongue</label>
                                <input type="text" class="form-control" id="mother_tongue" name="mother_tongue" 
                                       value="{{ $profile->mother_tongue ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="qualification" class="form-label">Highest Education</label>
                                <input type="text" class="form-control" id="qualification" name="qualification" 
                                       value="{{ $profile->qualification ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- File Uploads -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-upload me-2"></i>
                            File Uploads
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="profile_photo" class="form-label">Profile Photo</label>
                                <input type="file" class="form-control" id="profile_photo" name="profile_photo" 
                                       accept=".jpg,.jpeg,.png">
                                <div class="form-text">JPG/PNG only, max 50 KB</div>
                                @if($profile && $profile->profile_photo)
                                <div class="mt-3">
                                    <strong>Current Profile Photo:</strong><br>
                                    <img src="{{ $profile->profile_photo_url }}" 
                                         alt="Current Profile Photo" class="img-fluid rounded" 
                                         style="max-width: 150px; max-height: 150px; object-fit: cover;">
                                    <div class="mt-2">
                                        <small class="text-muted">File: {{ basename($profile->profile_photo) }}</small>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="signature" class="form-label">Signature</label>
                                <input type="file" class="form-control" id="signature" name="signature" 
                                       accept=".jpg,.jpeg,.png">
                                <div class="form-text">JPG/PNG only, max 20 KB</div>
                                @if($profile && $profile->signature)
                                <div class="mt-3">
                                    <strong>Current Signature:</strong><br>
                                    <img src="{{ $profile->signature_url }}" 
                                         alt="Current Signature" class="img-fluid" 
                                         style="max-width: 200px; max-height: 80px; object-fit: contain;">
                                    <div class="mt-2">
                                        <small class="text-muted">File: {{ basename($profile->signature) }}</small>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Professional Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-briefcase me-2"></i>
                            Professional Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="course_completed_from" class="form-label">Course Completed From</label>
                                <input type="text" class="form-control" id="course_completed_from" name="course_completed_from" 
                                       value="{{ $profile->course_completed_from ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_of_completion" class="form-label">Date of Completion</label>
                                <input type="date" class="form-control" id="date_of_completion" name="date_of_completion" 
                                       value="{{ $profile && $profile->date_of_completion && $profile->date_of_completion !== '1900-01-01' ? $profile->date_of_completion->format('Y-m-d') : '' }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="current_section" class="form-label">Current Section</label>
                                <input type="text" class="form-control" id="current_section" name="current_section" 
                                       value="{{ $profile->current_section ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="designation" class="form-label">Designation</label>
                                <input type="text" class="form-control" id="designation" name="designation" 
                                       value="{{ $profile->designation ?? '' }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date_of_joining" class="form-label">Date of Joining</label>
                                <input type="date" class="form-control" id="date_of_joining" name="date_of_joining" 
                                       value="{{ $profile && $profile->date_of_joining && $profile->date_of_joining !== '1900-01-01' ? $profile->date_of_joining->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="qualification_id" class="form-label">Qualification</label>
                                <select class="form-select" id="qualification_id" name="qualification_id">
                                    <option value="">Select Qualification</option>
                                    @foreach($qualifications as $qualification)
                                        <option value="{{ $qualification->id }}" 
                                                {{ ($profile->qualification_id ?? '') == $qualification->id ? 'selected' : '' }}>
                                            {{ $qualification->qf_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-geo-alt me-2"></i>
                            Address Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="address_permanent" class="form-label">Permanent Address</label>
                            <textarea class="form-control" id="address_permanent" name="address_permanent" rows="3">{{ $profile->address_permanent ?? '' }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="address_correspondence" class="form-label">Correspondence Address</label>
                            <textarea class="form-control" id="address_correspondence" name="address_correspondence" rows="3">{{ $profile->address_correspondence ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- TOT Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-award me-2"></i>
                            TOT (Training of Trainers) Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="hidden" name="tot_done" value="0">
                                <input class="form-check-input" type="checkbox" id="tot_done" name="tot_done" value="1" 
                                       {{ ($profile->tot_done ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="tot_done">
                                    Have you completed TOT (Training of Trainers)?
                                </label>
                            </div>
                        </div>
                        <div id="totDetails" class="row" style="display: {{ ($profile->tot_done ?? false) ? 'flex' : 'none' }};">
                            <div class="col-md-6 mb-3">
                                <label for="tot_certification_date" class="form-label">TOT Certification Date</label>
                                <input type="date" class="form-control" id="tot_certification_date" name="tot_certification_date" 
                                       value="{{ $profile->tot_certification_date ? $profile->tot_certification_date->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tot_certificate_number" class="form-label">TOT Certificate Number</label>
                                <input type="text" class="form-control" id="tot_certificate_number" name="tot_certificate_number" 
                                       value="{{ $profile->tot_certificate_number ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>

                                 <!-- TOA (Training of Assessor) -->
                 <div class="card shadow-sm mb-4">
                     <div class="card-header bg-white">
                         <h5 class="card-title mb-0">
                             <i class="bi bi-award me-2"></i>
                             TOA (Training of Assessor)
                         </h5>
                     </div>
                     <div class="card-body">
                         <div class="mb-3">
                             <div class="form-check">
                                 <input type="hidden" name="toa_done" value="0">
                                 <input class="form-check-input" type="checkbox" id="toa_done" name="toa_done" value="1" 
                                        {{ ($profile->toa_done ?? false) ? 'checked' : '' }}>
                                 <label class="form-check-label" for="toa_done">
                                     Have you completed TOA (Training of Assessor)?
                                 </label>
                             </div>
                             <div class="form-text">
                                 <i class="bi bi-info-circle me-1"></i>
                                 TOA certification is required for assessor roles in the platform.
                             </div>
                         </div>
                         
                         <div id="toaDetails" class="row" style="display: {{ ($profile->toa_done ?? false) ? 'flex' : 'none' }};">
                             <div class="col-md-4 mb-3">
                                 <label for="toa_certification_date" class="form-label">TOA Certification Date</label>
                                 <input type="date" class="form-control" id="toa_certification_date" name="toa_certification_date" 
                                        value="{{ $profile->toa_certification_date ? $profile->toa_certification_date->format('Y-m-d') : '' }}">
                                 <div class="form-text">Date when TOA certification was completed</div>
                             </div>
                             <div class="col-md-4 mb-3">
                                 <label for="toa_certificate_number" class="form-label">TOA Certificate Number</label>
                                 <input type="text" class="form-control" id="toa_certificate_number" name="toa_certificate_number" 
                                        value="{{ $profile->toa_certificate_number ?? '' }}" placeholder="e.g., TOA2024001">
                                 <div class="form-text">Unique certificate number (must be unique across all faculty)</div>
                             </div>
                             <div class="col-md-4 mb-3">
                                 <label for="toa_completed_at" class="form-label">Completed Date</label>
                                 <input type="datetime-local" class="form-control" id="toa_completed_at" name="toa_completed_at" 
                                        value="{{ $profile->toa_completed_at ? $profile->toa_completed_at->format('Y-m-d\TH:i') : '' }}" readonly>
                                 <div class="form-text">Date and time when TOA was completed</div>
                             </div>
                             <div class="col-md-4 mb-3">
                                 <label for="toa_version" class="form-label">TOA Version</label>
                                 <input type="text" class="form-control" id="toa_version" name="toa_version" 
                                        value="{{ $profile->toa_version ?? '1.0' }}" placeholder="e.g., 1.0">
                                 <div class="form-text">Version of TOA training completed</div>
                             </div>
                             <div class="col-md-8 mb-3">
                                 <label for="toa_notes" class="form-label">Additional Notes</label>
                                 <textarea class="form-control" id="toa_notes" name="toa_notes" rows="2" 
                                           placeholder="Any additional notes or comments about TOA training">{{ $profile->toa_notes ?? '' }}</textarea>
                             </div>
                         </div>
                     </div>
                 </div>

                <!-- Module Proficiency -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-puzzle me-2"></i>
                            Module Proficiency
                        </h5>
                    </div>
                    <div class="card-body">
                                                 <div class="mb-3">
                             <label class="form-label">Select Modules You Are Proficient In</label>
                             <div id="proficientModulesContainer">
                                 <div class="row">
                                     @foreach($allModules as $module)
                                     <div class="col-md-6 mb-2">
                                         <div class="form-check">
                                             <input class="form-check-input proficient-module" type="checkbox" 
                                                    name="proficient_module_ids[]" value="{{ $module->id }}" 
                                                    id="proficient_{{ $module->id }}"
                                                    {{ in_array($module->id, $profile->getProficientModuleIdsArray()) ? 'checked' : '' }}>
                                             <label class="form-check-label" for="proficient_{{ $module->id }}">
                                                 {{ $module->module_name }}
                                             </label>
                                         </div>
                                     </div>
                                     @endforeach
                                 </div>
                             </div>
                         </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="hidden" name="is_sme" value="0">
                                <input class="form-check-input" type="checkbox" id="is_sme" name="is_sme" value="1" 
                                       {{ ($profile->is_sme ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_sme">
                                    I am an SME (Subject Matter Expert)
                                </label>
                            </div>
                        </div>
                        
                                                 <div id="smeModules" style="display: {{ ($profile->is_sme ?? false) ? 'block' : 'none' }};">
                             <label class="form-label">Select Qualifications Where You Are an SME</label>
                             <div id="smeModulesContainer">
                                 <div class="row">
                                     @foreach($qualifications as $qualification)
                                     <div class="col-md-6 mb-2">
                                         <div class="form-check">
                                             <input class="form-check-input sme-qualification" type="checkbox" 
                                                    name="sme_qualification_ids[]" value="{{ $qualification->id }}" 
                                                    id="sme_qualification_{{ $qualification->id }}"
                                                    {{ in_array($qualification->id, $profile->getSmeQualificationIdsArray()) ? 'checked' : '' }}>
                                             <label class="form-check-label" for="sme_qualification_{{ $qualification->id }}">
                                                 {{ $qualification->qf_name }}
                                             </label>
                                         </div>
                                     </div>
                                     @endforeach
                                 </div>
                             </div>
                         </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex justify-content-between">
                    <a href="{{ route(\App\Helpers\DashboardHelper::getDashboardRoute($user->user_role)) }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Back to Dashboard
                    </a>
                    <div class="d-flex gap-2">
                       
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-2"></i>
                            SAVE
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin Profile form JavaScript loaded');
    
    // CSRF token setup
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // TOT checkbox toggle
    const totCheckbox = document.getElementById('tot_done');
    const totDetails = document.getElementById('totDetails');
    
    totCheckbox.addEventListener('change', function() {
        if (this.checked) {
            totDetails.style.display = 'flex';
        } else {
            totDetails.style.display = 'none';
        }
    });

         // TOA checkbox toggle
     const toaCheckbox = document.getElementById('toa_done');
     const toaDetails = document.getElementById('toaDetails');
     
     toaCheckbox.addEventListener('change', function() {
         const toaCompletedAt = document.getElementById('toa_completed_at');
         
         if (this.checked) {
             toaDetails.style.display = 'flex';
             // Set current date and time when TOA is completed
             const now = new Date();
             const year = now.getFullYear();
             const month = String(now.getMonth() + 1).padStart(2, '0');
             const day = String(now.getDate()).padStart(2, '0');
             const hours = String(now.getHours()).padStart(2, '0');
             const minutes = String(now.getMinutes()).padStart(2, '0');
             toaCompletedAt.value = `${year}-${month}-${day}T${hours}:${minutes}`;
         } else {
             toaDetails.style.display = 'none';
             toaCompletedAt.value = '';
         }
     });
    
         // SME checkbox toggle
     const smeCheckbox = document.getElementById('is_sme');
     const smeModules = document.getElementById('smeModules');
     
     smeCheckbox.addEventListener('change', function() {
         if (this.checked) {
             smeModules.style.display = 'block';
         } else {
             smeModules.style.display = 'none';
             // Uncheck all SME qualifications
             document.querySelectorAll('.sme-qualification').forEach(checkbox => {
                 checkbox.checked = false;
             });
         }
     });
    
         // Note: All modules and qualifications are now displayed statically
     // No need for dynamic loading since we show all available options
    
    // Form submission
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Saving...';
        
        // Debug: Log form data before submission
        console.log('Profile form data being sent:');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }
        
        fetch('{{ route("admin.profile.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(JSON.stringify(data));
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert(data.message);
                //window.location.reload(); // Refresh the page after save
                // Optionally redirect to view page
                window.location.href = '{{ route("admin.profile.view") }}';
            } else {
                alert(data.message || 'Failed to save profile');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            try {
                const errorData = JSON.parse(error.message);
                if (errorData.errors) {
                    // Show specific validation errors
                    let errorMessage = 'Please correct the following errors:\n\n';
                    Object.keys(errorData.errors).forEach(field => {
                        errorData.errors[field].forEach(error => {
                            errorMessage += `• ${error}\n`;
                        });
                    });
                    alert(errorMessage);
                } else {
                    alert(errorData.message || 'Failed to save profile');
                }
            } catch (e) {
                alert('An error occurred while saving the profile');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while saving the profile');
        })
        .finally(() => {
            // Reset loading state
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
});
</script>
@endpush 