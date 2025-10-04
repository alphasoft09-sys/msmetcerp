@extends('admin.layout')

@section('title', 'TC Management')

@section('content')
<style>
.sortable {
    cursor: pointer;
    user-select: none;
    transition: background-color 0.2s;
}

.sortable:hover {
    background-color: rgba(255, 255, 255, 0.1) !important;
}

.sortable i {
    transition: all 0.2s;
}

.btn-group .btn {
    border-radius: 0.375rem !important;
    margin: 0 1px;
}

.btn-group .btn:first-child {
    border-top-left-radius: 0.375rem !important;
    border-bottom-left-radius: 0.375rem !important;
}

.btn-group .btn:last-child {
    border-top-right-radius: 0.375rem !important;
    border-bottom-right-radius: 0.375rem !important;
}

.d-flex.gap-1 .btn {
    border-radius: 0.375rem !important;
}

.table-responsive {
    border-radius: 0.5rem;
    overflow: hidden;
}

.avatar-sm {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: white;
}

.serial-number {
    font-weight: 600;
    min-width: 30px;
    text-align: center;
}

.serial-number .badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.5rem;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    align-items: center;
}

.action-buttons .btn {
    min-width: 60px;
    height: auto;
    padding: 0.375rem 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.375rem;
    transition: all 0.2s ease;
    font-size: 0.75rem;
    font-weight: 500;
}

.action-buttons .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-outline-primary:hover {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: white;
}

.btn-outline-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
}

/* Filter loader styles */
.position-relative .form-control,
.position-relative .form-select {
    padding-right: 2.5rem;
}

.position-relative .spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

/* Ensure loaders don't interfere with input */
.position-absolute.top-50.end-0.translate-middle-y {
    pointer-events: none;
    z-index: 1;
}

/* Read-only field styling */
input[readonly] {
    background-color: #f8f9fa !important;
    color: #6c757d !important;
    cursor: not-allowed !important;
    border-color: #dee2e6 !important;
}

input[readonly]:focus {
    box-shadow: none !important;
    border-color: #dee2e6 !important;
}
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-building-add me-2"></i>
                        TC Management
                    </h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTcModal">
                        <i class="bi bi-plus-circle me-1"></i>
                        Add New TC
                    </button>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Assessment Agency Panel</strong> - Manage Training Centers and their administrators.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>

                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="card border-light">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="bi bi-funnel me-2"></i>
                                        Filter Options
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-2">
                                            <label for="nameFilter" class="form-label">Admin Name</label>
                                            <div class="position-relative">
                                                <input type="text" class="form-control" id="nameFilter" placeholder="Search by admin name...">
                                                <div class="position-absolute top-50 end-0 translate-middle-y me-2 d-none" id="nameFilterLoader">
                                                    <div class="spinner-border spinner-border-sm text-muted"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label for="emailFilter" class="form-label">Email</label>
                                            <div class="position-relative">
                                                <input type="text" class="form-control" id="emailFilter" placeholder="Search by email...">
                                                <div class="position-absolute top-50 end-0 translate-middle-y me-2 d-none" id="emailFilterLoader">
                                                    <div class="spinner-border spinner-border-sm text-muted"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label for="tcCodeFilter" class="form-label">TC Code</label>
                                            <div class="position-relative">
                                                <input type="text" class="form-control" id="tcCodeFilter" placeholder="Search by TC code...">
                                                <div class="position-absolute top-50 end-0 translate-middle-y me-2 d-none" id="tcCodeFilterLoader">
                                                    <div class="spinner-border spinner-border-sm text-muted"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label for="shotCodeFilter" class="form-label">Short Code</label>
                                            <div class="position-relative">
                                                <input type="text" class="form-control" id="shotCodeFilter" placeholder="Search by short code...">
                                                <div class="position-absolute top-50 end-0 translate-middle-y me-2 d-none" id="shotCodeFilterLoader">
                                                    <div class="spinner-border spinner-border-sm text-muted"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label for="tcNameFilter" class="form-label">TC Name</label>
                                            <div class="position-relative">
                                                <input type="text" class="form-control" id="tcNameFilter" placeholder="Search by TC name...">
                                                <div class="position-absolute top-50 end-0 translate-middle-y me-2 d-none" id="tcNameFilterLoader">
                                                    <div class="spinner-border spinner-border-sm text-muted"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" id="clearFilters">
                                                <i class="bi bi-x-circle me-1"></i>
                                                Clear Filters
                                            </button>
                                            <span class="ms-2 text-muted small" id="filterStatus">
                                                Showing all {{ count($tcAdmins) }} TC Admins
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover" id="tcTable">
                            <thead class="table-dark">
                                <tr>
                                    <th class="sortable" data-sort="sno" style="width: 80px;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>S.No.</span>
                                            <i class="bi bi-arrow-down-up text-muted"></i>
                                        </div>
                                    </th>
                                    <th class="sortable" data-sort="name">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Admin Name</span>
                                            <i class="bi bi-arrow-down-up text-muted"></i>
                                        </div>
                                    </th>
                                    <th class="sortable" data-sort="email">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Email</span>
                                            <i class="bi bi-arrow-down-up text-muted"></i>
                                        </div>
                                    </th>
                                    <th class="sortable" data-sort="tcCode">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>TC Code</span>
                                            <i class="bi bi-arrow-down-up text-muted"></i>
                                        </div>
                                    </th>
                                    <th class="sortable" data-sort="shotCode">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Short Code</span>
                                            <i class="bi bi-arrow-down-up text-muted"></i>
                                        </div>
                                    </th>
                                    <th class="sortable" data-sort="tcName">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>TC Name</span>
                                            <i class="bi bi-arrow-down-up text-muted"></i>
                                        </div>
                                    </th>
                                    <th class="sortable" data-sort="tcPhone">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>TC Phone</span>
                                            <i class="bi bi-arrow-down-up text-muted"></i>
                                        </div>
                                    </th>
                                    <th class="sortable" data-sort="created">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Created</span>
                                            <i class="bi bi-arrow-down-up text-muted"></i>
                                        </div>
                                    </th>
                                    <th style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tcAdmins as $index => $tcAdmin)
                                    <tr data-name="{{ strtolower($tcAdmin->name) }}" 
                                        data-email="{{ strtolower($tcAdmin->email) }}" 
                                        data-tcCode="{{ strtolower($tcAdmin->from_tc) }}"
                                        data-shotCode="{{ strtolower($tcAdmin->shot_code) }}"
                                        data-tcName="{{ strtolower($tcAdmin->tc_name ?? '') }}"
                                        data-tcPhone="{{ strtolower($tcAdmin->tc_phone ?? '') }}"
                                        data-created="{{ $tcAdmin->created_at->timestamp }}"
                                        data-sno="{{ $index + 1 }}">
                                        <td class="text-center serial-number">
                                            <span class="badge bg-secondary">{{ $index + 1 }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-3">
                                                    <div class="avatar-title bg-primary rounded-circle">
                                                        {{ strtoupper(substr($tcAdmin->name, 0, 1)) }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $tcAdmin->name }}</h6>
                                                    <small class="text-muted">TC Admin</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $tcAdmin->email }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $tcAdmin->from_tc }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">{{ $tcAdmin->shot_code }}</span>
                                        </td>
                                        <td>{{ $tcAdmin->tc_name ?? 'N/A' }}</td>
                                        <td>{{ $tcAdmin->tc_phone ?? 'N/A' }}</td>
                                        <td>{{ $tcAdmin->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="action-buttons">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-primary edit-tc" 
                                                        data-id="{{ $tcAdmin->id }}"
                                                        data-name="{{ $tcAdmin->name }}"
                                                        data-email="{{ $tcAdmin->email }}"
                                                        data-shotCode="{{ $tcAdmin->shot_code }}"
                                                        data-tcCode="{{ $tcAdmin->from_tc ?: 'TC' . str_pad($tcAdmin->id, 3, '0', STR_PAD_LEFT) }}"
                                                        data-tcName="{{ $tcAdmin->tc_name ?: $tcAdmin->name . ' Training Center' }}"
                                                        data-tcAddress="{{ $tcAdmin->tc_address ?: 'Address for ' . $tcAdmin->name . ' Training Center' }}"
                                                        data-tcPhone="{{ $tcAdmin->tc_phone ?: '+91-98765' . str_pad($tcAdmin->id, 5, '0', STR_PAD_LEFT) }}"
                                                        title="Edit TC">
                                                    Edit
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger delete-tc" 
                                                        data-id="{{ $tcAdmin->id }}"
                                                        data-name="{{ $tcAdmin->name }}"
                                                        title="Delete TC">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="noDataRow">
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="bi bi-building display-4"></i>
                                                <p class="mt-2">No TC Admins found</p>
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTcModal">
                                                    <i class="bi bi-plus-circle me-1"></i>
                                                    Add First TC
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add TC Modal -->
<div class="modal fade" id="addTcModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-building-add me-2"></i>
                    Add New Training Center
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addTcForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="addName" class="form-label">Admin Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="addName" name="name" required>
                            <div class="invalid-feedback" id="addNameError"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="addEmail" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="addEmail" name="email" required>
                            <div class="invalid-feedback" id="addEmailError"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="addTcCode" class="form-label">TC Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="addTcCode" name="tc_code" required>
                            <div class="invalid-feedback" id="addTcCodeError"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="addShotCode" class="form-label">Short Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="addShotCode" name="shot_code" maxlength="2" placeholder="2 letters only" required>
                            <div class="form-text">Enter exactly 2 letters (e.g., TC, AA, BB). Once assigned, this code cannot be changed or reused.</div>
                            <div class="invalid-feedback" id="addShotCodeError"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="addTcName" class="form-label">TC Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="addTcName" name="tc_name" required>
                            <div class="invalid-feedback" id="addTcNameError"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="addTcPhone" class="form-label">TC Phone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="addTcPhone" name="tc_phone" required>
                            <div class="invalid-feedback" id="addTcPhoneError"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="addTcAddress" class="form-label">TC Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="addTcAddress" name="tc_address" rows="3" required></textarea>
                            <div class="invalid-feedback" id="addTcAddressError"></div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="addTcBtn">
                        <span id="addTcText">Add TC</span>
                        <span id="addTcLoader" class="spinner-border spinner-border-sm d-none ms-2"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit TC Modal -->
<div class="modal fade" id="editTcModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square me-2"></i>
                    Edit Training Center
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editTcForm">
                <input type="hidden" id="editTcId" name="tc_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editName" class="form-label">Admin Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editName" name="name" required>
                            <div class="invalid-feedback" id="editNameError"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editEmail" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                            <div class="invalid-feedback" id="editEmailError"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editShotCode" class="form-label">Short Code</label>
                            <input type="text" class="form-control" id="editShotCode" readonly>
                            <div class="form-text text-muted">Short codes cannot be edited once assigned</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editTcName" class="form-label">TC Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editTcName" name="tc_name" required>
                            <div class="invalid-feedback" id="editTcNameError"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editTcPhone" class="form-label">TC Phone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editTcPhone" name="tc_phone" required>
                            <div class="invalid-feedback" id="editTcPhoneError"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editTcAddress" class="form-label">TC Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="editTcAddress" name="tc_address" rows="3" required></textarea>
                            <div class="invalid-feedback" id="editTcAddressError"></div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="editTcBtn">
                        <span id="editTcText">Update TC</span>
                        <span id="editTcLoader" class="spinner-border spinner-border-sm d-none ms-2"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the TC Admin <strong id="deleteTcName"></strong>?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
                <p class="text-info"><small><i class="bi bi-info-circle me-1"></i>Note: The short code will be preserved and cannot be reused by other TCs.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <span id="deleteTcText">Delete</span>
                    <span id="deleteTcLoader" class="spinner-border spinner-border-sm d-none ms-2"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let deleteTcId = null;
    let currentSort = { column: null, direction: 'asc' };
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const addTcModal = new bootstrap.Modal(document.getElementById('addTcModal'));
    const editTcModal = new bootstrap.Modal(document.getElementById('editTcModal'));
    
    // Filter functionality
    const nameFilter = document.getElementById('nameFilter');
    const emailFilter = document.getElementById('emailFilter');
    const tcCodeFilter = document.getElementById('tcCodeFilter');
    const shotCodeFilter = document.getElementById('shotCodeFilter');
    const tcNameFilter = document.getElementById('tcNameFilter');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const filterStatus = document.getElementById('filterStatus');
    const noDataRow = document.getElementById('noDataRow');
    
    // Function to get current table rows
    function getTableRows() {
        return document.querySelectorAll('#tcTable tbody tr:not(#noDataRow)');
    }
    
    // Filter loaders
    const nameFilterLoader = document.getElementById('nameFilterLoader');
    const emailFilterLoader = document.getElementById('emailFilterLoader');
    const tcCodeFilterLoader = document.getElementById('tcCodeFilterLoader');
    const shotCodeFilterLoader = document.getElementById('shotCodeFilterLoader');
    const tcNameFilterLoader = document.getElementById('tcNameFilterLoader');
    
    // Show/hide filter loader
    function showFilterLoader(loader, show) {
        if (loader) {
            if (show) {
                loader.classList.remove('d-none');
            } else {
                loader.classList.add('d-none');
            }
        }
    }
    
    // Sorting functionality
    document.querySelectorAll('.sortable').forEach(header => {
        header.addEventListener('click', function() {
            const column = this.dataset.sort;
            const direction = currentSort.column === column && currentSort.direction === 'asc' ? 'desc' : 'asc';
            
            // Update sort state
            currentSort = { column, direction };
            
            // Update sort indicators
            updateSortIndicators(column, direction);
            
            // Sort table
            sortTable(column, direction);
        });
    });
    
    function updateSortIndicators(activeColumn, direction) {
        // Reset all indicators
        document.querySelectorAll('.sortable i').forEach(icon => {
            icon.className = 'bi bi-arrow-down-up text-muted';
        });
        
        // Set active indicator
        const activeHeader = document.querySelector(`[data-sort="${activeColumn}"] i`);
        if (activeHeader) {
            activeHeader.className = direction === 'asc' 
                ? 'bi bi-arrow-up text-white' 
                : 'bi bi-arrow-down text-white';
        }
    }
    
    function sortTable(column, direction) {
        const tbody = document.querySelector('#tcTable tbody');
        const rows = Array.from(tbody.querySelectorAll('tr:not(#noDataRow)'));
        
        rows.sort((a, b) => {
            let aValue, bValue;
            
            switch(column) {
                case 'sno':
                    aValue = parseInt(a.dataset.sno);
                    bValue = parseInt(b.dataset.sno);
                    break;
                case 'name':
                    aValue = a.querySelector('h6').textContent.toLowerCase();
                    bValue = b.querySelector('h6').textContent.toLowerCase();
                    break;
                case 'email':
                    aValue = a.cells[2].textContent.toLowerCase();
                    bValue = b.cells[2].textContent.toLowerCase();
                    break;
                case 'tcCode':
                    aValue = a.cells[3].textContent.toLowerCase();
                    bValue = b.cells[3].textContent.toLowerCase();
                    break;
                case 'shotCode':
                    aValue = a.cells[4].textContent.toLowerCase();
                    bValue = b.cells[4].textContent.toLowerCase();
                    break;
                case 'tcName':
                    aValue = a.cells[5].textContent.toLowerCase(); // Updated index for TC Name
                    bValue = b.cells[5].textContent.toLowerCase();
                    break;
                case 'tcPhone':
                    aValue = a.cells[6].textContent.toLowerCase(); // Updated index for TC Phone
                    bValue = b.cells[6].textContent.toLowerCase();
                    break;
                case 'created':
                    aValue = parseInt(a.dataset.created);
                    bValue = parseInt(b.dataset.created);
                    break;
                default:
                    return 0;
            }
            
            if (aValue < bValue) return direction === 'asc' ? -1 : 1;
            if (aValue > bValue) return direction === 'asc' ? 1 : -1;
            return 0;
        });
        
        // Re-append sorted rows and update serial numbers
        rows.forEach((row, index) => {
            tbody.appendChild(row);
            // Update the serial number badge
            const snoBadge = row.querySelector('td:first-child .badge');
            if (snoBadge) {
                snoBadge.textContent = index + 1;
            }
            // Update the data attribute
            row.dataset.sno = index + 1;
        });
        
        // Update filter status
        updateFilterStatus();
    }
    
    function filterTable() {
        const nameValue = nameFilter.value.toLowerCase().trim();
        const emailValue = emailFilter.value.toLowerCase().trim();
        const tcCodeValue = tcCodeFilter.value.toLowerCase().trim();
        const shotCodeValue = shotCodeFilter.value.toLowerCase().trim();
        const tcNameValue = tcNameFilter.value.toLowerCase().trim();
        
        const tableRows = getTableRows();
        
        let visibleCount = 0;
        
        tableRows.forEach((row, index) => {
            // Get data from both data attributes and cell content for better reliability
            const name = (row.dataset.name || row.querySelector('h6')?.textContent || '').toLowerCase();
            const email = (row.dataset.email || row.cells[2]?.textContent || '').toLowerCase();
            const tcCode = (row.dataset.tcCode || row.cells[3]?.querySelector('.badge')?.textContent || row.cells[3]?.textContent || '').toLowerCase();
            const shotCode = (row.dataset.shotCode || row.cells[4]?.querySelector('.badge')?.textContent || row.cells[4]?.textContent || '').toLowerCase();
            const tcName = (row.dataset.tcName || row.cells[5]?.textContent || '').toLowerCase(); // Updated index for TC Name
            
            const nameMatch = nameValue === '' || name.includes(nameValue);
            const emailMatch = emailValue === '' || email.includes(emailValue);
            const tcCodeMatch = tcCodeValue === '' || tcCode.includes(tcCodeValue);
            const shotCodeMatch = shotCodeValue === '' || shotCode.includes(shotCodeValue);
            const tcNameMatch = tcNameValue === '' || tcName.includes(tcNameValue);
            
            if (nameMatch && emailMatch && tcCodeMatch && shotCodeMatch && tcNameMatch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Show/hide no data row
        if (noDataRow) {
            if (visibleCount === 0) {
                noDataRow.style.display = '';
                const noDataText = noDataRow.querySelector('p');
                if (noDataText) {
                    noDataText.textContent = 'No TC Admins match the current filters';
                }
            } else {
                noDataRow.style.display = 'none';
            }
        }
        
        // Update filter status
        updateFilterStatus(visibleCount, tableRows.length);
    }
    
    function updateFilterStatus(visibleCount = null, totalCount = null) {
        if (visibleCount === null) {
            const tableRows = getTableRows();
            visibleCount = Array.from(tableRows).filter(row => row.style.display !== 'none').length;
            totalCount = tableRows.length;
        }
        filterStatus.textContent = `Showing ${visibleCount} of ${totalCount} TC Admins`;
    }
    
    // Add event listeners for filters with loaders
    nameFilter.addEventListener('input', function() {
        showFilterLoader(nameFilterLoader, true);
        setTimeout(() => {
            filterTable();
            showFilterLoader(nameFilterLoader, false);
        }, 300);
    });
    
    emailFilter.addEventListener('input', function() {
        showFilterLoader(emailFilterLoader, true);
        setTimeout(() => {
            filterTable();
            showFilterLoader(emailFilterLoader, false);
        }, 300);
    });
    
    tcCodeFilter.addEventListener('input', function() {
        showFilterLoader(tcCodeFilterLoader, true);
        setTimeout(() => {
            filterTable();
            showFilterLoader(tcCodeFilterLoader, false);
        }, 300);
    });
    
    shotCodeFilter.addEventListener('input', function() {
        showFilterLoader(shotCodeFilterLoader, true);
        setTimeout(() => {
            filterTable();
            showFilterLoader(shotCodeFilterLoader, false);
        }, 300);
    });
    
    tcNameFilter.addEventListener('input', function() {
        showFilterLoader(tcNameFilterLoader, true);
        setTimeout(() => {
            filterTable();
            showFilterLoader(tcNameFilterLoader, false);
        }, 300);
    });
    
    // Clear filters
    clearFiltersBtn.addEventListener('click', function() {
        // Show loaders briefly
        showFilterLoader(nameFilterLoader, true);
        showFilterLoader(emailFilterLoader, true);
        showFilterLoader(tcCodeFilterLoader, true);
        showFilterLoader(shotCodeFilterLoader, true);
        showFilterLoader(tcNameFilterLoader, true);
        
        nameFilter.value = '';
        emailFilter.value = '';
        tcCodeFilter.value = '';
        shotCodeFilter.value = '';
        tcNameFilter.value = '';
        
        setTimeout(() => {
            filterTable();
            // Hide all loaders
            showFilterLoader(nameFilterLoader, false);
            showFilterLoader(emailFilterLoader, false);
            showFilterLoader(tcCodeFilterLoader, false);
            showFilterLoader(shotCodeFilterLoader, false);
            showFilterLoader(tcNameFilterLoader, false);
        }, 300);
    });

    // Add TC Form
    document.getElementById('addTcForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const addBtn = document.getElementById('addTcBtn');
        const addText = document.getElementById('addTcText');
        const addLoader = document.getElementById('addTcLoader');
        
        // Clear previous errors
        clearFormErrors('add');
        
        // Client-side validation for shot_code
        const shotCode = formData.get('shot_code');
        if (shotCode && shotCode.length !== 2) {
            const shotCodeInput = document.getElementById('addShotCode');
            const shotCodeError = document.getElementById('addShotCodeError');
            shotCodeInput.classList.add('is-invalid');
            shotCodeError.textContent = 'Short code must be exactly 2 characters long';
            return;
        }
        
        // Convert shot_code to uppercase
        if (shotCode) {
            formData.set('shot_code', shotCode.toUpperCase());
        }
        
        // Show loader
        setButtonLoading(addBtn, addText, addLoader, true);
        
        fetch('{{ route("admin.tc-management.store") }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message and reload page
                showAlert('success', data.message);
                addTcModal.hide();
                this.reset();
                location.reload();
            } else {
                // Show validation errors
                if (data.errors) {
                    console.log('Validation errors received:', data.errors);
                    Object.keys(data.errors).forEach(field => {
                        // Convert field name to proper ID format (e.g., tc_code -> addTcCode)
                        const fieldId = 'add' + field.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join('');
                        const input = document.getElementById(fieldId);
                        const errorDiv = document.getElementById(fieldId + 'Error');
                        
                        console.log(`Field: ${field}, FieldId: ${fieldId}, Input found: ${!!input}, ErrorDiv found: ${!!errorDiv}`);
                        
                        if (input && errorDiv) {
                            input.classList.add('is-invalid');
                            errorDiv.textContent = data.errors[field][0];
                            console.log(`Error set for ${fieldId}: ${data.errors[field][0]}`);
                        } else {
                            console.warn(`Could not find elements for field: ${field} (${fieldId})`);
                        }
                    });
                } else {
                    showAlert('error', data.message || 'An error occurred');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'An error occurred. Please try again.');
        })
        .finally(() => {
            setButtonLoading(addBtn, addText, addLoader, false);
        });
    });

    // Edit TC Form
    document.getElementById('editTcForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const tcId = document.getElementById('editTcId').value;
        const formData = new FormData(this);
        formData.append('_method', 'PUT');
        const editBtn = document.getElementById('editTcBtn');
        const editText = document.getElementById('editTcText');
        const editLoader = document.getElementById('editTcLoader');
        
        // Debug: Log form data
        console.log('Form submission - TC ID:', tcId);
        console.log('Form data entries:');
        for (let [key, value] of formData.entries()) {
            console.log(`  ${key}: ${value}`);
        }
        
        // Clear previous errors
        clearFormErrors('edit');
        
        // Show loader
        setButtonLoading(editBtn, editText, editLoader, true);
        
        fetch(`/admin/tc-management/${tcId}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                // Show success message and reload page
                showAlert('success', data.message);
                editTcModal.hide();
                location.reload();
            } else {
                // Show validation errors
                if (data.errors) {
                    console.log('Validation errors received:', data.errors);
                    Object.keys(data.errors).forEach(field => {
                        // Convert field name to proper ID format (e.g., tc_code -> editTcCode)
                        const fieldId = 'edit' + field.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join('');
                        const input = document.getElementById(fieldId);
                        const errorDiv = document.getElementById(fieldId + 'Error');
                        
                        console.log(`Field: ${field}, FieldId: ${fieldId}, Input found: ${!!input}, ErrorDiv found: ${!!errorDiv}`);
                        
                        if (input && errorDiv) {
                            input.classList.add('is-invalid');
                            errorDiv.textContent = data.errors[field][0];
                            console.log(`Error set for ${fieldId}: ${data.errors[field][0]}`);
                        } else {
                            console.warn(`Could not find elements for field: ${field} (${fieldId})`);
                        }
                    });
                } else {
                    showAlert('error', data.message || 'An error occurred');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'An error occurred. Please try again.');
        })
        .finally(() => {
            setButtonLoading(editBtn, editText, editLoader, false);
        });
    });

    // Handle edit button clicks
    document.querySelectorAll('.edit-tc').forEach(button => {
        button.addEventListener('click', function() {
            const tcId = this.dataset.id;
            const name = this.dataset.name;
            const email = this.dataset.email;
            const tcName = this.dataset.tcName;
            const tcAddress = this.dataset.tcAddress;
            const tcPhone = this.dataset.tcPhone;
            
            // Debug: Log all data attributes
            console.log('Edit button clicked with data:', {
                tcId, name, email, tcName, tcAddress, tcPhone
            });
            console.log('All data attributes:', this.dataset);
            
            // Get form elements
            const editTcId = document.getElementById('editTcId');
            const editName = document.getElementById('editName');
            const editEmail = document.getElementById('editEmail');
            const editShotCode = document.getElementById('editShotCode');
            const editTcName = document.getElementById('editTcName');
            const editTcAddress = document.getElementById('editTcAddress');
            const editTcPhone = document.getElementById('editTcPhone');
            
            // Debug: Check if elements exist
            console.log('Form elements found:', {
                editTcId: !!editTcId,
                editName: !!editName,
                editEmail: !!editEmail,
                editShotCode: !!editShotCode,
                editTcName: !!editTcName,
                editTcAddress: !!editTcAddress,
                editTcPhone: !!editTcPhone
            });
            
            // Fallback: Get data from table cells if data attributes are empty
            const row = this.closest('tr');
            const fallbackName = name || row.querySelector('h6')?.textContent || '';
            const fallbackEmail = email || row.cells[2]?.textContent || '';
            const fallbackTcName = tcName || row.cells[5]?.textContent || ''; // Updated index for TC Name
            const fallbackTcPhone = tcPhone || row.cells[6]?.textContent || ''; // Updated index for TC Phone
            
            // Populate edit form with fallback values
            if (editTcId) editTcId.value = tcId || '';
            if (editName) editName.value = fallbackName;
            if (editEmail) editEmail.value = fallbackEmail;
            if (editShotCode) editShotCode.value = this.dataset.shotCode || 'N/A';
            if (editTcName) editTcName.value = fallbackTcName;
            if (editTcAddress) editTcAddress.value = tcAddress || 'Address for ' + fallbackName;
            if (editTcPhone) editTcPhone.value = fallbackTcPhone;
            
            // Debug: Log the values after setting
            console.log('Values set in form:', {
                tcId: editTcId ? editTcId.value : 'element not found',
                name: editName ? editName.value : 'element not found',
                email: editEmail ? editEmail.value : 'element not found',
                shotCode: editShotCode ? editShotCode.value : 'element not found',
                tcName: editTcName ? editTcName.value : 'element not found',
                tcAddress: editTcAddress ? editTcAddress.value : 'element not found',
                tcPhone: editTcPhone ? editTcPhone.value : 'element not found'
            });
            
            // Clear previous errors
            clearFormErrors('edit');
            
            // Show modal
            editTcModal.show();
        });
    });

    // Handle delete button clicks
    document.querySelectorAll('.delete-tc').forEach(button => {
        button.addEventListener('click', function() {
            deleteTcId = this.dataset.id;
            document.getElementById('deleteTcName').textContent = this.dataset.name;
            deleteModal.show();
        });
    });

    // Handle confirm delete
    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (!deleteTcId) return;

        const deleteBtn = this;
        const deleteText = document.getElementById('deleteTcText');
        const deleteLoader = document.getElementById('deleteTcLoader');

        setButtonLoading(deleteBtn, deleteText, deleteLoader, true);

        fetch(`/admin/tc-management/${deleteTcId}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message and reload page
                showAlert('success', data.message);
                deleteModal.hide();
                location.reload();
            } else {
                showAlert('error', data.message || 'An error occurred');
                deleteModal.hide();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'An error occurred. Please try again.');
            deleteModal.hide();
        })
        .finally(() => {
            setButtonLoading(deleteBtn, deleteText, deleteLoader, false);
        });
    });

    // Helper functions
    function setButtonLoading(button, textElement, loaderElement, loading) {
        if (loading) {
            button.disabled = true;
            textElement.textContent = textElement.textContent.includes('Add') ? 'Adding...' : 
                                    textElement.textContent.includes('Update') ? 'Updating...' : 'Deleting...';
            loaderElement.classList.remove('d-none');
        } else {
            button.disabled = false;
            textElement.textContent = textElement.textContent.includes('Adding') ? 'Add TC' : 
                                    textElement.textContent.includes('Updating') ? 'Update TC' : 'Delete';
            loaderElement.classList.add('d-none');
        }
    }

    function clearFormErrors(prefix) {
        const modal = document.getElementById(`${prefix}TcModal`);
        if (!modal) return;
        
        // Clear validation states from all form inputs
        const inputs = modal.querySelectorAll('.form-control, .form-select');
        inputs.forEach(input => {
            input.classList.remove('is-invalid');
            input.classList.remove('is-valid');
        });
        
        // Clear error messages
        const errorDivs = modal.querySelectorAll('.invalid-feedback');
        errorDivs.forEach(div => {
            div.textContent = '';
        });
        
        // Clear success messages
        const successDivs = modal.querySelectorAll('.valid-feedback');
        successDivs.forEach(div => {
            div.textContent = '';
        });
    }

    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const iconClass = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
        
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="bi ${iconClass} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // Insert alert at the top of the card body
        const cardBody = document.querySelector('.card-body');
        cardBody.insertAdjacentHTML('afterbegin', alertHtml);
        
        // Auto-remove alert after 5 seconds
        setTimeout(() => {
            const alert = cardBody.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }
});
</script>
@endpush 