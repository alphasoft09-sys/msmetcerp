@extends('admin.layout')

@section('title', 'Qualification List')

@section('content')

@php
    // Helper function to ensure request parameters are always arrays
    function getRequestArray($key, $default = []) {
        $value = request($key, $default);
        if (is_array($value)) {
            return $value;
        }
        return $value ? [$value] : [];
    }
@endphp

<style>
/* Dropdown styling fixes */
.dropdown-check-list {
    position: relative;
}

.dropdown-check-list .dropdown-menu {
    max-height: 300px;
    overflow-y: auto;
    z-index: 9999 !important;
    position: absolute !important;
    top: 100% !important;
    left: 0 !important;
    right: 0 !important;
    margin-top: 0 !important;
    border: 1px solid #dee2e6 !important;
    border-radius: 0.375rem !important;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    background-color: #fff !important;
}

.dropdown-check-list .dropdown-item {
    padding: 0.5rem 1rem;
    cursor: pointer;
}

.dropdown-check-list .dropdown-item:hover {
    background-color: #f8f9fa;
}

.dropdown-check-list .form-check {
    margin: 0;
    padding: 0;
}

.dropdown-check-list .form-check-input {
    margin-right: 0.5rem;
}

.dropdown-check-list .form-check-label {
    cursor: pointer;
    width: 100%;
    margin: 0;
}

/* Selected tags styling */
.selected-options-display {
    margin-top: 0.5rem;
}

.selected-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.25rem;
}

.selected-tag {
    background-color: #e9ecef;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.selected-tag .remove-tag {
    cursor: pointer;
    color: #6c757d;
    font-weight: bold;
    padding: 0 0.25rem;
}

.selected-tag .remove-tag:hover {
    color: #dc3545;
}

/* Dropdown button styling */
.dropdown-check-list .btn {
    text-align: left;
    position: relative;
}

.dropdown-check-list .btn::after {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
}

/* Ensure dropdowns stay open when clicking inside */
.dropdown-check-list .dropdown-menu {
    pointer-events: auto;
}

.dropdown-check-list .dropdown-item {
    pointer-events: auto;
}

/* Additional fixes for dropdown positioning */
.dropdown-check-list {
    overflow: visible !important;
}

.dropdown-check-list .btn {
    overflow: visible !important;
}

/* Ensure parent containers don't clip dropdowns */
.col-md-4, .col-md-2, .col-md-3 {
    overflow: visible !important;
}

.row {
    overflow: visible !important;
}

/* Force dropdown to appear above everything */
.dropdown-check-list .dropdown-menu.show {
    display: block !important;
    z-index: 9999 !important;
    position: absolute !important;
    transform: none !important;
}

/* Fix for Bootstrap dropdown positioning */
.dropdown-check-list .dropdown {
    position: relative !important;
}

.dropdown-check-list .dropdown-toggle::after {
    position: absolute !important;
    right: 0.75rem !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
}

/* Global Bootstrap dropdown fixes */
.dropdown-menu {
    z-index: 9999 !important;
}

.dropdown-menu.show {
    display: block !important;
    z-index: 9999 !important;
}

/* Ensure all containers allow overflow for dropdowns */
.container-fluid, .container, .row, .col, [class*="col-"] {
    overflow: visible !important;
}

/* Fix for any potential clipping issues */
body {
    overflow-x: hidden;
}

/* Ensure dropdowns are always on top */
.dropdown-check-list {
    position: relative !important;
    z-index: 1000 !important;
}

.dropdown-check-list .dropdown-menu {
    z-index: 9999 !important;
    position: absolute !important;
    top: 100% !important;
    left: 0 !important;
    right: 0 !important;
    margin-top: 0 !important;
    transform: none !important;
}
</style>

<div class="container-fluid">

    
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="bi bi-award-fill me-2"></i>
                        Qualification List
                    </h1>
                    <p class="text-muted">
                        @if($user->user_role === 4)
                            Manage qualifications and their module mappings
                        @else
                            View qualifications and their module details
                        @endif
                    </p>
                </div>
                @if($user->user_role === 4) {{-- Only show for Assessment Agency --}}
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#uploadExcelModal">
                        <i class="bi bi-file-earmark-csv me-2"></i>
                        Upload CSV
                    </button>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addQualificationModal">
                        <i class="bi bi-plus-circle me-2"></i>
                        Add Qualification
                    </button>

                </div>
                @endif
            </div>

            <!-- Search and Filter Section -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-search me-2"></i>
                        Search & Filter
                    </h6>
                </div>
                <div class="card-body">
                    <form id="searchFilterForm">
                        <div class="row">
                            <!-- Search -->
                            <div class="col-md-4 mb-3">
                                <label for="search" class="form-label">
                                    <i class="bi bi-search me-1"></i>
                                    Search Qualifications
                                </label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Search by name, NQR number, sector, level, or type...">
                                <div class="form-text">Search across all qualification fields</div>
                            </div>
                            
                            <!-- Sector Filter (Multiple) -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <i class="bi bi-building me-1"></i>
                                    Sectors
                                </label>
                                <div class="dropdown-check-list" id="sectorsDropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span id="sectorsDisplay">All Sectors</span>
                                        <span class="badge bg-primary ms-2" id="sectorsCount" style="display: none;">0</span>
                                    </button>
                                    <ul class="dropdown-menu w-100" id="sectorsList">
                                        <li class="dropdown-item">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="sector_all" value="all" checked>
                                                <label class="form-check-label" for="sector_all">
                                                    <strong>All Sectors</strong>
                                                </label>
                                            </div>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        @foreach($sectors as $sector)
                                        <li class="dropdown-item">
                                            <div class="form-check">
                                                <input class="form-check-input sector-checkbox" type="checkbox" id="sector_{{ $loop->index }}" value="{{ $sector }}" 
                                                       {{ in_array($sector, getRequestArray('sectors')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="sector_{{ $loop->index }}">
                                                    {{ $sector }}
                                                </label>
                                            </div>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <!-- Selected Sectors Display -->
                                <div class="selected-options-display" id="sectorsSelectedDisplay" style="display: none;">
                                    <div class="selected-tags" id="sectorsSelectedTags"></div>
                                </div>
                            </div>
                            
                            <!-- Level Filter (Multiple) -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <i class="bi bi-layers me-1"></i>
                                    Levels
                                </label>
                                <div class="dropdown-check-list" id="levelsDropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span id="levelsDisplay">All Levels</span>
                                        <span class="badge bg-primary ms-2" id="levelsCount" style="display: none;">0</span>
                                    </button>
                                    <ul class="dropdown-menu w-100" id="levelsList">
                                        <li class="dropdown-item">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="level_all" value="all" checked>
                                                <label class="form-check-label" for="level_all">
                                                    <strong>All Levels</strong>
                                                </label>
                                            </div>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        @foreach($levels as $level)
                                        <li class="dropdown-item">
                                            <div class="form-check">
                                                <input class="form-check-input level-checkbox" type="checkbox" id="level_{{ $loop->index }}" value="{{ $level }}" 
                                                       {{ in_array($level, getRequestArray('levels')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="level_{{ $loop->index }}">
                                                    {{ $level }}
                                                </label>
                                            </div>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <!-- Selected Levels Display -->
                                <div class="selected-options-display" id="levelsSelectedDisplay" style="display: none;">
                                    <div class="selected-tags" id="levelsSelectedTags"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Type Filter (Multiple) -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <i class="bi bi-tag me-1"></i>
                                    Types
                                </label>
                                <div class="dropdown-check-list" id="typesDropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span id="typesDisplay">All Types</span>
                                        <span class="badge bg-primary ms-2" id="typesCount" style="display: none;">0</span>
                                    </button>
                                    <ul class="dropdown-menu w-100" id="typesList">
                                        <li class="dropdown-item">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="type_all" value="all" checked>
                                                <label class="form-check-label" for="type_all">
                                                    <strong>All Types</strong>
                                                </label>
                                            </div>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        @foreach($types as $type)
                                        <li class="dropdown-item">
                                            <div class="form-check">
                                                <input class="form-check-input type-checkbox" type="checkbox" id="type_{{ $loop->index }}" value="{{ $type }}" 
                                                       {{ in_array($type, getRequestArray('qf_types')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="type_{{ $loop->index }}">
                                                    {{ $type }}
                                                </label>
                                            </div>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <!-- Selected Types Display -->
                                <div class="selected-options-display" id="typesSelectedDisplay" style="display: none;">
                                    <div class="selected-tags" id="typesSelectedTags"></div>
                                </div>
                            </div>
                            
                            <!-- Modules Count Filter -->
                            <div class="col-md-2 mb-3">
                                <label for="modules_count" class="form-label">
                                    <i class="bi bi-puzzle me-1"></i>
                                    Modules
                                </label>
                                <select class="form-select" id="modules_count" name="modules_count">
                                    <option value="">All</option>
                                    <option value="with_modules" {{ request('modules_count') == 'with_modules' ? 'selected' : '' }}>
                                        With Modules
                                    </option>
                                    <option value="without_modules" {{ request('modules_count') == 'without_modules' ? 'selected' : '' }}>
                                        Without Modules
                                    </option>
                                </select>
                            </div>
                            
                            <!-- Hours Range Filter -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label">
                                    <i class="bi bi-clock me-1"></i>
                                    Total Hours
                                </label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="number" class="form-control" id="hours_min" name="hours_min" 
                                               value="{{ request('hours_min') }}" placeholder="Min" min="0">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" class="form-control" id="hours_max" name="hours_max" 
                                               value="{{ request('hours_max') }}" placeholder="Max" min="0">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Sort -->
                            <div class="col-md-3 mb-3">
                                <label for="sort_by" class="form-label">
                                    <i class="bi bi-sort-down me-1"></i>
                                    Sort By
                                </label>
                                <div class="row g-2">
                                    <div class="col-8">
                                        <select class="form-select" id="sort_by" name="sort_by">
                                            <option value="created_at" {{ request('sort_by', 'created_at') == 'created_at' ? 'selected' : '' }}>Date Created</option>
                                            <option value="qf_name" {{ request('sort_by') == 'qf_name' ? 'selected' : '' }}>Name</option>
                                            <option value="nqr_no" {{ request('sort_by') == 'nqr_no' ? 'selected' : '' }}>NQR Number</option>
                                            <option value="sector" {{ request('sort_by') == 'sector' ? 'selected' : '' }}>Sector</option>
                                            <option value="level" {{ request('sort_by') == 'level' ? 'selected' : '' }}>Level</option>
                                            <option value="qf_type" {{ request('sort_by') == 'qf_type' ? 'selected' : '' }}>Type</option>
                                            <option value="qf_total_hour" {{ request('sort_by') == 'qf_total_hour' ? 'selected' : '' }}>Total Hours</option>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <select class="form-select" id="sort_order" name="sort_order">
                                            <option value="desc" {{ request('sort_order', 'desc') == 'desc' ? 'selected' : '' }}>Desc</option>
                                            <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Asc</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Active Filters Display -->
                        <div id="activeFilters" class="mb-3" style="display: none;">
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <span class="text-muted small">Active Filters:</span>
                                <div id="filterTags"></div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="button" id="searchBtn" class="btn btn-primary">
                                        <i class="bi bi-search me-2"></i>
                                        Search
                                    </button>
                                    <button type="button" id="resetBtn" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-clockwise me-2"></i>
                                        Clear All
                                    </button>
                                    <button type="button" id="clearSearchBtn" class="btn btn-outline-warning">
                                        <i class="bi bi-x-circle me-2"></i>
                                        Clear Search
                                    </button>
                                    <div class="ms-auto">
                                        <button type="button" id="toggleFiltersBtn" class="btn btn-outline-info btn-sm">
                                            <i class="bi bi-funnel me-1"></i>
                                            <span id="toggleFiltersText">Show Advanced</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results Summary -->
            <div id="resultsSummary" class="alert alert-info mb-4" style="display: none;">
                <i class="bi bi-info-circle me-2"></i>
                <span id="resultsText"></span>
            </div>

            <!-- Qualifications Table -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-ul me-2"></i>
                        Qualifications
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Loader -->
                    <div id="tableLoader" class="text-center py-5" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Loading qualifications...</p>
                    </div>
                    
                    <!-- Table Content -->
                    <div id="tableContent">
                        @include('admin.qualifications.partials.table', ['qualifications' => $qualifications, 'user' => $user])
                    </div>
                    
                    <!-- Pagination -->
                    <div id="paginationContent">
                        @include('admin.qualifications.partials.pagination', ['qualifications' => $qualifications])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Qualification Modal -->
<div class="modal fade" id="addQualificationModal" tabindex="-1" aria-labelledby="addQualificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addQualificationModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>
                    Add New Qualification
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addQualificationForm" method="POST" action="{{ route('admin.qualifications.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="qf_name" class="form-label">Qualification Name *</label>
                            <input type="text" class="form-control" id="qf_name" name="qf_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nqr_no" class="form-label">NQR Number *</label>
                            <input type="text" class="form-control" id="nqr_no" name="nqr_no" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="sector" class="form-label">Sector *</label>
                            <input type="text" class="form-control" id="sector" name="sector" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="level" class="form-label">Level *</label>
                            <input type="text" class="form-control" id="level" name="level" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="qf_type" class="form-label">Type *</label>
                            <input type="text" class="form-control" id="qf_type" name="qf_type" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="qf_total_hour" class="form-label">Total Hours *</label>
                            <input type="number" class="form-control" id="qf_total_hour" name="qf_total_hour" required min="1">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addQualificationForm" class="btn btn-primary">
                    <i class="bi bi-check me-2"></i>
                    Add Qualification
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Modules Modal -->
<div class="modal fade" id="viewModulesModal" tabindex="-1" aria-labelledby="viewModulesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModulesModalLabel">
                    <i class="bi bi-eye me-2"></i>
                    View Modules for: <span id="viewModulesQualificationName"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="viewModulesContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading modules...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Map Modules Modal -->
<div class="modal fade" id="mapModulesModal" tabindex="-1" aria-labelledby="mapModulesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mapModulesModalLabel">
                    <i class="bi bi-diagram-3 me-2"></i>
                    Map Modules
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Content will be loaded via AJAX -->
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading modules...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Qualification Modal -->
<div class="modal fade" id="editQualificationModal" tabindex="-1" aria-labelledby="editQualificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editQualificationModalLabel">
                    <i class="bi bi-pencil me-2"></i>
                    Edit Qualification
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editQualificationForm">
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_qf_name" class="form-label">Qualification Name *</label>
                            <input type="text" class="form-control" id="edit_qf_name" name="qf_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_nqr_no" class="form-label">NQR Number *</label>
                            <input type="text" class="form-control" id="edit_nqr_no" name="nqr_no" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_sector" class="form-label">Sector *</label>
                            <input type="text" class="form-control" id="edit_sector" name="sector" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_level" class="form-label">Level *</label>
                            <input type="text" class="form-control" id="edit_level" name="level" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_qf_type" class="form-label">Qualification Type *</label>
                            <input type="text" class="form-control" id="edit_qf_type" name="qf_type" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_qf_total_hour" class="form-label">Total Hours *</label>
                            <input type="number" class="form-control" id="edit_qf_total_hour" name="qf_total_hour" min="1" required>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span id="editQualificationText">Update Qualification</span>
                        <span id="editQualificationLoader" class="spinner-border spinner-border-sm d-none ms-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Qualification Modal -->
<div class="modal fade" id="deleteQualificationModal" tabindex="-1" aria-labelledby="deleteQualificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteQualificationModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the qualification "<strong id="deleteQualificationName"></strong>"?</p>
                <p class="text-danger"><small>This action cannot be undone and will also remove all module mappings.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteQualification">
                    <span id="deleteQualificationText">Delete</span>
                    <span id="deleteQualificationLoader" class="spinner-border spinner-border-sm d-none ms-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Wait for both DOM and Bootstrap to be ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded and ready');
    
    // Wait for Bootstrap to be available
    function waitForBootstrap() {
        return new Promise((resolve) => {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                resolve();
            } else {
                // Check every 100ms for Bootstrap
                const interval = setInterval(() => {
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        clearInterval(interval);
                        resolve();
                    }
                }, 100);
            }
        });
    }
    
    // Initialize the application once Bootstrap is ready
    waitForBootstrap().then(() => {
        console.log('Bootstrap is ready');
        initializeApplication();
    });
    
    function initializeApplication() {
    // CSRF token setup
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    console.log('CSRF Token:', csrfToken);
        console.log('Bootstrap Modal available:', typeof bootstrap !== 'undefined' && bootstrap.Modal);
        console.log('Modal elements found:', document.querySelectorAll('.modal').length);
        
        // Modal management system
        const modalInstances = new Map();
        
        // Initialize all modals once
        function initializeModals() {
            const modalElements = document.querySelectorAll('.modal');
            modalElements.forEach(modalElement => {
                const modalId = modalElement.id;
                if (!modalInstances.has(modalId)) {
                    const modalInstance = new bootstrap.Modal(modalElement, {
                        backdrop: true,
                        keyboard: true,
                        focus: true
                    });
                    modalInstances.set(modalId, modalInstance);
                    
                    // Add event listeners for proper cleanup
                    modalElement.addEventListener('hidden.bs.modal', function() {
                        console.log(`Modal ${modalId} hidden`);
                        // Ensure backdrop is properly removed
                        const backdrops = document.querySelectorAll('.modal-backdrop');
                        backdrops.forEach(backdrop => {
                            if (backdrop.parentNode) {
                                backdrop.parentNode.removeChild(backdrop);
                            }
                        });
                        document.body.classList.remove('modal-open');
                        document.body.style.overflow = '';
                        document.body.style.paddingRight = '';
                    });
                }
            });
        }
        
        // Function to show modal safely
        function showModal(modalId) {
            const modalInstance = modalInstances.get(modalId);
            if (modalInstance) {
                // Hide any other open modals first
                modalInstances.forEach((instance, id) => {
                    if (id !== modalId && instance._isShown) {
                        instance.hide();
                    }
                });
                
                // Small delay to ensure other modals are closed
                setTimeout(() => {
                    modalInstance.show();
                }, 100);
            } else {
                console.error(`Modal instance not found for: ${modalId}`);
            }
        }
        
        // Initialize modals
        initializeModals();
        
        // Global backdrop cleanup on page visibility change
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                // Clean up any lingering backdrops when page becomes visible
                const backdrops = document.querySelectorAll('.modal-backdrop');
                const openModals = document.querySelectorAll('.modal.show');
                
                if (backdrops.length > 0 && openModals.length === 0) {
                    console.log('Cleaning up orphaned backdrops on visibility change');
                    backdrops.forEach(backdrop => {
                        if (backdrop.parentNode) {
                            backdrop.parentNode.removeChild(backdrop);
                        }
                    });
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                }
            }
        });
    
    // Current search parameters
    let currentParams = {};
    
    // Initialize with current URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.forEach((value, key) => {
        currentParams[key] = value;
    });
    
    console.log('Initial URL params:', currentParams);
    
    // Initialize dropdown checkboxes
    initializeDropdownCheckboxes();
    
    // Debug function to check dropdown status
    function debugDropdowns() {
        console.log('=== Dropdown Debug Info ===');
        ['sector', 'level', 'type'].forEach(prefix => {
            const allCheckbox = document.getElementById(`${prefix}_all`);
            const checkboxes = document.querySelectorAll(`.${prefix}-checkbox`);
            const displaySpan = document.getElementById(`${prefix}sDisplay`);
            const countSpan = document.getElementById(`${prefix}sCount`);
            
            console.log(`${prefix} dropdown:`, {
                allCheckbox: allCheckbox ? 'Found' : 'Missing',
                checkboxes: checkboxes.length,
                displaySpan: displaySpan ? 'Found' : 'Missing',
                countSpan: countSpan ? 'Found' : 'Missing'
            });
        });
    }
    
    // Initialize dropdown checkboxes functionality
    function initializeDropdownCheckboxes() {
        console.log('Initializing dropdown checkboxes');
        
        // Debug current state
        debugDropdowns();
        
        // Wait a bit for DOM to be fully ready
        setTimeout(() => {
        // Sectors dropdown
        initializeCheckboxDropdown('sectors', 'sector', 'Sectors');
        
        // Levels dropdown
        initializeCheckboxDropdown('levels', 'level', 'Levels');
        
        // Types dropdown
        initializeCheckboxDropdown('qf_types', 'type', 'Types');
            
            // Fix dropdown positioning
            fixDropdownPositioning();
            
            // Debug after initialization
            debugDropdowns();
        }, 100);
    }
    
    // Fix dropdown positioning issues
    function fixDropdownPositioning() {
        const dropdowns = document.querySelectorAll('.dropdown-check-list');
        
        dropdowns.forEach(dropdown => {
            const button = dropdown.querySelector('.dropdown-toggle');
            const menu = dropdown.querySelector('.dropdown-menu');
            
            if (button && menu) {
                // Ensure proper positioning
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Force dropdown to show properly
                    setTimeout(() => {
                        menu.style.zIndex = '9999';
                        menu.style.position = 'absolute';
                        menu.style.top = '100%';
                        menu.style.left = '0';
                        menu.style.right = '0';
                        menu.style.display = 'block';
                        menu.classList.add('show');
                    }, 10);
                });
                
                // Prevent dropdown from closing when clicking inside
                menu.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
        });
    }
    
    function initializeCheckboxDropdown(paramName, prefix, displayName) {
        console.log(`Initializing ${paramName} dropdown`);
        
        const allCheckbox = document.getElementById(`${prefix}_all`);
        const checkboxes = document.querySelectorAll(`.${prefix}-checkbox`);
        const displaySpan = document.getElementById(`${prefix}sDisplay`);
        const countSpan = document.getElementById(`${prefix}sCount`);
        
        console.log(`${paramName} elements:`, {
            allCheckbox: allCheckbox ? 1 : 0,
            checkboxes: checkboxes.length,
            displaySpan: displaySpan ? 1 : 0,
            countSpan: countSpan ? 1 : 0
        });
        
        // Check if elements exist
        if (!allCheckbox || !displaySpan || !countSpan) {
            console.error(`Missing elements for ${paramName} dropdown`);
            return;
        }
        
        // Remove existing event listeners to prevent duplicates
        const newAllCheckbox = allCheckbox.cloneNode(true);
        allCheckbox.parentNode.replaceChild(newAllCheckbox, allCheckbox);
        
        // Handle "All" checkbox
        newAllCheckbox.addEventListener('change', function() {
            console.log('All checkbox changed:', this.checked, 'for', paramName);
            
            if (this.checked) {
                // Uncheck all other checkboxes
                checkboxes.forEach(checkbox => checkbox.checked = false);
                updateDropdownDisplay(paramName, displayName, [], displaySpan, countSpan);
                delete currentParams[paramName];
            } else {
                // If "All" is unchecked, check the first option
                if (checkboxes.length > 0) {
                    checkboxes[0].checked = true;
                    const selectedValues = [checkboxes[0].value];
                    updateDropdownDisplay(paramName, displayName, selectedValues, displaySpan, countSpan);
                    delete currentParams[paramName];
                    currentParams[paramName] = selectedValues;
                }
            }
            
            console.log('Updated currentParams after All checkbox:', currentParams);
            updateActiveFilters();
            loadQualifications();
        });
        
        // Handle individual checkboxes
        checkboxes.forEach(checkbox => {
            // Remove existing event listeners
            const newCheckbox = checkbox.cloneNode(true);
            checkbox.parentNode.replaceChild(newCheckbox, checkbox);
            
            newCheckbox.addEventListener('change', function() {
                console.log('Checkbox changed:', this.value, this.checked);
                
                const selectedCheckboxes = Array.from(document.querySelectorAll(`.${prefix}-checkbox`)).filter(cb => cb.checked);
                const selectedValues = selectedCheckboxes.map(cb => cb.value);
            
            console.log('Selected values:', selectedValues);
            
            // Update "All" checkbox
                newAllCheckbox.checked = selectedValues.length === 0;
            
            // Update display
            updateDropdownDisplay(paramName, displayName, selectedValues, displaySpan, countSpan);
            
            // Update parameters
            if (selectedValues.length === 0) {
                delete currentParams[paramName];
            } else {
                currentParams[paramName] = selectedValues;
            }
            
            console.log('Updated currentParams:', currentParams);
            updateActiveFilters();
            loadQualifications();
        });
        
        // Prevent dropdown from closing when clicking on checkboxes
            newCheckbox.addEventListener('click', function(e) {
            e.stopPropagation();
            });
        });
        
        newAllCheckbox.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        
        // Initialize display based on current parameters
        const currentValues = currentParams[paramName] || [];
        if (currentValues.length > 0) {
            // Check corresponding checkboxes
            document.querySelectorAll(`.${prefix}-checkbox`).forEach(checkbox => {
                if (currentValues.includes(checkbox.value)) {
                    checkbox.checked = true;
                }
            });
            newAllCheckbox.checked = false;
            updateDropdownDisplay(paramName, displayName, currentValues, displaySpan, countSpan);
        } else {
            // Initialize selected options display for empty state
            updateSelectedOptionsDisplay(paramName, []);
        }
    }
    
    // Update dropdown display
    function updateDropdownDisplay(paramName, displayName, selectedValues, displaySpan, countSpan) {
        try {
            if (!displaySpan || !countSpan) {
                console.warn('Display elements not found for:', paramName);
                return;
            }
            
        if (selectedValues.length === 0) {
                displaySpan.textContent = 'All ' + displayName;
                displaySpan.classList.remove('selected-text');
                displaySpan.classList.add('default-text');
                countSpan.style.display = 'none';
        } else if (selectedValues.length === 1) {
                displaySpan.textContent = selectedValues[0];
                displaySpan.classList.remove('default-text');
                displaySpan.classList.add('selected-text');
                countSpan.style.display = 'none';
        } else {
                displaySpan.textContent = selectedValues[0] + ' +' + (selectedValues.length - 1) + ' more';
                displaySpan.classList.remove('default-text');
                displaySpan.classList.add('selected-text');
                countSpan.textContent = selectedValues.length;
                countSpan.style.display = 'inline';
        }
        
        // Update selected options display
        updateSelectedOptionsDisplay(paramName, selectedValues);
        } catch (error) {
            console.error('Error in updateDropdownDisplay:', error);
        }
    }
    
    // Update selected options display
    function updateSelectedOptionsDisplay(paramName, selectedValues) {
        const displayId = paramName.replace('qf_', '') + 'sSelectedDisplay';
        const tagsId = paramName.replace('qf_', '') + 'sSelectedTags';
        const displayElement = document.getElementById(displayId);
        const tagsElement = document.getElementById(tagsId);
        
        if (!displayElement || !tagsElement) return;
        
        if (selectedValues.length === 0) {
            displayElement.style.display = 'none';
            tagsElement.innerHTML = '';
        } else {
            displayElement.style.display = 'block';
            displayElement.classList.add('show');
            
            // Clear existing tags
            tagsElement.innerHTML = '';
            
            // Add new tags
            selectedValues.forEach(value => {
                const tag = document.createElement('span');
                tag.className = 'selected-tag';
                tag.innerHTML = `
                    ${value}
                    <span class="remove-tag" data-value="${value}" data-param="${paramName}"></span>
                `;
                
                // Add click event to remove tag
                tag.querySelector('.remove-tag').addEventListener('click', function(e) {
                    e.stopPropagation();
                    const valueToRemove = this.dataset.value;
                    const paramToUpdate = this.dataset.param;
                    removeSelectedOption(paramToUpdate, valueToRemove);
                });
                
                tagsElement.appendChild(tag);
            });
        }
    }
    
    // Remove selected option
    function removeSelectedOption(paramName, valueToRemove) {
        const prefix = paramName.replace('qf_', '').replace('s', '');
        const checkboxes = document.querySelectorAll(`.${prefix}-checkbox`);
        const allCheckbox = document.getElementById(`${prefix}_all`);
        const displaySpan = document.getElementById(`${prefix}sDisplay`);
        const countSpan = document.getElementById(`${prefix}sCount`);
        
        // Uncheck the corresponding checkbox
        checkboxes.forEach(checkbox => {
            if (checkbox.value === valueToRemove) {
                checkbox.checked = false;
            }
        });
        
        // Get remaining selected values
        const selectedCheckboxes = Array.from(checkboxes).filter(cb => cb.checked);
        const selectedValues = selectedCheckboxes.map(cb => cb.value);
        
        // Update "All" checkbox
        allCheckbox.checked = selectedValues.length === 0;
        
        // Update display
        const displayName = prefix.charAt(0).toUpperCase() + prefix.slice(1) + 's';
        updateDropdownDisplay(paramName, displayName, selectedValues, displaySpan, countSpan);
        
        // Update parameters
        if (selectedValues.length === 0) {
            delete currentParams[paramName];
        } else {
            currentParams[paramName] = selectedValues;
        }
        
        updateActiveFilters();
        loadQualifications();
    }
    
    // Load qualifications via AJAX
    function loadQualifications() {
        console.log('=== loadQualifications called ===');
        console.log('Loading qualifications with params:', currentParams);
        console.log('Current URL:', window.location.href);
        
        // Show loader
        const tableLoader = document.getElementById('tableLoader');
        const tableContent = document.getElementById('tableContent');
        const paginationContent = document.getElementById('paginationContent');
        const resultsSummary = document.getElementById('resultsSummary');
        
        if (tableLoader) tableLoader.style.display = 'block';
        if (tableContent) tableContent.style.display = 'none';
        if (paginationContent) paginationContent.style.display = 'none';
        if (resultsSummary) resultsSummary.style.display = 'none';
        
        // Update URL without reloading
        const newUrl = window.location.pathname + (Object.keys(currentParams).length > 0 ? '?' + new URLSearchParams(currentParams).toString() : '');
        window.history.pushState({}, '', newUrl);
        
        console.log('Sending AJAX request with data:', currentParams);
        console.log('CSRF Token:', csrfToken);
        console.log('Route:', '{{ route("admin.qualifications.ajax") }}');
        
        // Make AJAX request
        const requestBody = Object.keys(currentParams).length > 0 ? currentParams : {};
        
        fetch('{{ route("admin.qualifications.ajax") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(requestBody)
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    // Update table content
                if (tableContent && data.html) {
                    tableContent.innerHTML = data.html;
                    tableContent.style.display = 'block';
                }
                    
                    // Update pagination
                if (paginationContent && data.pagination) {
                    paginationContent.innerHTML = data.pagination;
                    paginationContent.style.display = 'block';
                }
                    
                    // Update results summary
                if (data) {
                    updateResultsSummary(data);
                }
                
                // Re-initialize dropdowns after content update
                setTimeout(() => {
                    initializeDropdownCheckboxes();
                }, 100);
                    
                    console.log('Qualifications loaded successfully');
                } else {
                    console.error('Failed to load qualifications:', data.message);
                    showAlert('danger', data.message || 'Failed to load qualifications');
                
                // Show error state in table
                if (tableContent) {
                    tableContent.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Failed to load qualifications: ${data.message || 'Unknown error'}
                        </div>
                    `;
                    tableContent.style.display = 'block';
                }
            }
        })
        .catch(error => {
                console.error('AJAX Error:', error);
            console.error('Error details:', {
                message: error.message,
                stack: error.stack,
                currentParams: currentParams,
                requestBody: requestBody
            });
            
            let errorMessage = 'An error occurred while loading qualifications';
            if (error.message) {
                errorMessage += ': ' + error.message;
            }
            
            showAlert('danger', errorMessage);
            
            // Show error state in table
            if (tableContent) {
                tableContent.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        ${errorMessage}
                        <br><small>Please check the browser console for more details.</small>
                    </div>
                `;
                tableContent.style.display = 'block';
            }
        })
        .finally(() => {
            // Hide loader
            if (tableLoader) tableLoader.style.display = 'none';
        });
    }
    
    // Update results summary
    function updateResultsSummary(data) {
        try {
            const resultsTextElement = document.getElementById('resultsText');
            const resultsSummaryElement = document.getElementById('resultsSummary');
            
            if (!resultsTextElement || !resultsSummaryElement) {
                console.warn('Results summary elements not found');
                return;
            }
            
        let summaryText = `Showing ${data.from || 0} to ${data.to || 0} of ${data.total} qualification(s)`;
        
        if (currentParams.search) {
            summaryText += ` matching "<strong>${currentParams.search}</strong>"`;
        }
        if (currentParams.sectors && currentParams.sectors.length > 0) {
            summaryText += ` in sector(s) "<strong>${currentParams.sectors.join(', ')}</strong>"`;
        }
        if (currentParams.levels && currentParams.levels.length > 0) {
            summaryText += ` at level(s) "<strong>${currentParams.levels.join(', ')}</strong>"`;
        }
        if (currentParams.qf_types && currentParams.qf_types.length > 0) {
            summaryText += ` of type(s) "<strong>${currentParams.qf_types.join(', ')}</strong>"`;
        }
        
            resultsTextElement.innerHTML = summaryText;
            resultsSummaryElement.style.display = 'block';
        } catch (error) {
            console.error('Error in updateResultsSummary:', error);
        }
    }
    
    // Attach modal event listeners
    function attachModalEventListeners() {
        console.log('Attaching modal event listeners');
        
        // View Modules Modal
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('view-modules-btn')) {
            e.preventDefault();
                const qualificationId = e.target.dataset.qualificationId;
                const qualificationName = e.target.dataset.qualificationName;
            
            console.log('Opening view modules modal for:', qualificationId, qualificationName);
            
            // Load modules data via AJAX
                fetch(`/admin/qualifications/${qualificationId}/modules/view`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Modules view response:', data);
                    if (data.success) {
                        document.querySelector('#viewModulesModal .modal-title').textContent = `View Modules - ${qualificationName}`;
                        document.querySelector('#viewModulesModal .modal-body').innerHTML = data.html;
                        showModal('viewModulesModal');
                    } else {
                        showAlert('danger', data.message || 'Failed to load modules');
                    }
                })
                .catch(error => {
                    console.error('Error loading modules:', error);
                    showAlert('danger', 'Failed to load modules data');
                });
                }
        });
        
        // Map Modules Modal
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('map-modules-btn')) {
            e.preventDefault();
                const qualificationId = e.target.dataset.qualificationId;
                const qualificationName = e.target.dataset.qualificationName;
            
            console.log('Opening map modules modal for:', qualificationId, qualificationName);
            
            // Load modules data via AJAX
                fetch(`/admin/qualifications/${qualificationId}/modules/map`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Modules mapping response:', data);
                    if (data.success) {
                        document.querySelector('#mapModulesModal .modal-title').textContent = `Map Modules - ${qualificationName}`;
                        document.querySelector('#mapModulesModal .modal-body').innerHTML = data.html;
                        
                        // Add submit button to modal footer
                        const modalFooter = document.querySelector('#mapModulesModal .modal-footer');
                        if (modalFooter) {
                            modalFooter.innerHTML = `
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" form="moduleMappingForm" class="btn btn-primary">
                                    <i class="bi bi-check me-2"></i>
                                    Save Mappings
                                </button>
                            `;
                        }
                        
                        // Set the qualification ID for the form submission
                        window.currentQualificationId = qualificationId;
                        
                        showModal('mapModulesModal');
                    } else {
                        showAlert('danger', data.message || 'Failed to load modules for mapping');
                    }
                })
                .catch(error => {
                    console.error('Error loading modules for mapping:', error);
                    showAlert('danger', 'Failed to load modules for mapping');
                });
                }
        });
        
        // Edit Qualification Modal
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('edit-qualification-btn')) {
            e.preventDefault();
                const qualificationData = JSON.parse(e.target.dataset.qualification);
            
            console.log('Opening edit qualification modal for:', qualificationData);
            
            // Populate the edit form with qualification data
            if (qualificationData) {
                console.log('Populating form with data:', qualificationData);
                    document.querySelector('#editQualificationModal #edit_qf_name').value = qualificationData.qf_name;
                    document.querySelector('#editQualificationModal #edit_nqr_no').value = qualificationData.nqr_no;
                    document.querySelector('#editQualificationModal #edit_sector').value = qualificationData.sector;
                    document.querySelector('#editQualificationModal #edit_level').value = qualificationData.level;
                    document.querySelector('#editQualificationModal #edit_qf_type').value = qualificationData.qf_type;
                    document.querySelector('#editQualificationModal #edit_qf_total_hour').value = qualificationData.qf_total_hour;
                
                // Debug: Check if values were set
                console.log('Form values after setting:');
                    console.log('qf_name:', document.querySelector('#editQualificationModal #edit_qf_name').value);
                    console.log('nqr_no:', document.querySelector('#editQualificationModal #edit_nqr_no').value);
                    console.log('sector:', document.querySelector('#editQualificationModal #edit_sector').value);
                    console.log('level:', document.querySelector('#editQualificationModal #edit_level').value);
                    console.log('qf_type:', document.querySelector('#editQualificationModal #edit_qf_type').value);
                    console.log('qf_total_hour:', document.querySelector('#editQualificationModal #edit_qf_total_hour').value);
                
                // Set the form action
                    document.querySelector('#editQualificationForm').action = `/admin/qualifications/${qualificationData.id}`;
                
                    showModal('editQualificationModal');
                
                // Ensure modal is fully shown before allowing form submission
                    document.getElementById('editQualificationModal').addEventListener('shown.bs.modal', function() {
                    console.log('Modal fully shown, form ready for submission');
                });
            } else {
                showAlert('danger', 'Qualification data not found');
                }
            }
        });
        
        // Delete Qualification Modal
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('delete-qualification-btn')) {
            e.preventDefault();
                const qualificationId = e.target.dataset.qualificationId;
                const qualificationName = e.target.dataset.qualificationName;
            
            console.log('Opening delete qualification modal for:', qualificationId, qualificationName);
            
            // Set modal content
                document.querySelector('#deleteQualificationModal .modal-body').innerHTML = `
                <p>Are you sure you want to delete the qualification "<strong>${qualificationName}</strong>"?</p>
                <p class="text-danger">This action cannot be undone.</p>
                `;
            
            // Set delete form action
                document.querySelector('#deleteQualificationForm').action = `/admin/qualifications/${qualificationId}`;
                
                showModal('deleteQualificationModal');
            }
        });
        

        
        // Handle Excel upload form submission
        document.getElementById('uploadExcelForm').addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Submitting Excel upload form');
            
            const formData = new FormData(this);
            const skipDuplicates = document.getElementById('skipDuplicates').checked;
            formData.append('skipDuplicates', skipDuplicates);
            
            // Show progress bar
            document.querySelector('.progress').style.display = 'block';
            document.querySelector('.progress-bar').style.width = '0%';
            document.querySelector('.progress-bar').textContent = '0%';
            
            // Hide previous results
            document.getElementById('uploadResults').style.display = 'none';
            document.getElementById('uploadErrors').style.display = 'none';
            
            fetch('{{ route("admin.qualifications.upload-excel") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                }
            })
            .then(response => response.json())
            .then(data => {
                    console.log('Upload response:', data);
                    
                    if (data.success) {
                    document.getElementById('uploadStats').innerHTML = `
                            <ul class="mb-0">
                                <li>Total records processed: ${data.total}</li>
                                <li>Successfully imported: ${data.imported}</li>
                                <li>Skipped duplicates: ${data.skipped}</li>
                                <li>Errors: ${data.errors}</li>
                            </ul>
                    `;
                    document.getElementById('uploadResults').style.display = 'block';
                        
                        // Reload qualifications table
                        setTimeout(() => {
                            loadQualifications();
                        }, 2000);
                    } else {
                    document.getElementById('errorList').innerHTML = data.message || 'Upload failed';
                    document.getElementById('uploadErrors').style.display = 'block';
                    }
            })
            .catch(error => {
                    console.error('Upload error:', error);
                document.getElementById('errorList').innerHTML = 'An error occurred during upload. Please try again.';
                document.getElementById('uploadErrors').style.display = 'block';
            })
            .finally(() => {
                document.querySelector('.progress').style.display = 'none';
            });
        });
        
        // Handle form submissions
        document.getElementById('addQualificationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Submitting add qualification form');
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                }
            })
            .then(response => response.json())
            .then(data => {
                    console.log('Add qualification response:', data);
                    
                    if (data.success) {
                    const modalInstance = modalInstances.get('addQualificationModal');
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                        showAlert('success', 'Qualification added successfully');
                        loadQualifications();
                    } else {
                        showAlert('danger', data.message || 'Failed to add qualification');
                    }
            })
            .catch(error => {
                    console.error('Add qualification error:', error);
                    showAlert('danger', 'An error occurred while adding qualification');
            });
        });
        
        document.getElementById('editQualificationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Submitting edit qualification form');
            console.log('Form action:', this.action);
            console.log('Form elements:', this.elements);
            
            const formData = new FormData(this);
            
            // Ensure _method is included for PUT request
            formData.append('_method', 'PUT');
            
            // Debug: Log form data
            console.log('Form data being sent:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Updating...';
            submitBtn.disabled = true;
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                    console.log('Edit qualification response:', data);
                    
                    if (data.success) {
                    const modalInstance = modalInstances.get('editQualificationModal');
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                        showAlert('success', 'Qualification updated successfully');
                        loadQualifications();
                    } else {
                        showAlert('danger', data.message || 'Failed to update qualification');
                    }
            })
            .catch(error => {
                    console.error('Edit qualification error:', error);
                showAlert('danger', 'An error occurred while updating qualification');
            })
            .finally(() => {
                    // Restore button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
        
        document.getElementById('deleteQualificationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Submitting delete qualification form');
            
            fetch(this.action, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                }
            })
            .then(response => response.json())
            .then(data => {
                    console.log('Delete qualification response:', data);
                    
                    if (data.success) {
                    const modalInstance = modalInstances.get('deleteQualificationModal');
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                        showAlert('success', 'Qualification deleted successfully');
                        loadQualifications();
                    } else {
                        showAlert('danger', data.message || 'Failed to delete qualification');
                    }
            })
            .catch(error => {
                    console.error('Delete qualification error:', error);
                    showAlert('danger', 'An error occurred while deleting qualification');
            });
        });
        
        // Module Mapping Form (delegated event listener)
        document.addEventListener('submit', function(e) {
            if (e.target.id === 'moduleMappingForm') {
                e.preventDefault();
                console.log('Module mapping form submitted');
                
                const selectedModules = Array.from(document.querySelectorAll('.module-checkbox:checked')).map(checkbox => checkbox.value);
                const qualificationId = e.target.dataset.qualificationId || window.currentQualificationId;
                
                if (!qualificationId) {
                    showAlert('danger', 'Qualification ID not found');
                    return;
                }
                
                console.log('Selected modules:', selectedModules);
                console.log('Qualification ID:', qualificationId);
                
                // Show loading state
                const submitBtn = document.querySelector('button[type="submit"][form="moduleMappingForm"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Saving...';
                submitBtn.disabled = true;
                
                fetch(`/admin/qualifications/${qualificationId}/modules`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        module_ids: selectedModules
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Module mapping response:', data);
                    
                    if (data.success) {
                        showAlert('success', 'Module mappings updated successfully');
                        
                        // Close modal and reload page after a short delay
                        setTimeout(() => {
                            const modalInstance = modalInstances.get('mapModulesModal');
                            if (modalInstance) {
                                modalInstance.hide();
                            }
                            loadQualifications();
                        }, 2000);
                    } else {
                        showAlert('danger', data.message || 'Failed to update module mappings');
                    }
                })
                .catch(error => {
                    console.error('Module mapping error:', error);
                    showAlert('danger', 'An error occurred while updating module mappings');
                })
                .finally(() => {
                    // Restore button state
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
            }
        });
    }
    
    // Search input functionality with debounce
    let searchTimeout;
    document.getElementById('search').addEventListener('input', function() {
        const searchValue = this.value;
        
        // Clear previous timeout
        clearTimeout(searchTimeout);
        
        // Set new timeout for search
        searchTimeout = setTimeout(() => {
            if (searchValue.length > 0) {
                currentParams.search = searchValue;
            } else {
                delete currentParams.search;
            }
            updateActiveFilters();
            loadQualifications();
        }, 500); // Wait 500ms after user stops typing
    });
    
    // Search button functionality
    document.getElementById('searchBtn').addEventListener('click', function() {
        const searchValue = document.getElementById('search').value;
        if (searchValue.length > 0) {
            currentParams.search = searchValue;
        } else {
            delete currentParams.search;
        }
        updateActiveFilters();
        loadQualifications();
    });
    
    // Reset button
    document.getElementById('resetBtn').addEventListener('click', function() {
        console.log('Reset button clicked');
        
        try {
        // Clear form inputs
            const searchFilterForm = document.getElementById('searchFilterForm');
            if (searchFilterForm) {
                searchFilterForm.reset();
            }
        
        // Reset checkbox dropdowns
        resetCheckboxDropdowns();
        
        // Clear current params
        currentParams = {};
        
        // Hide active filters
            const activeFilters = document.getElementById('activeFilters');
            if (activeFilters) {
                activeFilters.style.display = 'none';
            }
            
            // Clear search input
            const searchInput = document.getElementById('search');
            if (searchInput) {
                searchInput.value = '';
            }
        
        // Load without filters
        loadQualifications();
        } catch (error) {
            console.error('Error in reset button click:', error);
            showAlert('danger', 'An error occurred while resetting filters');
        }
    });
    
    // Reset checkbox dropdowns
    function resetCheckboxDropdowns() {
        try {
        // Reset sectors
            const sectorAll = document.getElementById('sector_all');
            if (sectorAll) {
                sectorAll.checked = true;
                document.querySelectorAll('.sector-checkbox').forEach(cb => cb.checked = false);
        updateDropdownDisplay('sectors', 'Sectors', [], 
                    document.getElementById('sectorsDisplay'), 
                    document.getElementById('sectorsCount'));
                const sectorsSelectedDisplay = document.getElementById('sectorsSelectedDisplay');
                const sectorsSelectedTags = document.getElementById('sectorsSelectedTags');
                if (sectorsSelectedDisplay) sectorsSelectedDisplay.style.display = 'none';
                if (sectorsSelectedTags) sectorsSelectedTags.innerHTML = '';
            }
        
        // Reset levels
            const levelAll = document.getElementById('level_all');
            if (levelAll) {
                levelAll.checked = true;
                document.querySelectorAll('.level-checkbox').forEach(cb => cb.checked = false);
        updateDropdownDisplay('levels', 'Levels', [], 
                    document.getElementById('levelsDisplay'), 
                    document.getElementById('levelsCount'));
                const levelsSelectedDisplay = document.getElementById('levelsSelectedDisplay');
                const levelsSelectedTags = document.getElementById('levelsSelectedTags');
                if (levelsSelectedDisplay) levelsSelectedDisplay.style.display = 'none';
                if (levelsSelectedTags) levelsSelectedTags.innerHTML = '';
            }
        
        // Reset types
            const typeAll = document.getElementById('type_all');
            if (typeAll) {
                typeAll.checked = true;
                document.querySelectorAll('.type-checkbox').forEach(cb => cb.checked = false);
        updateDropdownDisplay('qf_types', 'Types', [], 
                    document.getElementById('typesDisplay'), 
                    document.getElementById('typesCount'));
                const typesSelectedDisplay = document.getElementById('typesSelectedDisplay');
                const typesSelectedTags = document.getElementById('typesSelectedTags');
                if (typesSelectedDisplay) typesSelectedDisplay.style.display = 'none';
                if (typesSelectedTags) typesSelectedTags.innerHTML = '';
            }
        } catch (error) {
            console.error('Error in resetCheckboxDropdowns:', error);
        }
    }
    
    // Clear search button
    document.getElementById('clearSearchBtn').addEventListener('click', function() {
        console.log('Clear search button clicked');
        document.getElementById('search').value = '';
        delete currentParams.search;
        updateActiveFilters();
        loadQualifications();
    });
    
    // Toggle filters button
    document.getElementById('toggleFiltersBtn').addEventListener('click', function() {
        const advancedFilters = document.querySelectorAll('.col-md-4, .col-md-2, .col-md-3');
        const isVisible = advancedFilters[0].style.display !== 'none';
        
        advancedFilters.forEach(filter => {
            filter.style.display = isVisible ? 'none' : 'block';
        });
        document.getElementById('toggleFiltersText').textContent = isVisible ? 'Show Advanced' : 'Hide Advanced';
    });
    
    // Clear search on Escape key
    document.getElementById('search').addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            console.log('Escape key pressed');
            this.value = '';
            delete currentParams.search;
            updateActiveFilters();
            loadQualifications();
        }
    });
    

    
    // Update active filters display
    function updateActiveFilters() {
        const activeFilters = [];
        
        Object.keys(currentParams).forEach(key => {
            if (key !== 'page' && currentParams[key] && currentParams[key].length > 0) {
                if (Array.isArray(currentParams[key])) {
                    currentParams[key].forEach(value => {
                        activeFilters.push({ key, value });
                    });
                } else {
                    activeFilters.push({ key, value: currentParams[key] });
                }
            }
        });
        
        if (activeFilters.length === 0) {
            document.getElementById('activeFilters').style.display = 'none';
            document.getElementById('filterTags').innerHTML = '';
        } else {
            document.getElementById('activeFilters').style.display = 'block';
            document.getElementById('filterTags').innerHTML = '';
            
            activeFilters.forEach(filter => {
                const tag = document.createElement('span');
                tag.className = 'badge bg-primary me-2 mb-2';
                tag.innerHTML = `
                    ${filter.value}
                    <i class="bi bi-x-circle ms-1 remove-filter" data-key="${filter.key}" data-value="${filter.value}"></i>
                `;
                
                tag.querySelector('.remove-filter').addEventListener('click', function() {
                    const key = this.dataset.key;
                    const value = this.dataset.value;
                    
                    if (Array.isArray(currentParams[key])) {
                        currentParams[key] = currentParams[key].filter(v => v !== value);
                        if (currentParams[key].length === 0) {
                            delete currentParams[key];
                        }
                    } else {
                        delete currentParams[key];
                    }
                    
                    updateActiveFilters();
                    loadQualifications();
                });
                
                document.getElementById('filterTags').appendChild(tag);
            });
        }
    }
    
    // Show alert function
    function showAlert(type, message) {
        console.log('Showing alert:', type, message);
        
        // Create alert element
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        // Add to page
        const alertContainer = document.createElement('div');
        alertContainer.className = 'alert-container';
        alertContainer.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertContainer.innerHTML = alertHtml;
        document.body.appendChild(alertContainer);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            alertContainer.style.opacity = '0';
            alertContainer.style.transition = 'opacity 0.5s';
            setTimeout(() => {
                if (alertContainer.parentNode) {
                    alertContainer.parentNode.removeChild(alertContainer);
                }
            }, 500);
        }, 5000);
    }
    
    // Make showAlert globally accessible
    window.showAlert = showAlert;
    
    // Initial load
    loadQualifications();
    
    // Attach event listeners that should only be attached once (using event delegation)
    attachPaginationEventListeners();
    attachSortEventListeners();
    attachModalEventListeners();
    
    // Attach pagination event listeners (only once)
    function attachPaginationEventListeners() {
        console.log('Attaching pagination event listeners (once)');
        
        // Use event delegation for pagination links
        document.addEventListener('click', function(e) {
            if (e.target.closest('#paginationContent .page-link[data-page]')) {
            e.preventDefault();
                const page = e.target.closest('[data-page]').dataset.page;
            currentParams.page = page;
            loadQualifications();
            }
        });
    }
    
    // Attach sort event listeners (only once)
    function attachSortEventListeners() {
        console.log('Attaching sort event listeners (once)');
        
        // Use event delegation for sortable headers
        document.addEventListener('click', function(e) {
            if (e.target.closest('#tableContent .sortable')) {
            e.preventDefault();
                const sortBy = e.target.closest('.sortable').dataset.sort;
            const currentSort = currentParams.sort_by || 'created_at';
            const currentOrder = currentParams.sort_order || 'desc';
            
            if (currentSort === sortBy) {
                currentParams.sort_order = currentOrder === 'asc' ? 'desc' : 'asc';
            } else {
                currentParams.sort_by = sortBy;
                currentParams.sort_order = 'asc';
            }
            
            loadQualifications();
            }
        });
    }
    
    // Attach modal event listeners on page load
    attachModalEventListeners();
    }
});
</script>

<!-- Excel Upload Modal -->
<div class="modal fade" id="uploadExcelModal" tabindex="-1" aria-labelledby="uploadExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadExcelModalLabel">
                    <i class="bi bi-file-earmark-csv me-2"></i>
                    Upload Qualifications CSV
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Instructions:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Upload a CSV file with qualification data</li>
                        <li>Make sure to save your CSV file as UTF-8 encoding</li>
                        <li>First row should contain column headers</li>
                        <li>Required columns: qf_name, nqr_no, sector, level, qf_type, qf_total_hour</li>
                    </ul>
                </div>
                
                <form id="uploadExcelForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="csv_file" class="form-label">Select CSV File</label>
                        <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
                        <div class="form-text">Only CSV files are allowed. Maximum size: 10MB</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="skipDuplicates" name="skipDuplicates">
                            <label class="form-check-label" for="skipDuplicates">
                                Skip duplicate entries (based on NQR number)
                            </label>
                        </div>
                    </div>
                    
                    <div class="progress mb-3" style="display: none;">
                        <div class="progress-bar" role="progressbar" style="width: 0%">0%</div>
                    </div>
                    
                    <div id="uploadResults" class="alert alert-success" style="display: none;">
                        <h6><i class="bi bi-check-circle me-2"></i>Upload Completed Successfully!</h6>
                        <div id="uploadStats"></div>
                    </div>
                    
                    <div id="uploadErrors" class="alert alert-danger" style="display: none;">
                        <h6><i class="bi bi-exclamation-triangle me-2"></i>Upload Failed</h6>
                        <div id="errorList"></div>
                    </div>
                </form>
                
                <div class="mt-3">
                    <a href="{{ route('admin.qualifications.download-template') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-download me-2"></i>
                        Download Template
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="uploadExcelForm" class="btn btn-primary">
                    <i class="bi bi-upload me-2"></i>
                    Upload File
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Qualification Modal -->
<div class="modal fade" id="viewQualificationModal" tabindex="-1" aria-labelledby="viewQualificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewQualificationModalLabel">
                    <i class="bi bi-eye me-2"></i>
                    View Qualification Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Content will be loaded via AJAX -->
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading qualification details...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Qualification Modal -->
<div class="modal fade" id="deleteQualificationModal" tabindex="-1" aria-labelledby="deleteQualificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteQualificationModalLabel">
                    <i class="bi bi-trash me-2"></i>
                    Delete Qualification
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Content will be set via JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteQualificationForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-2"></i>
                        Delete Qualification
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection