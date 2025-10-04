@extends('admin.layout')

@section('title', 'Create Exam Schedule')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="bi bi-plus-circle me-2"></i>
                        Create Exam Schedule
                    </h1>
                    <p class="text-muted">Create a new exam schedule for your students</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.faculty.exam-schedules.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Back to List
                    </a>
                </div>
            </div>

            <!-- Validation Error Alert -->
            <div class="alert alert-danger" id="validationErrors" style="display: none;">
                <h6 class="alert-heading">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Please correct the following errors:
                </h6>
                <ul class="mb-0" id="errorList">
                    <!-- Error messages will be populated here -->
                </ul>
            </div>

            <!-- Success Alert -->
            <div class="alert alert-success" id="successAlert" style="display: none;">
                <h6 class="alert-heading">
                    <i class="bi bi-check-circle me-2"></i>
                    Success!
                </h6>
                <p class="mb-0" id="successMessage">
                    <!-- Success message will be populated here -->
                </p>
            </div>

            <!-- Loading Overlay -->
            <div class="loading-overlay" id="loadingOverlay" style="display: none;">
                <div class="loading-content">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-white" id="loadingMessage">Saving exam schedule...</p>
                </div>
            </div>

            <!-- Form Progress Indicator -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="progress-step active" id="step1">
                                <i class="bi bi-info-circle fs-4"></i>
                                <div class="mt-2">Basic Info</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="progress-step" id="step2">
                                <i class="bi bi-people fs-4"></i>
                                <div class="mt-2">Students</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="progress-step" id="step3">
                                <i class="bi bi-puzzle fs-4"></i>
                                <div class="mt-2">Modules</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="progress-step" id="step4">
                                <i class="bi bi-check-circle fs-4"></i>
                                <div class="mt-2">Submit</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form id="examScheduleForm" enctype="multipart/form-data">
                @csrf
                
                <!-- Basic Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            Basic Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="course_name" class="form-label">Course/Qualification *</label>
                                <select class="form-select" id="course_name" name="course_name" required>
                                    <option value="">Select Course/Qualification</option>
                                    @foreach($qualifications as $qualification)
                                        <option value="{{ $qualification->qf_name }}">{{ $qualification->qf_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="batch_code" class="form-label">Batch Code *</label>
                                <input type="text" class="form-control" id="batch_code" name="batch_code" 
                                       placeholder="e.g., BATCH2024" maxlength="10" required>
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Enter batch code using only letters and numbers (max 10 characters)
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="semester" class="form-label">Semester *</label>
                                <select class="form-select" id="semester" name="semester" required>
                                    <option value="">Select Semester</option>
                                    @foreach($semesters as $semester)
                                        <option value="{{ $semester }}">Semester {{ $semester }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Select the semester for which the exam is being conducted (1-8)
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="exam_type" class="form-label">Exam Type *</label>
                                <select class="form-select" id="exam_type" name="exam_type" required>
                                    <option value="">Select Exam Type</option>
                                    <option value="Internal">Internal</option>
                                    <option value="Final">Final</option>
                                    <option value="Special Final">Special Final</option>
                                </select>
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Choose the type of examination being conducted
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="exam_coordinator" class="form-label">Exam Coordinator</label>
                                <input type="text" class="form-control" id="exam_coordinator" name="exam_coordinator" 
                                       value="{{ $user->name }}" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="exam_start_date" class="form-label">Exam Start Date *</label>
                                <input type="date" class="form-control" id="exam_start_date" name="exam_start_date" 
                                       min="{{ date('Y-m-d') }}" required>
                                <div class="form-text">
                                    <i class="bi bi-calendar me-1"></i>
                                    Select the start date of the examination period (must be today or future)
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="exam_end_date" class="form-label">Exam End Date *</label>
                                <input type="date" class="form-control" id="exam_end_date" name="exam_end_date" required>
                                <div class="form-text">
                                    <i class="bi bi-calendar me-1"></i>
                                    Select the end date of the examination period (must be after start date)
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="program_number" class="form-label">Program Number (P No) *</label>
                                <input type="text" class="form-control" id="program_number" name="program_number" 
                                       placeholder="e.g., P2024/001" maxlength="255" required>
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Enter the program number to fetch eligible students (letters, numbers, hyphens, and forward slashes only)
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="centre_id" class="form-label">Centre *</label>
                                <select class="form-select" id="centre_id" name="centre_id" required>
                                    <option value="">Select Centre</option>
                                </select>
                                <div class="form-text">Select the centre where the exam will be conducted</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-outline-primary d-block" id="fetchStudentsBtn">
                                    <i class="bi bi-people me-2"></i>
                                    Fetch Students
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Student Selection -->
                <div class="card shadow-sm mb-4" id="studentSection" style="display: none;">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-people me-2"></i>
                            Student Selection
                            <span class="badge bg-danger ms-2" id="studentRequiredBadge">Required</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="studentList">
                            <!-- Students will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- Module Details -->
                <div class="card shadow-sm mb-4" id="moduleSection" style="display: none;">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-puzzle me-2"></i>
                                Module Details
                                <span class="badge bg-info ms-2" id="moduleCountBadge">0 selected</span>
                            </h5>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="restoreModulesBtn" style="display: none;">
                                <i class="bi bi-arrow-clockwise me-1"></i>
                                Restore All Modules
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="moduleList">
                            <!-- Modules will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- File Uploads -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-file-earmark me-2"></i>
                            File Uploads
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="course_completion_file" class="form-label">Course Completion File</label>
                                <input type="file" class="form-control" id="course_completion_file" name="course_completion_file" 
                                       accept=".pdf,.doc,.docx" required>
                                <div class="form-text">
                                    <i class="bi bi-file-earmark me-1"></i>
                                    Upload course completion certificate or related document (PDF, DOC, DOCX only, max 5MB)
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="student_details_file" class="form-label">Student Details File</label>
                                <input type="file" class="form-control" id="student_details_file" name="student_details_file" 
                                       accept=".pdf,.doc,.docx,.xls,.xlsx" required>
                                <div class="form-text">
                                    <i class="bi bi-file-earmark me-1"></i>
                                    Upload student details or attendance sheet (PDF, DOC, DOCX, XLS, XLSX only, max 5MB)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="terms_accepted" name="terms_accepted" value="1" required>
                            <label class="form-check-label" for="terms_accepted">
                                I agree to the Terms and Conditions *
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.faculty.exam-schedules.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Cancel
                    </a>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary" id="validateFormBtn">
                            <i class="bi bi-check-circle me-2"></i>
                            Validate Form
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-send me-2"></i>
                            SAVE
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Student Selection Modal -->
<div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentModalLabel">Select Students</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="studentModalContent">
                    <!-- Student list will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmStudentsBtn">
                    <i class="bi bi-check-circle me-2"></i>
                    Confirm Selection
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Create Exam Schedule page JavaScript loaded');
    
    // CSRF token setup
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Ensure CSRF token is available
    if (!csrfToken) {
        console.error('CSRF token not found. Please refresh the page.');
        alert('CSRF token not found. Please refresh the page and try again.');
    }
    
    // Elements
    const form = document.getElementById('examScheduleForm');
    const courseSelect = document.getElementById('course_name');
    const programNumberInput = document.getElementById('program_number');
    const centreSelect = document.getElementById('centre_id');
    const fetchStudentsBtn = document.getElementById('fetchStudentsBtn');
    const studentSection = document.getElementById('studentSection');
    const moduleSection = document.getElementById('moduleSection');
    const studentList = document.getElementById('studentList');
    const moduleList = document.getElementById('moduleList');
    const studentRequiredBadge = document.getElementById('studentRequiredBadge');
    
    // Validation state
    let validationErrors = {};
    let isFormValid = false;
    let isSubmitting = false;
    
    // Initialize validation
    initializeValidation();
    
    // Load centres on page load
    loadCentres();
    
    // Load centres for the user's TC
    function loadCentres() {
        // Use different route based on user role (faculty vs admin/head)
        const centresUrl = '{{ Auth::user()->user_role === 5 ? route("admin.faculty.centres.get-for-tc") : route("admin.centres.get-for-tc") }}';
        
        console.log('Loading centres from URL:', centresUrl);
        console.log('User role:', {{ Auth::user()->user_role }});
        console.log('User TC:', '{{ Auth::user()->from_tc }}');
        
        fetch(centresUrl, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Centres response:', data);
            if (data.success) {
                centreSelect.innerHTML = '<option value="">Select Centre</option>';
                data.centres.forEach(centre => {
                    centreSelect.innerHTML += `<option value="${centre.id}">${centre.centre_name}</option>`;
                });
                console.log('Loaded ' + data.centres.length + ' centres');
            } else {
                console.error('Failed to load centres:', data.message);
                console.error('Debug info:', data.debug);
                centreSelect.innerHTML = '<option value="">No centres available</option>';
            }
        })
        .catch(error => {
            console.error('Error loading centres:', error);
            centreSelect.innerHTML = '<option value="">Error loading centres</option>';
        });
    }
    
    // Store selected students and modules
    let selectedStudents = [];
    let selectedModules = [];
    let allModules = []; // Store all original modules
    let studentsFetched = false;
    
    // Course selection change
    courseSelect.addEventListener('change', function() {
        const courseName = this.value;
        console.log('Course selection changed to:', courseName);
        
        if (courseName) {
            console.log('Loading modules for course:', courseName);
            loadModulesByQualification(courseName);
        } else {
            console.log('No course selected, hiding module section');
            moduleSection.style.display = 'none';
        }
    });
    
    // Restore all modules button
    document.getElementById('restoreModulesBtn').addEventListener('click', function() {
        if (allModules.length > 0) {
            displayModules(allModules);
            showAlert('success', 'All modules have been restored to the exam schedule.');
        }
    });
    
    // Fetch students button
    fetchStudentsBtn.addEventListener('click', function() {
        const programNumber = programNumberInput.value.trim();
        if (!programNumber) {
            alert('Please enter a program number');
            programNumberInput.focus();
            return;
        }
        loadStudentsByProgram(programNumber);
    });
    
    // Load students by program number
    function loadStudentsByProgram(programNumber) {
        fetchStudentsBtn.disabled = true;
        fetchStudentsBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Loading...';
        
        fetch(`/admin/faculty/exam-schedules/students/by-program?program_number=${encodeURIComponent(programNumber)}`, {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayStudentModal(data.students, data.warning);
                studentsFetched = true;
            } else {
                alert(data.message || 'Failed to fetch students');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while fetching students');
        })
        .finally(() => {
            fetchStudentsBtn.disabled = false;
            fetchStudentsBtn.innerHTML = '<i class="bi bi-people me-2"></i>Fetch Students';
        });
    }
    
    // Display student selection modal
    function displayStudentModal(students, warning = null) {
        let html = '';
        
        // Add warning message if provided
        if (warning) {
            html += `
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    ${warning}
                </div>
            `;
        }
        
        html += `
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Select the students who are eligible for this exam. You can select multiple students.
                <strong>At least one student must be selected to proceed.</strong>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Available Students (${students.length})</h6>
                <button type="button" class="btn btn-outline-primary btn-sm" id="selectAllStudentsBtn">
                    <i class="bi bi-check-all me-1"></i>
                    Select All
                </button>
            </div>
            <div class="row">
        `;
        
        students.forEach(student => {
            html += `
                <div class="col-md-6 mb-3">
                    <div class="form-check">
                        <input class="form-check-input student-checkbox" type="checkbox" 
                               value="${student.roll_no}" id="student_${student.roll_no}">
                        <label class="form-check-label" for="student_${student.roll_no}">
                            <strong>${student.name}</strong><br>
                            <small class="text-muted">Roll No: ${student.roll_no}</small>
                        </label>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        
        document.getElementById('studentModalContent').innerHTML = html;
        
        // Add event listener for Select All button
        const selectAllBtn = document.getElementById('selectAllStudentsBtn');
        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function() {
                const studentCheckboxes = document.querySelectorAll('.student-checkbox');
                const allChecked = Array.from(studentCheckboxes).every(cb => cb.checked);
                
                studentCheckboxes.forEach(checkbox => {
                    checkbox.checked = !allChecked;
                });
                
                // Update button text
                this.innerHTML = allChecked ? 
                    '<i class="bi bi-check-all me-1"></i>Select All' : 
                    '<i class="bi bi-x-lg me-1"></i>Deselect All';
            });
        }
        
        const modal = new bootstrap.Modal(document.getElementById('studentModal'));
        modal.show();
    }
    
    // Confirm student selection
    document.getElementById('confirmStudentsBtn').addEventListener('click', function() {
        const selectedCheckboxes = document.querySelectorAll('.student-checkbox:checked');
        selectedStudents = [];
        
        selectedCheckboxes.forEach(checkbox => {
            const rollNo = checkbox.value;
            const name = checkbox.closest('.form-check').querySelector('label strong').textContent;
            selectedStudents.push({
                student_roll_no: rollNo,
                is_selected: true
            });
        });
        
        if (selectedStudents.length === 0) {
            alert('Please select at least one student to proceed');
            return;
        }
        
        displaySelectedStudents();
        bootstrap.Modal.getInstance(document.getElementById('studentModal')).hide();
        
        // Update the required badge
        studentRequiredBadge.textContent = `${selectedStudents.length} Selected`;
        studentRequiredBadge.className = 'badge bg-success ms-2';
    });
    
    // Display selected students
    function displaySelectedStudents() {
        let html = `
            <div class="alert alert-success">
                <i class="bi bi-check-circle me-2"></i>
                ${selectedStudents.length} student(s) selected
            </div>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Roll No</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        selectedStudents.forEach(student => {
            html += `
                <tr>
                    <td>${student.student_roll_no}</td>
                    <td><span class="badge bg-success">Selected</span></td>
                </tr>
            `;
        });
        
        html += `
                    </tbody>
                </table>
            </div>
        `;
        
        studentList.innerHTML = html;
        studentSection.style.display = 'block';
    }
    
    // Load modules by qualification
    function loadModulesByQualification(qualificationName) {
        console.log('Loading modules for qualification:', qualificationName);
        
        // Show loading state
        moduleList.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading modules for ${qualificationName}...</p>
            </div>
        `;
        moduleSection.style.display = 'block';
        
        fetch(`/admin/faculty/exam-schedules/modules/by-qualification?qualification_name=${encodeURIComponent(qualificationName)}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Modules data:', data);
            if (data.success) {
                allModules = data.modules; // Store all modules
                displayModules(data.modules);
            } else {
                moduleList.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        ${data.message || 'Failed to fetch modules'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading modules:', error);
            moduleList.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    An error occurred while fetching modules. Please try again.
                </div>
            `;
        });
    }
    
    // Display modules
    function displayModules(modules) {
        console.log('Displaying modules:', modules);
        
        if (!modules || modules.length === 0) {
            moduleList.innerHTML = `
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    No modules found for this qualification. Please contact the administrator.
                </div>
            `;
            return;
        }
        
        let html = `
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                <strong>${modules.length} module(s)</strong> are mapped to this qualification. 
                Modules with both Theory and Practical will be split into separate exam entries.
                <br><small class="mt-1 d-block">All remaining modules must have complete information filled out.</small>
            </div>
        `;
        
        let moduleIndex = 0;
        modules.forEach((module, originalIndex) => {
            // If module has both theory and practical, create separate entries
            if (module.is_theory && module.is_practical) {
                // Theory Exam Entry
            html += `
                    <div class="card mb-3 module-card" data-module-id="${module.id}" data-exam-type="theory">
                        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                        <h6 class="mb-0">
                            <i class="bi bi-puzzle me-2"></i>
                                Module ${moduleIndex + 1}: ${module.module_name} - Theory Exam
                        </h6>
                            <button type="button" class="btn btn-sm btn-outline-light remove-module-btn" 
                                    data-module-id="${module.id}" data-module-name="${module.module_name} - Theory">
                            <i class="bi bi-trash me-1"></i>
                            Remove Module
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Module Name</label>
                                <input type="text" class="form-control" value="${module.module_name} - Theory" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NOS Code</label>
                                    <input type="text" class="form-control" name="modules[${moduleIndex}][nos_code]" 
                                       value="${module.nos_code}" readonly>
                                    <input type="hidden" name="modules[${moduleIndex}][is_theory]" value="1">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                               id="module_${moduleIndex}_theory" name="modules[${moduleIndex}][is_theory]" value="1" checked disabled>
                                        <label class="form-check-label" for="module_${moduleIndex}_theory">
                                        Theory Exam
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                               id="module_${moduleIndex}_practical" name="modules[${moduleIndex}][is_practical]" value="1" disabled>
                                        <label class="form-check-label" for="module_${moduleIndex}_practical">
                                        Practical Exam
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Venue *</label>
                                    <input type="text" class="form-control module-input" name="modules[${moduleIndex}][venue]" 
                                           placeholder="Enter venue name" minlength="2" maxlength="100" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Invigilator *</label>
                                    <input type="text" class="form-control module-input" name="modules[${moduleIndex}][invigilator]" 
                                           placeholder="Enter invigilator name" minlength="2" maxlength="100" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Exam Date *</label>
                                    <input type="date" class="form-control module-input" name="modules[${moduleIndex}][exam_date]" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Start Time *</label>
                                    <input type="time" class="form-control module-input" name="modules[${moduleIndex}][start_time]" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">End Time *</label>
                                    <input type="time" class="form-control module-input" name="modules[${moduleIndex}][end_time]" required>
                            </div>
                        </div>
                    </div>
                </div>
            `;
                moduleIndex++;

                // Practical Exam Entry
                html += `
                    <div class="card mb-3 module-card" data-module-id="${module.id}" data-exam-type="practical">
                        <div class="card-header d-flex justify-content-between align-items-center bg-success text-white">
                            <h6 class="mb-0">
                                <i class="bi bi-puzzle me-2"></i>
                                Module ${moduleIndex + 1}: ${module.module_name} - Practical Exam
                            </h6>
                            <button type="button" class="btn btn-sm btn-outline-light remove-module-btn" 
                                    data-module-id="${module.id}" data-module-name="${module.module_name} - Practical">
                                <i class="bi bi-trash me-1"></i>
                                Remove Module
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                                            <div class="col-md-6 mb-3">
                                <label class="form-label">Module Name</label>
                                <input type="text" class="form-control" value="${module.module_name} - Practical" readonly>
                            </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">NOS Code</label>
                                    <input type="text" class="form-control" name="modules[${moduleIndex}][nos_code]" 
                                           value="${module.nos_code}" readonly>
                                    <input type="hidden" name="modules[${moduleIndex}][is_theory]" value="0">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               id="module_${moduleIndex}_theory" name="modules[${moduleIndex}][is_theory]" value="0" disabled>
                                        <label class="form-check-label" for="module_${moduleIndex}_theory">
                                            Theory Exam
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               id="module_${moduleIndex}_practical" name="modules[${moduleIndex}][is_practical]" value="1" checked disabled>
                                        <label class="form-check-label" for="module_${moduleIndex}_practical">
                                            Practical Exam
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Venue *</label>
                                    <input type="text" class="form-control module-input" name="modules[${moduleIndex}][venue]" 
                                           placeholder="Enter venue name" minlength="2" maxlength="100" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Invigilator *</label>
                                    <input type="text" class="form-control module-input" name="modules[${moduleIndex}][invigilator]" 
                                           placeholder="Enter invigilator name" minlength="2" maxlength="100" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Exam Date *</label>
                                    <input type="date" class="form-control module-input" name="modules[${moduleIndex}][exam_date]" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Start Time *</label>
                                    <input type="time" class="form-control module-input" name="modules[${moduleIndex}][start_time]" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">End Time *</label>
                                    <input type="time" class="form-control module-input" name="modules[${moduleIndex}][end_time]" required>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                moduleIndex++;
            } else {
                // Single exam type (Theory only or Practical only)
                const examType = module.is_theory ? 'Theory' : 'Practical';
                const bgColor = module.is_theory ? 'bg-primary' : 'bg-success';
                const textColor = 'text-white';
                
                html += `
                    <div class="card mb-3 module-card" data-module-id="${module.id}" data-exam-type="${examType.toLowerCase()}">
                        <div class="card-header d-flex justify-content-between align-items-center ${bgColor} ${textColor}">
                            <h6 class="mb-0">
                                <i class="bi bi-puzzle me-2"></i>
                                Module ${moduleIndex + 1}: ${module.module_name} - ${examType} Exam
                            </h6>
                            <button type="button" class="btn btn-sm btn-outline-light remove-module-btn" 
                                    data-module-id="${module.id}" data-module-name="${module.module_name} - ${examType}">
                                <i class="bi bi-trash me-1"></i>
                                Remove Module
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                                            <div class="col-md-6 mb-3">
                                <label class="form-label">Module Name</label>
                                <input type="text" class="form-control" value="${module.module_name} - ${examType}" readonly>
                            </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">NOS Code</label>
                                    <input type="text" class="form-control" name="modules[${moduleIndex}][nos_code]" 
                                           value="${module.nos_code}" readonly>
                                    <input type="hidden" name="modules[${moduleIndex}][is_theory]" value="${module.is_theory ? '1' : '0'}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               id="module_${moduleIndex}_theory" name="modules[${moduleIndex}][is_theory]" value="1" ${module.is_theory ? 'checked' : ''} disabled>
                                        <label class="form-check-label" for="module_${moduleIndex}_theory">
                                            Theory Exam
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               id="module_${moduleIndex}_practical" name="modules[${moduleIndex}][is_practical]" value="1" ${module.is_practical ? 'checked' : ''} disabled>
                                        <label class="form-check-label" for="module_${moduleIndex}_practical">
                                            Practical Exam
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Venue *</label>
                                    <input type="text" class="form-control module-input" name="modules[${moduleIndex}][venue]" 
                                           placeholder="Enter venue name" minlength="2" maxlength="100" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Invigilator *</label>
                                    <input type="text" class="form-control module-input" name="modules[${moduleIndex}][invigilator]" 
                                           placeholder="Enter invigilator name" minlength="2" maxlength="100" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Exam Date *</label>
                                    <input type="date" class="form-control module-input" name="modules[${moduleIndex}][exam_date]" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Start Time *</label>
                                    <input type="time" class="form-control module-input" name="modules[${moduleIndex}][start_time]" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">End Time *</label>
                                    <input type="time" class="form-control module-input" name="modules[${moduleIndex}][end_time]" required>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                moduleIndex++;
            }
        });
        
        moduleList.innerHTML = html;
        moduleSection.style.display = 'block';
        
        // Update module count badge
        updateModuleCount();
        
        // Add event listeners for remove module buttons
        document.querySelectorAll('.remove-module-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const moduleId = this.getAttribute('data-module-id');
                const moduleName = this.getAttribute('data-module-name');
                
                if (confirm(`Are you sure you want to remove "${moduleName}" from this exam schedule?`)) {
                    const moduleCard = this.closest('.module-card');
                    moduleCard.remove();
                    
                    // Update module indices for remaining modules
                    updateModuleIndices();
                    
                    // Update module count badge
                    updateModuleCount();
                    
                    // Show success message
                    showAlert('success', `Module "${moduleName}" has been removed from the exam schedule.`);
                    
                    // Check if any modules remain
                    const remainingModules = document.querySelectorAll('.module-card');
                    if (remainingModules.length === 0) {
                        moduleList.innerHTML = `
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                No modules selected for this exam schedule. Please select at least one module.
                            </div>
                        `;
                    }
                }
            });
        });
    }
    
    // Function to update module indices after removal
    function updateModuleIndices() {
        const moduleCards = document.querySelectorAll('.module-card');
        moduleCards.forEach((card, newIndex) => {
            // Update all input names with new index
            const inputs = card.querySelectorAll('input[name*="modules["]');
            inputs.forEach(input => {
                const oldName = input.name;
                const newName = oldName.replace(/modules\[\d+\]/, `modules[${newIndex}]`);
                input.name = newName;
                
                // Update IDs for checkboxes
                if (input.type === 'checkbox') {
                    const oldId = input.id;
                    if (oldId.includes('theory')) {
                        input.id = `module_${newIndex}_theory`;
                        const label = card.querySelector(`label[for="${oldId}"]`);
                        if (label) label.setAttribute('for', input.id);
                    } else if (oldId.includes('practical')) {
                        input.id = `module_${newIndex}_practical`;
                        const label = card.querySelector(`label[for="${oldId}"]`);
                        if (label) label.setAttribute('for', input.id);
                    }
                }
            });
            
            // Update module number in header
            const header = card.querySelector('.card-header h6');
            if (header) {
                const moduleName = header.textContent.split(': ')[1];
                header.innerHTML = `<i class="bi bi-puzzle me-2"></i>Module ${newIndex + 1}: ${moduleName}`;
            }
        });
    }
    
    // Function to update module count badge
    function updateModuleCount() {
        const moduleCountBadge = document.getElementById('moduleCountBadge');
        const restoreModulesBtn = document.getElementById('restoreModulesBtn');
        const remainingModules = document.querySelectorAll('.module-card');
        const count = remainingModules.length;
        
        if (count === 0) {
            moduleCountBadge.textContent = '0 selected';
            moduleCountBadge.className = 'badge bg-warning ms-2';
        } else {
            moduleCountBadge.textContent = `${count} selected`;
            moduleCountBadge.className = 'badge bg-info ms-2';
        }
        
        // Show restore button if modules have been removed
        if (count < allModules.length) {
            restoreModulesBtn.style.display = 'inline-block';
        } else {
            restoreModulesBtn.style.display = 'none';
        }
    }
    
    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Prevent multiple submissions
        if (isSubmitting) {
            console.log('Form is already being submitted');
            return;
        }
        
        isSubmitting = true;
        
        // Clear any previous global errors
        clearGlobalErrors();
        
        // Validate the entire form
        if (!validateEntireForm()) {
            // Show comprehensive validation summary
            showValidationSummary();
            return;
        }
        
        // Validate that students have been fetched and selected
        if (!studentsFetched) {
            showGlobalError('Please fetch and select students before submitting the form');
            fetchStudentsBtn.focus();
            return;
        }
        
        if (selectedStudents.length === 0) {
            showGlobalError('Please select at least one student before submitting the form');
            fetchStudentsBtn.focus();
            return;
        }
        
        // Validate that at least one module is selected
        const remainingModules = document.querySelectorAll('.module-card');
        if (remainingModules.length === 0) {
            showGlobalError('Please select at least one module for this exam schedule');
            return;
        }
        
        // Additional validation before submission
        if (!validateFormDataBeforeSubmission()) {
            hideLoadingOverlay();
            isSubmitting = false;
            return;
        }
        
        // Show loading overlay
        showLoadingOverlay('Saving exam schedule...');
        
        // Prepare form data properly
        const formData = new FormData(form);
        
        // Add students to form data properly
        if (selectedStudents.length === 0) {
            showGlobalError('No students selected. Please select at least one student.');
            hideLoadingOverlay();
            isSubmitting = false;
            return;
        }
        
        selectedStudents.forEach((student, index) => {
            formData.append(`students[${index}][student_roll_no]`, student.student_roll_no);
            formData.append(`students[${index}][is_selected]`, '1');
        });
        
        // Ensure all module data is properly added
        const moduleCards = document.querySelectorAll('.module-card');
        if (moduleCards.length === 0) {
            showGlobalError('No modules selected. Please select at least one module.');
            hideLoadingOverlay();
            isSubmitting = false;
            return;
        }
        
        moduleCards.forEach((moduleCard, index) => {
            const nosCodeInput = moduleCard.querySelector('input[name*="[nos_code]"]');
            const isTheoryInput = moduleCard.querySelector('input[name*="[is_theory]"]');
            const venueInput = moduleCard.querySelector('input[name*="[venue]"]');
            const invigilatorInput = moduleCard.querySelector('input[name*="[invigilator]"]');
            const examDateInput = moduleCard.querySelector('input[name*="[exam_date]"]');
            const startTimeInput = moduleCard.querySelector('input[name*="[start_time]"]');
            const endTimeInput = moduleCard.querySelector('input[name*="[end_time]"]');
            
            if (!nosCodeInput || !venueInput || !invigilatorInput || !examDateInput || !startTimeInput || !endTimeInput) {
                showGlobalError(`Module ${index + 1} is missing required fields. Please check the form.`);
                hideLoadingOverlay();
                isSubmitting = false;
                return;
            }
            
            const nosCode = nosCodeInput.value;
            const isTheory = isTheoryInput ? isTheoryInput.value : '1';
            const venue = venueInput.value.trim();
            const invigilator = invigilatorInput.value.trim();
            const examDate = examDateInput.value;
            const startTime = startTimeInput.value;
            const endTime = endTimeInput.value;
            
            // Validate module data
            if (!venue || !invigilator || !examDate || !startTime || !endTime) {
                showGlobalError(`Module ${index + 1} has incomplete data. Please fill all required fields.`);
                hideLoadingOverlay();
                isSubmitting = false;
                return;
            }
            
            formData.append(`modules[${index}][nos_code]`, nosCode);
            formData.append(`modules[${index}][is_theory]`, isTheory);
            formData.append(`modules[${index}][venue]`, venue);
            formData.append(`modules[${index}][invigilator]`, invigilator);
            formData.append(`modules[${index}][exam_date]`, examDate);
            formData.append(`modules[${index}][start_time]`, startTime);
            formData.append(`modules[${index}][end_time]`, endTime);
        });
        
        // Ensure terms_accepted is set
        formData.append('terms_accepted', '1');
        
        // Debug: Log form data before submission
        console.log('Selected students:', selectedStudents);
        console.log('Form data being sent:');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Saving...';
        
        // Disable all form inputs during submission
        const formInputs = form.querySelectorAll('input, select, textarea, button');
        formInputs.forEach(input => {
            if (input !== submitBtn) {
                input.disabled = true;
            }
        });
        
        // Create a timeout promise
        const timeoutPromise = new Promise((_, reject) => {
            setTimeout(() => reject(new Error('Request timeout - server is taking too long to respond')), 30000); // 30 seconds timeout
        });
        
        // Create the fetch promise
        const fetchPromise = fetch('{{ route("admin.faculty.exam-schedules.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                // Don't set Content-Type for FormData - let the browser set it with boundary
            },
            body: formData
        });
        
        // Race between fetch and timeout
        Promise.race([fetchPromise, timeoutPromise])
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(JSON.stringify(data));
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Success response:', data);
            
            if (data.success) {
                showSuccessMessage('Exam schedule created successfully! Redirecting...');
                
                // Reset form state
                selectedStudents = [];
                studentsFetched = false;
                isSubmitting = false;
                
                // Redirect after a short delay
                setTimeout(() => {
                    window.location.href = '{{ route("admin.faculty.exam-schedules.index") }}';
                }, 2000);
            } else {
                throw new Error(data.message || 'Failed to create exam schedule');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            hideLoadingOverlay();
            
            let errorMessage = 'An error occurred while creating the exam schedule. Please try again.';
            
            if (error.message.includes('timeout')) {
                errorMessage = 'Request timeout - the server is taking too long to respond. Please try again.';
            } else if (error.message.includes('Failed to fetch')) {
                errorMessage = 'Network error - please check your internet connection and try again.';
            } else if (error.message.includes('413')) {
                errorMessage = 'File size too large - please reduce the size of uploaded files and try again.';
            } else if (error.message.includes('500')) {
                errorMessage = 'Server error - please try again later or contact support.';
            } else if (error.message.includes('422')) {
                errorMessage = 'Validation error - please check the form and try again.';
            }
            
            try {
                const errorData = JSON.parse(error.message);
                console.log('Parsed error data:', errorData);
                
                if (errorData.errors) {
                    showValidationErrors(errorData.errors);
                } else if (errorData.message) {
                    showGlobalError(errorData.message);
                } else {
                    showGlobalError(errorMessage);
                }
            } catch (e) {
                console.log('Error parsing error message:', e);
                showGlobalError(errorMessage);
            }
        })
        .finally(() => {
            // Re-enable all form inputs
            formInputs.forEach(input => {
                input.disabled = false;
            });
            
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            
            // Reset submission state
            isSubmitting = false;
        });
    });
    
    // Validate form button
    document.getElementById('validateFormBtn').addEventListener('click', function() {
        validateEntireForm();
        showValidationSummary();
    });
    
    // Save as draft
    document.getElementById('saveDraftBtn').addEventListener('click', function() {
        // Similar to submit but with draft status
        // Implementation would be similar to form submission
        alert('Save as draft functionality will be implemented');
    });

    // Function to show validation errors
    function showValidationErrors(errors) {
        const errorAlert = document.getElementById('validationErrors');
        const errorList = document.getElementById('errorList');
        
        // Clear previous errors
        errorList.innerHTML = '';
        
        // Add each error to the list
        Object.keys(errors).forEach(field => {
            errors[field].forEach(error => {
                const li = document.createElement('li');
                li.textContent = error;
                errorList.appendChild(li);
            });
        });
        
        // Show the error alert
        errorAlert.style.display = 'block';
        
        // Scroll to the top to show errors
        errorAlert.scrollIntoView({ behavior: 'smooth' });
        
        // Highlight invalid fields
        Object.keys(errors).forEach(field => {
            const fieldElement = document.querySelector(`[name="${field}"]`);
            if (fieldElement) {
                fieldElement.classList.add('is-invalid');
                
                // Remove invalid class when user starts typing
                fieldElement.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                });
            }
        });
    }

    // Function to clear validation errors
    function clearValidationErrors() {
        const errorAlert = document.getElementById('validationErrors');
        errorAlert.style.display = 'none';
        
        // Remove invalid classes from all fields
        document.querySelectorAll('.is-invalid').forEach(field => {
            field.classList.remove('is-invalid');
        });
    }

    // Clear validation errors when form is submitted successfully
    document.getElementById('examScheduleForm').addEventListener('submit', function() {
        clearValidationErrors();
    });

    // Auto-format batch code input
    document.getElementById('batch_code').addEventListener('blur', function() {
        let value = this.value.trim();
        // Remove all spaces and convert to uppercase
        value = value.replace(/\s+/g, '').toUpperCase();
        this.value = value;
    });

    // Auto-format program number input
    document.getElementById('program_number').addEventListener('blur', function() {
        let value = this.value.trim();
        // Remove extra spaces
        value = value.replace(/\s+/g, ' ');
        this.value = value;
    });
    
    // ==================== VALIDATION FUNCTIONS ====================
    
    function initializeValidation() {
        // Add keyup event listeners for real-time validation
        addKeyupValidation();
        
        // Add module time validation
        addModuleTimeValidation();
        
        // Add date validation
        addDateValidation();
        
        // Add file validation
        addFileValidation();
    }
    
    function addKeyupValidation() {
        // Batch code validation
        document.getElementById('batch_code').addEventListener('keyup', function() {
            validateBatchCode(this);
            clearGlobalErrors(); // Clear global errors when user starts typing
            clearSuccessMessage(); // Clear success message when user starts typing
        });
        
        // Program number validation
        document.getElementById('program_number').addEventListener('keyup', function() {
            validateProgramNumber(this);
            clearGlobalErrors(); // Clear global errors when user starts typing
            clearSuccessMessage(); // Clear success message when user starts typing
        });
        
        // Course name validation
        courseSelect.addEventListener('change', function() {
            validateCourseName(this);
            clearGlobalErrors(); // Clear global errors when user makes selection
            clearSuccessMessage(); // Clear success message when user makes selection
        });
        
        // Semester validation
        document.getElementById('semester').addEventListener('change', function() {
            validateSemester(this);
            clearGlobalErrors(); // Clear global errors when user makes selection
            clearSuccessMessage(); // Clear success message when user makes selection
        });
        
        // Exam type validation
        document.getElementById('exam_type').addEventListener('change', function() {
            validateExamType(this);
            clearGlobalErrors(); // Clear global errors when user makes selection
            clearSuccessMessage(); // Clear success message when user makes selection
        });
        
        // Centre validation
        centreSelect.addEventListener('change', function() {
            validateCentre(this);
            clearGlobalErrors(); // Clear global errors when user makes selection
            clearSuccessMessage(); // Clear success message when user makes selection
        });
        
        // Terms acceptance validation
        document.getElementById('terms_accepted').addEventListener('change', function() {
            clearGlobalErrors(); // Clear global errors when user accepts terms
            clearSuccessMessage(); // Clear success message when user accepts terms
        });
    }
    
    function addModuleTimeValidation() {
        // This will be called when modules are dynamically added
        document.addEventListener('change', function(e) {
            if (e.target.name && e.target.name.includes('modules') && 
                (e.target.name.includes('start_time') || e.target.name.includes('end_time') || e.target.name.includes('exam_date'))) {
                validateModuleTime(e.target);
            }
        });
        
        document.addEventListener('keyup', function(e) {
            if (e.target.name && e.target.name.includes('modules') && 
                (e.target.name.includes('venue') || e.target.name.includes('invigilator'))) {
                validateModuleField(e.target);
            }
        });
    }
    
    function addDateValidation() {
        const examStartDate = document.getElementById('exam_start_date');
        const examEndDate = document.getElementById('exam_end_date');
        
        examStartDate.addEventListener('change', function() {
            validateExamDates();
        });
        
        examEndDate.addEventListener('change', function() {
            validateExamDates();
        });
    }
    
    function addFileValidation() {
        const courseCompletionFile = document.getElementById('course_completion_file');
        const studentDetailsFile = document.getElementById('student_details_file');
        
        courseCompletionFile.addEventListener('change', function() {
            validateFile(this, 'course_completion_file');
        });
        
        studentDetailsFile.addEventListener('change', function() {
            validateFile(this, 'student_details_file');
        });
    }
    
    // Individual validation functions
    function validateBatchCode(input) {
        const value = input.value.trim();
        const batchCodeRegex = /^[A-Z0-9]{1,10}$/;
        
        clearFieldError(input);
        
        if (!value) {
            showFieldError(input, 'Batch code is required');
            return false;
        }
        
        if (!batchCodeRegex.test(value)) {
            showFieldError(input, 'Batch code must contain only letters and numbers (max 10 characters)');
            return false;
        }
        
        showFieldSuccess(input);
        return true;
    }
    
    function validateProgramNumber(input) {
        const value = input.value.trim();
        const programNumberRegex = /^[A-Z0-9\-\/]{1,255}$/;
        
        clearFieldError(input);
        
        if (!value) {
            showFieldError(input, 'Program number is required');
            return false;
        }
        
        if (!programNumberRegex.test(value)) {
            showFieldError(input, 'Program number can only contain letters, numbers, hyphens, and forward slashes');
            return false;
        }
        
        showFieldSuccess(input);
        return true;
    }
    
    function validateCourseName(select) {
        clearFieldError(select);
        
        if (!select.value) {
            showFieldError(select, 'Please select a course/qualification');
            return false;
        }
        
        showFieldSuccess(select);
        return true;
    }
    
    function validateSemester(select) {
        clearFieldError(select);
        
        if (!select.value) {
            showFieldError(select, 'Please select a semester');
            return false;
        }
        
        showFieldSuccess(select);
        return true;
    }
    
    function validateExamType(select) {
        clearFieldError(select);
        
        if (!select.value) {
            showFieldError(select, 'Please select an exam type');
            return false;
        }
        
        showFieldSuccess(select);
        return true;
    }
    
    function validateCentre(select) {
        clearFieldError(select);
        
        if (!select.value) {
            showFieldError(select, 'Please select a centre');
            return false;
        }
        
        showFieldSuccess(select);
        return true;
    }
    
    function validateModuleTime(input) {
        const moduleCard = input.closest('.module-card');
        const moduleIndex = getModuleIndex(moduleCard);
        
        if (input.name.includes('start_time') || input.name.includes('end_time')) {
            validateModuleTimeRange(moduleCard, moduleIndex);
        } else if (input.name.includes('exam_date')) {
            validateModuleDate(moduleCard, moduleIndex);
        }
        
        // Check for time conflicts after individual validation
        setTimeout(() => {
            validateModuleTimeConflicts();
        }, 100);
    }
    
    function validateModuleField(input) {
        const value = input.value.trim();
        const fieldName = input.name.includes('venue') ? 'Venue' : 'Invigilator';
        
        clearFieldError(input);
        
        if (!value) {
            showFieldError(input, `${fieldName} is required`);
            return false;
        }
        
        if (value.length < 2) {
            showFieldError(input, `${fieldName} must be at least 2 characters long`);
            return false;
        }
        
        showFieldSuccess(input);
        return true;
    }
    
    function validateModuleTimeRange(moduleCard, moduleIndex) {
        const startTimeInput = moduleCard.querySelector('input[name*="[start_time]"]');
        const endTimeInput = moduleCard.querySelector('input[name*="[end_time]"]');
        
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;
        
        clearFieldError(startTimeInput);
        clearFieldError(endTimeInput);
        
        if (!startTime) {
            showFieldError(startTimeInput, 'Start time is required');
            return false;
        }
        
        if (!endTime) {
            showFieldError(endTimeInput, 'End time is required');
            return false;
        }
        
        if (startTime >= endTime) {
            showFieldError(startTimeInput, 'Start time must be before end time');
            showFieldError(endTimeInput, 'End time must be after start time');
            return false;
        }
        
        // Check if exam duration is reasonable (at least 30 minutes, max 8 hours)
        const start = new Date(`2000-01-01T${startTime}`);
        const end = new Date(`2000-01-01T${endTime}`);
        const durationHours = (end - start) / (1000 * 60 * 60);
        
        if (durationHours < 0.5) {
            showFieldError(startTimeInput, 'Exam duration must be at least 30 minutes');
            showFieldError(endTimeInput, 'Exam duration must be at least 30 minutes');
            return false;
        }
        
        if (durationHours > 8) {
            showFieldError(startTimeInput, 'Exam duration cannot exceed 8 hours');
            showFieldError(endTimeInput, 'Exam duration cannot exceed 8 hours');
            return false;
        }
        
        showFieldSuccess(startTimeInput);
        showFieldSuccess(endTimeInput);
        return true;
    }
    
    function validateModuleDate(moduleCard, moduleIndex) {
        const examDateInput = moduleCard.querySelector('input[name*="[exam_date]"]');
        const examDate = examDateInput.value;
        const examStartDate = document.getElementById('exam_start_date').value;
        const examEndDate = document.getElementById('exam_end_date').value;
        
        clearFieldError(examDateInput);
        
        if (!examDate) {
            showFieldError(examDateInput, 'Exam date is required');
            return false;
        }
        
        if (examStartDate && examDate < examStartDate) {
            showFieldError(examDateInput, `Exam date must be on or after ${examStartDate}`);
            return false;
        }
        
        if (examEndDate && examDate > examEndDate) {
            showFieldError(examDateInput, `Exam date must be on or before ${examEndDate}`);
            return false;
        }
        
        // Check if date is not in the past
        const today = new Date().toISOString().split('T')[0];
        if (examDate < today) {
            showFieldError(examDateInput, 'Exam date cannot be in the past');
            return false;
        }
        
        showFieldSuccess(examDateInput);
        return true;
    }
    
    function validateExamDates() {
        const examStartDate = document.getElementById('exam_start_date');
        const examEndDate = document.getElementById('exam_end_date');
        
        const startDate = examStartDate.value;
        const endDate = examEndDate.value;
        
        clearFieldError(examStartDate);
        clearFieldError(examEndDate);
        
        if (!startDate) {
            showFieldError(examStartDate, 'Exam start date is required');
            return false;
        }
        
        if (!endDate) {
            showFieldError(examEndDate, 'Exam end date is required');
            return false;
        }
        
        if (startDate >= endDate) {
            showFieldError(examStartDate, 'Start date must be before end date');
            showFieldError(examEndDate, 'End date must be after start date');
            return false;
        }
        
        // Check if start date is not in the past
        const today = new Date().toISOString().split('T')[0];
        if (startDate < today) {
            showFieldError(examStartDate, 'Exam start date cannot be in the past');
            return false;
        }
        
        showFieldSuccess(examStartDate);
        showFieldSuccess(examEndDate);
        return true;
    }
    
    function validateFile(input, fieldName) {
        const file = input.files[0];
        
        clearFieldError(input);
        
        if (!file) {
            showFieldError(input, 'Please select a file');
            return false;
        }
        
        // Check file size (5MB limit)
        const maxSize = 5 * 1024 * 1024; // 5MB in bytes
        if (file.size > maxSize) {
            showFieldError(input, 'File size must be less than 5MB');
            return false;
        }
        
        // Check file type
        const allowedTypes = {
            'course_completion_file': ['.pdf', '.doc', '.docx'],
            'student_details_file': ['.pdf', '.doc', '.docx', '.xls', '.xlsx']
        };
        
        const fileName = file.name.toLowerCase();
        const hasValidExtension = allowedTypes[fieldName].some(ext => fileName.endsWith(ext));
        
        if (!hasValidExtension) {
            showFieldError(input, `File must be one of: ${allowedTypes[fieldName].join(', ')}`);
            return false;
        }
        
        showFieldSuccess(input);
        return true;
    }
    
    // Helper functions for validation
    function showFieldError(input, message) {
        input.classList.remove('is-valid');
        input.classList.add('is-invalid');
        
        // Remove existing error message
        const existingError = input.parentNode.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }
        
        // Add new error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        input.parentNode.appendChild(errorDiv);
        
        // Add to validation errors
        validationErrors[input.name] = message;
    }
    
    function showFieldSuccess(input) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        
        // Remove existing error message
        const existingError = input.parentNode.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }
        
        // Add success message
        const successDiv = document.createElement('div');
        successDiv.className = 'valid-feedback';
        successDiv.textContent = '✓ Valid';
        input.parentNode.appendChild(successDiv);
        
        // Remove from validation errors
        delete validationErrors[input.name];
    }
    
    function clearFieldError(input) {
        input.classList.remove('is-invalid', 'is-valid');
        
        // Remove existing error/success messages
        const existingError = input.parentNode.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }
        
        const existingSuccess = input.parentNode.querySelector('.valid-feedback');
        if (existingSuccess) {
            existingSuccess.remove();
        }
    }
    
    function getModuleIndex(moduleCard) {
        const moduleCards = document.querySelectorAll('.module-card');
        return Array.from(moduleCards).indexOf(moduleCard);
    }
    
    function validateAllModules() {
        const moduleCards = document.querySelectorAll('.module-card');
        let allValid = true;
        
        moduleCards.forEach((moduleCard, index) => {
            const venue = moduleCard.querySelector('input[name*="[venue]"]');
            const invigilator = moduleCard.querySelector('input[name*="[invigilator]"]');
            const examDate = moduleCard.querySelector('input[name*="[exam_date]"]');
            const startTime = moduleCard.querySelector('input[name*="[start_time]"]');
            const endTime = moduleCard.querySelector('input[name*="[end_time]"]');
            
            if (!validateModuleField(venue) || 
                !validateModuleField(invigilator) || 
                !validateModuleDate(moduleCard, index) || 
                !validateModuleTimeRange(moduleCard, index)) {
                allValid = false;
            }
        });
        
        // Check for time conflicts between modules
        if (!validateModuleTimeConflicts()) {
            allValid = false;
        }
        
        return allValid;
    }
    
    function validateModuleTimeConflicts() {
        const moduleCards = document.querySelectorAll('.module-card');
        const moduleSchedules = [];
        
        // Collect all module schedules
        moduleCards.forEach((moduleCard, index) => {
            const examDate = moduleCard.querySelector('input[name*="[exam_date]"]').value;
            const startTime = moduleCard.querySelector('input[name*="[start_time]"]').value;
            const endTime = moduleCard.querySelector('input[name*="[end_time]"]').value;
            const moduleName = moduleCard.querySelector('.card-header h6').textContent.split(': ')[1];
            
            if (examDate && startTime && endTime) {
                moduleSchedules.push({
                    index: index,
                    date: examDate,
                    start: startTime,
                    end: endTime,
                    name: moduleName,
                    card: moduleCard
                });
            }
        });
        
        // Check for conflicts
        const conflicts = [];
        for (let i = 0; i < moduleSchedules.length; i++) {
            for (let j = i + 1; j < moduleSchedules.length; j++) {
                const module1 = moduleSchedules[i];
                const module2 = moduleSchedules[j];
                
                // Check if modules are on the same date
                if (module1.date === module2.date) {
                    // Check for time overlap
                    const start1 = new Date(`2000-01-01T${module1.start}`);
                    const end1 = new Date(`2000-01-01T${module1.end}`);
                    const start2 = new Date(`2000-01-01T${module2.start}`);
                    const end2 = new Date(`2000-01-01T${module2.end}`);
                    
                    if ((start1 < end2) && (start2 < end1)) {
                        conflicts.push({
                            module1: module1,
                            module2: module2
                        });
                    }
                }
            }
        }
        
        // Show conflicts if any
        if (conflicts.length > 0) {
            conflicts.forEach(conflict => {
                const errorMessage = `Time conflict: "${conflict.module1.name}" (${conflict.module1.start}-${conflict.module1.end}) overlaps with "${conflict.module2.name}" (${conflict.module2.start}-${conflict.module2.end}) on ${conflict.module1.date}`;
                
                // Show error on both modules
                const startTime1 = conflict.module1.card.querySelector('input[name*="[start_time]"]');
                const endTime1 = conflict.module1.card.querySelector('input[name*="[end_time]"]');
                const startTime2 = conflict.module2.card.querySelector('input[name*="[start_time]"]');
                const endTime2 = conflict.module2.card.querySelector('input[name*="[end_time]"]');
                
                showFieldError(startTime1, errorMessage);
                showFieldError(endTime1, errorMessage);
                showFieldError(startTime2, errorMessage);
                showFieldError(endTime2, errorMessage);
                
                // Highlight conflicting modules
                conflict.module1.card.classList.add('has-time-conflict');
                conflict.module2.card.classList.add('has-time-conflict');
            });
            
            return false;
        } else {
            // Remove conflict highlighting from all modules
            document.querySelectorAll('.module-card').forEach(card => {
                card.classList.remove('has-time-conflict');
            });
        }
        
        return true;
    }
    
    function validateEntireForm() {
        let isValid = true;
        
        // Validate basic fields
        if (!validateBatchCode(document.getElementById('batch_code'))) isValid = false;
        if (!validateProgramNumber(document.getElementById('program_number'))) isValid = false;
        if (!validateCourseName(courseSelect)) isValid = false;
        if (!validateSemester(document.getElementById('semester'))) isValid = false;
        if (!validateExamType(document.getElementById('exam_type'))) isValid = false;
        if (!validateCentre(centreSelect)) isValid = false;
        if (!validateExamDates()) isValid = false;
        
        // Validate files
        if (!validateFile(document.getElementById('course_completion_file'), 'course_completion_file')) isValid = false;
        if (!validateFile(document.getElementById('student_details_file'), 'student_details_file')) isValid = false;
        
        // Validate modules
        if (!validateAllModules()) isValid = false;
        
        // Validate students
        if (selectedStudents.length === 0) {
            showGlobalError('Please select at least one student');
            isValid = false;
        }
        
        // Validate terms
        if (!document.getElementById('terms_accepted').checked) {
            showGlobalError('Please accept the terms and conditions');
            isValid = false;
        }
        
        isFormValid = isValid;
        return isValid;
    }
    
    function showGlobalError(message) {
        const errorAlert = document.getElementById('validationErrors');
        const errorList = document.getElementById('errorList');
        
        // Clear previous errors
        errorList.innerHTML = '';
        
        // Add error message
        const li = document.createElement('li');
        li.textContent = message;
        errorList.appendChild(li);
        
        // Show the error alert
        errorAlert.style.display = 'block';
        
        // Scroll to the top to show errors
        errorAlert.scrollIntoView({ behavior: 'smooth' });
    }
    
    function clearGlobalErrors() {
        const errorAlert = document.getElementById('validationErrors');
        errorAlert.style.display = 'none';
    }
    
    function showLoadingOverlay(message = 'Loading...') {
        const overlay = document.getElementById('loadingOverlay');
        const messageElement = document.getElementById('loadingMessage');
        
        if (messageElement) {
            messageElement.textContent = message;
        }
        
        if (overlay) {
            overlay.style.display = 'flex';
        }
    }
    
    function hideLoadingOverlay() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
    }
    
    function showSuccessMessage(message) {
        hideLoadingOverlay();
        
        const successAlert = document.getElementById('successAlert');
        const successMessage = document.getElementById('successMessage');
        
        if (successMessage) {
            successMessage.textContent = message;
        }
        
        if (successAlert) {
            successAlert.style.display = 'block';
            successAlert.scrollIntoView({ behavior: 'smooth' });
        }
    }
    
    function clearSuccessMessage() {
        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            successAlert.style.display = 'none';
        }
    }
    
    function validateFormDataBeforeSubmission() {
        // Check if all required fields are filled
        const requiredFields = [
            'course_name',
            'batch_code',
            'semester',
            'exam_type',
            'exam_start_date',
            'exam_end_date',
            'program_number',
            'centre_id'
        ];
        
        let isValid = true;
        
        requiredFields.forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (field && !field.value.trim()) {
                showFieldError(field, `${fieldName.replace('_', ' ')} is required`);
                isValid = false;
            }
        });
        
        // Check if files are selected
        const courseCompletionFile = document.getElementById('course_completion_file');
        const studentDetailsFile = document.getElementById('student_details_file');
        
        if (!courseCompletionFile.files[0]) {
            showFieldError(courseCompletionFile, 'Course completion file is required');
            isValid = false;
        }
        
        if (!studentDetailsFile.files[0]) {
            showFieldError(studentDetailsFile, 'Student details file is required');
            isValid = false;
        }
        
        // Check if terms are accepted
        if (!document.getElementById('terms_accepted').checked) {
            showGlobalError('Please accept the terms and conditions');
            isValid = false;
        }
        
        // Check if students are selected
        if (selectedStudents.length === 0) {
            showGlobalError('Please select at least one student');
            isValid = false;
        }
        
        // Check if modules are selected and have all required data
        const moduleCards = document.querySelectorAll('.module-card');
        if (moduleCards.length === 0) {
            showGlobalError('Please select at least one module');
            isValid = false;
        } else {
            moduleCards.forEach((moduleCard, index) => {
                const venue = moduleCard.querySelector('input[name*="[venue]"]');
                const invigilator = moduleCard.querySelector('input[name*="[invigilator]"]');
                const examDate = moduleCard.querySelector('input[name*="[exam_date]"]');
                const startTime = moduleCard.querySelector('input[name*="[start_time]"]');
                const endTime = moduleCard.querySelector('input[name*="[end_time]"]');
                
                if (!venue || !venue.value.trim()) {
                    showFieldError(venue, `Module ${index + 1}: Venue is required`);
                    isValid = false;
                }
                if (!invigilator || !invigilator.value.trim()) {
                    showFieldError(invigilator, `Module ${index + 1}: Invigilator is required`);
                    isValid = false;
                }
                if (!examDate || !examDate.value) {
                    showFieldError(examDate, `Module ${index + 1}: Exam date is required`);
                    isValid = false;
                }
                if (!startTime || !startTime.value) {
                    showFieldError(startTime, `Module ${index + 1}: Start time is required`);
                    isValid = false;
                }
                if (!endTime || !endTime.value) {
                    showFieldError(endTime, `Module ${index + 1}: End time is required`);
                    isValid = false;
                }
            });
        }
        
        if (!isValid) {
            showGlobalError('Please fill in all required fields before submitting');
        }
        
        return isValid;
    }
    
    function showValidationSummary() {
        const errorAlert = document.getElementById('validationErrors');
        const errorList = document.getElementById('errorList');
        
        // Clear previous errors
        errorList.innerHTML = '';
        
        // Collect all validation errors
        const allErrors = [];
        
        // Add field-specific errors
        Object.values(validationErrors).forEach(error => {
            allErrors.push(error);
        });
        
        // Add module-specific errors
        const moduleCards = document.querySelectorAll('.module-card');
        moduleCards.forEach((moduleCard, index) => {
            const moduleName = moduleCard.querySelector('.card-header h6').textContent.split(': ')[1];
            const invalidFields = moduleCard.querySelectorAll('.is-invalid');
            
            invalidFields.forEach(field => {
                const errorMessage = field.parentNode.querySelector('.invalid-feedback');
                if (errorMessage) {
                    allErrors.push(`${moduleName}: ${errorMessage.textContent}`);
                }
            });
        });
        
        // Add global errors
        if (selectedStudents.length === 0) {
            allErrors.push('Please select at least one student');
        }
        
        if (!document.getElementById('terms_accepted').checked) {
            allErrors.push('Please accept the terms and conditions');
        }
        
        // Show errors if any
        if (allErrors.length > 0) {
            allErrors.forEach(error => {
                const li = document.createElement('li');
                li.textContent = error;
                errorList.appendChild(li);
            });
            
            errorAlert.style.display = 'block';
            errorAlert.scrollIntoView({ behavior: 'smooth' });
        } else {
            errorAlert.style.display = 'none';
        }
    }
    
    console.log('Create Exam Schedule page JavaScript initialization complete');
});
</script>

@push('styles')
<style>
    .student-card {
        transition: all 0.3s ease;
    }
    
    .student-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .student-card.selected {
        border-color: #28a745;
        background-color: #f8fff9;
    }
    
    .module-card {
        transition: all 0.3s ease;
    }
    
    .module-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .module-card.selected {
        border-color: #007bff;
        background-color: #f8f9ff;
    }

    /* Progress Steps Styling */
    .progress-step {
        padding: 15px;
        border-radius: 8px;
        background-color: #f8f9fa;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .progress-step.active {
        background-color: #007bff;
        border-color: #007bff;
        color: white;
    }

    .progress-step.completed {
        background-color: #28a745;
        border-color: #28a745;
        color: white;
    }

    .progress-step:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    /* Form Validation Styling */
    .form-control.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    .form-control.is-valid {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }

    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .valid-feedback {
        display: block;
        color: #28a745;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    /* Field Hints Styling */
    .form-text {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }

    .form-text i {
        color: #007bff;
    }

    /* Required Field Indicator */
    .form-label::after {
        content: " *";
        color: #dc3545;
        font-weight: bold;
    }

    .form-label.optional::after {
        content: "";
    }
    
    /* Module Input Styling */
    .module-input:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    .module-input.is-valid:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
    
    .module-input.is-invalid:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    
    /* Validation Alert Styling */
    #validationErrors {
        border-left: 4px solid #dc3545;
    }
    
    #validationErrors .alert-heading {
        color: #721c24;
    }
    
    /* Time Conflict Highlighting */
    .module-card.has-time-conflict {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    
    .module-card.has-time-conflict .card-header {
        background-color: #dc3545 !important;
    }
    
    /* Success State Styling */
    .module-card.is-valid {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
    
    /* Loading Overlay Styling */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .loading-content {
        background-color: rgba(255, 255, 255, 0.1);
        padding: 2rem;
        border-radius: 10px;
        text-align: center;
        backdrop-filter: blur(10px);
    }
    
    .loading-content .spinner-border {
        width: 3rem;
        height: 3rem;
    }
    
    /* Success Alert Styling */
    #successAlert {
        border-left: 4px solid #28a745;
    }
    
    #successAlert .alert-heading {
        color: #155724;
    }
</style>
@endpush 