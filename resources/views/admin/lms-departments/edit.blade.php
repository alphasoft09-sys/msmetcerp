@extends('admin.layout')

@section('title', 'Edit LMS Department')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1 text-primary">
                        <i class="bi bi-pencil-square me-2"></i>
                        Edit Department: {{ $lmsDepartment->department_name }}
                    </h1>
                    <p class="text-muted mb-0">
                        Update department information
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

    <!-- Edit Form -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Department Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.lms-departments.update', $lmsDepartment) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="department_name" class="form-label">Department Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('department_name') is-invalid @enderror" 
                                   id="department_name" name="department_name" value="{{ old('department_name', $lmsDepartment->department_name) }}" 
                                   placeholder="Enter department name" required>
                            @error('department_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="Enter department description">{{ old('description', $lmsDepartment->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       {{ old('is_active', $lmsDepartment->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active Department
                                </label>
                            </div>
                            <small class="form-text text-muted">Inactive departments won't be available for faculty to create sites.</small>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Note:</strong> The department slug will be regenerated automatically from the department name.
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.lms-departments.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>
                                Update Department
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
                        <div class="form-control-plaintext" id="preview-name">{{ $lmsDepartment->department_name }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Department Slug</label>
                        <div class="form-control-plaintext" id="preview-slug">{{ $lmsDepartment->department_slug }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <div class="form-control-plaintext" id="preview-description">{{ $lmsDepartment->description ?: 'No description' }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <div class="form-control-plaintext">
                            @if($lmsDepartment->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Department URL</label>
                        <div class="form-control-plaintext">
                            <code>{{ url('/lms/') }}/<span id="preview-url">{{ $lmsDepartment->department_slug }}</span></code>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Department Stats -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Department Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary">{{ $lmsDepartment->lmsSites->count() }}</h4>
                                <small class="text-muted">Total Sites</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success">{{ $lmsDepartment->lmsSites->where('is_approved', true)->count() }}</h4>
                            <small class="text-muted">Approved Sites</small>
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
