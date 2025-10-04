@extends('admin.layout')

@section('title', 'LMS Sites Management')

@section('content')
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
.table-responsive {
    border-radius: 0.375rem;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.table th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    white-space: nowrap;
    position: sticky;
    top: 0;
    z-index: 10;
}

.table td {
    border-bottom: 1px solid #dee2e6;
    white-space: nowrap;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.badge {
    font-size: 0.75em;
    padding: 0.35em 0.65em;
}

.btn-group .btn {
    margin-right: 0.25rem;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
}

/* Custom scrollbar for webkit browsers */
.table-responsive::-webkit-scrollbar {
    height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>

<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1 text-primary">
                        <i class="bi bi-check-circle me-2"></i>
                        LMS Sites Management
                    </h1>
                    <p class="text-muted mb-0">
                        Review, approve, and manage faculty-submitted LMS sites
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="refreshTable()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Container -->
    <div id="alert-container"></div>

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.tc-lms.admin-index') }}" class="row g-3" id="filterForm">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Search sites...">
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="department" class="form-label">Department</label>
                            <select class="form-select" id="department" name="department">
                                <option value="">All Departments</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->department_name }}" 
                                            {{ request('department') == $department->department_name ? 'selected' : '' }}>
                                        {{ $department->department_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                   value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search me-1"></i>Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- LMS Sites Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">LMS Sites ({{ $lmsSites->total() }} total)</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-secondary" onclick="exportToCSV()">
                            <i class="bi bi-download me-1"></i>Export CSV
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($lmsSites->count() > 0)
                        <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                            <table class="table table-hover mb-0" style="min-width: 1200px;">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th style="min-width: 300px; width: 25%;">Site Details</th>
                                        <th style="min-width: 180px; width: 15%;">Faculty</th>
                                        <th style="min-width: 120px; width: 10%;">Department</th>
                                        <th style="min-width: 100px; width: 8%;">TC Code</th>
                                        <th style="min-width: 100px; width: 8%;">Status</th>
                                        <th style="min-width: 120px; width: 10%;">Submitted</th>
                                        <th style="min-width: 280px; width: 24%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lmsSites as $site)
                                        <tr id="site-row-{{ $site->id }}">
                                            <td style="vertical-align: middle;">
                                                <div class="d-flex flex-column">
                                                    <strong class="text-primary mb-1">{{ $site->site_title }}</strong>
                                                    @if($site->site_description)
                                                        <small class="text-muted">{{ Str::limit($site->site_description, 80) }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td style="vertical-align: middle;">
                                                <div class="d-flex flex-column">
                                                    <strong>{{ $site->faculty->name }}</strong>
                                                    <small class="text-muted text-truncate" style="max-width: 150px;" title="{{ $site->faculty->email }}">{{ $site->faculty->email }}</small>
                                                </div>
                                            </td>
                                            <td style="vertical-align: middle;">
                                                <span class="badge bg-info text-wrap">{{ $site->site_department }}</span>
                                            </td>
                                            <td style="vertical-align: middle;">
                                                <code class="bg-light px-2 py-1 rounded">{{ $site->tc_code }}</code>
                                            </td>
                                            <td style="vertical-align: middle;">
                                                @switch($site->status)
                                                    @case('draft')
                                                        <span class="badge bg-secondary">Draft</span>
                                                        @break
                                                    @case('submitted')
                                                        <span class="badge bg-warning">Submitted</span>
                                                        @break
                                                    @case('approved')
                                                        <span class="badge bg-success">Approved</span>
                                                        @break
                                                    @case('rejected')
                                                        <span class="badge bg-danger">Rejected</span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td style="vertical-align: middle;">
                                                <div class="d-flex flex-column">
                                                    <small class="text-muted">{{ $site->created_at->format('M d, Y') }}</small>
                                                    <small class="text-muted">{{ $site->created_at->format('H:i') }}</small>
                                                </div>
                                            </td>
                                            <td style="vertical-align: middle;">
                                                <div class="d-flex flex-wrap gap-1">
                                                    <!-- View Button -->
                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                            onclick="viewSiteDetails({{ $site->id }}); return false;" title="View Site">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    
                                                    
                                                    @if($site->status === 'draft')
                                                        <!-- Submit Button -->
                                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                                onclick="submitSite({{ $site->id }})" title="Submit for Approval">
                                                            <i class="bi bi-send"></i>
                                                        </button>
                                                    @elseif($site->status === 'submitted')
                                                        <!-- Approve Button -->
                                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                                onclick="approveSite({{ $site->id }})" title="Approve Site">
                                                            <i class="bi bi-check-circle"></i>
                                                        </button>
                                                        
                                                        <!-- Reject Button -->
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                onclick="rejectSite({{ $site->id }})" title="Reject Site">
                                                            <i class="bi bi-x-circle"></i>
                                                        </button>
                                                    @elseif($site->status === 'approved')
                                                        <!-- Grant Edit Permission Button -->
                                                        @if(!$site->can_edit_after_approval)
                                                            <button type="button" class="btn btn-sm btn-outline-info" 
                                                                    onclick="grantEditPermission({{ $site->id }})" title="Grant Edit Permission to Faculty">
                                                                <i class="bi bi-unlock"></i>
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-sm btn-outline-warning" 
                                                                    onclick="revokeEditPermission({{ $site->id }})" title="Revoke Edit Permission from Faculty">
                                                                <i class="bi bi-lock"></i>
                                                            </button>
                                                        @endif
                                                    @endif
                                                    
                                                    <!-- Delete Button -->
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            onclick="deleteSite({{ $site->id }})" title="Delete Site">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4 p-3">
                            {{ $lmsSites->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <h4 class="mt-3 text-muted">No Sites Found</h4>
                            <p class="text-muted">There are no LMS sites matching your criteria.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewDetailsModalLabel">
                    <i class="bi bi-info-circle me-2"></i>Site Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="view-details-content">
                    <div class="text-center py-5">
                        <div class="spinner-border text-info" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3">Loading site details...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="previewModalLabel">
                    <i class="bi bi-eye me-2"></i>Site Preview
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="preview-content">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3">Loading preview...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectModalLabel">
                    <i class="bi bi-x-circle me-2"></i>Reject LMS Site
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" 
                                  placeholder="Please provide a detailed reason for rejection..." required></textarea>
                        <div class="form-text">This reason will be visible to the faculty member.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-1"></i>Reject Site
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this LMS site?</p>
                <p class="text-muted">This action cannot be undone and will permanently remove all site data.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="bi bi-trash me-1"></i>Delete Site
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.badge {
    font-size: 0.75em;
}

.modal-xl {
    max-width: 95%;
}

#preview-content {
    min-height: 400px;
}

.preview-header {
    background: #f8f9fa;
    padding: 1rem;
    border-bottom: 1px solid #dee2e6;
}

.preview-body {
    padding: 1rem;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

.site-content-preview {
    max-height: 500px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    background: #fff;
}

.site-content-preview img {
    max-width: 100%;
    height: auto;
}

.site-content-preview table {
    width: 100%;
    border-collapse: collapse;
}

.site-content-preview table, 
.site-content-preview th, 
.site-content-preview td {
    border: 1px solid #dee2e6;
}

.site-content-preview th, 
.site-content-preview td {
    padding: 0.5rem;
}

.site-content-preview h1, 
.site-content-preview h2, 
.site-content-preview h3, 
.site-content-preview h4, 
.site-content-preview h5, 
.site-content-preview h6 {
    margin-top: 1rem;
    margin-bottom: 0.5rem;
}

.site-content-preview p {
    margin-bottom: 1rem;
}

.site-content-preview ul, 
.site-content-preview ol {
    margin-bottom: 1rem;
    padding-left: 2rem;
}
</style>
@endpush

@push('scripts')
<script>
// Global variables
let currentSiteId = null;
let deleteSiteId = null;

// CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// View site details function - made globally available
window.viewSiteDetails = function(siteId) {
    console.log('viewSiteDetails called with siteId:', siteId);
    
    // Prevent any default behavior
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    currentSiteId = siteId;
    const modal = new bootstrap.Modal(document.getElementById('viewDetailsModal'));
    const viewContent = document.getElementById('view-details-content');
    
    console.log('Modal elements found:', {
        modal: !!modal,
        viewContent: !!viewContent
    });
    
    // Show loading
    viewContent.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-info" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3">Loading site details...</p>
        </div>
    `;
    
    
    modal.show();
    
    console.log('Modal shown, making AJAX request...');
    
    // Load site details via AJAX
    fetch(`/admin/tc-lms-admin/${siteId}/preview-ajax`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Response received:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Data received:', data);
        if (data.success) {
            displaySiteDetails(data.data);
        } else {
            viewContent.innerHTML = `
                <div class="alert alert-danger m-3">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error loading site details: ${data.message}
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error loading site details:', error);
        viewContent.innerHTML = `
            <div class="alert alert-danger m-3">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Error loading site details. Please try again.
            </div>
        `;
    });
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh every 30 seconds
    setInterval(refreshTable, 30000);
});

// Show alert function
function showAlert(message, type) {
    const alertContainer = document.getElementById('alert-container');
    const alertId = 'alert-' + Date.now();
    
    const alertHtml = `
        <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    alertContainer.insertAdjacentHTML('beforeend', alertHtml);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        const alertElement = document.getElementById(alertId);
        if (alertElement) {
            alertElement.remove();
        }
    }, 5000);
}

// Edit site function
function editSite(siteId) {
    window.location.href = `/admin/tc-lms/${siteId}/edit`;
}

// Submit site function
function submitSite(siteId) {
    if (confirm('Are you sure you want to submit this site for approval?')) {
        fetch(`/admin/tc-lms/${siteId}/submit`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                updateSiteStatus(siteId, 'submitted');
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error submitting site:', error);
            showAlert('Error submitting site. Please try again.', 'danger');
        });
    }
}

// Refresh table function
function refreshTable() {
    window.location.reload();
}

// Display site details
function displaySiteDetails(data) {
    const viewContent = document.getElementById('view-details-content');
    
    viewContent.innerHTML = `
        <div class="row">
            <!-- Site Information -->
            <div class="col-md-6">
                <div class="card border-0">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Site Information</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="fw-bold text-muted">Site Title:</td>
                                <td>${data.site_title}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Department:</td>
                                <td><span class="badge bg-info">${data.site_department}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">TC Code:</td>
                                <td><code class="bg-light px-2 py-1 rounded">${data.tc_code}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Status:</td>
                                <td><span class="badge bg-${getStatusColor(data.status)}">${data.status.toUpperCase()}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Created:</td>
                                <td>${data.created_at}</td>
                            </tr>
                        </table>
                        
                        <div class="mt-3">
                            <h6 class="text-muted">Description:</h6>
                            <p class="text-muted">${data.site_description || 'No description provided'}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Faculty Information -->
            <div class="col-md-6">
                <div class="card border-0">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-person me-2"></i>Faculty Information</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="fw-bold text-muted">Name:</td>
                                <td>${data.faculty_name}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Email:</td>
                                <td><a href="mailto:${data.faculty_email}" class="text-decoration-none">${data.faculty_email}</a></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Site Content -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-file-text me-2"></i>Site Content</h6>
                    </div>
                    <div class="card-body">
                        ${data.content ? `
                            <div class="site-content-preview">
                                ${data.content}
                            </div>
                        ` : `
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                No content available for this site.
                            </div>
                        `}
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Preview site function
function previewSite(siteId) {
    currentSiteId = siteId;
    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    const previewContent = document.getElementById('preview-content');
    
    // Show loading
    previewContent.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3">Loading preview...</p>
        </div>
    `;
    
    modal.show();
    
    // Load preview content via AJAX
    fetch(`/admin/tc-lms-admin/${siteId}/preview-ajax`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayPreview(data.data);
        } else {
            previewContent.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error loading preview: ${data.message}
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error loading preview:', error);
        previewContent.innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Error loading preview. Please try again.
            </div>
        `;
    });
}

// Display preview content
function displayPreview(data) {
    const previewContent = document.getElementById('preview-content');
    
    previewContent.innerHTML = `
        <div class="preview-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="mb-1">${data.site_title}</h4>
                    <p class="text-muted mb-2">${data.site_description || 'No description provided'}</p>
                    <div class="d-flex gap-3">
                        <span class="badge bg-info">${data.site_department}</span>
                        <span class="badge bg-secondary">${data.tc_code}</span>
                        <span class="badge bg-${getStatusColor(data.status)}">${data.status.toUpperCase()}</span>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <small class="text-muted">
                        <strong>Faculty:</strong> ${data.faculty_name}<br>
                        <strong>Email:</strong> ${data.faculty_email}<br>
                        <strong>Submitted:</strong> ${data.created_at}
                    </small>
                </div>
            </div>
        </div>
        <div class="preview-body">
            ${data.content ? data.content : '<div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>No content available for preview.</div>'}
        </div>
    `;
}

// Get status color
function getStatusColor(status) {
    switch(status) {
        case 'draft': return 'secondary';
        case 'submitted': return 'warning';
        case 'approved': return 'success';
        case 'rejected': return 'danger';
        default: return 'secondary';
    }
}

// Approve site function
function approveSite(siteId) {
    if (confirm('Are you sure you want to approve this LMS site?')) {
        fetch(`/admin/tc-lms-admin/${siteId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                updateSiteStatus(siteId, 'approved');
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error approving site:', error);
            showAlert('Error approving site. Please try again.', 'danger');
        });
    }
}

// Reject site function
function rejectSite(siteId) {
    currentSiteId = siteId;
    const rejectForm = document.getElementById('rejectForm');
    rejectForm.action = `/admin/tc-lms-admin/${siteId}/reject`;
    rejectForm.reset();
    
    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    modal.show();
}

// Handle reject form submission
document.getElementById('rejectForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const siteId = currentSiteId;
    
    fetch(this.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            updateSiteStatus(siteId, 'rejected');
            bootstrap.Modal.getInstance(document.getElementById('rejectModal')).hide();
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error rejecting site:', error);
        showAlert('Error rejecting site. Please try again.', 'danger');
    });
});

// Delete site function
function deleteSite(siteId) {
    deleteSiteId = siteId;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Handle delete confirmation
document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (deleteSiteId) {
        fetch(`/admin/tc-lms-admin/${deleteSiteId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                document.getElementById(`site-row-${deleteSiteId}`).remove();
                bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error deleting site:', error);
            showAlert('Error deleting site. Please try again.', 'danger');
        });
    }
});

// Update site status in UI
function updateSiteStatus(siteId, newStatus) {
    const row = document.getElementById(`site-row-${siteId}`);
    if (row) {
        const statusCell = row.querySelector('td:nth-child(5)');
        const actionsCell = row.querySelector('td:nth-child(7)');
        
        // Update status badge
        const statusColor = getStatusColor(newStatus);
        statusCell.innerHTML = `<span class="badge bg-${statusColor}">${newStatus.toUpperCase()}</span>`;
        
        // Update actions (remove approve/reject buttons)
        if (newStatus === 'approved' || newStatus === 'rejected') {
            actionsCell.innerHTML = actionsCell.innerHTML.replace(
                /<button[^>]*onclick="approveSite[^>]*>.*?<\/button>/g, ''
            ).replace(
                /<button[^>]*onclick="rejectSite[^>]*>.*?<\/button>/g, ''
            );
        }
    }
}

// Grant edit permission function
function grantEditPermission(siteId) {
    if (confirm('Are you sure you want to grant edit permission to the faculty for this site?')) {
        fetch(`/admin/tc-lms-admin/${siteId}/grant-edit-permission`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                updateEditPermissionButton(siteId, true);
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error granting edit permission:', error);
            showAlert('Error granting edit permission. Please try again.', 'danger');
        });
    }
}

// Revoke edit permission function
function revokeEditPermission(siteId) {
    if (confirm('Are you sure you want to revoke edit permission from the faculty for this site?')) {
        fetch(`/admin/tc-lms-admin/${siteId}/revoke-edit-permission`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                updateEditPermissionButton(siteId, false);
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error revoking edit permission:', error);
            showAlert('Error revoking edit permission. Please try again.', 'danger');
        });
    }
}

// Update edit permission button in UI
function updateEditPermissionButton(siteId, hasPermission) {
    const row = document.getElementById(`site-row-${siteId}`);
    if (row) {
        const actionsCell = row.querySelector('td:last-child');
        if (actionsCell) {
            // Find the edit permission button and update it
            const editPermissionBtn = actionsCell.querySelector('button[onclick*="Edit Permission"]');
            if (editPermissionBtn) {
                if (hasPermission) {
                    editPermissionBtn.innerHTML = '<i class="bi bi-lock"></i>';
                    editPermissionBtn.className = 'btn btn-sm btn-outline-warning';
                    editPermissionBtn.setAttribute('onclick', `revokeEditPermission(${siteId})`);
                    editPermissionBtn.setAttribute('title', 'Revoke Edit Permission from Faculty');
                } else {
                    editPermissionBtn.innerHTML = '<i class="bi bi-unlock"></i>';
                    editPermissionBtn.className = 'btn btn-sm btn-outline-info';
                    editPermissionBtn.setAttribute('onclick', `grantEditPermission(${siteId})`);
                    editPermissionBtn.setAttribute('title', 'Grant Edit Permission to Faculty');
                }
            }
        }
    }
}

// Export to CSV function
function exportToCSV() {
    // This would implement CSV export functionality
    showAlert('CSV export feature will be implemented soon.', 'info');
}

// Filter form auto-submit on change
document.getElementById('status').addEventListener('change', function() {
    document.getElementById('filterForm').submit();
});

document.getElementById('department').addEventListener('change', function() {
    document.getElementById('filterForm').submit();
});
</script>
@endpush
@endsection