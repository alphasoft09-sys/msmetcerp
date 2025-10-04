@extends('admin.layout')

@section('title', 'Student Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="bi bi-people-fill me-2"></i>
                        Student Management
                    </h1>
                    <p class="text-muted">Manage students for TC: <strong>{{ $tcCode }}</strong></p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.students.upload') }}" class="btn btn-success">
                        <i class="bi bi-upload me-2"></i>
                        Upload Excel/CSV
                    </a>
                    <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        Add Student
                    </a>
                </div>
            </div>

            @if(!$tableExists)
                <div class="alert alert-warning" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Student table not found!</strong> The student table for this TC does not exist. Please contact the administrator to create it.
                </div>
            @else
                <!-- Search and Filter Section -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.students.index') }}" class="row g-3">
                            <div class="col-md-6">
                                <label for="search" class="form-label">Search Students</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="search" name="search" 
                                           value="{{ $search }}" placeholder="Search by name, roll number, or reference number...">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="per_page" class="form-label">Per Page</label>
                                <select class="form-select" id="per_page" name="per_page">
                                    <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bi bi-funnel me-1"></i>
                                    Filter
                                </button>
                                <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-clockwise me-1"></i>
                                    Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Students Table -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-table me-2"></i>
                                Students List
                                @if($search)
                                    <span class="badge bg-info ms-2">Filtered</span>
                                @endif
                            </h5>
                            <div class="text-muted">
                                Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} 
                                of {{ $students->total() }} students
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 60px;">#</th>
                                        <th>Photo</th>
                                        <th class="sortable" data-sort="Name">
                                            Name
                                            <i class="bi bi-arrow-down-up sort-icon"></i>
                                        </th>
                                        <th class="sortable" data-sort="RollNo">
                                            Roll No
                                            <i class="bi bi-arrow-down-up sort-icon"></i>
                                        </th>
                                        <th class="sortable" data-sort="RefNo">
                                            Ref No
                                            <i class="bi bi-arrow-down-up sort-icon"></i>
                                        </th>
                                        <th class="sortable" data-sort="ProgName">
                                            Program
                                            <i class="bi bi-arrow-down-up sort-icon"></i>
                                        </th>
                                        <th class="sortable" data-sort="MobileNo">
                                            Contact
                                            <i class="bi bi-arrow-down-up sort-icon"></i>
                                        </th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($students as $index => $student)
                                        <tr>
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ $students->firstItem() + $index }}</span>
                                            </td>
                                            <td>
                                                @if($student->Photo)
                                                    <img src="{{ route('images.students', ['filename' => basename($student->Photo)]) }}" 
                                                         alt="Student Photo" 
                                                         class="rounded-circle" 
                                                         width="40" height="40">
                                                @else
                                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="bi bi-person text-white"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $student->Name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $student->FatherName }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $student->RollNo }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $student->RefNo }}</span>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $student->ProgName }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $student->EducationName }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <i class="bi bi-telephone me-1"></i>
                                                    {{ $student->MobileNo }}
                                                    @if($student->Email)
                                                        <br>
                                                        <i class="bi bi-envelope me-1"></i>
                                                        <small>{{ $student->Email }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($student->Email)
                                                    <span class="badge bg-success">Has Login</span>
                                                @else
                                                    <span class="badge bg-warning">No Login</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                            onclick="viewStudent({{ $student->id }})"
                                                            title="View Details">
                                                        View
                                                    </button>
                                                   
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="bi bi-inbox display-4"></i>
                                                    <p class="mt-2">No students found</p>
                                                    @if($search)
                                                        <p>Try adjusting your search criteria</p>
                                                    @else
                                                        <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
                                                            <i class="bi bi-plus-circle me-2"></i>
                                                            Add First Student
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    @if($students->hasPages())
                        <div class="card-footer">
                            <div class="pagination-with-results">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="pagination-stats">
                                        <i class="bi bi-info-circle me-2"></i>
                                        Showing <strong>{{ $students->firstItem() ?? 0 }}</strong> to <strong>{{ $students->lastItem() ?? 0 }}</strong> 
                                        of <strong>{{ $students->total() }}</strong> students
                                    </div>
                                    <div class="pagination-wrapper">
                                        @if($students->hasPages())
                                            <nav aria-label="Students pagination">
                                                <ul class="pagination pagination-sm mb-0">
                                                    {{-- Previous Page Link --}}
                                                    @if($students->onFirstPage())
                                                        <li class="page-item disabled">
                                                            <span class="page-link">
                                                                <i class="bi bi-chevron-left"></i>
                                                            </span>
                                                        </li>
                                                    @else
                                                        <li class="page-item">
                                                            <a class="page-link" href="javascript:void(0)" data-page="{{ $students->currentPage() - 1 }}" aria-label="Previous">
                                                                <i class="bi bi-chevron-left"></i>
                                                            </a>
                                                        </li>
                                                    @endif

                                                    {{-- Pagination Elements --}}
                                                    @foreach($students->getUrlRange(1, $students->lastPage()) as $page => $url)
                                                        @if($page == $students->currentPage())
                                                            <li class="page-item active">
                                                                <span class="page-link">{{ $page }}</span>
                                                            </li>
                                                        @else
                                                            <li class="page-item">
                                                                <a class="page-link" href="javascript:void(0)" data-page="{{ $page }}">{{ $page }}</a>
                                                            </li>
                                                        @endif
                                                    @endforeach

                                                    {{-- Next Page Link --}}
                                                    @if($students->hasMorePages())
                                                        <li class="page-item">
                                                            <a class="page-link" href="javascript:void(0)" data-page="{{ $students->currentPage() + 1 }}" aria-label="Next">
                                                                <i class="bi bi-chevron-right"></i>
                                                            </a>
                                                        </li>
                                                    @else
                                                        <li class="page-item disabled">
                                                            <span class="page-link">
                                                                <i class="bi bi-chevron-right"></i>
                                                            </span>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </nav>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Student Details Modal -->
<div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentModalLabel">Student Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="studentModalBody">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editStudentBtn">Edit Student</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the student <strong id="deleteStudentName"></strong>?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete Student</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentStudentId = null;

function viewStudent(studentId) {
    currentStudentId = studentId;
    
    // Show loading
    document.getElementById('studentModalBody').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading student details...</p>
        </div>
    `;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('studentModal'));
    modal.show();
    
    // Load student details via fetch
    fetch(`/admin/students/${studentId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load student details');
            }
            return response.text();
        })
        .then(html => {
            document.getElementById('studentModalBody').innerHTML = html;
            document.getElementById('editStudentBtn').href = `/admin/students/${studentId}/edit`;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('studentModalBody').innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Failed to load student details. Please try again.
                </div>
            `;
        });
}

function deleteStudent(studentId, studentName) {
    currentStudentId = studentId;
    document.getElementById('deleteStudentName').textContent = studentName;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (!currentStudentId) return;
    
    const btn = this;
    const originalText = btn.textContent;
    
    btn.disabled = true;
    btn.innerHTML = `
        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
        Deleting...
    `;
    
    fetch(`/admin/students/${currentStudentId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
            modal.hide();
            showAlert('success', 'Student deleted successfully');
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showAlert('error', data.message || 'Failed to delete student');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Failed to delete student');
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = originalText;
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
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
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

// Auto-submit form when per_page changes
document.getElementById('per_page').addEventListener('change', function() {
    this.closest('form').submit();
});

// Pagination Links
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.pagination .page-link[data-page]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const page = this.getAttribute('data-page');
            
            // Get current URL and update page parameter
            const url = new URL(window.location);
            url.searchParams.set('page', page);
            
            // Navigate to the new URL
            window.location.href = url.toString();
        });
    });
});
</script>
@endpush 