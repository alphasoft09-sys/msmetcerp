@extends('admin.layout')

@section('title', 'Header Layout Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="bi bi-image me-2"></i>
                        Header Layout Management
                    </h1>
                    <p class="text-muted">Upload and manage header layout images for each Tool Room (TC)</p>
                </div>
            </div>

            <!-- Alert Messages -->
            <div id="alertContainer"></div>

            <!-- Tool Rooms List -->
            <div class="row">
                @foreach($tcs as $tc)
                    <div class="col-lg-6 col-xl-4 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-building me-2"></i>
                                    Tool Room: {{ $tc->from_tc }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <!-- Current Header Layout Preview -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Current Header Layout:</label>
                                    <div class="current-layout-container" id="current-layout-{{ $tc->from_tc }}">
                                        @if(isset($headerLayouts[$tc->from_tc]))
                                            <div class="text-center">
                                                <img src="{{ $headerLayouts[$tc->from_tc]->header_layout_url }}" 
                                                     alt="Header Layout for {{ $tc->from_tc }}" 
                                                     class="img-fluid border rounded" 
                                                     style="max-height: 150px; max-width: 100%;">
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        Uploaded: {{ $headerLayouts[$tc->from_tc]->updated_at->format('M d, Y H:i') }}
                                                    </small>
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-center py-4">
                                                <i class="bi bi-image display-4 text-muted"></i>
                                                <p class="text-muted mt-2">No header layout uploaded</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Upload Form -->
                                <form class="upload-form" data-tc-id="{{ $tc->from_tc }}">
                                    @csrf
                                    <input type="hidden" name="tc_id" value="{{ $tc->from_tc }}">
                                    
                                    <div class="mb-3">
                                        <label for="header_layout_{{ $tc->from_tc }}" class="form-label">
                                            <i class="bi bi-upload me-1"></i>
                                            Upload Header Layout
                                        </label>
                                        <input type="file" 
                                               class="form-control" 
                                               id="header_layout_{{ $tc->from_tc }}" 
                                               name="header_layout" 
                                               accept="image/png,image/jpg,image/jpeg"
                                               data-tc-id="{{ $tc->from_tc }}">
                                        <div class="form-text">
                                            Accepted formats: PNG, JPG, JPEG (Max: 2MB)
                                        </div>
                                    </div>

                                    <!-- Preview Container -->
                                    <div class="preview-container mb-3" id="preview-{{ $tc->from_tc }}" style="display: none;">
                                        <label class="form-label fw-bold">Preview:</label>
                                        <div class="text-center">
                                            <img id="preview-image-{{ $tc->from_tc }}" 
                                                 class="img-fluid border rounded" 
                                                 style="max-height: 150px; max-width: 100%;">
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="d-flex gap-2">
                                        <button type="submit" 
                                                class="btn btn-primary btn-sm upload-btn" 
                                                data-tc-id="{{ $tc->from_tc }}">
                                            <i class="bi bi-upload me-1"></i>
                                            <span class="btn-text">Upload</span>
                                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                        </button>
                                        
                                        @if(isset($headerLayouts[$tc->from_tc]))
                                            <button type="button" 
                                                    class="btn btn-danger btn-sm delete-btn" 
                                                    data-tc-id="{{ $tc->from_tc }}">
                                                <i class="bi bi-trash me-1"></i>
                                                Delete
                                            </button>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- No TCs Message -->
            @if($tcs->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-building display-1 text-muted"></i>
                    <h3 class="text-muted mt-3">No Tool Rooms Found</h3>
                    <p class="text-muted">No Tool Rooms (TCs) are currently registered in the system.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Header Layout Management page loaded');
    
    // CSRF token setup
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // File input change handlers
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function() {
            const tcId = this.getAttribute('data-tc-id');
            const file = this.files[0];
            
            if (file) {
                // Validate file type
                const allowedTypes = ['image/png', 'image/jpg', 'image/jpeg'];
                if (!allowedTypes.includes(file.type)) {
                    showAlert('danger', 'Please select a valid image file (PNG, JPG, JPEG)');
                    this.value = '';
                    return;
                }
                
                // Validate file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    showAlert('danger', 'File size must be less than 2MB');
                    this.value = '';
                    return;
                }
                
                // Show preview
                showPreview(tcId, file);
            } else {
                hidePreview(tcId);
            }
        });
    });
    
    // Upload form submission
    document.querySelectorAll('.upload-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const tcId = this.getAttribute('data-tc-id');
            const fileInput = this.querySelector('input[type="file"]');
            const uploadBtn = this.querySelector('.upload-btn');
            const btnText = uploadBtn.querySelector('.btn-text');
            const spinner = uploadBtn.querySelector('.spinner-border');
            
            if (!fileInput.files[0]) {
                showAlert('warning', 'Please select a file to upload');
                return;
            }
            
            // Show loading state
            uploadBtn.disabled = true;
            btnText.textContent = 'Uploading...';
            spinner.classList.remove('d-none');
            
            const formData = new FormData(this);
            
            fetch('{{ route("admin.tc-header-layouts.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    
                    // Update current layout display
                    updateCurrentLayout(tcId, data.image_url);
                    
                    // Reset form
                    form.reset();
                    hidePreview(tcId);
                    
                    // Add delete button if not exists
                    addDeleteButton(tcId);
                } else {
                    showAlert('danger', data.message || 'Upload failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while uploading the file');
            })
            .finally(() => {
                // Reset loading state
                uploadBtn.disabled = false;
                btnText.textContent = 'Upload';
                spinner.classList.add('d-none');
            });
        });
    });
    
    // Delete button handlers
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-btn') || e.target.closest('.delete-btn')) {
            const btn = e.target.classList.contains('delete-btn') ? e.target : e.target.closest('.delete-btn');
            const tcId = btn.getAttribute('data-tc-id');
            
            if (confirm(`Are you sure you want to delete the header layout for Tool Room ${tcId}?`)) {
                deleteHeaderLayout(tcId);
            }
        }
    });
    
    // Show image preview
    function showPreview(tcId, file) {
        const previewContainer = document.getElementById(`preview-${tcId}`);
        const previewImage = document.getElementById(`preview-image-${tcId}`);
        
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            previewContainer.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
    
    // Hide image preview
    function hidePreview(tcId) {
        const previewContainer = document.getElementById(`preview-${tcId}`);
        previewContainer.style.display = 'none';
    }
    
    // Update current layout display
    function updateCurrentLayout(tcId, imageUrl) {
        const container = document.getElementById(`current-layout-${tcId}`);
        container.innerHTML = `
            <div class="text-center">
                <img src="${imageUrl}" 
                     alt="Header Layout for ${tcId}" 
                     class="img-fluid border rounded" 
                     style="max-height: 150px; max-width: 100%;">
                <div class="mt-2">
                    <small class="text-muted">
                        Uploaded: ${new Date().toLocaleDateString('en-US', { 
                            month: 'short', 
                            day: 'numeric', 
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        })}
                    </small>
                </div>
            </div>
        `;
    }
    
    // Add delete button
    function addDeleteButton(tcId) {
        const uploadBtn = document.querySelector(`[data-tc-id="${tcId}"] .upload-btn`);
        const buttonContainer = uploadBtn.parentElement;
        
        // Check if delete button already exists
        if (!buttonContainer.querySelector('.delete-btn')) {
            const deleteBtn = document.createElement('button');
            deleteBtn.type = 'button';
            deleteBtn.className = 'btn btn-danger btn-sm delete-btn';
            deleteBtn.setAttribute('data-tc-id', tcId);
            deleteBtn.innerHTML = '<i class="bi bi-trash me-1"></i>Delete';
            buttonContainer.appendChild(deleteBtn);
        }
    }
    
    // Delete header layout
    function deleteHeaderLayout(tcId) {
        fetch(`/admin/tc-header-layouts/${tcId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                
                // Update current layout display
                const container = document.getElementById(`current-layout-${tcId}`);
                container.innerHTML = `
                    <div class="text-center py-4">
                        <i class="bi bi-image display-4 text-muted"></i>
                        <p class="text-muted mt-2">No header layout uploaded</p>
                    </div>
                `;
                
                // Remove delete button
                const deleteBtn = document.querySelector(`[data-tc-id="${tcId}"] .delete-btn`);
                if (deleteBtn) {
                    deleteBtn.remove();
                }
            } else {
                showAlert('danger', data.message || 'Delete failed');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'An error occurred while deleting the header layout');
        });
    }
    
    // Show alert message
    function showAlert(type, message) {
        const alertContainer = document.getElementById('alertContainer');
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        alertContainer.appendChild(alertDiv);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
    
    console.log('Header Layout Management page JavaScript initialization complete');
});
</script>
@endpush 