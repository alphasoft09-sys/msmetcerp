@extends('admin.layout')

@section('title', 'Assessment Agency - Exam Schedules')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1 text-primary">
                        <i class="bi bi-shield-check me-2"></i>
                        Assessment Agency Dashboard
                    </h1>
                    <p class="text-muted mb-0">Review and approve exam schedules from all Training Centers</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" id="exportBtn">
                        <i class="bi bi-download me-2"></i>
                        Export Data
                    </button>
                    <button class="btn btn-primary" id="refreshBtn">
                        <i class="bi bi-arrow-clockwise me-2"></i>
                        Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Approval
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="pendingCount">
                                {{ $examSchedules->where('status', 'tc_admin_approved')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock-history fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Approved
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="approvedCount">
                                {{ $examSchedules->where('status', 'received')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                On Hold
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="holdCount">
                                {{ $examSchedules->where('status', 'hold')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-pause-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Rejected
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="rejectedCount">
                                {{ $examSchedules->where('status', 'rejected')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-x-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabbed Interface -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <ul class="nav nav-tabs card-header-tabs" id="examScheduleTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">
                                <i class="bi bi-collection me-2"></i>
                                All Schedules
                                <span class="badge bg-primary ms-2">{{ $examSchedules->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="false">
                                <i class="bi bi-clock-history me-2"></i>
                                Pending Approval
                                <span class="badge bg-warning ms-2">{{ $examSchedules->where('status', 'tc_admin_approved')->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab" aria-controls="approved" aria-selected="false">
                                <i class="bi bi-check-circle me-2"></i>
                                Approved
                                <span class="badge bg-success ms-2">{{ $examSchedules->where('status', 'received')->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="hold-tab" data-bs-toggle="tab" data-bs-target="#hold" type="button" role="tab" aria-controls="hold" aria-selected="false">
                                <i class="bi bi-pause-circle me-2"></i>
                                On Hold
                                <span class="badge bg-secondary ms-2">{{ $examSchedules->where('status', 'hold')->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab" aria-controls="rejected" aria-selected="false">
                                <i class="bi bi-x-circle me-2"></i>
                                Rejected
                                <span class="badge bg-danger ms-2">{{ $examSchedules->where('status', 'rejected')->count() }}</span>
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-0">
                    <div class="tab-content" id="examScheduleTabContent">
                        <!-- All Schedules Tab -->
                        <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                            <div class="p-3">
                                <!-- Filters for All -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="card bg-light">
                                            <div class="card-body py-2">
                                                <form id="allFilterForm" class="row g-2">
                                                    <div class="col-md-2">
                                                        <select class="form-select form-select-sm" name="tc_code">
                                                            <option value="">All TCs</option>
                                                            @foreach($examSchedules->pluck('tc_code')->unique() as $tcCode)
                                                                <option value="{{ $tcCode }}" {{ request('tc_code') === $tcCode ? 'selected' : '' }}>{{ $tcCode }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <select class="form-select form-select-sm" name="exam_type">
                                                            <option value="">All Types</option>
                                                            <option value="Internal" {{ request('exam_type') === 'Internal' ? 'selected' : '' }}>Internal</option>
                                                            <option value="External" {{ request('exam_type') === 'External' ? 'selected' : '' }}>External</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <select class="form-select form-select-sm" name="status">
                                                            <option value="">All Status</option>
                                                            <option value="tc_admin_approved">Pending</option>
                                                            <option value="received">Approved</option>
                                                            <option value="hold">On Hold</option>
                                                            <option value="rejected">Rejected</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control form-control-sm" name="search" 
                                                               placeholder="Search..." value="{{ request('search') }}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="d-flex gap-2">
                                                            <button type="submit" class="btn btn-primary btn-sm">
                                                                <i class="bi bi-search me-1"></i>
                                                                Filter
                                                            </button>
                                                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearFilters('all')">
                                                                <i class="bi bi-x-circle"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- All Schedules Table -->
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Course/Qualification</th>
                                                <th>TC Code</th>
                                                <th>Batch Code</th>
                                                <th>Semester</th>
                                                <th>Exam Type</th>
                                                <th>Coordinator</th>
                                                <th>Exam Period</th>
                                                <th>Status</th>
                                                <th>Action By</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($examSchedules as $index => $schedule)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $schedule->course_name }}</td>
                                                <td>{{ $schedule->tc_code }}</td>
                                                <td>{{ $schedule->batch_code }}</td>
                                                <td>{{ $schedule->semester }}</td>
                                                <td>{{ $schedule->exam_type }}</td>
                                                <td>{{ $schedule->exam_coordinator }}</td>
                                                <td>
                                                    {{ $schedule->exam_start_date ? $schedule->exam_start_date->format('d/m/Y') : 'N/A' }}
                                                    <br>
                                                    <small class="text-muted">to</small>
                                                    <br>
                                                    {{ $schedule->exam_end_date ? $schedule->exam_end_date->format('d/m/Y') : 'N/A' }}
                                                </td>
                                                <td>
                                                    @php
                                                        $statusClass = match($schedule->status) {
                                                            'tc_admin_approved' => 'warning',
                                                            'received' => 'success',
                                                            'hold' => 'secondary',
                                                            'rejected' => 'danger',
                                                            default => 'primary'
                                                        };
                                                        $statusText = match($schedule->status) {
                                                            'tc_admin_approved' => 'Pending',
                                                            'received' => 'Approved',
                                                            'hold' => 'On Hold',
                                                            'rejected' => 'Rejected',
                                                            default => 'Unknown'
                                                        };
                                                    @endphp
                                                    <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                                                </td>
                                                <td>
                                                    @if($schedule->status === 'hold' && $schedule->heldByUser)
                                                        <div class="text-muted small">
                                                            <i class="bi bi-pause-circle me-1"></i>
                                                            Held by: {{ $schedule->heldByUser->name }}
                                                            <br>
                                                            <small>{{ $schedule->held_at ? $schedule->held_at->format('d/m/Y H:i') : 'N/A' }}</small>
                                                        </div>
                                                    @elseif($schedule->status === 'rejected' && $schedule->rejectedByUser)
                                                        <div class="text-danger small">
                                                            <i class="bi bi-x-circle me-1"></i>
                                                            Rejected by: {{ $schedule->rejectedByUser->name }}
                                                            <br>
                                                            <small>{{ $schedule->rejected_at ? $schedule->rejected_at->format('d/m/Y H:i') : 'N/A' }}</small>
                                                        </div>
                                                    @elseif($schedule->status === 'received' && $schedule->approvedByUser)
                                                        <div class="text-success small">
                                                            <i class="bi bi-check-circle me-1"></i>
                                                            Approved by: {{ $schedule->approvedByUser->name }}
                                                            <br>
                                                            <small>{{ $schedule->approved_at ? $schedule->approved_at->format('d/m/Y H:i') : 'N/A' }}</small>
                                                        </div>
                                                    @else
                                                        <span class="text-muted small">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('admin.aa.exam-schedules.show', $schedule->id) }}" 
                                                           class="btn btn-outline-primary" title="View Details">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        @if($schedule->status === 'tc_admin_approved')
                                                        <button type="button" class="btn btn-outline-success"
                                                                onclick="approveSchedule('{{ $schedule->id }}')" title="Approve">
                                                            <i class="bi bi-check-lg"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger"
                                                                onclick="rejectSchedule('{{ $schedule->id }}')" title="Reject">
                                                            <i class="bi bi-x-lg"></i>
                                                        </button>
                                                         <button type="button" class="btn btn-outline-warning"
                                                                 onclick="holdSchedule('{{ $schedule->id }}')" title="Hold">
                                                             <i class="bi bi-pause-circle"></i>
                                                        </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                @if($examSchedules->count() === 0)
                                <div class="text-center py-5">
                                    <i class="bi bi-inbox display-1 text-muted"></i>
                                    <h4 class="mt-3 text-muted">No Schedules Found</h4>
                                    <p class="text-muted">No exam schedules match your current filters.</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Pending Approval Tab -->
                        <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                            <div class="p-3">
                                <!-- Filters for Pending -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="card bg-light">
                                            <div class="card-body py-2">
                                                <form id="pendingFilterForm" class="row g-2">
                                                    <div class="col-md-3">
                                                        <select class="form-select form-select-sm" name="tc_code">
                                                            <option value="">All TCs</option>
                                                            @foreach($examSchedules->pluck('tc_code')->unique() as $tcCode)
                                                                <option value="{{ $tcCode }}" {{ request('tc_code') === $tcCode ? 'selected' : '' }}>{{ $tcCode }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select class="form-select form-select-sm" name="exam_type">
                                                            <option value="">All Types</option>
                                                            <option value="Internal" {{ request('exam_type') === 'Internal' ? 'selected' : '' }}>Internal</option>
                                                            <option value="External" {{ request('exam_type') === 'External' ? 'selected' : '' }}>External</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control form-control-sm" name="search" 
                                                               placeholder="Search..." value="{{ request('search') }}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="d-flex gap-2">
                                                            <button type="submit" class="btn btn-warning btn-sm">
                                                                <i class="bi bi-search me-1"></i>
                                                                Filter
                                                            </button>
                                                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearFilters('pending')">
                                                                <i class="bi bi-x-circle"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Pending Schedules Table -->
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-warning">
                                            <tr>
                                                <th>#</th>
                                                <th>Course/Qualification</th>
                                                <th>TC Code</th>
                                                <th>Batch Code</th>
                                                <th>Semester</th>
                                                <th>Exam Type</th>
                                                <th>Coordinator</th>
                                                <th>Exam Period</th>
                                                <th>Created</th>
                                                <th>Action By</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($examSchedules->where('status', 'tc_admin_approved') as $index => $schedule)
                                            <tr>
                                                <td class="text-center">
                                                    <span class="badge bg-warning">{{ $loop->iteration }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <strong class="text-primary">{{ $schedule->course_name }}</strong>
                                                        <small class="text-muted">{{ $schedule->program_number ?? 'N/A' }}</small>
                                                    </div>
                                                </td>
                                                <td><span class="badge bg-info">{{ $schedule->tc_code }}</span></td>
                                                <td>{{ $schedule->batch_code }}</td>
                                                <td><span class="badge bg-secondary">Sem {{ $schedule->semester }}</span></td>
                                                <td>
                                                    <span class="badge {{ $schedule->exam_type === 'Internal' ? 'bg-success' : 'bg-warning' }}">
                                                        {{ $schedule->exam_type }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <i class="bi bi-person-circle text-primary"></i>
                                                        </div>
                                                        <div>
                                                            <div class="fw-medium">{{ $schedule->exam_coordinator }}</div>
                                                            <small class="text-muted">{{ $schedule->faculty->name ?? 'N/A' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <small class="fw-medium">
                                                            {{ \Carbon\Carbon::parse($schedule->exam_start_date)->format('M d, Y') }}
                                                        </small>
                                                        <small class="text-muted">
                                                            to {{ \Carbon\Carbon::parse($schedule->exam_end_date)->format('M d, Y') }}
                                                        </small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <small class="fw-medium">
                                                            {{ $schedule->created_at->format('M d, Y') }}
                                                        </small>
                                                        <small class="text-muted">
                                                            {{ $schedule->created_at->format('H:i') }}
                                                        </small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-muted small">Pending Approval</span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.aa.exam-schedules.show', $schedule->id) }}" 
                                                           class="btn btn-sm btn-outline-primary" title="View Details">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-outline-success approve-schedule-btn"
                                                                data-schedule-id="{{ $schedule->id }}"
                                                                data-schedule-name="{{ $schedule->course_name }}"
                                                                data-role="4"
                                                                data-tc-code="{{ $schedule->tc_code }}"
                                                                title="Approve">
                                                            <i class="bi bi-check-lg"></i>
                                                        </button>
                                                                                                                 <button type="button" class="btn btn-sm btn-outline-danger reject-schedule-btn"
                                                                 data-schedule-id="{{ $schedule->id }}"
                                                                 data-schedule-name="{{ $schedule->course_name }}"
                                                                 data-role="4"
                                                                 title="Reject">
                                                             <i class="bi bi-x-lg"></i>
                                                         </button>
                                                         <button type="button" class="btn btn-sm btn-outline-warning hold-schedule-btn"
                                                                 data-schedule-id="{{ $schedule->id }}"
                                                                 data-schedule-name="{{ $schedule->course_name }}"
                                                                 data-role="4"
                                                                 title="Hold">
                                                             <i class="bi bi-pause-circle"></i>
                                                         </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                @if($examSchedules->where('status', 'tc_admin_approved')->count() === 0)
                                <div class="text-center py-5">
                                    <i class="bi bi-clock-history display-1 text-muted"></i>
                                    <h4 class="mt-3 text-muted">No Pending Approvals</h4>
                                    <p class="text-muted">All exam schedules have been processed.</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Approved Tab -->
                        <div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
                            <div class="p-3">
                                <!-- Filters for Approved -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="card bg-light">
                                            <div class="card-body py-2">
                                                <form id="approvedFilterForm" class="row g-2">
                                                    <div class="col-md-3">
                                                        <select class="form-select form-select-sm" name="tc_code">
                                                            <option value="">All TCs</option>
                                                            @foreach($examSchedules->pluck('tc_code')->unique() as $tcCode)
                                                                <option value="{{ $tcCode }}" {{ request('tc_code') === $tcCode ? 'selected' : '' }}>{{ $tcCode }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select class="form-select form-select-sm" name="exam_type">
                                                            <option value="">All Types</option>
                                                            <option value="Internal" {{ request('exam_type') === 'Internal' ? 'selected' : '' }}>Internal</option>
                                                            <option value="External" {{ request('exam_type') === 'External' ? 'selected' : '' }}>External</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control form-control-sm" name="search" 
                                                               placeholder="Search..." value="{{ request('search') }}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="d-flex gap-2">
                                                            <button type="submit" class="btn btn-success btn-sm">
                                                                <i class="bi bi-search me-1"></i>
                                                                Filter
                                                            </button>
                                                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearFilters('approved')">
                                                                <i class="bi bi-x-circle"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Approved Schedules Table -->
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-success">
                                            <tr>
                                                <th>#</th>
                                                <th>Course/Qualification</th>
                                                <th>TC Code</th>
                                                <th>Batch Code</th>
                                                <th>Semester</th>
                                                <th>Exam Type</th>
                                                <th>File Number</th>
                                                <th>Approved Date</th>
                                                <th>Action By</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($examSchedules->where('status', 'received') as $index => $schedule)
                                            <tr>
                                                <td class="text-center">
                                                    <span class="badge bg-success">{{ $loop->iteration }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <strong class="text-primary">{{ $schedule->course_name }}</strong>
                                                        <small class="text-muted">{{ $schedule->program_number ?? 'N/A' }}</small>
                                                    </div>
                                                </td>
                                                <td><span class="badge bg-info">{{ $schedule->tc_code }}</span></td>
                                                <td>{{ $schedule->batch_code }}</td>
                                                <td><span class="badge bg-secondary">Sem {{ $schedule->semester }}</span></td>
                                                <td>
                                                    <span class="badge {{ $schedule->exam_type === 'Internal' ? 'bg-success' : 'bg-warning' }}">
                                                        {{ $schedule->exam_type }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($schedule->file_no)
                                                        <span class="badge bg-success text-white font-monospace">
                                                            {{ $schedule->file_no }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">
                                                            <i class="bi bi-clock me-1"></i>
                                                            Pending
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <small class="fw-medium">
                                                            {{ $schedule->updated_at->format('M d, Y') }}
                                                        </small>
                                                        <small class="text-muted">
                                                            {{ $schedule->updated_at->format('H:i') }}
                                                        </small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.aa.exam-schedules.show', $schedule->id) }}" 
                                                           class="btn btn-sm btn-outline-primary" title="View Details">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.aa.exam-schedules.fullview', $schedule->id) }}" 
                                                           class="btn btn-sm btn-outline-secondary" target="_blank" title="Print View">
                                                            <i class="bi bi-printer"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                @if($examSchedules->where('status', 'received')->count() === 0)
                                <div class="text-center py-5">
                                    <i class="bi bi-check-circle display-1 text-muted"></i>
                                    <h4 class="mt-3 text-muted">No Approved Schedules</h4>
                                    <p class="text-muted">No exam schedules have been approved yet.</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- On Hold Tab -->
                        <div class="tab-pane fade" id="hold" role="tabpanel" aria-labelledby="hold-tab">
                            <div class="p-3">
                                <!-- Filters for Hold -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="card bg-light">
                                            <div class="card-body py-2">
                                                <form id="holdFilterForm" class="row g-2">
                                                    <div class="col-md-3">
                                                        <select class="form-select form-select-sm" name="tc_code">
                                                            <option value="">All TCs</option>
                                                            @foreach($examSchedules->pluck('tc_code')->unique() as $tcCode)
                                                                <option value="{{ $tcCode }}" {{ request('tc_code') === $tcCode ? 'selected' : '' }}>{{ $tcCode }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select class="form-select form-select-sm" name="exam_type">
                                                            <option value="">All Types</option>
                                                            <option value="Internal" {{ request('exam_type') === 'Internal' ? 'selected' : '' }}>Internal</option>
                                                            <option value="External" {{ request('exam_type') === 'External' ? 'selected' : '' }}>External</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control form-control-sm" name="search" 
                                                               placeholder="Search..." value="{{ request('search') }}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="d-flex gap-2">
                                                            <button type="submit" class="btn btn-secondary btn-sm">
                                                                <i class="bi bi-search me-1"></i>
                                                                Filter
                                                            </button>
                                                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearFilters('hold')">
                                                                <i class="bi bi-x-circle"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Hold Schedules Table -->
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th>#</th>
                                                <th>Course/Qualification</th>
                                                <th>TC Code</th>
                                                <th>Batch Code</th>
                                                <th>Semester</th>
                                                <th>Exam Type</th>
                                                <th>Hold Date</th>
                                                <th>Action By</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($examSchedules->where('status', 'hold') as $index => $schedule)
                                            <tr>
                                                <td class="text-center">
                                                    <span class="badge bg-secondary">{{ $loop->iteration }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <strong class="text-primary">{{ $schedule->course_name }}</strong>
                                                        <small class="text-muted">{{ $schedule->program_number ?? 'N/A' }}</small>
                                                    </div>
                                                </td>
                                                <td><span class="badge bg-info">{{ $schedule->tc_code }}</span></td>
                                                <td>{{ $schedule->batch_code }}</td>
                                                <td><span class="badge bg-secondary">Sem {{ $schedule->semester }}</span></td>
                                                <td>
                                                    <span class="badge {{ $schedule->exam_type === 'Internal' ? 'bg-success' : 'bg-warning' }}">
                                                        {{ $schedule->exam_type }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <small class="fw-medium">
                                                            {{ $schedule->updated_at->format('M d, Y') }}
                                                        </small>
                                                        <small class="text-muted">
                                                            {{ $schedule->updated_at->format('H:i') }}
                                                        </small>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($schedule->heldByUser)
                                                        <div class="text-muted small">
                                                            <i class="bi bi-pause-circle me-1"></i>
                                                            {{ $schedule->heldByUser->name }}
                                                            <br>
                                                            <small>{{ $schedule->held_at ? $schedule->held_at->format('d/m/Y H:i') : 'N/A' }}</small>
                                                        </div>
                                                    @else
                                                        <span class="text-muted small">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.aa.exam-schedules.show', $schedule->id) }}" 
                                                           class="btn btn-sm btn-outline-primary" title="View Details">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.aa.exam-schedules.fullview', $schedule->id) }}" 
                                                           class="btn btn-sm btn-outline-secondary" target="_blank" title="Print View">
                                                            <i class="bi bi-printer"></i>
                                                        </a>
                                                        @if($schedule->held_by === $user->id)
                                                        <button type="button" class="btn btn-sm btn-outline-success approve-schedule-btn"
                                                                data-schedule-id="{{ $schedule->id }}"
                                                                data-schedule-name="{{ $schedule->course_name }}"
                                                                data-role="4"
                                                                data-tc-code="{{ $schedule->tc_code }}"
                                                                title="Resume">
                                                            <i class="bi bi-play-circle"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger reject-schedule-btn"
                                                                data-schedule-id="{{ $schedule->id }}"
                                                                data-schedule-name="{{ $schedule->course_name }}"
                                                                data-role="4"
                                                                title="Reject">
                                                            <i class="bi bi-x-lg"></i>
                                                        </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                @if($examSchedules->where('status', 'hold')->count() === 0)
                                <div class="text-center py-5">
                                    <i class="bi bi-pause-circle display-1 text-muted"></i>
                                    <h4 class="mt-3 text-muted">No Schedules On Hold</h4>
                                    <p class="text-muted">No exam schedules are currently on hold.</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Rejected Tab -->
                        <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
                            <div class="p-3">
                                <!-- Filters for Rejected -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="card bg-light">
                                            <div class="card-body py-2">
                                                <form id="rejectedFilterForm" class="row g-2">
                                                    <div class="col-md-3">
                                                        <select class="form-select form-select-sm" name="tc_code">
                                                            <option value="">All TCs</option>
                                                            @foreach($examSchedules->pluck('tc_code')->unique() as $tcCode)
                                                                <option value="{{ $tcCode }}" {{ request('tc_code') === $tcCode ? 'selected' : '' }}>{{ $tcCode }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select class="form-select form-select-sm" name="exam_type">
                                                            <option value="">All Types</option>
                                                            <option value="Internal" {{ request('exam_type') === 'Internal' ? 'selected' : '' }}>Internal</option>
                                                            <option value="External" {{ request('exam_type') === 'External' ? 'selected' : '' }}>External</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control form-control-sm" name="search" 
                                                               placeholder="Search..." value="{{ request('search') }}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="d-flex gap-2">
                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                <i class="bi bi-search me-1"></i>
                                                                Filter
                                                            </button>
                                                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearFilters('rejected')">
                                                                <i class="bi bi-x-circle"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Rejected Schedules Table -->
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-danger">
                                            <tr>
                                                <th>#</th>
                                                <th>Course/Qualification</th>
                                                <th>TC Code</th>
                                                <th>Batch Code</th>
                                                <th>Semester</th>
                                                <th>Exam Type</th>
                                                <th>Rejection Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($examSchedules->where('status', 'rejected') as $index => $schedule)
                                            <tr>
                                                <td class="text-center">
                                                    <span class="badge bg-danger">{{ $loop->iteration }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <strong class="text-primary">{{ $schedule->course_name }}</strong>
                                                        <small class="text-muted">{{ $schedule->program_number ?? 'N/A' }}</small>
                                                    </div>
                                                </td>
                                                <td><span class="badge bg-info">{{ $schedule->tc_code }}</span></td>
                                                <td>{{ $schedule->batch_code }}</td>
                                                <td><span class="badge bg-secondary">Sem {{ $schedule->semester }}</span></td>
                                                <td>
                                                    <span class="badge {{ $schedule->exam_type === 'Internal' ? 'bg-success' : 'bg-warning' }}">
                                                        {{ $schedule->exam_type }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <small class="fw-medium">
                                                            {{ $schedule->updated_at->format('M d, Y') }}
                                                        </small>
                                                        <small class="text-muted">
                                                            {{ $schedule->updated_at->format('H:i') }}
                                                        </small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.aa.exam-schedules.show', $schedule->id) }}" 
                                                           class="btn btn-sm btn-outline-primary" title="View Details">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.aa.exam-schedules.fullview', $schedule->id) }}" 
                                                           class="btn btn-sm btn-outline-secondary" target="_blank" title="Print View">
                                                            <i class="bi bi-printer"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                @if($examSchedules->where('status', 'rejected')->count() === 0)
                                <div class="text-center py-5">
                                    <i class="bi bi-x-circle display-1 text-muted"></i>
                                    <h4 class="mt-3 text-muted">No Rejected Schedules</h4>
                                    <p class="text-muted">No exam schedules have been rejected.</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Modals -->
@include('admin.exam-schedules.partials.aa-modals')

@endsection

@push('styles')
<style>
/* Enhanced styling for Assessment Agency dashboard */
.border-left-warning {
    border-left: 4px solid #f6c23e !important;
}

.border-left-success {
    border-left: 4px solid #1cc88a !important;
}

.border-left-secondary {
    border-left: 4px solid #858796 !important;
}

.border-left-danger {
    border-left: 4px solid #e74a3b !important;
}

.avatar-sm {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background-color: #f8f9fc;
}

/* Tab styling */
.nav-tabs .nav-link {
    border: none;
    border-bottom: 3px solid transparent;
    color: #6c757d;
    font-weight: 500;
    padding: 0.75rem 1rem;
    transition: all 0.2s ease;
}

.nav-tabs .nav-link:hover {
    border-color: transparent;
    color: #495057;
    background-color: #f8f9fa;
}

.nav-tabs .nav-link.active {
    border-bottom: 3px solid #007bff;
    color: #007bff;
    background-color: transparent;
    font-weight: 600;
}

.nav-tabs .nav-link .badge {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

/* Table styling */
.table th {
    font-weight: 600;
    color: #5a5c69;
    border-bottom: 2px solid #e3e6f0;
}

.table td {
    vertical-align: middle;
    border-bottom: 1px solid #e3e6f0;
}

/* Badge enhancements */
.badge {
    font-size: 0.75rem;
    font-weight: 600;
}

/* Button group styling */
.btn-group .btn {
    border-radius: 0.375rem !important;
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
    }
    
    .btn-group .btn {
        margin-bottom: 2px;
        margin-right: 0;
    }
    
    .nav-tabs {
        flex-wrap: wrap;
    }
    
    .nav-tabs .nav-link {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
    }
}

/* Loading states */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

/* Success animations */
.success-animation {
    animation: successPulse 0.5s ease-in-out;
}

@keyframes successPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* Tab content animations */
.tab-pane {
    transition: opacity 0.15s linear;
}

.tab-pane.fade {
    opacity: 0;
}

.tab-pane.fade.show {
    opacity: 1;
}

/* Serial number column styling */
.table th:first-child,
.table td:first-child {
    width: 60px;
    text-align: center;
}

.table td:first-child .badge {
    font-size: 0.8rem;
    padding: 0.4rem 0.6rem;
    min-width: 30px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Assessment Agency Exam Schedules page loaded');
    
    // Initialize modals
    let aaApprovalModal, rejectModal, holdModal;
    
    try {
        aaApprovalModal = new bootstrap.Modal(document.getElementById('aaApprovalModal'));
        rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));
        holdModal = new bootstrap.Modal(document.getElementById('holdModal'));
        console.log('All modals initialized successfully');
    } catch (error) {
        console.error('Error initializing modals:', error);
    }
    
    // CSRF token setup
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Filter functionality for each tab
    const filterForms = ['allFilterForm', 'pendingFilterForm', 'approvedFilterForm', 'holdFilterForm', 'rejectedFilterForm'];
    
    filterForms.forEach(formId => {
        const form = document.getElementById(formId);
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                applyFilters(this);
            });
        }
    });
    
    // Export functionality
    document.getElementById('exportBtn').addEventListener('click', function() {
        const activeTab = document.querySelector('.nav-link.active');
        const tabId = activeTab.getAttribute('data-bs-target').replace('#', '');
        
        // Get current filters from the active tab
        const form = document.getElementById(tabId + 'FilterForm');
        const formData = new FormData(form);
        const params = new URLSearchParams();
        
        // Add status filter based on active tab
        switch(tabId) {
            case 'pending':
                params.append('status', 'tc_admin_approved');
                break;
            case 'approved':
                params.append('status', 'received');
                break;
            case 'hold':
                params.append('status', 'hold');
                break;
            case 'rejected':
                params.append('status', 'rejected');
                break;
        }
        
        for (let [key, value] of formData.entries()) {
            if (value) {
                params.append(key, value);
            }
        }
        
        window.open(`/admin/aa/exam-schedules/export?${params.toString()}`, '_blank');
    });
    
    // Refresh functionality
    document.getElementById('refreshBtn').addEventListener('click', function() {
        window.location.reload();
    });
    
    // Global functions for button actions
    window.approveSchedule = function(scheduleId) {
        // Find the button and trigger click
        const btn = document.querySelector(`[data-schedule-id="${scheduleId}"].approve-schedule-btn`);
        if (btn) {
            btn.click();
        }
    };
    
    window.rejectSchedule = function(scheduleId) {
        // Find the button and trigger click
        const btn = document.querySelector(`[data-schedule-id="${scheduleId}"].reject-schedule-btn`);
        if (btn) {
            btn.click();
        }
    };
    
    window.holdSchedule = function(scheduleId) {
        // Find the button and trigger click
        const btn = document.querySelector(`[data-schedule-id="${scheduleId}"].hold-schedule-btn`);
        if (btn) {
            btn.click();
        }
    };
    
    // Global function for clearing filters
    window.clearFilters = function(tabName) {
        const form = document.getElementById(tabName + 'FilterForm');
        if (form) {
            form.reset();
            const inputs = form.querySelectorAll('input, select');
            inputs.forEach(input => {
                if (input.type === 'text') {
                    input.value = '';
                } else if (input.tagName === 'SELECT') {
                    input.selectedIndex = 0;
                }
            });
            window.location.href = window.location.pathname;
        }
    };
    
    function applyFilters(form) {
        const formData = new FormData(form);
        const params = new URLSearchParams();
        
        for (let [key, value] of formData.entries()) {
            if (value) {
                params.append(key, value);
            }
        }
        
        window.location.href = `${window.location.pathname}?${params.toString()}`;
    }
    
    // Modal event handlers
    // Approve Schedule Button Click
    document.querySelectorAll('.approve-schedule-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const scheduleId = this.getAttribute('data-schedule-id');
            const scheduleName = this.getAttribute('data-schedule-name');
            const tcCode = this.getAttribute('data-tc-code');
            
            // Set modal content
            document.getElementById('aaApprovalScheduleName').textContent = scheduleName;
            document.getElementById('aaApprovalForm').setAttribute('data-schedule-id', scheduleId);
            document.getElementById('aaApprovalForm').setAttribute('data-tc-code', tcCode);
            
            // Generate file number preview
            generateFileNumberPreview(scheduleId, tcCode);
            
            // Show modal
            if (aaApprovalModal) {
                aaApprovalModal.show();
            }
        });
    });

    // Reject Schedule Button Click
    document.querySelectorAll('.reject-schedule-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const scheduleId = this.getAttribute('data-schedule-id');
            const scheduleName = this.getAttribute('data-schedule-name');
            
            document.getElementById('rejectScheduleName').textContent = scheduleName;
            document.getElementById('rejectForm').setAttribute('data-schedule-id', scheduleId);
            
            if (rejectModal) {
                rejectModal.show();
            }
        });
    });

    // Hold Schedule Button Click
    document.querySelectorAll('.hold-schedule-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const scheduleId = this.getAttribute('data-schedule-id');
            const scheduleName = this.getAttribute('data-schedule-name');
            
            document.getElementById('holdScheduleName').textContent = scheduleName;
            document.getElementById('holdForm').setAttribute('data-schedule-id', scheduleId);
            
            if (holdModal) {
                holdModal.show();
            }
        });
    });

    // Form submission handlers
    // AA Approval Form
    document.getElementById('aaApprovalForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const scheduleId = this.getAttribute('data-schedule-id');
        const remarks = document.getElementById('aaApprovalComment').value;
        const aaApprovalText = document.getElementById('aaApprovalText');
        const aaApprovalLoader = document.getElementById('aaApprovalLoader');
        
        // Show loading state
        aaApprovalText.textContent = 'Approving...';
        aaApprovalLoader.classList.remove('d-none');
        
        // Send approval request
        fetch(`/admin/aa/exam-schedules/${scheduleId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                remarks: remarks
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showAlert('success', 'Exam schedule approved successfully!');
                // Hide modal
                aaApprovalModal.hide();
                // Reload page after delay
                setTimeout(() => {
                window.location.reload();
                }, 1500);
            } else {
                showAlert('error', data.message || 'Failed to approve exam schedule');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'An error occurred while approving the schedule');
        })
        .finally(() => {
            // Reset loading state
            aaApprovalText.textContent = 'Approve & Assign File Number';
            aaApprovalLoader.classList.add('d-none');
        });
    });
    
    // Reject Form
        document.getElementById('rejectForm').addEventListener('submit', function(e) {
            e.preventDefault();
        
            const scheduleId = this.getAttribute('data-schedule-id');
        const comment = document.getElementById('rejectComment').value;
            const rejectText = document.getElementById('rejectText');
            const rejectLoader = document.getElementById('rejectLoader');
            
        if (!comment.trim()) {
            showAlert('error', 'Please provide a reason for rejection');
            return;
        }
        
        // Show loading state
            rejectText.textContent = 'Rejecting...';
            rejectLoader.classList.remove('d-none');
            
        // Send rejection request
            fetch(`/admin/aa/exam-schedules/${scheduleId}/reject`, {
                method: 'POST',
                headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
                },
            body: JSON.stringify({
                comment: comment
            })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                showAlert('success', 'Exam schedule rejected successfully!');
                rejectModal.hide();
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
                } else {
                showAlert('error', data.message || 'Failed to reject exam schedule');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            showAlert('error', 'An error occurred while rejecting the schedule');
            })
            .finally(() => {
                rejectText.textContent = 'Reject';
                rejectLoader.classList.add('d-none');
            });
        });
        
    // Hold Form
        document.getElementById('holdForm').addEventListener('submit', function(e) {
            e.preventDefault();
        
            const scheduleId = this.getAttribute('data-schedule-id');
        const comment = document.getElementById('holdComment').value;
            const holdText = document.getElementById('holdText');
            const holdLoader = document.getElementById('holdLoader');
            
        if (!comment.trim()) {
            showAlert('error', 'Please provide a reason for putting on hold');
            return;
        }
        
        // Show loading state
        holdText.textContent = 'Putting on Hold...';
            holdLoader.classList.remove('d-none');
            
        // Send hold request
            fetch(`/admin/aa/exam-schedules/${scheduleId}/hold`, {
                method: 'POST',
                headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
                },
            body: JSON.stringify({
                comment: comment
            })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                showAlert('success', 'Exam schedule put on hold successfully!');
                holdModal.hide();
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
                } else {
                showAlert('error', data.message || 'Failed to put schedule on hold');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            showAlert('error', 'An error occurred while putting schedule on hold');
            })
            .finally(() => {
                holdText.textContent = 'Hold';
                holdLoader.classList.add('d-none');
        });
    });
    
    // Helper functions
    function generateFileNumberPreview(scheduleId, tcCode) {
        const fileNumberElement = document.getElementById('aaGeneratedFileNumber');
        
        // Reset classes and show loading state
        fileNumberElement.className = 'file-number-display';
        fileNumberElement.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2" role="status">
                <span class="visually-hidden">Loading...</span>
                            </span>
            Generating...
        `;
        
        // Fetch file number preview
        fetch(`/api/file-number-preview/${scheduleId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                fileNumberElement.className = 'file-number-display success';
                fileNumberElement.innerHTML = `
                    <i class="bi bi-file-earmark-text me-2"></i>
                    ${data.file_number}
                `;
            } else {
                fileNumberElement.className = 'file-number-display error';
                fileNumberElement.innerHTML = `
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error generating file number
                `;
            }
        })
        .catch(error => {
            console.error('Error generating file number:', error);
            fileNumberElement.className = 'file-number-display error';
            fileNumberElement.innerHTML = `
                <i class="bi bi-exclamation-triangle me-2"></i>
                Error generating file number
            `;
        });
    }
    
    function showAlert(type, message) {
        // Create alert element
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Add to page
        document.body.appendChild(alertDiv);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
});
</script>
@endpush 