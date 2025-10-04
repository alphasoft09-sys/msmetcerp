@extends('admin.layout')

@section('title', 'Admin Management')

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
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-people me-2"></i>
                        Admin Management
                    </h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                        <i class="bi bi-plus-circle me-1"></i>
                        Add New Admin
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
                        <strong>TC Code: {{ $user->from_tc }}</strong> - You can only view and manage admins from your training center.
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
                                            <label for="nameFilter" class="form-label">Name</label>
                                            <div class="position-relative">
                                                <input type="text" class="form-control" id="nameFilter" placeholder="Search by name...">
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
                                            <label for="roleFilter" class="form-label">Role</label>
                                            <div class="position-relative">
                                                <select class="form-select" id="roleFilter">
                                                    <option value="">All Roles</option>
                                                    @php
                                                        $user = Auth::user();
                                                        $allowedRoles = [];
                                                        if ($user->user_role === 1) { // TC Admin
                                                            $allowedRoles = [2, 3, 5]; // TC Head, Exam Cell, TC Faculty (removed Assessment Agency)
                                                        } elseif ($user->user_role === 2) { // TC Head
                                                            $allowedRoles = [3, 5]; // Exam Cell, TC Faculty only
                                                        }
                                                        
                                                        // Check if TC Head already exists
                                                        $existingTcHead = \App\Models\User::where('user_role', 2)
                                                            ->where('from_tc', $user->from_tc)
                                                            ->first();
                                                    @endphp
                                                    @foreach(\App\Http\Controllers\AdminManagementController::getRoleNames() as $roleId => $roleName)
                                                        @if(in_array($roleId, $allowedRoles))
                                                            @if($roleId == 2 && $existingTcHead)
                                                                {{-- Skip TC Head if one already exists --}}
                                                            @else
                                                                <option value="{{ $roleName }}">{{ $roleName }}</option>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </select>
                                                <div class="position-absolute top-50 end-0 translate-middle-y me-2 d-none" id="roleFilterLoader">
                                                    <div class="spinner-border spinner-border-sm text-muted"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label for="tcFilter" class="form-label">TC Code</label>
                                            <div class="position-relative">
                                                <input type="text" class="form-control" id="tcFilter" placeholder="Search by TC code..." value="{{ $user->from_tc }}" readonly>
                                                <div class="position-absolute top-50 end-0 translate-middle-y me-2 d-none" id="tcFilterLoader">
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
                                                Showing all {{ count($admins) }} admins
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover" id="adminTable">
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
                                            <span>Name</span>
                                            <i class="bi bi-arrow-down-up text-muted"></i>
                                        </div>
                                    </th>
                                    <th class="sortable" data-sort="email">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Email</span>
                                            <i class="bi bi-arrow-down-up text-muted"></i>
                                        </div>
                                    </th>
                                    <th class="sortable" data-sort="role">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Role</span>
                                            <i class="bi bi-arrow-down-up text-muted"></i>
                                        </div>
                                    </th>
                                    <th class="sortable" data-sort="tc">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>TC Code</span>
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
                                @forelse($admins as $index => $admin)
                                    <tr data-name="{{ strtolower($admin->name) }}" 
                                        data-email="{{ strtolower($admin->email) }}" 
                                        data-role="{{ \App\Http\Controllers\AdminManagementController::getRoleNames()[$admin->user_role] }}"
                                        data-tc="{{ strtolower($admin->from_tc) }}"
                                        data-created="{{ $admin->created_at->timestamp }}"
                                        data-sno="{{ $index + 1 }}">
                                        <td class="text-center serial-number">
                                            <span class="badge bg-secondary">{{ $index + 1 }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-3">
                                                    <div class="avatar-title bg-primary rounded-circle">
                                                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $admin->name }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $admin->email }}</td>
                                        <td>
                                            <span class="badge bg-{{ $admin->user_role == 2 ? 'success' : ($admin->user_role == 3 ? 'info' : ($admin->user_role == 4 ? 'warning' : 'secondary')) }}">
                                                {{ \App\Http\Controllers\AdminManagementController::getRoleNames()[$admin->user_role] }}
                                            </span>
                                        </td>
                                        <td>{{ $admin->from_tc }}</td>
                                        <td>{{ $admin->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="action-buttons">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-primary edit-admin" 
                                                        data-id="{{ $admin->id }}"
                                                        data-name="{{ $admin->name }}"
                                                        data-email="{{ $admin->email }}"
                                                        data-role="{{ $admin->user_role }}"
                                                        data-tc="{{ $admin->from_tc }}"
                                                        title="Edit Admin">
                                                    Edit
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger delete-admin" 
                                                        data-id="{{ $admin->id }}"
                                                        data-name="{{ $admin->name }}"
                                                        title="Delete Admin">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="noDataRow">
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="bi bi-people display-4"></i>
                                                <p class="mt-2">No admin users found</p>
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                                                    <i class="bi bi-plus-circle me-1"></i>
                                                    Add First Admin
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

<!-- Add Admin Modal -->
<div class="modal fade" id="addAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus me-2"></i>
                    Add New Admin
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addAdminForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="addName" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="addName" name="name" required>
                        <div class="invalid-feedback" id="addNameError"></div>
                    </div>
                    <div class="mb-3">
                        <label for="addEmail" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="addEmail" name="email" required>
                        <div class="invalid-feedback" id="addEmailError"></div>
                    </div>
                    <div class="mb-3">
                        <label for="addRole" class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select" id="addRole" name="user_role" required>
                            <option value="">Select Role</option>
                            @php
                                $user = Auth::user();
                                $allowedRoles = [];
                                if ($user->user_role === 1) { // TC Admin
                                    $allowedRoles = [2, 3, 5]; // TC Head, Exam Cell, TC Faculty (removed Assessment Agency)
                                } elseif ($user->user_role === 2) { // TC Head
                                    $allowedRoles = [3, 5]; // Exam Cell, TC Faculty only
                                }
                                
                                // Check if TC Head already exists
                                $existingTcHead = \App\Models\User::where('user_role', 2)
                                    ->where('from_tc', $user->from_tc)
                                    ->first();
                            @endphp
                            @foreach(\App\Http\Controllers\AdminManagementController::getRoleNames() as $roleId => $roleName)
                                @if(in_array($roleId, $allowedRoles))
                                    @if($roleId == 2 && $existingTcHead)
                                        {{-- Skip TC Head if one already exists --}}
                                    @else
                                        <option value="{{ $roleId }}">{{ $roleName }}</option>
                                    @endif
                                @endif
                            @endforeach
                        </select>
                        <div class="invalid-feedback" id="addRoleError"></div>
                        @if($existingTcHead && $user->user_role === 1)
                            <small class="text-muted">Note: TC Head already exists for this training center.</small>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="addTc" class="form-label">TC Code</label>
                        <input type="text" class="form-control" id="addTc" name="from_tc" value="{{ $user->from_tc }}" readonly>
                        <small class="text-muted">Automatically set to your training center</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="addAdminBtn">
                        <span id="addAdminText">Add Admin</span>
                        <span id="addAdminLoader" class="spinner-border spinner-border-sm d-none ms-2"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Admin Modal -->
<div class="modal fade" id="editAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square me-2"></i>
                    Edit Admin
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editAdminForm">
                <input type="hidden" id="editAdminId" name="admin_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editName" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editName" name="name" required>
                        <div class="invalid-feedback" id="editNameError"></div>
                    </div>
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                        <div class="invalid-feedback" id="editEmailError"></div>
                    </div>
                    <div class="mb-3">
                        <label for="editRole" class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select" id="editRole" name="user_role" required>
                            <option value="">Select Role</option>
                            @php
                                $user = Auth::user();
                                $allowedRoles = [];
                                if ($user->user_role === 1) { // TC Admin
                                    $allowedRoles = [2, 3, 5]; // TC Head, Exam Cell, TC Faculty (removed Assessment Agency)
                                } elseif ($user->user_role === 2) { // TC Head
                                    $allowedRoles = [3, 5]; // Exam Cell, TC Faculty only
                                }
                                
                                // Check if TC Head already exists
                                $existingTcHead = \App\Models\User::where('user_role', 2)
                                    ->where('from_tc', $user->from_tc)
                                    ->first();
                            @endphp
                            @foreach(\App\Http\Controllers\AdminManagementController::getRoleNames() as $roleId => $roleName)
                                @if(in_array($roleId, $allowedRoles))
                                    @if($roleId == 2 && $existingTcHead)
                                        {{-- Skip TC Head if one already exists --}}
                                    @else
                                        <option value="{{ $roleId }}">{{ $roleName }}</option>
                                    @endif
                                @endif
                            @endforeach
                        </select>
                        <div class="invalid-feedback" id="editRoleError"></div>
                        @if($existingTcHead && $user->user_role === 1)
                            <small class="text-muted">Note: TC Head already exists for this training center.</small>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="editTc" class="form-label">TC Code</label>
                        <input type="text" class="form-control" id="editTc" name="from_tc" value="{{ $user->from_tc }}" readonly>
                        <small class="text-muted">Cannot be changed</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="editAdminBtn">
                        <span id="editAdminText">Update Admin</span>
                        <span id="editAdminLoader" class="spinner-border spinner-border-sm d-none ms-2"></span>
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
                <p>Are you sure you want to delete the admin user <strong id="deleteAdminName"></strong>?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <span id="deleteAdminText">Delete</span>
                    <span id="deleteAdminLoader" class="spinner-border spinner-border-sm d-none ms-2"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let deleteAdminId = null;
    let currentSort = { column: null, direction: 'asc' };
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const addAdminModal = new bootstrap.Modal(document.getElementById('addAdminModal'));
    const editAdminModal = new bootstrap.Modal(document.getElementById('editAdminModal'));
    
    // Filter functionality
    const nameFilter = document.getElementById('nameFilter');
    const emailFilter = document.getElementById('emailFilter');
    const roleFilter = document.getElementById('roleFilter');
    const tcFilter = document.getElementById('tcFilter');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const filterStatus = document.getElementById('filterStatus');
    const noDataRow = document.getElementById('noDataRow');
    
    // Function to get current table rows
    function getTableRows() {
        return document.querySelectorAll('#adminTable tbody tr:not(#noDataRow)');
    }
    
    // Filter loaders
    const nameFilterLoader = document.getElementById('nameFilterLoader');
    const emailFilterLoader = document.getElementById('emailFilterLoader');
    const roleFilterLoader = document.getElementById('roleFilterLoader');
    const tcFilterLoader = document.getElementById('tcFilterLoader');
    
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
        const tbody = document.querySelector('#adminTable tbody');
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
                case 'role':
                    aValue = a.cells[3].textContent.toLowerCase();
                    bValue = b.cells[3].textContent.toLowerCase();
                    break;
                case 'tc':
                    aValue = a.cells[4].textContent.toLowerCase();
                    bValue = b.cells[4].textContent.toLowerCase();
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
        const roleValue = roleFilter.value.toLowerCase().trim();
        const tcValue = tcFilter.value.toLowerCase().trim();
        
        const tableRows = getTableRows();
        console.log('Filtering with:', { nameValue, emailValue, roleValue, tcValue });
        console.log('Total table rows:', tableRows.length);
        
        let visibleCount = 0;
        
        tableRows.forEach((row, index) => {
            const name = (row.dataset.name || '').toLowerCase();
            const email = (row.dataset.email || '').toLowerCase();
            const role = (row.dataset.role || '').toLowerCase();
            const tc = (row.dataset.tc || '').toLowerCase();
            
            console.log(`Row ${index}:`, { name, email, role, tc });
            
            const nameMatch = nameValue === '' || name.includes(nameValue);
            const emailMatch = emailValue === '' || email.includes(emailValue);
            const roleMatch = roleValue === '' || role === roleValue;
            const tcMatch = tcValue === '' || tc.includes(tcValue);
            
            console.log(`Row ${index} matches:`, { nameMatch, emailMatch, roleMatch, tcMatch });
            
            if (nameMatch && emailMatch && roleMatch && tcMatch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        console.log('Visible count:', visibleCount);
        
        // Show/hide no data row
        if (noDataRow) {
            if (visibleCount === 0) {
                noDataRow.style.display = '';
                const noDataText = noDataRow.querySelector('p');
                if (noDataText) {
                    noDataText.textContent = 'No admins match the current filters';
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
        filterStatus.textContent = `Showing ${visibleCount} of ${totalCount} admins`;
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
    
    roleFilter.addEventListener('change', function() {
        showFilterLoader(roleFilterLoader, true);
        setTimeout(() => {
            filterTable();
            showFilterLoader(roleFilterLoader, false);
        }, 300);
    });
    
    tcFilter.addEventListener('input', function() {
        showFilterLoader(tcFilterLoader, true);
        setTimeout(() => {
            filterTable();
            showFilterLoader(tcFilterLoader, false);
        }, 300);
    });
    
    // Clear filters
    clearFiltersBtn.addEventListener('click', function() {
        // Show loaders briefly
        showFilterLoader(nameFilterLoader, true);
        showFilterLoader(emailFilterLoader, true);
        showFilterLoader(roleFilterLoader, true);
        showFilterLoader(tcFilterLoader, true);
        
        nameFilter.value = '';
        emailFilter.value = '';
        roleFilter.value = '';
        tcFilter.value = '{{ $user->from_tc }}'; // Reset to current TC
        
        setTimeout(() => {
            filterTable();
            // Hide all loaders
            showFilterLoader(nameFilterLoader, false);
            showFilterLoader(emailFilterLoader, false);
            showFilterLoader(roleFilterLoader, false);
            showFilterLoader(tcFilterLoader, false);
        }, 300);
    });

    // Add Admin Form
    document.getElementById('addAdminForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const addBtn = document.getElementById('addAdminBtn');
        const addText = document.getElementById('addAdminText');
        const addLoader = document.getElementById('addAdminLoader');
        
        // Clear previous errors
        clearFormErrors('add');
        
        // Show loader
        setButtonLoading(addBtn, addText, addLoader, true);
        
        fetch('{{ route("admin.management.store") }}', {
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
                addAdminModal.hide();
                this.reset();
                location.reload();
            } else {
                // Show validation errors
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const input = document.getElementById('add' + field.charAt(0).toUpperCase() + field.slice(1));
                        const errorDiv = document.getElementById('add' + field.charAt(0).toUpperCase() + field.slice(1) + 'Error');
                        if (input && errorDiv) {
                            input.classList.add('is-invalid');
                            errorDiv.textContent = data.errors[field][0];
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

    // Edit Admin Form
    document.getElementById('editAdminForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const adminId = document.getElementById('editAdminId').value;
        const formData = new FormData(this);
        formData.append('_method', 'PUT'); // Add method spoofing for Laravel
        const editBtn = document.getElementById('editAdminBtn');
        const editText = document.getElementById('editAdminText');
        const editLoader = document.getElementById('editAdminLoader');
        
        // Debug: Log form data
        console.log('Edit form data:');
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }
        
        // Clear previous errors
        clearFormErrors('edit');
        
        // Show loader
        setButtonLoading(editBtn, editText, editLoader, true);
        
        fetch(`/admin/management/${adminId}`, {
            method: 'POST', // Use POST for method spoofing
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
                editAdminModal.hide();
                location.reload();
            } else {
                // Show validation errors
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const input = document.getElementById('edit' + field.charAt(0).toUpperCase() + field.slice(1));
                        const errorDiv = document.getElementById('edit' + field.charAt(0).toUpperCase() + field.slice(1) + 'Error');
                        if (input && errorDiv) {
                            input.classList.add('is-invalid');
                            errorDiv.textContent = data.errors[field][0];
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
    document.querySelectorAll('.edit-admin').forEach(button => {
        button.addEventListener('click', function() {
            const adminId = this.dataset.id;
            const name = this.dataset.name;
            const email = this.dataset.email;
            const role = this.dataset.role;
            const tc = this.dataset.tc;
            
            console.log('Edit button clicked:', { adminId, name, email, role, tc });
            
            // Populate edit form
            document.getElementById('editAdminId').value = adminId;
            document.getElementById('editName').value = name;
            document.getElementById('editEmail').value = email;
            document.getElementById('editTc').value = tc;
            
            // Dynamically populate role dropdown to ensure current role is available
            populateEditRoleDropdown(role);
            
            // Clear previous errors
            clearFormErrors('edit');
            
            // Show modal
            editAdminModal.show();
        });
    });
    
    // Function to populate edit role dropdown
    function populateEditRoleDropdown(currentRole) {
        const roleSelect = document.getElementById('editRole');
        const currentUserRole = {{ Auth::user()->user_role }};
        
        // Clear existing options except the first one
        roleSelect.innerHTML = '<option value="">Select Role</option>';
        
        // Define allowed roles based on current user's role
        let allowedRoles = [];
        if (currentUserRole === 1) { // TC Admin
            allowedRoles = [2, 3, 5]; // TC Head, Exam Cell, TC Faculty (removed Assessment Agency)
        } else if (currentUserRole === 2) { // TC Head
            allowedRoles = [3, 5]; // Exam Cell, TC Faculty only
        }
        
        // Check if TC Head already exists (for TC Admin only)
        let existingTcHead = false;
        if (currentUserRole === 1) {
            // This would need to be passed from the server, but for now we'll include TC Head
            // and let the server validation handle it
        }
        
        // Role names mapping
        const roleNames = {
            1: 'TC Admin',
            2: 'TC Head',
            3: 'Exam Cell',
            4: 'Assessment Agency',
            5: 'TC Faculty'
        };
        
        // Add options
        allowedRoles.forEach(roleId => {
            if (roleId == 2 && existingTcHead) {
                // Skip TC Head if one already exists (except for current admin)
                if (currentRole != 2) {
                    return;
                }
            }
            
            const option = document.createElement('option');
            option.value = roleId;
            option.textContent = roleNames[roleId];
            roleSelect.appendChild(option);
        });
        
        // Set the current role as selected
        roleSelect.value = currentRole;
        
        console.log('Role dropdown populated. Current role:', currentRole, 'Selected value:', roleSelect.value);
    }

    // Handle delete button clicks
    document.querySelectorAll('.delete-admin').forEach(button => {
        button.addEventListener('click', function() {
            deleteAdminId = this.dataset.id;
            document.getElementById('deleteAdminName').textContent = this.dataset.name;
            deleteModal.show();
        });
    });

    // Handle confirm delete
    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (!deleteAdminId) return;

        const deleteBtn = this;
        const deleteText = document.getElementById('deleteAdminText');
        const deleteLoader = document.getElementById('deleteAdminLoader');

        setButtonLoading(deleteBtn, deleteText, deleteLoader, true);

        fetch(`/admin/management/${deleteAdminId}`, {
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
            textElement.textContent = textElement.textContent.includes('Adding') ? 'Add Admin' : 
                                    textElement.textContent.includes('Updating') ? 'Update Admin' : 'Delete';
            loaderElement.classList.add('d-none');
        }
    }

    function clearFormErrors(prefix) {
        const inputs = document.querySelectorAll(`#${prefix}AdminModal .form-control, #${prefix}AdminModal .form-select`);
        inputs.forEach(input => {
            input.classList.remove('is-invalid');
        });
        
        const errorDivs = document.querySelectorAll(`#${prefix}AdminModal .invalid-feedback`);
        errorDivs.forEach(div => {
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