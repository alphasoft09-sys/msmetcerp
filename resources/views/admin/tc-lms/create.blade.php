@extends('admin.layout')

@section('title', 'Create Educational LMS Site - SEO Optimized Content Management')

@section('meta')
<meta name="description" content="Create SEO-optimized educational content for students, research papers, and manuals following Government of India policies. Advanced LMS content management system.">
<meta name="keywords" content="LMS, educational content, SEO, digital learning, NEP 2020, Government of India, student education, research papers, academic content">
<meta name="author" content="Educational LMS System">
<meta name="robots" content="index, follow">
<meta property="og:title" content="Create Educational LMS Site - SEO Optimized">
<meta property="og:description" content="Create SEO-optimized educational content for students, research papers, and manuals following Government of India policies.">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Create Educational LMS Site - SEO Optimized">
<meta name="twitter:description" content="Create SEO-optimized educational content for students, research papers, and manuals following Government of India policies.">
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1 text-primary">
                        <i class="bi bi-book me-2"></i>
                        Create Educational LMS Site
                    </h1>
                    <p class="text-muted mb-0">
                        Create educational content for students, manuals, research papers following Government of India policies
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.tc-lms.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Back to Sites
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Educational Guidance Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-info shadow-sm" id="educational-guidance-card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightbulb me-2"></i>
                        Educational Content Guidelines
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <i class="bi bi-mortarboard display-4 text-primary"></i>
                                <h6 class="mt-2">Student Education</h6>
                                <small class="text-muted">Create courses, tutorials, and learning materials for students</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <i class="bi bi-journal-text display-4 text-success"></i>
                                <h6 class="mt-2">Research Papers</h6>
                                <small class="text-muted">Publish academic research and scholarly articles</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <i class="bi bi-file-earmark-text display-4 text-warning"></i>
                                <h6 class="mt-2">Manuals & Guides</h6>
                                <small class="text-muted">Create instructional manuals and reference guides</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-light border-start border-info border-4" id="policy-compliance-section">
                        <h6 class="alert-heading">
                            <i class="bi bi-shield-check me-2"></i>
                            Government of India Policy Compliance
                        </h6>
                        <p class="mb-2">Ensure your content follows these guidelines:</p>
                        <ul class="mb-0">
                            <li><strong>Digital India Initiative:</strong> Promote digital literacy and e-learning</li>
                            <li><strong>National Education Policy 2020:</strong> Align with NEP guidelines for quality education</li>
                            <li><strong>Accessibility Standards:</strong> Ensure content is accessible to all students</li>
                            <li><strong>Academic Integrity:</strong> Maintain high standards of academic honesty</li>
                            <li><strong>Multilingual Support:</strong> Consider content in regional languages when appropriate</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-gear me-2"></i>
                        Site Information
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.tc-lms.store') }}" id="createSiteForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="site_title" class="form-label">
                                        Site Title <span class="text-danger">*</span>
                                        <i class="bi bi-question-circle text-muted ms-1" 
                                           data-bs-toggle="tooltip" 
                                           title="Choose a unique, descriptive title for your educational content"></i>
                                    </label>
                                    <input type="text" class="form-control @error('site_title') is-invalid @enderror" 
                                           id="site_title" name="site_title" value="{{ old('site_title') }}" 
                                           placeholder="e.g., Advanced Mathematics for Engineering Students" 
                                           required autocomplete="off">
                                    <div class="invalid-feedback" id="title-error"></div>
                                    <div class="valid-feedback" id="title-success" style="display: none;">
                                        <i class="bi bi-check-circle me-1"></i>Title is available
                                    </div>
                                    @error('site_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="site_department" class="form-label">
                                        Department <span class="text-danger">*</span>
                                        <i class="bi bi-question-circle text-muted ms-1" 
                                           data-bs-toggle="tooltip" 
                                           title="Select the department this content belongs to"></i>
                                    </label>
                                    <select class="form-select @error('site_department') is-invalid @enderror" 
                                            id="site_department" name="site_department" required>
                                        <option value="">Select Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->department_name }}" 
                                                    {{ old('site_department') == $department->department_name ? 'selected' : '' }}>
                                                {{ $department->department_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('site_department')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="site_description" class="form-label">
                                Site Description
                                <i class="bi bi-question-circle text-muted ms-1" 
                                   data-bs-toggle="tooltip" 
                                   title="Provide a brief overview of what students will learn from this content"></i>
                            </label>
                            <textarea class="form-control @error('site_description') is-invalid @enderror" 
                                      id="site_description" name="site_description" rows="4" 
                                      placeholder="Describe the learning objectives, target audience, and key topics covered in this educational content...">{{ old('site_description') }}</textarea>
                            <div class="form-text">
                                <i class="bi bi-lightbulb me-1"></i>
                                <strong>Tip:</strong> Include learning objectives, prerequisites, and what students will achieve after completing this content.
                            </div>
                            @error('site_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- SEO Fields -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="seo_title" class="form-label">
                                        SEO Title
                                        <i class="bi bi-question-circle text-muted ms-1" 
                                           data-bs-toggle="tooltip" 
                                           title="Custom title for search engines (max 60 characters)"></i>
                                    </label>
                                    <input type="text" class="form-control @error('seo_title') is-invalid @enderror" 
                                           id="seo_title" name="seo_title" value="{{ old('seo_title') }}" 
                                           placeholder="e.g., Advanced Mathematics Course for Engineering Students"
                                           maxlength="60">
                                    <div class="form-text">
                                        <span id="seo-title-count">0</span>/60 characters
                                        <span class="text-success ms-2" id="seo-title-status"></span>
                                    </div>
                                    @error('seo_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="seo_keywords" class="form-label">
                                        SEO Keywords
                                        <i class="bi bi-question-circle text-muted ms-1" 
                                           data-bs-toggle="tooltip" 
                                           title="Comma-separated keywords for search engines"></i>
                                    </label>
                                    <input type="text" class="form-control @error('seo_keywords') is-invalid @enderror" 
                                           id="seo_keywords" name="seo_keywords" value="{{ old('seo_keywords') }}" 
                                           placeholder="mathematics, engineering, calculus, algebra, education">
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Separate keywords with commas. Focus on educational terms.
                                    </div>
                                    @error('seo_keywords')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="seo_description" class="form-label">
                                SEO Meta Description
                                <i class="bi bi-question-circle text-muted ms-1" 
                                   data-bs-toggle="tooltip" 
                                   title="Description that appears in search results (max 160 characters)"></i>
                            </label>
                            <textarea class="form-control @error('seo_description') is-invalid @enderror" 
                                      id="seo_description" name="seo_description" rows="3" 
                                      placeholder="Brief description that will appear in search engine results..."
                                      maxlength="160">{{ old('seo_description') }}</textarea>
                            <div class="form-text">
                                <span id="seo-desc-count">0</span>/160 characters
                                <span class="text-success ms-2" id="seo-desc-status"></span>
                                <br><i class="bi bi-lightbulb me-1"></i>
                                <strong>Tip:</strong> Include key learning outcomes and target audience.
                            </div>
                            @error('seo_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Site URL Preview
                                <i class="bi bi-question-circle text-muted ms-1" 
                                   data-bs-toggle="tooltip" 
                                   title="This URL will be used to access your educational content"></i>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">{{ url('/lms/') }}/</span>
                                <input type="text" class="form-control" id="url_preview" readonly 
                                       placeholder="URL will be generated automatically">
                                <button class="btn btn-outline-secondary" type="button" id="regenerate-url">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </div>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                URL is generated from your title. Hyphens (-) are allowed and will be used to separate words.
                            </div>
                        </div>

                        <!-- Content Type Selection -->
                        <div class="mb-4">
                            <label class="form-label">
                                Content Type
                                <i class="bi bi-question-circle text-muted ms-1" 
                                   data-bs-toggle="tooltip" 
                                   title="Select the type of educational content you're creating"></i>
                            </label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="content_type" id="course" value="course" checked>
                                        <label class="form-check-label" for="course">
                                            <i class="bi bi-mortarboard me-2"></i>
                                            <strong>Course</strong>
                                            <br><small class="text-muted">Structured learning modules for students</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="content_type" id="research" value="research">
                                        <label class="form-check-label" for="research">
                                            <i class="bi bi-journal-text me-2"></i>
                                            <strong>Research Paper</strong>
                                            <br><small class="text-muted">Academic research and scholarly articles</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="content_type" id="manual" value="manual">
                                        <label class="form-check-label" for="manual">
                                            <i class="bi bi-file-earmark-text me-2"></i>
                                            <strong>Manual/Guide</strong>
                                            <br><small class="text-muted">Instructional manuals and reference guides</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-success border-start border-success border-4">
                            <h6 class="alert-heading">
                                <i class="bi bi-check-circle me-2"></i>
                                Ready to Create Your Educational Content
                            </h6>
                            <p class="mb-2">After creating the site, you'll be able to:</p>
                            <ul class="mb-0">
                                <li>Design your content using our advanced editor</li>
                                <li>Add images, videos, and interactive elements</li>
                                <li>Create quizzes and assessments</li>
                                <li>Track student progress and engagement</li>
                                <li>Publish and share your educational content</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.tc-lms.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="createBtn" disabled>
                                <i class="bi bi-plus-circle me-2"></i>
                                Create Educational Site
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Ensure the educational guidance section stays visible */
#educational-guidance-card {
    position: relative;
    z-index: 10;
    opacity: 1 !important;
    visibility: visible !important;
    display: block !important;
}

#policy-compliance-section {
    position: relative;
    z-index: 10;
    opacity: 1 !important;
    visibility: visible !important;
    display: block !important;
    animation: none !important;
    transition: none !important;
}

/* Prevent any hiding animations or transitions */
#educational-guidance-card,
#policy-compliance-section {
    animation: none !important;
    transition: none !important;
    transform: none !important;
}

/* Ensure the section is always visible */
.educational-guidance-section {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ensure the educational guidance section stays visible
    const guidanceCard = document.getElementById('educational-guidance-card');
    const policySection = document.getElementById('policy-compliance-section');
    
    function ensureSectionVisible() {
        if (guidanceCard) {
            guidanceCard.style.display = 'block';
            guidanceCard.style.visibility = 'visible';
            guidanceCard.style.opacity = '1';
            guidanceCard.style.position = 'relative';
            guidanceCard.style.zIndex = '10';
        }
        
        if (policySection) {
            policySection.style.display = 'block';
            policySection.style.visibility = 'visible';
            policySection.style.opacity = '1';
            policySection.style.position = 'relative';
            policySection.style.zIndex = '10';
        }
    }
    
    // Ensure visibility on page load
    ensureSectionVisible();
    
    // Check every 2 seconds to ensure section stays visible
    setInterval(ensureSectionVisible, 2000);
    
    // Also ensure visibility on any DOM changes
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' || mutation.type === 'childList') {
                ensureSectionVisible();
            }
        });
    });
    
    if (guidanceCard) {
        observer.observe(guidanceCard, { 
            attributes: true, 
            childList: true, 
            subtree: true 
        });
    }
    
    if (policySection) {
        observer.observe(policySection, { 
            attributes: true, 
            childList: true, 
            subtree: true 
        });
    }
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    const titleInput = document.getElementById('site_title');
    const urlPreview = document.getElementById('url_preview');
    const regenerateBtn = document.getElementById('regenerate-url');
    const createBtn = document.getElementById('createBtn');
    const form = document.getElementById('createSiteForm');
    
    // SEO fields
    const seoTitleInput = document.getElementById('seo_title');
    const seoDescInput = document.getElementById('seo_description');
    const seoKeywordsInput = document.getElementById('seo_keywords');
    
    let titleCheckTimeout;
    let isTitleValid = false;
    
    function generateUrlSlug(title) {
        return title.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '') // Allow letters, numbers, spaces, and hyphens
            .replace(/\s+/g, '-')         // Replace spaces with hyphens
            .replace(/-+/g, '-')          // Replace multiple hyphens with single hyphen
            .replace(/^-+|-+$/g, '');     // Remove leading/trailing hyphens
    }
    
    function checkTitleDuplicate(title) {
        if (!title || title.length < 3) {
            showTitleError('Title must be at least 3 characters long');
            return;
        }
        
        fetch('/admin/tc-lms/check-title-duplicate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ title: title })
        })
        .then(response => response.json())
        .then(data => {
            if (data.available) {
                showTitleSuccess();
                isTitleValid = true;
                updateCreateButton();
            } else {
                showTitleError(data.message || 'This title is already taken. Please choose a different one.');
                isTitleValid = false;
                updateCreateButton();
            }
        })
        .catch(error => {
            console.error('Error checking title:', error);
            showTitleError('Error checking title availability. Please try again.');
            isTitleValid = false;
            updateCreateButton();
        });
    }
    
    function showTitleError(message) {
        titleInput.classList.remove('is-valid');
        titleInput.classList.add('is-invalid');
        document.getElementById('title-error').textContent = message;
        document.getElementById('title-success').style.display = 'none';
    }
    
    function showTitleSuccess() {
        titleInput.classList.remove('is-invalid');
        titleInput.classList.add('is-valid');
        document.getElementById('title-success').style.display = 'block';
        document.getElementById('title-error').textContent = '';
    }
    
    function updateCreateButton() {
        const department = document.getElementById('site_department').value;
        if (isTitleValid && department) {
            createBtn.disabled = false;
        } else {
            createBtn.disabled = true;
        }
    }
    
    function updateUrlPreview() {
        const slug = generateUrlSlug(titleInput.value);
        urlPreview.value = slug || 'your-site-url';
    }
    
    // Title input handler with debouncing
    titleInput.addEventListener('input', function() {
        const title = this.value.trim();
        
        // Clear previous timeout
        clearTimeout(titleCheckTimeout);
        
        // Update URL preview immediately
        updateUrlPreview();
        
        // Reset validation state
        titleInput.classList.remove('is-valid', 'is-invalid');
        document.getElementById('title-success').style.display = 'none';
        document.getElementById('title-error').textContent = '';
        isTitleValid = false;
        updateCreateButton();
        
        // Check for duplicates after 500ms delay
        if (title.length >= 3) {
            titleCheckTimeout = setTimeout(() => {
                checkTitleDuplicate(title);
            }, 500);
        }
    });
    
    // Regenerate URL button
    regenerateBtn.addEventListener('click', function() {
        updateUrlPreview();
    });
    
    // Department change handler
    document.getElementById('site_department').addEventListener('change', function() {
        updateCreateButton();
    });
    
    // Form submission handler
    form.addEventListener('submit', function(e) {
        if (!isTitleValid) {
            e.preventDefault();
            showTitleError('Please wait for title validation to complete');
            return false;
        }
        
        // Show loading state
        createBtn.disabled = true;
        createBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Creating Site...';
    });
    
    // SEO field validation and character counting
    function updateSeoTitleCount() {
        const count = seoTitleInput.value.length;
        document.getElementById('seo-title-count').textContent = count;
        
        const status = document.getElementById('seo-title-status');
        if (count === 0) {
            status.textContent = '';
            status.className = 'text-muted ms-2';
        } else if (count <= 50) {
            status.textContent = '✓ Good length';
            status.className = 'text-success ms-2';
        } else if (count <= 60) {
            status.textContent = '⚠ Near limit';
            status.className = 'text-warning ms-2';
        } else {
            status.textContent = '✗ Too long';
            status.className = 'text-danger ms-2';
        }
    }
    
    function updateSeoDescCount() {
        const count = seoDescInput.value.length;
        document.getElementById('seo-desc-count').textContent = count;
        
        const status = document.getElementById('seo-desc-status');
        if (count === 0) {
            status.textContent = '';
            status.className = 'text-muted ms-2';
        } else if (count <= 140) {
            status.textContent = '✓ Good length';
            status.className = 'text-success ms-2';
        } else if (count <= 160) {
            status.textContent = '⚠ Near limit';
            status.className = 'text-warning ms-2';
        } else {
            status.textContent = '✗ Too long';
            status.className = 'text-danger ms-2';
        }
    }
    
    function autoGenerateSeoFields() {
        const title = titleInput.value.trim();
        const description = document.getElementById('site_description').value.trim();
        
        // Auto-generate SEO title if empty
        if (title && !seoTitleInput.value) {
            seoTitleInput.value = title.length > 60 ? title.substring(0, 57) + '...' : title;
            updateSeoTitleCount();
        }
        
        // Auto-generate SEO description if empty
        if (description && !seoDescInput.value) {
            const shortDesc = description.length > 160 ? description.substring(0, 157) + '...' : description;
            seoDescInput.value = shortDesc;
            updateSeoDescCount();
        }
        
        // Auto-generate keywords from title and description
        if (title && !seoKeywordsInput.value) {
            const words = (title + ' ' + description)
                .toLowerCase()
                .replace(/[^\w\s]/g, '')
                .split(/\s+/)
                .filter(word => word.length > 3)
                .slice(0, 10)
                .join(', ');
            seoKeywordsInput.value = words;
        }
    }
    
    // SEO field event listeners
    if (seoTitleInput) {
        seoTitleInput.addEventListener('input', updateSeoTitleCount);
    }
    
    if (seoDescInput) {
        seoDescInput.addEventListener('input', updateSeoDescCount);
    }
    
    // Auto-generate SEO fields when main fields change
    titleInput.addEventListener('input', function() {
        setTimeout(autoGenerateSeoFields, 100);
    });
    
    document.getElementById('site_description').addEventListener('input', function() {
        setTimeout(autoGenerateSeoFields, 100);
    });
    
    // Initial counts
    updateSeoTitleCount();
    updateSeoDescCount();
    
    // Initial URL preview
    updateUrlPreview();
});
</script>
@endpush
@endsection
