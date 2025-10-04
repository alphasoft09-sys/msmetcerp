@extends('admin.layout')

@section('title', 'My Profile')

@section('content')
<div class="fade-in-up">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>My Profile</h1>
                <p>View your profile information</p>
            </div>
            <div class="btn-group">
                <a href="{{ route('admin.profile.form') }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>
                    Edit Profile
                </a>
                <a href="{{ route(\App\Helpers\DashboardHelper::getDashboardRoute(Auth::user()->user_role)) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Profile Photo and Basic Info -->
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-body text-center">
                        @if($profile->profile_photo)
                            <img src="{{ $profile->profile_photo_url }}" 
                                 alt="Profile Photo" class="img-fluid rounded-circle mb-3" 
                                 style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                 style="width: 150px; height: 150px;">
                                <i class="bi bi-person display-4 text-muted"></i>
                            </div>
                        @endif
                        
                        <h5 class="card-title">{{ $user->name }}</h5>
                        <p class="text-muted">{{ $user->email }}</p>
                        
                        @if($profile->signature)
                            <div class="mt-3">
                                <strong>Signature:</strong><br>
                                <img src="{{ $profile->signature_url }}" 
                                     alt="Signature" class="img-fluid" style="max-height: 60px;">
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Basic Information -->
            <div class="col-md-8">
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
                                <strong>Contact Number:</strong><br>
                                {{ $profile->contact_no ?? 'Not provided' }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Date of Birth:</strong><br>
                                {{ $profile->dob ? $profile->dob->format('F d, Y') : 'Not provided' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Category:</strong><br>
                                @if($profile->category)
                                    <span class="badge bg-info">{{ $profile->category }}</span>
                                @else
                                    Not provided
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Blood Group:</strong><br>
                                {{ $profile->blood_group ?? 'Not provided' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Mother Tongue:</strong><br>
                                {{ $profile->mother_tongue ?? 'Not provided' }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Highest Education:</strong><br>
                                {{ $profile->qualification ?? 'Not provided' }}
                            </div>
                        </div>
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
                        <strong>Course Completed From:</strong><br>
                        {{ $profile->course_completed_from ?? 'Not provided' }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Date of Completion:</strong><br>
                        {{ $profile->date_of_completion ? $profile->date_of_completion->format('F d, Y') : 'Not provided' }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Current Section:</strong><br>
                        {{ $profile->current_section ?? 'Not provided' }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Designation:</strong><br>
                        {{ $profile->designation ?? 'Not provided' }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Date of Joining:</strong><br>
                        {{ $profile->date_of_joining ? $profile->date_of_joining->format('F d, Y') : 'Not provided' }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Qualification:</strong><br>
                        @if($profile->qualification_id && $profile->qualificationRelation)
                            {{ $profile->qualificationRelation->qf_name }}
                        @elseif($profile->qualification)
                            {{ $profile->qualification }}
                        @else
                            Not provided
                        @endif
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
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Permanent Address:</strong><br>
                        {{ $profile->address_permanent ?? 'Not provided' }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Correspondence Address:</strong><br>
                        {{ $profile->address_correspondence ?? 'Not provided' }}
                    </div>
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
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>TOT Completed:</strong><br>
                        @if($profile->tot_done)
                            <span class="badge bg-success">Yes</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </div>
                    @if($profile->tot_done)
                    <div class="col-md-6 mb-3">
                        <strong>Certification Date:</strong><br>
                        {{ $profile->tot_certification_date ? $profile->tot_certification_date->format('F d, Y') : 'Not provided' }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Certificate Number:</strong><br>
                        {{ $profile->tot_certificate_number ?? 'Not provided' }}
                    </div>
                    @endif
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
                 <div class="row">
                     <div class="col-md-6 mb-3">
                         <strong>TOA Completed:</strong><br>
                         @if($profile->toa_done)
                             <span class="badge bg-success">Yes</span>
                         @else
                             <span class="badge bg-secondary">No</span>
                         @endif
                     </div>
                     @if($profile->toa_done)
                     <div class="col-md-6 mb-3">
                         <strong>Certification Date:</strong><br>
                         {{ $profile->toa_certification_date ? $profile->toa_certification_date->format('F d, Y') : 'Not provided' }}
                     </div>
                     <div class="col-md-6 mb-3">
                         <strong>Certificate Number:</strong><br>
                         {{ $profile->toa_certificate_number ?? 'Not provided' }}
                     </div>
                     <div class="col-md-6 mb-3">
                         <strong>Completed Date:</strong><br>
                         {{ $profile->toa_completed_at ? $profile->toa_completed_at->format('F d, Y \a\t g:i A') : 'Not provided' }}
                     </div>
                     <div class="col-md-6 mb-3">
                         <strong>TOA Version:</strong><br>
                         {{ $profile->toa_version ?? '1.0' }}
                     </div>
                     @if($profile->toa_notes)
                     <div class="col-md-12 mb-3">
                         <strong>Additional Notes:</strong><br>
                         {{ $profile->toa_notes }}
                     </div>
                     @endif
                     @endif
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
                    <strong>Proficient Modules:</strong><br>
                    @if($profile->proficientModules()->count() > 0)
                        @foreach($profile->proficientModules() as $module)
                            <span class="badge bg-primary me-1 mb-1">{{ $module->module_name }}</span>
                        @endforeach
                    @else
                        <span class="text-muted">No modules selected</span>
                    @endif
                </div>
                
                <div class="mb-3">
                    <strong>SME Status:</strong><br>
                    @if($profile->is_sme)
                        <span class="badge bg-success">Subject Matter Expert</span>
                    @else
                        <span class="badge bg-secondary">Not an SME</span>
                    @endif
                </div>
                
                @if($profile->is_sme)
                <div class="mb-3">
                    <strong>SME Qualifications:</strong><br>
                    @if($profile->smeQualifications()->count() > 0)
                        @foreach($profile->smeQualifications() as $qualification)
                            <span class="badge bg-success me-1 mb-1">{{ $qualification->qf_name }}</span>
                        @endforeach
                    @else
                        <span class="text-muted">No SME qualifications selected</span>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection 