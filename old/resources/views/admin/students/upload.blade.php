@extends('admin.layout')

@section('title', 'Upload Students')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="bi bi-upload me-2"></i>
                        Upload Students
                    </h1>
                    <p class="text-muted">Upload student data from Excel or CSV file for TC: <strong>{{ $tcCode }}</strong></p>
                </div>
                <div>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Back to Students
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Upload Section -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-file-earmark-arrow-up me-2"></i>
                                Upload File
                            </h5>
                        </div>
                        <div class="card-body">
                            <form id="uploadForm" method="POST" action="{{ route('admin.students.upload.store') }}" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="mb-4">
                                    <label for="file" class="form-label">Select File <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" id="file" name="file" 
                                           accept=".csv" required>
                                    <div class="form-text">
                                        Supported format: CSV (.csv) only. Maximum file size: 10MB
                                    </div>
                                    <div class="invalid-feedback" id="fileError"></div>
                                </div>

                                <!-- Progress Bar -->
                                <div id="uploadProgress" class="mb-4 d-none">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Upload Progress</span>
                                        <span id="progressText" class="text-muted">0%</span>
                                    </div>
                                    <div class="progress" style="height: 25px;">
                                        <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" 
                                             role="progressbar" style="width: 0%" 
                                             aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                            <span id="progressLabel">Preparing upload...</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <h6 class="alert-heading">
                                        <i class="bi bi-info-circle me-2"></i>
                                        Important Notes:
                                    </h6>
                                    <ul class="mb-0">
                                        <li>Make sure your file has the correct column headers (see template below)</li>
                                        <li><strong>Program Name must contain a valid P number (e.g., P102023--SAP Business ONE)</strong></li>
                                        <li>Only the program number (P102023) will be stored in the database</li>
                                        <li>Records without valid P numbers will be rejected</li>
                                        <li>Reference Number and Roll Number must be unique</li>
                                        <li>If email is provided, login credentials will be created automatically</li>
                                        <li>Date format should be DD/MM/YYYY or YYYY-MM-DD</li>
                                        <li>Gender should be: Male, Female, or Other</li>
                                        <li>Minority should be: Yes or No</li>
                                    </ul>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-outline-secondary" onclick="clearForm()">
                                        <i class="bi bi-arrow-clockwise me-2"></i>
                                        Clear
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="uploadBtn">
                                        <i class="bi bi-upload me-2"></i>
                                        <span id="uploadText">Upload File</span>
                                        <span id="uploadLoader" class="spinner-border spinner-border-sm d-none ms-2" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Upload Results -->
                    <div id="uploadResults" class="card mt-4 d-none">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-list-check me-2"></i>
                                Upload Results
                            </h5>
                        </div>
                        <div class="card-body" id="resultsContent">
                            <!-- Results will be displayed here -->
                        </div>
                    </div>
                </div>

                <!-- Template and Instructions -->
                <div class="col-md-4">
                    <!-- Template Download -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-download me-2"></i>
                                Download Template
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3">
                                Download the template file to ensure your data is in the correct format.
                            </p>
                            <a href="{{ route('admin.students.template') }}" class="btn btn-success w-100">
                                <i class="bi bi-file-earmark-excel me-2"></i>
                                Download Excel Template
                            </a>
                        </div>
                    </div>

                    <!-- Column Guide -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-table me-2"></i>
                                Column Guide
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Column</th>
                                            <th>Required</th>
                                            <th>Format</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><code>ProgName</code></td>
                                            <td><span class="badge bg-danger">Yes</span></td>
                                            <td>P Number (e.g., P102023--Program Name)</td>
                                        </tr>
                                        <tr>
                                            <td><code>RefNo</code></td>
                                            <td><span class="badge bg-danger">Yes</span></td>
                                            <td>Text (Unique)</td>
                                        </tr>
                                        <tr>
                                            <td><code>RollNo</code></td>
                                            <td><span class="badge bg-danger">Yes</span></td>
                                            <td>Text (Unique)</td>
                                        </tr>
                                        <tr>
                                            <td><code>Name</code></td>
                                            <td><span class="badge bg-danger">Yes</span></td>
                                            <td>Text</td>
                                        </tr>
                                        <tr>
                                            <td><code>FatherName</code></td>
                                            <td><span class="badge bg-danger">Yes</span></td>
                                            <td>Text</td>
                                        </tr>
                                        <tr>
                                            <td><code>DOB</code></td>
                                            <td><span class="badge bg-danger">Yes</span></td>
                                            <td>DD/MM/YYYY</td>
                                        </tr>
                                        <tr>
                                            <td><code>Gender</code></td>
                                            <td><span class="badge bg-danger">Yes</span></td>
                                            <td>Male/Female/Other</td>
                                        </tr>
                                        <tr>
                                            <td><code>Category</code></td>
                                            <td><span class="badge bg-danger">Yes</span></td>
                                            <td>General/OBC/SC/ST/EWS</td>
                                        </tr>
                                        <tr>
                                            <td><code>Minority</code></td>
                                            <td><span class="badge bg-warning">No</span></td>
                                            <td>Yes/No</td>
                                        </tr>
                                        <tr>
                                            <td><code>MinorityType</code></td>
                                            <td><span class="badge bg-warning">No</span></td>
                                            <td>Text</td>
                                        </tr>
                                        <tr>
                                            <td><code>EducationName</code></td>
                                            <td><span class="badge bg-danger">Yes</span></td>
                                            <td>Text</td>
                                        </tr>
                                        <tr>
                                            <td><code>Address</code></td>
                                            <td><span class="badge bg-danger">Yes</span></td>
                                            <td>Text</td>
                                        </tr>
                                        <tr>
                                            <td><code>City</code></td>
                                            <td><span class="badge bg-danger">Yes</span></td>
                                            <td>Text</td>
                                        </tr>
                                        <tr>
                                            <td><code>State</code></td>
                                            <td><span class="badge bg-danger">Yes</span></td>
                                            <td>Text</td>
                                        </tr>
                                        <tr>
                                            <td><code>District</code></td>
                                            <td><span class="badge bg-danger">Yes</span></td>
                                            <td>Text</td>
                                        </tr>
                                        <tr>
                                            <td><code>Country</code></td>
                                            <td><span class="badge bg-danger">Yes</span></td>
                                            <td>Text</td>
                                        </tr>
                                        <tr>
                                            <td><code>Pincode</code></td>
                                            <td><span class="badge bg-danger">Yes</span></td>
                                            <td>6 digits</td>
                                        </tr>
                                        <tr>
                                            <td><code>MobileNo</code></td>
                                            <td><span class="badge bg-danger">Yes</span></td>
                                            <td>10 digits</td>
                                        </tr>
                                        <tr>
                                            <td><code>PhoneNo</code></td>
                                            <td><span class="badge bg-warning">No</span></td>
                                            <td>10 digits</td>
                                        </tr>
                                        <tr>
                                            <td><code>Email</code></td>
                                            <td><span class="badge bg-warning">No</span></td>
                                            <td>Valid email</td>
                                        </tr>
                                        <tr>
                                            <td><code>TraineeFee</code></td>
                                            <td><span class="badge bg-warning">No</span></td>
                                            <td>Number</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('uploadForm');
    const uploadBtn = document.getElementById('uploadBtn');
    const uploadText = document.getElementById('uploadText');
    const uploadLoader = document.getElementById('uploadLoader');
    const fileInput = document.getElementById('file');
    const uploadProgress = document.getElementById('uploadProgress');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const progressLabel = document.getElementById('progressLabel');
    
    // File upload form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate file
        if (!fileInput.files[0]) {
            showAlert('error', 'Please select a file to upload');
            return;
        }
        
        const file = fileInput.files[0];
        const maxSize = 10 * 1024 * 1024; // 10MB
        
        if (file.size > maxSize) {
            showAlert('error', 'File size must be less than 10MB');
            return;
        }
        
        const allowedTypes = [
            'text/csv'
        ];
        
        if (!allowedTypes.includes(file.type) && !file.name.match(/\.(csv)$/i)) {
            showAlert('error', 'Please select a valid CSV file');
            return;
        }
        
        // Show loading
        uploadBtn.disabled = true;
        uploadText.textContent = 'Uploading...';
        uploadLoader.classList.remove('d-none');
        uploadProgress.classList.remove('d-none'); // Show progress bar
        progressBar.style.width = '0%'; // Reset progress bar
        progressText.textContent = '0%';
        progressLabel.textContent = 'Preparing upload...';
        
        // Create FormData
        const formData = new FormData(form);
        
        // Progress tracking stages
        const stages = [
            { progress: 10, label: 'Preparing file...' },
            { progress: 25, label: 'Validating file...' },
            { progress: 40, label: 'Uploading to server...' },
            { progress: 60, label: 'Processing data...' },
            { progress: 80, label: 'Validating records...' },
            { progress: 95, label: 'Saving to database...' },
            { progress: 100, label: 'Complete!' }
        ];
        
        let currentStage = 0;
        const progressInterval = setInterval(() => {
            if (currentStage < stages.length - 1) {
                const stage = stages[currentStage];
                updateProgress(stage.progress, stage.label);
                currentStage++;
            }
        }, 300);
        
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData
        })
        .then(response => {
            clearInterval(progressInterval);
            updateProgress(95, 'Finalizing...');
            return response.json();
        })
        .then(response => {
            updateProgress(100, 'Complete!');
            
            setTimeout(() => {
                if (response.success) {
                    showAlert('success', 'File uploaded successfully!');
                    showUploadResults(response.results);
                } else {
                    showAlert('error', response.message || 'Failed to upload file');
                }
            }, 500);
        })
        .catch(error => {
            clearInterval(progressInterval);
            console.error('Upload error:', error);
            updateProgress(0, 'Upload failed');
            showAlert('error', 'Failed to upload file. Please try again.');
        })
        .finally(() => {
            // Reset button
            uploadBtn.disabled = false;
            uploadText.textContent = 'Upload File';
            uploadLoader.classList.add('d-none');
            
            // Hide progress bar after a delay
            setTimeout(() => {
                uploadProgress.classList.add('d-none');
            }, 2000);
        });
    });
    
    // File input change event
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            // Show file info
            const fileInfo = `
                <div class="alert alert-info">
                    <i class="bi bi-file-earmark me-2"></i>
                    <strong>Selected file:</strong> ${file.name} (${formatFileSize(file.size)})
                </div>
            `;
            
            // Remove existing file info
            document.querySelectorAll('.alert-info').forEach(alert => alert.remove());
            
            // Add new file info after the file input
            this.closest('.mb-4').insertAdjacentHTML('afterend', fileInfo);
        }
    });
});

function showUploadResults(results) {
    const resultsDiv = document.getElementById('uploadResults');
    const contentDiv = document.getElementById('resultsContent');
    
    let html = `
        <div class="row">
            <div class="col-md-3">
                <div class="text-center">
                    <h3 class="text-primary">${results.total}</h3>
                    <p class="text-muted">Total Records</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <h3 class="text-success">${results.success}</h3>
                    <p class="text-muted">Successfully Added</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <h3 class="text-danger">${results.failed}</h3>
                    <p class="text-muted">Failed</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <h3 class="text-info">${Math.round((results.success / results.total) * 100)}%</h3>
                    <p class="text-muted">Success Rate</p>
                </div>
            </div>
        </div>
    `;
    
    if (results.errors && results.errors.length > 0) {
        html += `
            <hr>
            <h6 class="text-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Errors Found (${results.errors.length})
            </h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Row</th>
                            <th>Error</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        results.errors.forEach(error => {
            html += `
                <tr>
                    <td>${error.row}</td>
                    <td class="text-danger">${error.error}</td>
                    <td><small>${JSON.stringify(error.data)}</small></td>
                </tr>
            `;
        });
        
        html += `
                    </tbody>
                </table>
            </div>
        `;
    }
    
    if (results.success > 0) {
        html += `
            <div class="alert alert-success">
                <i class="bi bi-check-circle me-2"></i>
                <strong>Success!</strong> ${results.success} students have been added successfully.
                <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-success ms-2">
                    View Students
                </a>
            </div>
        `;
    }
    
    contentDiv.innerHTML = html;
    resultsDiv.classList.remove('d-none');
}

function clearForm() {
    document.getElementById('uploadForm').reset();
    document.getElementById('uploadResults').classList.add('d-none');
    document.querySelectorAll('.alert-info').forEach(alert => alert.remove());
    document.getElementById('uploadProgress').classList.add('d-none'); // Hide progress bar on clear
    progressBar.style.width = '0%';
    progressText.textContent = '0%';
    progressLabel.textContent = 'Preparing upload...';
}

function updateProgress(percentage, label) {
    progressBar.style.width = percentage + '%';
    progressBar.setAttribute('aria-valuenow', percentage);
    progressText.textContent = Math.round(percentage) + '%';
    progressLabel.textContent = label;
    
    // Update progress bar color based on percentage
    progressBar.className = 'progress-bar progress-bar-striped progress-bar-animated';
    if (percentage >= 100) {
        progressBar.classList.add('bg-success');
    } else if (percentage >= 50) {
        progressBar.classList.add('bg-info');
    } else if (percentage >= 25) {
        progressBar.classList.add('bg-warning');
    } else {
        progressBar.classList.add('bg-primary');
    }
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

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
    
    // Remove existing alerts (except info alerts)
    document.querySelectorAll('.alert').forEach(alert => {
        if (!alert.classList.contains('alert-info')) {
            alert.remove();
        }
    });
    
    // Add new alert at the top
    const container = document.querySelector('.container-fluid');
    container.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        const newAlert = document.querySelector('.alert');
        if (newAlert && !newAlert.classList.contains('alert-info')) {
            newAlert.style.transition = 'opacity 0.5s';
            newAlert.style.opacity = '0';
            setTimeout(() => newAlert.remove(), 500);
        }
    }, 5000);
}
</script>
@endpush 