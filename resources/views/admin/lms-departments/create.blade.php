@extends('admin.layout')

@section('title', 'Create LMS Department')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1 text-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        Create New Department
                    </h1>
                    <p class="text-muted mb-0">
                        Create a new department for LMS sites
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.lms-departments.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Back to Departments
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Form -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Department Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.lms-departments.store') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="department_name" class="form-label">Department Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('department_name') is-invalid @enderror" 
                                   id="department_name" name="department_name" value="{{ old('department_name') }}" 
                                   placeholder="Enter department name" required>
                            @error('department_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="Enter department description">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Note:</strong> The department slug will be generated automatically from the department name.
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.lms-departments.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>
                                Create Department
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Department Preview</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Department Name</label>
                        <div class="form-control-plaintext" id="preview-name">Enter department name</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Department Slug</label>
                        <div class="form-control-plaintext" id="preview-slug">department-slug</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <div class="form-control-plaintext" id="preview-description">No description</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Department URL</label>
                        <div class="form-control-plaintext">
                            <code>{{ url('/lms/') }}/<span id="preview-url">department-slug</span></code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('department_name');
    const descriptionInput = document.getElementById('description');
    const previewName = document.getElementById('preview-name');
    const previewSlug = document.getElementById('preview-slug');
    const previewUrl = document.getElementById('preview-url');
    const previewDescription = document.getElementById('preview-description');
    
    function generateSlug(name) {
        return name.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim('-');
    }
    
    function updatePreview() {
        const name = nameInput.value || 'Enter department name';
        const slug = generateSlug(nameInput.value) || 'department-slug';
        const description = descriptionInput.value || 'No description';
        
        previewName.textContent = name;
        previewSlug.textContent = slug;
        previewUrl.textContent = slug;
        previewDescription.textContent = description;
    }
    
    nameInput.addEventListener('input', updatePreview);
    descriptionInput.addEventListener('input', updatePreview);
    
    // Initial update
    updatePreview();
});
</script>
@endpush
@endsection
