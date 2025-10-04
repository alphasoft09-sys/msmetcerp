@extends('admin.layout')

@section('title', 'Exam Schedule Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="bi bi-calendar-event me-2"></i>
                        Exam Schedule Details
                    </h1>
                    <p class="text-muted">View complete exam schedule information</p>
                </div>
                <div class="d-flex gap-2">
                    @if($user->user_role === 5)
                        <a href="{{ route('admin.faculty.exam-schedules.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                        </a>
                    @elseif($user->user_role === 3)
                        <a href="{{ route('admin.exam-cell.exam-schedules.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                        </a>
                    @elseif($user->user_role === 1)
                        <a href="{{ route('admin.tc-admin.exam-schedules.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                        </a>
                    @elseif($user->user_role === 2)
                        <a href="{{ route('admin.tc-head.exam-schedules.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                        </a>
                    @elseif($user->user_role === 4)
                        <a href="{{ route('admin.aa.exam-schedules.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                        </a>
                    @endif
                    
                    @if($user->user_role === 5 && in_array($examSchedule->status, ['draft', 'hold']))
                    <a href="{{ route('admin.faculty.exam-schedules.edit', $examSchedule->id) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-2"></i>
                    </a>
                    @endif
                    
                    <!-- Full View Button -->
                    @php
                        $fullviewRoute = '';
                        switch($user->user_role) {
                            case 5:
                                $fullviewRoute = route('admin.faculty.exam-schedules.fullview', $examSchedule->id);
                                break;
                            case 3:
                                $fullviewRoute = route('admin.exam-cell.exam-schedules.fullview', $examSchedule->id);
                                break;
                            case 1:
                                $fullviewRoute = route('admin.tc-admin.exam-schedules.fullview', $examSchedule->id);
                                break;
                            case 2:
                                $fullviewRoute = route('admin.tc-head.exam-schedules.fullview', $examSchedule->id);
                                break;
                            case 4:
                                $fullviewRoute = route('admin.aa.exam-schedules.fullview', $examSchedule->id);
                                break;
                        }
                    @endphp
                    <a href="{{ $fullviewRoute }}" class="btn btn-info" target="_blank">
                        <i class="bi bi-printer me-2"></i>
                    </a>
                    
                    <!-- Hold Approval Actions - Only the person who put it on hold can approve -->
                    @if($examSchedule->status === 'hold' && $examSchedule->held_by === $user->user_role)
                    <button type="button" class="btn btn-success approve-schedule-btn"
                            data-schedule-id="{{ $examSchedule->id }}"
                            data-schedule-name="{{ $examSchedule->course_name }}">
                        <i class="bi bi-check-circle me-2"></i>
                        Approve (Resume)
                    </button>
                    @endif
                </div>
            </div>

            <!-- Status Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="card-title mb-1">{{ $examSchedule->course_name }}</h5>
                            <p class="text-muted mb-0">
                                Batch: {{ $examSchedule->batch_code }} | 
                                Semester: {{ $examSchedule->semester }} | 
                                Type: {{ $examSchedule->exam_type }}
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            @php
                                $statusColors = [
                                    'draft' => 'bg-secondary',
                                    'submitted' => 'bg-warning',
                                    'exam_cell_approved' => 'bg-info',
                                    'tc_admin_approved' => 'bg-primary',
                                    'received' => 'bg-success',
                                    'rejected' => 'bg-danger',
                                    'hold' => 'bg-warning'
                                ];
                                $statusLabels = [
                                    'draft' => 'Draft',
                                    'submitted' => 'Submitted',
                                    'exam_cell_approved' => 'Exam Cell Approved',
                                    'tc_admin_approved' => 'TC Admin Approved',
                                    'received' => 'Received',
                                    'rejected' => 'Rejected',
                                    'hold' => 'On Hold'
                                ];
                            @endphp
                            <span class="badge {{ $statusColors[$examSchedule->status] ?? 'bg-secondary' }} fs-6">
                                {{ $statusLabels[$examSchedule->status] ?? $examSchedule->status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Basic Information -->
                <div class="col-md-6">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                Basic Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Course/Qualification:</strong></td>
                                    <td>{{ $examSchedule->course_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Batch Code:</strong></td>
                                    <td>{{ $examSchedule->batch_code }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Semester:</strong></td>
                                    <td>{{ $examSchedule->semester }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Exam Type:</strong></td>
                                    <td><span class="badge bg-info">{{ $examSchedule->exam_type }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Exam Coordinator:</strong></td>
                                    <td>{{ $examSchedule->exam_coordinator }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Program Number:</strong></td>
                                    <td>{{ $examSchedule->program_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Centre:</strong></td>
                                    <td>
                                        @if($examSchedule->centre)
                                            <span class="badge bg-primary">{{ $examSchedule->centre->centre_name }}</span>
                                            <br><small class="text-muted">{{ $examSchedule->centre->address }}</small>
                                        @else
                                            <span class="text-muted">No centre selected</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Exam Period:</strong></td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($examSchedule->exam_start_date)->format('M d, Y') }} - 
                                        {{ \Carbon\Carbon::parse($examSchedule->exam_end_date)->format('M d, Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>File Number:</strong></td>
                                    <td>
                                        @if($examSchedule->file_no)
                                            <span class="badge bg-success text-white font-monospace fs-6">
                                                {{ $examSchedule->file_no }}
                                            </span>
                                        @else
                                            <span class="text-muted">
                                                <i class="bi bi-clock me-1"></i>
                                                Pending Approval
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $examSchedule->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Approval Status -->
                <div class="col-md-6">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-check-circle me-2"></i>
                                Approval Status
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="approval-timeline">
                                <div class="timeline-item {{ $examSchedule->status !== 'draft' ? 'completed' : '' }}">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6>Faculty</h6>
                                        <p class="text-muted mb-0">Schedule created</p>
                                        <small>{{ $examSchedule->created_at->format('M d, Y H:i') }}</small>
                                    </div>
                                </div>
                                
                                <div class="timeline-item {{ in_array($examSchedule->status, ['submitted', 'exam_cell_approved', 'tc_admin_approved', 'received']) ? 'completed' : '' }}">
                                    <div class="timeline-marker {{ in_array($examSchedule->status, ['submitted', 'exam_cell_approved', 'tc_admin_approved', 'received']) ? 'bg-success' : 'bg-light' }}"></div>
                                    <div class="timeline-content">
                                        <h6>Exam Cell</h6>
                                        <p class="text-muted mb-0">
                                            @if($examSchedule->status === 'submitted')
                                                Pending approval
                                            @elseif(in_array($examSchedule->status, ['exam_cell_approved', 'tc_admin_approved', 'received']))
                                                Approved
                                            @else
                                                Not submitted
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="timeline-item {{ in_array($examSchedule->status, ['tc_admin_approved', 'received']) ? 'completed' : '' }}">
                                    <div class="timeline-marker {{ in_array($examSchedule->status, ['tc_admin_approved', 'received']) ? 'bg-success' : 'bg-light' }}"></div>
                                    <div class="timeline-content">
                                        <h6>TC Admin</h6>
                                        <p class="text-muted mb-0">
                                            @if($examSchedule->status === 'exam_cell_approved')
                                                Pending approval
                                            @elseif(in_array($examSchedule->status, ['tc_admin_approved', 'received']))
                                                Approved
                                            @else
                                                Waiting for Exam Cell
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="timeline-item {{ $examSchedule->status === 'received' ? 'completed' : '' }}">
                                    <div class="timeline-marker {{ $examSchedule->status === 'received' ? 'bg-success' : 'bg-light' }}"></div>
                                    <div class="timeline-content">
                                        <h6>Assessment Agency</h6>
                                        <p class="text-muted mb-0">
                                            @if($examSchedule->status === 'tc_admin_approved')
                                                Pending approval
                                            @elseif($examSchedule->status === 'received')
                                                Received
                                            @else
                                                Waiting for TC Admin
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            @if($examSchedule->comment)
                            <div class="mt-3 p-3 bg-light rounded">
                                <h6><i class="bi bi-chat-dots me-2"></i>Latest Comment</h6>
                                <p class="mb-0">{{ $examSchedule->comment }}</p>
                            </div>
                            @endif
                            
                            @if($examSchedule->status === 'hold')
                            <div class="mt-3 p-3 bg-warning rounded">
                                <h6><i class="bi bi-pause-circle me-2"></i>Schedule On Hold</h6>
                                <p class="mb-1">
                                    @php
                                        $heldByLabels = [
                                            3 => 'Exam Cell',
                                            1 => 'TC Admin',
                                            2 => 'TC Head',
                                            4 => 'Assessment Agency'
                                        ];
                                    @endphp
                                    <strong>Put on hold by:</strong> {{ $heldByLabels[$examSchedule->held_by] ?? 'Unknown' }}
                                </p>
                                <p class="mb-0"><small class="text-muted">Only the person who put this schedule on hold can approve it to resume the process.</small></p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Students -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-people me-2"></i>
                        Selected Students ({{ $examSchedule->student_count }})
                    </h5>
                </div>
                <div class="card-body">
                    @if($examSchedule->student_count > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Roll No</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($examSchedule->student_roll_numbers as $rollNumber)
                                    <tr>
                                        <td>{{ $rollNumber }}</td>
                                        <td>
                                            <span class="badge bg-success">Selected</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-people display-4 text-muted"></i>
                            <p class="text-muted mt-2">No students selected for this exam schedule</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Modules -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-puzzle me-2"></i>
                        Module Details ({{ $examSchedule->modules->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    @if($examSchedule->modules->count() > 0)
                        <div class="row">
                            @foreach($examSchedule->modules as $module)
                            <div class="col-md-6 mb-3">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="bi bi-puzzle me-2"></i>
                                            {{ isset($qualificationModules[$module->nos_code]) ? $qualificationModules[$module->nos_code]->module_name : $module->nos_code }}
                                            <span class="badge {{ $module->is_theory ? 'bg-primary' : 'bg-success' }} ms-2">
                                                {{ $module->is_theory ? 'Theory' : 'Practical' }}
                                            </span>
                                        </h6>
                                        <small class="text-muted">NOS: {{ $module->nos_code }}</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6">
                                                <strong>Venue:</strong><br>
                                                {{ $module->venue }}
                                            </div>
                                            <div class="col-6">
                                                <strong>Invigilator:</strong><br>
                                                {{ $module->invigilator }}
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-4">
                                                <strong>Date:</strong><br>
                                                {{ \Carbon\Carbon::parse($module->exam_date)->format('M d, Y') }}
                                            </div>
                                            <div class="col-4">
                                                <strong>Start:</strong><br>
                                                {{ \Carbon\Carbon::parse($module->start_time)->format('H:i') }}
                                            </div>
                                            <div class="col-4">
                                                <strong>End:</strong><br>
                                                {{ \Carbon\Carbon::parse($module->end_time)->format('H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-puzzle display-4 text-muted"></i>
                            <p class="text-muted mt-2">No modules configured for this exam schedule</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Files -->
            @if($examSchedule->course_completion_file || $examSchedule->student_details_file)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-file-earmark me-2"></i>
                        Attached Files
                    </h5>
                </div>
                <div class="card-body">
                    @if(Auth::user()->user_role === 1 || Auth::user()->user_role === 2 || Auth::user()->user_role === 3 || Auth::user()->user_role === 4)
                    <!-- Debug information for admin users -->
                    <div class="alert alert-info mb-3">
                        <h6 class="alert-heading">Debug Information</h6>
                        <small>
                            <strong>Storage URL:</strong> {{ Storage::url($examSchedule->course_completion_file ?? $examSchedule->student_details_file) }}<br>
                            <strong>File exists:</strong> {{ Storage::disk('public')->exists($examSchedule->course_completion_file ?? $examSchedule->student_details_file) ? 'Yes' : 'No' }}<br>
                            <strong>Storage path:</strong> {{ storage_path('app/public/' . ($examSchedule->course_completion_file ?? $examSchedule->student_details_file)) }}
                        </small>
                    </div>
                    @endif
                    
                    <div class="row">
                        @if($examSchedule->course_completion_file)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center p-3 border rounded">
                                <i class="bi bi-file-earmark-text fs-2 text-primary me-3"></i>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">Course Completion File</h6>
                                    <small class="text-muted d-block mb-2">{{ $examSchedule->course_completion_file }}</small>
                                    <a href="{{ $examSchedule->course_completion_file_url }}" 
                                       target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                        <i class="bi bi-eye me-1"></i>
                                    </a>
                                    <a href="{{ $examSchedule->course_completion_file_url }}" 
                                       download class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-download me-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($examSchedule->student_details_file)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center p-3 border rounded">
                                <i class="bi bi-file-earmark-text fs-2 text-success me-3"></i>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">Student Details File</h6>
                                    <small class="text-muted d-block mb-2">{{ $examSchedule->student_details_file }}</small>
                                    <a href="{{ $examSchedule->student_details_file_url }}" 
                                       target="_blank" class="btn btn-sm btn-outline-success me-2">
                                        <i class="bi bi-eye me-1"></i>
                                    </a>
                                    <a href="{{ $examSchedule->student_details_file_url }}" 
                                       download class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-download me-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @else
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-file-earmark me-2"></i>
                        Attached Files
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center py-3">
                        <i class="bi bi-file-earmark display-4 text-muted"></i>
                        <p class="text-muted mt-2">No files attached to this exam schedule</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.approval-timeline {
    position: relative;
    padding-left: 30px;
}

.approval-timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-item.completed .timeline-marker {
    box-shadow: 0 0 0 2px #28a745;
}

.timeline-content h6 {
    margin-bottom: 5px;
    font-weight: 600;
}

.timeline-content p {
    margin-bottom: 5px;
}

.timeline-content small {
    color: #6c757d;
}
</style>
@endsection 