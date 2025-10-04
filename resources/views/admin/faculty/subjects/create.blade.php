@extends('admin.layout')

@section('title', 'Create New Subject')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Create New Subject</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('admin.faculty.subjects') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Subjects
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Subject Information</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.faculty.subjects.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Subject Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" 
                                       placeholder="e.g., Mathematics" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="code" class="form-label">Subject Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                       id="code" name="code" value="{{ old('code') }}" 
                                       placeholder="e.g., MATH101" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Unique code for the subject (e.g., MATH101, PHY101)</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="class_level" class="form-label">Class Level <span class="text-danger">*</span></label>
                        <select class="form-select @error('class_level') is-invalid @enderror" 
                                id="class_level" name="class_level" required>
                            <option value="">Select Class Level</option>
                            <option value="Class 10" {{ old('class_level') == 'Class 10' ? 'selected' : '' }}>Class 10</option>
                            <option value="Class 11" {{ old('class_level') == 'Class 11' ? 'selected' : '' }}>Class 11</option>
                            <option value="Class 12" {{ old('class_level') == 'Class 12' ? 'selected' : '' }}>Class 12</option>
                            <option value="Class 9" {{ old('class_level') == 'Class 9' ? 'selected' : '' }}>Class 9</option>
                            <option value="Class 8" {{ old('class_level') == 'Class 8' ? 'selected' : '' }}>Class 8</option>
                        </select>
                        @error('class_level')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4" 
                                  placeholder="Brief description of the subject...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.faculty.subjects') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Create Subject
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Guidelines</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="bi bi-info-circle me-2"></i>Subject Creation Guidelines</h6>
                    <ul class="mb-0">
                        <li>Subject name should be clear and descriptive</li>
                        <li>Subject code must be unique across your TC</li>
                        <li>Choose the appropriate class level</li>
                        <li>Description helps students understand the subject</li>
                    </ul>
                </div>
                
                <div class="alert alert-warning">
                    <h6><i class="bi bi-exclamation-triangle me-2"></i>Important Notes</h6>
                    <ul class="mb-0">
                        <li>Subject code cannot be changed after creation</li>
                        <li>You can only manage subjects for your TC</li>
                        <li>Subjects are automatically linked to your faculty account</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.faculty.schedules') }}" class="btn btn-outline-success">
                        <i class="bi bi-calendar-plus me-2"></i>Create Schedule
                    </a>
                    <a href="{{ route('admin.faculty.attendance') }}" class="btn btn-outline-info">
                        <i class="bi bi-check2-square me-2"></i>Take Attendance
                    </a>
                    <a href="{{ route('admin.faculty.progress') }}" class="btn btn-outline-warning">
                        <i class="bi bi-graph-up me-2"></i>Track Progress
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate subject code based on name
    const nameInput = document.getElementById('name');
    const codeInput = document.getElementById('code');
    
    nameInput.addEventListener('input', function() {
        if (!codeInput.value) {
            const name = this.value.trim();
            if (name) {
                // Generate code from name (first 3-4 letters + 101)
                const code = name.substring(0, 4).toUpperCase().replace(/\s+/g, '') + '101';
                codeInput.value = code;
            }
        }
    });
    
    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const name = nameInput.value.trim();
        const code = codeInput.value.trim();
        const classLevel = document.getElementById('class_level').value;
        
        if (!name || !code || !classLevel) {
            e.preventDefault();
            alert('Please fill in all required fields.');
            return false;
        }
    });
});
</script>
@endsection 