@extends('admin.layout')

@section('title', 'Qualification Modules')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="bi bi-puzzle-fill me-2"></i>
                        Qualification Modules
                    </h1>
                    <p class="text-muted">Manage qualification modules</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#uploadExcelModal">
                        <i class="bi bi-file-earmark-csv me-2"></i>
                        Upload CSV
                    </button>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModuleModal">
                        <i class="bi bi-plus-circle me-2"></i>
                        Add Module
                    </button>
                </div>
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
                            <div class="col-md-3 mb-3">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Search modules...">
                            </div>
                            
                            <!-- Module Type Filter -->
                            <div class="col-md-2 mb-3">
                                <label for="is_optional" class="form-label">Type</label>
                                <select class="form-select" id="is_optional" name="is_optional">
                                    <option value="">All Types</option>
                                    <option value="mandatory" {{ request('is_optional') == 'mandatory' ? 'selected' : '' }}>
                                        Mandatory
                                    </option>
                                    <option value="optional" {{ request('is_optional') == 'optional' ? 'selected' : '' }}>
                                        Optional
                                    </option>
                                </select>
                            </div>
                            
                            <!-- Hours Range -->
                            <div class="col-md-2 mb-3">
                                <label for="hours_min" class="form-label">Min Hours</label>
                                <input type="number" class="form-control" id="hours_min" name="hours_min" 
                                       value="{{ request('hours_min') }}" min="0" placeholder="Min">
                            </div>
                            
                            <div class="col-md-2 mb-3">
                                <label for="hours_max" class="form-label">Max Hours</label>
                                <input type="number" class="form-control" id="hours_max" name="hours_max" 
                                       value="{{ request('hours_max') }}" min="0" placeholder="Max">
                            </div>
                            
                            <!-- Credit Range -->
                            <div class="col-md-2 mb-3">
                                <label for="credit_min" class="form-label">Min Credit</label>
                                <input type="number" class="form-control" id="credit_min" name="credit_min" 
                                       value="{{ request('credit_min') }}" min="0" step="0.01" placeholder="Min">
                            </div>
                            
                            <div class="col-md-1 mb-3">
                                <label for="credit_max" class="form-label">Max Credit</label>
                                <input type="number" class="form-control" id="credit_max" name="credit_max" 
                                       value="{{ request('credit_max') }}" min="0" step="0.01" placeholder="Max">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="qualifications_count" class="form-label">Qualifications</label>
                                <select class="form-select" id="qualifications_count" name="qualifications_count">
                                    <option value="">All</option>
                                    <option value="with_qualifications" {{ request('qualifications_count') == 'with_qualifications' ? 'selected' : '' }}>
                                        With Qualifications
                                    </option>
                                    <option value="without_qualifications" {{ request('qualifications_count') == 'without_qualifications' ? 'selected' : '' }}>
                                        Without Qualifications
                                    </option>
                                </select>
                            </div>
                            
                            <!-- Sort -->
                            <div class="col-md-2 mb-3">
                                <label for="sort_by" class="form-label">Sort</label>
                                <select class="form-select" id="sort_by" name="sort_by">
                                    <option value="created_at" {{ request('sort_by', 'created_at') == 'created_at' ? 'selected' : '' }}>Date</option>
                                    <option value="module_name" {{ request('sort_by') == 'module_name' ? 'selected' : '' }}>Name</option>
                                    <option value="nos_code" {{ request('sort_by') == 'nos_code' ? 'selected' : '' }}>NOS Code</option>
                                    <option value="hour" {{ request('sort_by') == 'hour' ? 'selected' : '' }}>Hours</option>
                                    <option value="credit" {{ request('sort_by') == 'credit' ? 'selected' : '' }}>Credit</option>
                                </select>
                            </div>
                            
                            <div class="col-md-7 mb-3 d-flex align-items-end">
                                <div class="d-flex gap-2">
                                    <button type="button" id="searchBtn" class="btn btn-primary">
                                        <i class="bi bi-search me-2"></i>
                                        Search & Filter
                                    </button>
                                    <button type="button" id="resetBtn" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-clockwise me-2"></i>
                                        Reset
                                    </button>
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

            <!-- Modules Table -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-ul me-2"></i>
                        Modules
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Loader -->
                    <div id="tableLoader" class="text-center py-5" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Loading modules...</p>
                    </div>
                    
                    <!-- Table Content -->
                    <div id="tableContent">
                        @include('admin.qualification-modules.partials.table', ['modules' => $modules, 'user' => $user])
                    </div>
                    
                    <!-- Pagination -->
                    <div id="paginationContent">
                        @include('admin.qualification-modules.partials.pagination', ['modules' => $modules])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Module Modal -->
<div class="modal fade" id="addModuleModal" tabindex="-1" aria-labelledby="addModuleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModuleModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>
                    Add New Module
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addModuleForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="module_name" class="form-label">Module Name *</label>
                        <input type="text" class="form-control" id="module_name" name="module_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="nos_code" class="form-label">NOS Code *</label>
                        <input type="text" class="form-control" id="nos_code" name="nos_code" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_optional" name="is_optional" value="1">
                            <label class="form-check-label" for="is_optional">
                                Optional Module
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="hour" class="form-label">Hours *</label>
                            <input type="number" class="form-control" id="hour" name="hour" min="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="credit" class="form-label">Credit *</label>
                            <input type="number" class="form-control" id="credit" name="credit" min="0" step="0.01" required>
                        </div>
                    </div>
                    
                    <!-- Exam Type Section -->
                    <div class="mb-3">
                        <label class="form-label">Exam Type</label>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_theory" name="is_theory" value="1" checked>
                                    <label class="form-check-label" for="is_theory">
                                        Theory
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_practical" name="is_practical" value="1">
                                    <label class="form-check-label" for="is_practical">
                                        Practical
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_viva" name="is_viva" value="1">
                                    <label class="form-check-label" for="is_viva">
                                        Viva
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Marks Section -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="full_mark" class="form-label">Full Mark</label>
                            <input type="number" class="form-control" id="full_mark" name="full_mark" min="0" placeholder="e.g., 100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="pass_mark" class="form-label">Pass Mark</label>
                            <input type="number" class="form-control" id="pass_mark" name="pass_mark" min="0" placeholder="e.g., 40">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span id="addModuleText">Add Module</span>
                        <span id="addModuleLoader" class="spinner-border spinner-border-sm d-none ms-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Module Modal -->
<div class="modal fade" id="editModuleModal" tabindex="-1" aria-labelledby="editModuleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModuleModalLabel">
                    <i class="bi bi-pencil me-2"></i>
                    Edit Module
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editModuleForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_module_id" name="module_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_module_name" class="form-label">Module Name *</label>
                        <input type="text" class="form-control" id="edit_module_name" name="module_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_nos_code" class="form-label">NOS Code *</label>
                        <input type="text" class="form-control" id="edit_nos_code" name="nos_code" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_is_optional" name="is_optional" value="1">
                            <label class="form-check-label" for="edit_is_optional">
                                Optional Module
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_hour" class="form-label">Hours *</label>
                            <input type="number" class="form-control" id="edit_hour" name="hour" min="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_credit" class="form-label">Credit *</label>
                            <input type="number" class="form-control" id="edit_credit" name="credit" min="0" step="0.01" required>
                        </div>
                    </div>
                    
                    <!-- Exam Type Section -->
                    <div class="mb-3">
                        <label class="form-label">Exam Type</label>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit_is_theory" name="is_theory" value="1">
                                    <label class="form-check-label" for="edit_is_theory">
                                        Theory
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit_is_practical" name="is_practical" value="1">
                                    <label class="form-check-label" for="edit_is_practical">
                                        Practical
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit_is_viva" name="is_viva" value="1">
                                    <label class="form-check-label" for="edit_is_viva">
                                        Viva
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Marks Section -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_full_mark" class="form-label">Full Mark</label>
                            <input type="number" class="form-control" id="edit_full_mark" name="full_mark" min="0" placeholder="e.g., 100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_pass_mark" class="form-label">Pass Mark</label>
                            <input type="number" class="form-control" id="edit_pass_mark" name="pass_mark" min="0" placeholder="e.g., 40">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span id="editModuleText">Update Module</span>
                        <span id="editModuleLoader" class="spinner-border spinner-border-sm d-none ms-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModuleModal" tabindex="-1" aria-labelledby="deleteModuleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModuleModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the module "<strong id="deleteModuleName"></strong>"?</p>
                <p class="text-danger"><small>This action cannot be undone and will also remove all qualification mappings.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteModule">
                    <span id="deleteModuleText">Delete</span>
                    <span id="deleteModuleLoader" class="spinner-border spinner-border-sm d-none ms-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Upload Excel Modal -->
<div class="modal fade" id="uploadExcelModal" tabindex="-1" aria-labelledby="uploadExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadExcelModalLabel">
                    <i class="bi bi-file-earmark-csv me-2"></i>
                    Upload Modules from CSV
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="uploadExcelForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Instructions:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Upload a CSV file (.csv) with the following columns:</li>
                            <li><strong>Module Name</strong> - Required</li>
                            <li><strong>NOS Code</strong> - Required (must be unique)</li>
                            <li><strong>Is Optional</strong> - Optional (Yes/No, Y/N, 1/0, true/false)</li>
                            <li><strong>Hours</strong> - Required (numeric)</li>
                            <li><strong>Credit</strong> - Required (numeric)</li>
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <label for="excel_file" class="form-label">CSV File *</label>
                        <input type="file" class="form-control" id="excel_file" name="excel_file" 
                               accept=".csv" required>
                        <div class="form-text">
                            Supported format: .csv (Max size: 5MB)
                            <br>
                            <a href="#" class="text-decoration-none" onclick="downloadTemplate()">
                                <i class="bi bi-download me-1"></i>Download Sample Template
                            </a>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="skip_duplicates" name="skip_duplicates" value="1" checked>
                            <label class="form-check-label" for="skip_duplicates">
                                Skip duplicate NOS codes (recommended)
                            </label>
                        </div>
                    </div>
                    
                    <!-- Upload Progress -->
                    <div id="uploadProgress" class="progress mb-3" style="display: none;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                             role="progressbar" style="width: 0%"></div>
                    </div>
                    
                    <!-- Upload Results -->
                    <div id="uploadResults" style="display: none;">
                        <div class="alert alert-success">
                            <h6><i class="bi bi-check-circle me-2"></i>Upload Summary</h6>
                            <div id="uploadSummary"></div>
                        </div>
                    </div>
                    
                    <!-- Upload Errors -->
                    <div id="uploadErrors" style="display: none;">
                        <div class="alert alert-warning">
                            <h6><i class="bi bi-exclamation-triangle me-2"></i>Upload Issues</h6>
                            <div id="uploadErrorDetails"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="uploadExcelBtn">
                        <span id="uploadExcelText">Upload Modules</span>
                        <span id="uploadExcelLoader" class="spinner-border spinner-border-sm d-none ms-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Modules page JavaScript loaded');
    
    // CSRF token setup
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    console.log('CSRF Token:', csrfToken);
    
    // Elements
    const searchFilterForm = document.getElementById('searchFilterForm');
    const searchInput = document.getElementById('search');
    const filterSelects = document.querySelectorAll('#searchFilterForm select');
    const filterInputs = document.querySelectorAll('#searchFilterForm input[type="number"]');
    const searchBtn = document.getElementById('searchBtn');
    const resetBtn = document.getElementById('resetBtn');
    const tableLoader = document.getElementById('tableLoader');
    const tableContent = document.getElementById('tableContent');
    const paginationContent = document.getElementById('paginationContent');
    const resultsSummary = document.getElementById('resultsSummary');
    const resultsText = document.getElementById('resultsText');
    
    console.log('Elements found:', {
        searchFilterForm: !!searchFilterForm,
        searchInput: !!searchInput,
        filterSelects: filterSelects.length,
        filterInputs: filterInputs.length,
        searchBtn: !!searchBtn,
        resetBtn: !!resetBtn,
        tableLoader: !!tableLoader,
        tableContent: !!tableContent,
        paginationContent: !!paginationContent
    });
    
    // Current search parameters
    let currentParams = new URLSearchParams();
    
    // Initialize with current URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.forEach((value, key) => {
        currentParams.set(key, value);
    });
    
    console.log('Initial URL params:', Object.fromEntries(currentParams));
    
    // Auto-submit on search input (with 500ms delay)
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            console.log('Search input changed:', this.value);
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentParams.set('search', this.value);
                console.log('Triggering search with:', this.value);
                loadModules();
            }, 500);
        });
    }
    
    // Auto-submit on filter changes
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            console.log('Filter changed:', this.name, this.value);
            currentParams.set(this.name, this.value);
            loadModules();
        });
    });
    
    // Auto-submit on number input changes (with delay)
    filterInputs.forEach(input => {
        let inputTimeout;
        input.addEventListener('input', function() {
            console.log('Number input changed:', this.name, this.value);
            clearTimeout(inputTimeout);
            inputTimeout = setTimeout(() => {
                currentParams.set(this.name, this.value);
                loadModules();
            }, 1000);
        });
    });
    
    // Manual search button
    if (searchBtn) {
        searchBtn.addEventListener('click', function() {
            console.log('Search button clicked');
            // Update current params from form
            const formData = new FormData(searchFilterForm);
            formData.forEach((value, key) => {
                if (value) {
                    currentParams.set(key, value);
                } else {
                    currentParams.delete(key);
                }
            });
            console.log('Search params:', Object.fromEntries(currentParams));
            loadModules();
        });
    }
    
    // Reset button
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            console.log('Reset button clicked');
            // Clear form
            searchFilterForm.reset();
            
            // Clear current params
            currentParams = new URLSearchParams();
            
            // Load without filters
            loadModules();
        });
    }
    
    // Clear search on Escape key
    if (searchInput) {
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                console.log('Escape key pressed');
                this.value = '';
                currentParams.delete('search');
                loadModules();
            }
        });
    }
    
    // Load modules via AJAX
    function loadModules() {
        console.log('Loading modules with params:', Object.fromEntries(currentParams));
        
        // Show loader
        tableLoader.style.display = 'block';
        tableContent.style.display = 'none';
        paginationContent.style.display = 'none';
        resultsSummary.style.display = 'none';
        
        // Update URL without reloading
        const newUrl = window.location.pathname + (currentParams.toString() ? '?' + currentParams.toString() : '');
        window.history.pushState({}, '', newUrl);
        
        const requestData = Object.fromEntries(currentParams);
        console.log('Sending AJAX request with data:', requestData);
        
        // Make AJAX request
        fetch('{{ route("admin.qualification-modules.ajax") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify(requestData)
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                // Update table content
                tableContent.innerHTML = data.html;
                tableContent.style.display = 'block';
                
                // Update pagination
                paginationContent.innerHTML = data.pagination;
                paginationContent.style.display = 'block';
                
                // Update results summary
                updateResultsSummary(data);
                
                // Re-attach event listeners to new elements
                attachEventListeners();
                
                console.log('Modules loaded successfully');
            } else {
                console.error('Failed to load modules:', data.message);
                showAlert('danger', data.message || 'Failed to load modules');
            }
        })
        .catch(error => {
            console.error('AJAX Error:', error);
            showAlert('danger', 'An error occurred while loading modules');
        })
        .finally(() => {
            // Hide loader
            tableLoader.style.display = 'none';
        });
    }
    
    // Update results summary
    function updateResultsSummary(data) {
        const params = Object.fromEntries(currentParams);
        let summaryText = `Showing ${data.from || 0} to ${data.to || 0} of ${data.total} module(s)`;
        
        if (params.search) {
            summaryText += ` matching "<strong>${params.search}</strong>"`;
        }
        if (params.is_optional) {
            if (params.is_optional === 'optional') {
                summaryText += ' that are <strong>optional</strong>';
            } else {
                summaryText += ' that are <strong>mandatory</strong>';
            }
        }
        if (params.hours_min || params.hours_max) {
            summaryText += ' with hours ';
            if (params.hours_min && params.hours_max) {
                summaryText += `between <strong>${params.hours_min}</strong> and <strong>${params.hours_max}</strong>`;
            } else if (params.hours_min) {
                summaryText += `≥ <strong>${params.hours_min}</strong>`;
            } else {
                summaryText += `≤ <strong>${params.hours_max}</strong>`;
            }
        }
        if (params.credit_min || params.credit_max) {
            summaryText += ' with credit ';
            if (params.credit_min && params.credit_max) {
                summaryText += `between <strong>${params.credit_min}</strong> and <strong>${params.credit_max}</strong>`;
            } else if (params.credit_min) {
                summaryText += `≥ <strong>${params.credit_min}</strong>`;
            } else {
                summaryText += `≤ <strong>${params.credit_max}</strong>`;
            }
        }
        if (params.qualifications_count) {
            if (params.qualifications_count === 'with_qualifications') {
                summaryText += ' with qualifications';
            } else {
                summaryText += ' without qualifications';
            }
        }
        
        resultsText.innerHTML = summaryText;
        resultsSummary.style.display = 'block';
    }
    
    // Attach event listeners to dynamically loaded elements
    function attachEventListeners() {
        console.log('Attaching event listeners to new elements');
        
        // Sortable Table Headers
        document.querySelectorAll('.sortable').forEach(header => {
            header.addEventListener('click', function() {
                const sortField = this.getAttribute('data-sort');
                const currentSortBy = currentParams.get('sort_by') || 'created_at';
                const currentSortOrder = currentParams.get('sort_order') || 'desc';
                
                let newSortOrder = 'asc';
                
                // If clicking the same field, toggle order
                if (currentSortBy === sortField) {
                    newSortOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';
                }
                
                // Update current parameters
                currentParams.set('sort_by', sortField);
                currentParams.set('sort_order', newSortOrder);
                
                // Update sort indicators
                updateSortIndicators(sortField, newSortOrder);
                
                // Load data with new sorting
                loadModules();
            });
        });
        
        // Pagination Links
        document.querySelectorAll('.pagination .page-link[data-page]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = this.getAttribute('data-page');
                currentParams.set('page', page);
                loadModules();
            });
        });
        
        // Edit Module Buttons
        document.querySelectorAll('.edit-module-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const module = JSON.parse(this.getAttribute('data-module'));
                console.log('Edit button clicked, module data:', module);
                
                // Populate form fields
                document.getElementById('edit_module_id').value = module.id;
                document.getElementById('edit_module_name').value = module.module_name;
                document.getElementById('edit_nos_code').value = module.nos_code;
                document.getElementById('edit_hour').value = module.hour;
                document.getElementById('edit_credit').value = module.credit;
                document.getElementById('edit_is_optional').checked = module.is_optional;
                
                // Populate new exam type fields
                document.getElementById('edit_is_viva').checked = module.is_viva || false;
                document.getElementById('edit_is_practical').checked = module.is_practical || false;
                document.getElementById('edit_is_theory').checked = module.is_theory !== false; // Default to true if not set
                
                // Populate mark fields
                document.getElementById('edit_full_mark').value = module.full_mark || '';
                document.getElementById('edit_pass_mark').value = module.pass_mark || '';
                
                // Debug: Check if values were set
                console.log('Form values after setting:');
                console.log('module_id:', document.getElementById('edit_module_id').value);
                console.log('module_name:', document.getElementById('edit_module_name').value);
                console.log('nos_code:', document.getElementById('edit_nos_code').value);
                console.log('hour:', document.getElementById('edit_hour').value);
                console.log('credit:', document.getElementById('edit_credit').value);
                console.log('is_optional:', document.getElementById('edit_is_optional').checked);
                
                // Show modal
                const modalElement = document.getElementById('editModuleModal');
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
                
                // Ensure modal is properly shown
                console.log('Modal element:', modalElement);
                console.log('Modal backdrop:', document.querySelector('.modal-backdrop'));
                
                // Test if inputs are editable after modal is shown
                setTimeout(() => {
                    console.log('Testing input editability after modal shown:');
                    const moduleNameInput = document.getElementById('edit_module_name');
                    const nosCodeInput = document.getElementById('edit_nos_code');
                    console.log('module_name input readonly:', moduleNameInput.readOnly);
                    console.log('module_name input disabled:', moduleNameInput.disabled);
                    console.log('nos_code input readonly:', nosCodeInput.readOnly);
                    console.log('nos_code input disabled:', nosCodeInput.disabled);
                    
                    // Try to focus and test editing
                    moduleNameInput.focus();
                    console.log('module_name input focused');
                }, 500);
            });
        });
        
        // Delete Module Buttons
        document.querySelectorAll('.delete-module-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const moduleId = this.getAttribute('data-module-id');
                const moduleName = this.getAttribute('data-module-name');
                
                document.getElementById('deleteModuleName').textContent = moduleName;
                
                // Store module ID for deletion
                document.getElementById('confirmDeleteModule').setAttribute('data-module-id', moduleId);
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('deleteModuleModal'));
                modal.show();
            });
        });
        
        // Reset form when add modal is closed
        const addModuleModal = document.getElementById('addModuleModal');
        if (addModuleModal) {
            addModuleModal.addEventListener('hidden.bs.modal', function() {
                document.getElementById('addModuleForm').reset();
            });
        }
        

    }
    
    // Add Module Form
    const addModuleForm = document.getElementById('addModuleForm');
    if (addModuleForm) {
        addModuleForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Add module form submitted');
            
            const formData = new FormData(this);
            const addModuleText = document.getElementById('addModuleText');
            const addModuleLoader = document.getElementById('addModuleLoader');
            
            // Handle checkbox state - if unchecked, add a hidden field with value 0
            const isOptionalCheckbox = document.getElementById('is_optional');
            if (!isOptionalCheckbox.checked) {
                formData.append('is_optional', '0');
            }
            
            // Handle new checkbox fields
            const isVivaCheckbox = document.getElementById('is_viva');
            if (!isVivaCheckbox.checked) {
                formData.append('is_viva', '0');
            }
            
            const isPracticalCheckbox = document.getElementById('is_practical');
            if (!isPracticalCheckbox.checked) {
                formData.append('is_practical', '0');
            }
            
            const isTheoryCheckbox = document.getElementById('is_theory');
            if (!isTheoryCheckbox.checked) {
                formData.append('is_theory', '0');
            }
            
            // Debug: Log form data
            console.log('Add module form data being sent:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            
            // Show loading state
            addModuleText.textContent = 'Adding...';
            addModuleLoader.classList.remove('d-none');
            
            fetch('{{ route("admin.qualification-modules.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Add module response:', data);
                if (data.success) {
                    // Close modal and refresh page
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addModuleModal'));
                    if (modal) {
                        modal.hide();
                    }
                    window.location.reload();
                } else {
                    // Handle validation errors
                    if (data.errors) {
                        let errorMessage = 'Validation errors:\n';
                        Object.keys(data.errors).forEach(field => {
                            errorMessage += `${field}: ${data.errors[field].join(', ')}\n`;
                        });
                        alert(errorMessage);
                } else {
                    alert(data.message || 'Failed to add module');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                console.error('Response:', error.response);
                alert('An error occurred while adding the module');
            })
            .finally(() => {
                // Reset loading state
                addModuleText.textContent = 'Add Module';
                addModuleLoader.classList.add('d-none');
            });
        });
    }
    
    // Edit Module Form
    const editModuleForm = document.getElementById('editModuleForm');
    if (editModuleForm) {
        editModuleForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Edit module form submitted');
            
            const moduleId = document.getElementById('edit_module_id').value;
            console.log('Module ID:', moduleId);
            const formData = new FormData(this);
            const editModuleText = document.getElementById('editModuleText');
            const editModuleLoader = document.getElementById('editModuleLoader');
            
            // Ensure _method is included for PUT request
            formData.append('_method', 'PUT');
            
            // Handle checkbox state - if unchecked, add a hidden field with value 0
            const isOptionalCheckbox = document.getElementById('edit_is_optional');
            if (!isOptionalCheckbox.checked) {
                formData.append('is_optional', '0');
            }
            
            // Handle new checkbox fields
            const isVivaCheckbox = document.getElementById('edit_is_viva');
            if (!isVivaCheckbox.checked) {
                formData.append('is_viva', '0');
            }
            
            const isPracticalCheckbox = document.getElementById('edit_is_practical');
            if (!isPracticalCheckbox.checked) {
                formData.append('is_practical', '0');
            }
            
            const isTheoryCheckbox = document.getElementById('edit_is_theory');
            if (!isTheoryCheckbox.checked) {
                formData.append('is_theory', '0');
            }
            
            // Debug: Log form data
            console.log('Form data being sent:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            
            // Show loading state
            editModuleText.textContent = 'Updating...';
            editModuleLoader.classList.remove('d-none');
            
            fetch(`/admin/qualification-modules/${moduleId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Edit module response:', data);
                if (data.success) {
                    // Close modal and refresh page
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editModuleModal'));
                    if (modal) {
                        modal.hide();
                    }
                    window.location.reload();
                } else {
                    // Handle validation errors
                    if (data.errors) {
                        let errorMessage = 'Validation errors:\n';
                        Object.keys(data.errors).forEach(field => {
                            errorMessage += `${field}: ${data.errors[field].join(', ')}\n`;
                        });
                        alert(errorMessage);
                } else {
                    alert(data.message || 'Failed to update module');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                console.error('Response:', error.response);
                alert('An error occurred while updating the module');
            })
            .finally(() => {
                // Reset loading state
                editModuleText.textContent = 'Update Module';
                editModuleLoader.classList.add('d-none');
            });
        });
    }
    
    // Confirm Delete Button
    const confirmDeleteModule = document.getElementById('confirmDeleteModule');
    if (confirmDeleteModule) {
        confirmDeleteModule.addEventListener('click', function() {
            const moduleId = this.getAttribute('data-module-id');
            const deleteModuleText = document.getElementById('deleteModuleText');
            const deleteModuleLoader = document.getElementById('deleteModuleLoader');
            
            // Show loading state
            deleteModuleText.textContent = 'Deleting...';
            deleteModuleLoader.classList.remove('d-none');
            
            fetch(`/admin/qualification-modules/${moduleId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Refresh page directly
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to delete module');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the module');
            })
            .finally(() => {
                // Reset loading state
                deleteModuleText.textContent = 'Delete';
                deleteModuleLoader.classList.add('d-none');
            });
        });
    }
    
    // Helper function to show alerts
    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // Insert at the top of the main content
        const mainContent = document.querySelector('.main-content');
        mainContent.insertBefore(alertDiv, mainContent.firstChild);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
    
    // Initial load of event listeners
    attachEventListeners();
    
    // Upload Excel Form
    const uploadExcelForm = document.getElementById('uploadExcelForm');
    if (uploadExcelForm) {
        uploadExcelForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Upload Excel form submitted');
            
            const formData = new FormData(this);
            const uploadExcelText = document.getElementById('uploadExcelText');
            const uploadExcelLoader = document.getElementById('uploadExcelLoader');
            const uploadProgress = document.getElementById('uploadProgress');
            const uploadResults = document.getElementById('uploadResults');
            const uploadErrors = document.getElementById('uploadErrors');
            const progressBar = uploadProgress.querySelector('.progress-bar');
            
            // Reset previous results
            uploadResults.style.display = 'none';
            uploadErrors.style.display = 'none';
            
            // Show loading state
            uploadExcelText.textContent = 'Uploading...';
            uploadExcelLoader.classList.remove('d-none');
            uploadProgress.style.display = 'block';
            progressBar.style.width = '0%';
            progressBar.textContent = '0%';
            
            // Simulate progress
            let progress = 0;
            const progressInterval = setInterval(() => {
                progress += Math.random() * 15;
                if (progress > 90) progress = 90;
                progressBar.style.width = progress + '%';
                progressBar.textContent = Math.round(progress) + '%';
            }, 200);
            
            fetch('{{ route("admin.qualification-modules.upload-excel") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                clearInterval(progressInterval);
                progressBar.style.width = '100%';
                progressBar.textContent = '100%';
                
                console.log('Upload response:', data);
                
                if (data.success) {
                    // Show success results
                    const summary = document.getElementById('uploadSummary');
                    summary.innerHTML = `
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Total Records:</strong> ${data.total_records}
                            </div>
                            <div class="col-md-4">
                                <strong>Successfully Added:</strong> <span class="text-success">${data.added_count}</span>
                            </div>
                            <div class="col-md-4">
                                <strong>Skipped (Duplicates):</strong> <span class="text-warning">${data.skipped_count}</span>
                            </div>
                        </div>
                        ${data.errors && data.errors.length > 0 ? `
                            <div class="mt-3">
                                <strong>Errors:</strong> ${data.errors.length}
                                <div class="mt-2">
                                    <small class="text-muted">${data.errors.join('<br>')}</small>
                                </div>
                            </div>
                        ` : ''}
                    `;
                    uploadResults.style.display = 'block';
                    
                    // Refresh the modules list after successful upload
                    setTimeout(() => {
                        loadModules();
                    }, 2000);
                } else {
                    // Show error results
                    const errorDetails = document.getElementById('uploadErrorDetails');
                    errorDetails.innerHTML = `
                        <p><strong>Error:</strong> ${data.message}</p>
                        ${data.errors && data.errors.length > 0 ? `
                            <div class="mt-2">
                                <strong>Details:</strong>
                                <ul class="mb-0 mt-1">
                                    ${data.errors.map(error => `<li>${error}</li>`).join('')}
                                </ul>
                            </div>
                        ` : ''}
                    `;
                    uploadErrors.style.display = 'block';
                }
            })
            .catch(error => {
                clearInterval(progressInterval);
                console.error('Upload Error:', error);
                
                const errorDetails = document.getElementById('uploadErrorDetails');
                errorDetails.innerHTML = '<p><strong>Error:</strong> An error occurred while uploading the file. Please try again.</p>';
                uploadErrors.style.display = 'block';
            })
            .finally(() => {
                // Reset loading state
                uploadExcelText.textContent = 'Upload Modules';
                uploadExcelLoader.classList.add('d-none');
                
                // Hide progress after 2 seconds
                setTimeout(() => {
                    uploadProgress.style.display = 'none';
                }, 2000);
            });
        });
    }
    
    console.log('Modules page JavaScript initialization complete');
    
    // Initialize sort indicators
    initializeSortIndicators();
    
    // Download template function
    window.downloadTemplate = function() {
        // Redirect to the server-side template download
        window.location.href = '{{ route("admin.qualification-modules.download-template") }}';
    };
});

// Update sort indicators on table headers
function updateSortIndicators(activeField, activeOrder) {
    document.querySelectorAll('.sortable').forEach(header => {
        const field = header.getAttribute('data-sort');
        
        // Remove all active classes and reset icons
        header.classList.remove('active');
        header.removeAttribute('data-sort-direction');
        
        // Set active state for current field
        if (field === activeField) {
            header.classList.add('active');
            header.setAttribute('data-sort-direction', activeOrder);
        }
    });
}

// Initialize sort indicators based on current URL parameters
function initializeSortIndicators() {
    const urlParams = new URLSearchParams(window.location.search);
    const sortBy = urlParams.get('sort_by') || 'created_at';
    const sortOrder = urlParams.get('sort_order') || 'desc';
    
    updateSortIndicators(sortBy, sortOrder);
}
</script>
@endsection 