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
                                <input type="number" class="form-control" id="batch_code" name="batch_code" 
                                       placeholder="e.g., 2024" maxlength="10" required>
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
                            <label for="student_details_file" class="form-label">Student Tabulation</label>
                            <input type="file" class="form-control" id="student_details_file" name="student_details_file" 
                               accept=".xls,.xlsx,.csv" required>
                            <div class="form-text">
                            <i class="bi bi-file-earmark me-1"></i>
                            Upload Tabulation (XLS, XLSX, or CSV only, max 5MB)
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
                                    <input type="text" class="form-control" name="modules[${moduleIndex}][venue]" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Invigilator *</label>
                                    <input type="text" class="form-control" name="modules[${moduleIndex}][invigilator]" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Exam Date *</label>
                                    <input type="date" class="form-control" name="modules[${moduleIndex}][exam_date]" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Start Time *</label>
                                    <input type="time" class="form-control" name="modules[${moduleIndex}][start_time]" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">End Time *</label>
                                    <input type="time" class="form-control" name="modules[${moduleIndex}][end_time]" required>
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
                                    <input type="text" class="form-control" name="modules[${moduleIndex}][venue]" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Invigilator *</label>
                                    <input type="text" class="form-control" name="modules[${moduleIndex}][invigilator]" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Exam Date *</label>
                                    <input type="date" class="form-control" name="modules[${moduleIndex}][exam_date]" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Start Time *</label>
                                    <input type="time" class="form-control" name="modules[${moduleIndex}][start_time]" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">End Time *</label>
                                    <input type="time" class="form-control" name="modules[${moduleIndex}][end_time]" required>
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
                                    <input type="text" class="form-control" name="modules[${moduleIndex}][venue]" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Invigilator *</label>
                                    <input type="text" class="form-control" name="modules[${moduleIndex}][invigilator]" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Exam Date *</label>
                                    <input type="date" class="form-control" name="modules[${moduleIndex}][exam_date]" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Start Time *</label>
                                    <input type="time" class="form-control" name="modules[${moduleIndex}][start_time]" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">End Time *</label>
                                    <input type="time" class="form-control" name="modules[${moduleIndex}][end_time]" required>
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
        
        // Validate that students have been fetched and selected
        if (!studentsFetched) {
            alert('Please fetch and select students before submitting the form');
            fetchStudentsBtn.focus();
            return;
        }
        
        if (selectedStudents.length === 0) {
            alert('Please select at least one student before submitting the form');
            fetchStudentsBtn.focus();
            return;
        }
        
        // Validate that at least one module is selected
        const remainingModules = document.querySelectorAll('.module-card');
        if (remainingModules.length === 0) {
            alert('Please select at least one module for this exam schedule');
            return;
        }
        
        // Validate that all module fields are filled
        let allFieldsFilled = true;
        let emptyFields = [];
        let invalidDates = [];
        
        const examStartDate = document.getElementById('exam_start_date').value;
        const examEndDate = document.getElementById('exam_end_date').value;
        
        remainingModules.forEach((moduleCard, index) => {
            const venue = moduleCard.querySelector('input[name*="[venue]"]').value.trim();
            const invigilator = moduleCard.querySelector('input[name*="[invigilator]"]').value.trim();
            const examDate = moduleCard.querySelector('input[name*="[exam_date]"]').value.trim();
            const startTime = moduleCard.querySelector('input[name*="[start_time]"]').value.trim();
            const endTime = moduleCard.querySelector('input[name*="[end_time]"]').value.trim();
            
            if (!venue) {
                emptyFields.push(`Module ${index + 1}: Venue`);
            }
            if (!invigilator) {
                emptyFields.push(`Module ${index + 1}: Invigilator`);
            }
            if (!examDate) {
                emptyFields.push(`Module ${index + 1}: Exam Date`);
            } else {
                // Check if exam date is within the exam period
                if (examDate < examStartDate || examDate > examEndDate) {
                    invalidDates.push(`Module ${index + 1}: Exam date must be between ${examStartDate} and ${examEndDate}`);
                }
            }
            if (!startTime) {
                emptyFields.push(`Module ${index + 1}: Start Time`);
            }
            if (!endTime) {
                emptyFields.push(`Module ${index + 1}: End Time`);
            }
        });
        
        if (emptyFields.length > 0) {
            alert('Please fill in all required fields for the following modules:\n\n' + emptyFields.join('\n'));
            return;
        }
        
        if (invalidDates.length > 0) {
            alert('Please correct the following date issues:\n\n' + invalidDates.join('\n'));
            return;
        }
        
        // Validate form
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Add students to form data
        selectedStudents.forEach((student, index) => {
            const studentInput = document.createElement('input');
            studentInput.type = 'hidden';
            studentInput.name = `students[${index}][student_roll_no]`;
            studentInput.value = student.student_roll_no;
            form.appendChild(studentInput);
        });

        // Debug: Log form data before submission
        console.log('Selected students:', selectedStudents);
        console.log('Form data being sent:');
        const formDataDebug = new FormData(form);
        for (let [key, value] of formDataDebug.entries()) {
            console.log(`${key}: ${value}`);
        }
        
        // Submit form
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Submitting...';
        
        fetch('{{ route("admin.faculty.exam-schedules.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(JSON.stringify(data));
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('Exam schedule created successfully!');
                window.location.href = '{{ route("admin.faculty.exam-schedules.index") }}';
            } else {
                alert(data.message || 'Failed to create exam schedule');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            try {
                const errorData = JSON.parse(error.message);
                if (errorData.errors) {
                    showValidationErrors(errorData.errors);
                } else {
                    alert(errorData.message || 'Failed to create exam schedule');
                }
            } catch (e) {
                alert('An error occurred while creating the exam schedule');
            }
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
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
</style>
@endpush 