@extends('admin.layout')

@section('title', 'Exam Schedules')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1 text-primary">
                        <i class="bi bi-calendar-event me-2"></i>
                        @if($user->user_role === 5)
                            Faculty Dashboard
                        @elseif($user->user_role === 3)
                            Exam Cell Dashboard
                        @elseif($user->user_role === 1)
                            TC Admin Dashboard
                        @elseif($user->user_role === 2)
                            TC Head Dashboard
                        @elseif($user->user_role === 4)
                            Assessment Agency Dashboard
                        @endif
                    </h1>
                    <p class="text-muted mb-0">
                        @if($user->user_role === 5)
                            Manage your exam schedules and track approval status
                        @elseif($user->user_role === 3)
                            Review and approve exam schedules from faculty
                        @elseif($user->user_role === 1)
                            Review and approve exam schedules from your TC
                        @elseif($user->user_role === 2)
                            Review and approve exam schedules from your TC
                        @elseif($user->user_role === 4)
                            Review and approve exam schedules from all TCs
                        @endif
                    </p>
                </div>
                <div class="d-flex gap-2">
                    @if($user->user_role === 5)
                    <a href="{{ route('admin.faculty.exam-schedules.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle me-2"></i>
                        Create Exam Schedule
                    </a>
                    @endif
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
                                @if($user->user_role === 5)
                                    {{ $examSchedules->whereIn('status', ['draft', 'submitted', 'hold'])->where('created_by', $user->id)->count() }}
                                @elseif($user->user_role === 3)
                                    {{ $examSchedules->where('status', 'submitted')->count() }}
                                @elseif($user->user_role === 1 || $user->user_role === 2)
                                    {{ $examSchedules->where('status', 'exam_cell_approved')->count() }}
                                @elseif($user->user_role === 4)
                                    {{ $examSchedules->where('status', 'tc_admin_approved')->count() }}
                @endif
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
                                @if($user->user_role === 5)
                                    {{ $examSchedules->whereIn('status', ['exam_cell_approved', 'tc_admin_approved', 'received'])->where('created_by', $user->id)->count() }}
                                @elseif($user->user_role === 3)
                                    {{ $examSchedules->whereIn('status', ['exam_cell_approved', 'tc_admin_approved', 'received'])->count() }}
                                @elseif($user->user_role === 1 || $user->user_role === 2)
                                    {{ $examSchedules->where('status', 'tc_admin_approved')->count() }}
                                @elseif($user->user_role === 4)
                                    {{ $examSchedules->where('status', 'received')->count() }}
                                @endif
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
                                @if($user->user_role === 5)
                                    {{ $examSchedules->where('status', 'hold')->where('created_by', $user->id)->count() }}
                                @elseif($user->user_role === 3)
                                    {{ $examSchedules->where('status', 'hold')->count() }}
                                @elseif($user->user_role === 1 || $user->user_role === 2)
                                    {{ $examSchedules->where('status', 'hold')->where('held_by', $user->id)->count() }}
                                @elseif($user->user_role === 4)
                                    {{ $examSchedules->where('status', 'hold')->where('held_by', $user->id)->count() }}
                                @endif
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
                                @if($user->user_role === 5)
                                    {{ $examSchedules->where('status', 'rejected')->where('created_by', $user->id)->count() }}
                                @elseif($user->user_role === 3)
                                    {{ $examSchedules->where('status', 'rejected')->count() }}
                                @elseif($user->user_role === 1 || $user->user_role === 2)
                                    {{ $examSchedules->where('status', 'rejected')->where(function($query) use ($user) { $query->where('rejected_by', $user->id)->orWhere('tc_code', $user->tc_code); })->count() }}
                                @elseif($user->user_role === 4)
                                    {{ $examSchedules->where('status', 'rejected')->count() }}
                                @endif
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
                                <span class="badge bg-primary ms-2">
                                    @if($user->user_role === 5)
                                        {{ $examSchedules->where('created_by', $user->id)->count() }}
                                    @else
                                        {{ $examSchedules->count() }}
                                    @endif
                                </span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="false">
                                <i class="bi bi-clock-history me-2"></i>
                                Pending Approval
                                <span class="badge bg-warning ms-2">
                                    @if($user->user_role === 5)
                                        {{ $examSchedules->whereIn('status', ['draft', 'submitted', 'hold'])->where('created_by', $user->id)->count() }}
                                    @elseif($user->user_role === 3)
                                        {{ $examSchedules->where('status', 'submitted')->count() }}
                                    @elseif($user->user_role === 1 || $user->user_role === 2)
                                        {{ $examSchedules->where('status', 'exam_cell_approved')->count() }}
                                    @elseif($user->user_role === 4)
                                        {{ $examSchedules->where('status', 'tc_admin_approved')->count() }}
                                    @endif
                                </span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab" aria-controls="approved" aria-selected="false">
                                <i class="bi bi-check-circle me-2"></i>
                                Approved
                                <span class="badge bg-success ms-2">
                                    @if($user->user_role === 5)
                                        {{ $examSchedules->whereIn('status', ['exam_cell_approved', 'tc_admin_approved', 'received'])->where('created_by', $user->id)->count() }}
                                    @elseif($user->user_role === 3)
                                        {{ $examSchedules->whereIn('status', ['exam_cell_approved', 'tc_admin_approved', 'received'])->count() }}
                                    @elseif($user->user_role === 1 || $user->user_role === 2)
                                        {{ $examSchedules->where('status', 'tc_admin_approved')->count() }}
                                    @elseif($user->user_role === 4)
                                        {{ $examSchedules->where('status', 'received')->count() }}
                                    @endif
                                </span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="hold-tab" data-bs-toggle="tab" data-bs-target="#hold" type="button" role="tab" aria-controls="hold" aria-selected="false">
                                <i class="bi bi-pause-circle me-2"></i>
                                On Hold
                                <span class="badge bg-secondary ms-2">
                                    @if($user->user_role === 5)
                                        {{ $examSchedules->where('status', 'hold')->where('created_by', $user->id)->count() }}
                                    @elseif($user->user_role === 3)
                                        {{ $examSchedules->where('status', 'hold')->count() }}
                                    @elseif($user->user_role === 1 || $user->user_role === 2)
                                        {{ $examSchedules->where('status', 'hold')->where('held_by', $user->id)->count() }}
                                    @elseif($user->user_role === 4)
                                        {{ $examSchedules->where('status', 'hold')->where('held_by', $user->id)->count() }}
                                    @endif
                                </span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab" aria-controls="rejected" aria-selected="false">
                                <i class="bi bi-x-circle me-2"></i>
                                Rejected
                                <span class="badge bg-danger ms-2">
                                    @if($user->user_role === 5)
                                        {{ $examSchedules->where('status', 'rejected')->where('created_by', $user->id)->count() }}
                                    @elseif($user->user_role === 3)
                                        {{ $examSchedules->where('status', 'rejected')->count() }}
                                    @elseif($user->user_role === 1 || $user->user_role === 2)
                                        {{ $examSchedules->where('status', 'rejected')->where(function($query) use ($user) { $query->where('rejected_by', $user->id)->orWhere('tc_code', $user->tc_code); })->count() }}
                                    @elseif($user->user_role === 4)
                                        {{ $examSchedules->where('status', 'rejected')->count() }}
                                    @endif
                                </span>
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
                                                        <select class="form-select form-select-sm" name="exam_type">
                                                            <option value="">All Types</option>
                                                            <option value="Internal" {{ request('exam_type') === 'Internal' ? 'selected' : '' }}>Internal</option>
                                                            <option value="External" {{ request('exam_type') === 'External' ? 'selected' : '' }}>External</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <select class="form-select form-select-sm" name="status">
                                                            <option value="">All Status</option>
                                                            <option value="draft">Draft</option>
                                                            <option value="submitted">Submitted</option>
                                                            <option value="exam_cell_approved">Exam Cell Approved</option>
                                                            <option value="tc_admin_approved">TC Admin Approved</option>
                                                            <option value="received">Received</option>
                                                            <option value="hold">On Hold</option>
                                                            <option value="rejected">Rejected</option>
                                                        </select>
                                                    </div>
                                                    @if($user->user_role === 2 || $user->user_role === 3 || $user->user_role === 5)
                                                    <div class="col-md-2">
                                                        <select class="form-select form-select-sm" name="tc_code">
                                                            <option value="">All TCs</option>
                                                            @foreach($examSchedules->pluck('tc_code')->unique() as $tcCode)
                                                                <option value="{{ $tcCode }}" {{ request('tc_code') === $tcCode ? 'selected' : '' }}>{{ $tcCode }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    @endif
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
                                                @if($user->user_role !== 5)
                                                <th>TC Code</th>
                                                @endif
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
                                                @if($user->user_role !== 5)
                                                <td>{{ $schedule->tc_code }}</td>
                                                @endif
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
                                                            'draft' => 'secondary',
                                                            'submitted' => 'info',
                                                            'exam_cell_approved' => 'primary',
                                                            'tc_admin_approved' => 'warning',
                                                            'received' => 'success',
                                                            'hold' => 'secondary',
                                                            'rejected' => 'danger',
                                                            default => 'primary'
                                                        };
                                                        $statusText = match($schedule->status) {
                                                            'draft' => 'Draft',
                                                            'submitted' => 'Submitted',
                                                            'exam_cell_approved' => 'Exam Cell Approved',
                                                            'tc_admin_approved' => 'TC Admin Approved',
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
                                                            {{ $schedule->heldByUser->name }}
                                                            <br>
                                                            <small>{{ $schedule->held_at ? $schedule->held_at->format('d/m/Y H:i') : 'N/A' }}</small>
                                                        </div>
                                                    @elseif($schedule->status === 'rejected' && $schedule->rejectedByUser)
                                                        <div class="text-danger small">
                                                            <i class="bi bi-x-circle me-1"></i>
                                                            {{ $schedule->rejectedByUser->name }}
                                                            <br>
                                                            <small>{{ $schedule->rejected_at ? $schedule->rejected_at->format('d/m/Y H:i') : 'N/A' }}</small>
                                                        </div>
                                                    @elseif($schedule->status === 'received' && $schedule->approvedByUser)
                                                        <div class="text-success small">
                                                            <i class="bi bi-check-circle me-1"></i>
                                                            {{ $schedule->approvedByUser->name }}
                                                            <br>
                                                            <small>{{ $schedule->approved_at ? $schedule->approved_at->format('d/m/Y H:i') : 'N/A' }}</small>
                                                        </div>
                                                    @else
                                                        <span class="text-muted small">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        @if($user->user_role === 5)
                                                        <a href="{{ route('admin.faculty.exam-schedules.show', $schedule->id) }}" 
                                                           class="btn btn-outline-primary" title="View Details">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        @elseif($user->user_role === 3)
                                                        <a href="{{ route('admin.exam-cell.exam-schedules.show', $schedule->id) }}" 
                                                           class="btn btn-outline-primary" title="View Details">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        @elseif($user->user_role === 1)
                                                        <a href="{{ route('admin.tc-admin.exam-schedules.show', $schedule->id) }}" 
                                                           class="btn btn-outline-primary" title="View Details">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        @elseif($user->user_role === 2)
                                                        <a href="{{ route('admin.tc-head.exam-schedules.show', $schedule->id) }}" 
                                                           class="btn btn-outline-primary" title="View Details">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        @elseif($user->user_role === 4)
                                                        <a href="{{ route('admin.aa.exam-schedules.show', $schedule->id) }}" 
                                                           class="btn btn-outline-primary" title="View Details">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        @endif
                                                        @if($user->user_role === 5 && $schedule->status === 'draft')
                                                        <a href="{{ route('admin.faculty.exam-schedules.edit', $schedule->id) }}" 
                                                           class="btn btn-outline-warning" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        @endif
                                                        @if($user->user_role === 3 && $schedule->status === 'submitted')
                                                        <button type="button" class="btn btn-outline-success approve-schedule-btn"
                                                                data-schedule-id="{{ $schedule->id }}"
                                                                data-schedule-name="{{ $schedule->course_name }}"
                                                                data-role="3"
                                                                title="Approve">
                                                            <i class="bi bi-check-lg"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger reject-schedule-btn"
                                                                data-schedule-id="{{ $schedule->id }}"
                                                                data-schedule-name="{{ $schedule->course_name }}"
                                                                data-role="3"
                                                                title="Reject">
                                                            <i class="bi bi-x-lg"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-warning hold-schedule-btn"
                                                                data-schedule-id="{{ $schedule->id }}"
                                                                data-schedule-name="{{ $schedule->course_name }}"
                                                                data-role="3"
                                                                title="Hold">
                                                            <i class="bi bi-pause-circle"></i>
                                                        </button>
                                                        @endif
                                                        @if(($user->user_role === 1 || $user->user_role === 2) && $schedule->status === 'exam_cell_approved')
                                                        <button type="button" class="btn btn-outline-success approve-schedule-btn"
                                                                data-schedule-id="{{ $schedule->id }}"
                                                                data-schedule-name="{{ $schedule->course_name }}"
                                                                data-role="{{ $user->user_role }}"
                                                                data-tc-code="{{ $schedule->tc_code }}"
                                                                title="Approve">
                                                            <i class="bi bi-check-lg"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger reject-schedule-btn"
                                                                data-schedule-id="{{ $schedule->id }}"
                                                                data-schedule-name="{{ $schedule->course_name }}"
                                                                data-role="{{ $user->user_role }}"
                                                                title="Reject">
                                                            <i class="bi bi-x-lg"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-warning hold-schedule-btn"
                                                                data-schedule-id="{{ $schedule->id }}"
                                                                data-schedule-name="{{ $schedule->course_name }}"
                                                                data-role="{{ $user->user_role }}"
                                                                title="Hold">
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
                                                    <div class="col-md-2">
                                                        <select class="form-select form-select-sm" name="exam_type">
                                                            <option value="">All Types</option>
                                                            <option value="Internal" {{ request('exam_type') === 'Internal' ? 'selected' : '' }}>Internal</option>
                                                            <option value="External" {{ request('exam_type') === 'External' ? 'selected' : '' }}>External</option>
                                                        </select>
                                                    </div>
                                                    @if($user->user_role === 2 || $user->user_role === 3 || $user->user_role === 5)
                                                    <div class="col-md-2">
                                                        <select class="form-select form-select-sm" name="approved_by">
                                                            <option value="">All Approvers</option>
                                                            @if($user->user_role === 2)
                                                                <option value="exam_cell" {{ request('approved_by') === 'exam_cell' ? 'selected' : '' }}>Exam Cell</option>
                                                            @elseif($user->user_role === 3)
                                                                <option value="tc_head" {{ request('approved_by') === 'tc_head' ? 'selected' : '' }}>TC Head</option>
                                                            @elseif($user->user_role === 5)
                                                                <option value="exam_cell" {{ request('approved_by') === 'exam_cell' ? 'selected' : '' }}>Exam Cell</option>
                                                                <option value="tc_head" {{ request('approved_by') === 'tc_head' ? 'selected' : '' }}>TC Head</option>
                                                                <option value="tc_admin" {{ request('approved_by') === 'tc_admin' ? 'selected' : '' }}>TC Admin</option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                    @endif
                                                    <div class="col-md-2">
                                                        <input type="text" class="form-control form-control-sm" name="search" 
                                                               placeholder="Search..." value="{{ request('search') }}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="d-flex gap-2">
                                                            <button type="submit" class="btn btn-primary btn-sm">
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
                                        <th>Batch Code</th>
                                        <th>Semester</th>
                                        <th>Exam Type</th>
                                        <th>Exam Coordinator</th>
                                        <th>Exam Period</th>
                                        <th>Status</th>
                                        <th>Current Stage</th>
                                        <th>Created</th>
                                        <th>Action By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                            @php
                                                $pendingSchedules = $examSchedules->filter(function($schedule) use ($user) {
                                                    if($user->user_role === 5) {
                                                        // Faculty can see their own schedules (draft, submitted, hold) - NOT rejected
                                                        return in_array($schedule->status, ['draft', 'submitted', 'hold']) && $schedule->created_by === $user->id;
                                                    } elseif($user->user_role === 3) {
                                                        // Exam Cell can see submitted schedules from faculty
                                                        return $schedule->status === 'submitted';
                                                    } elseif($user->user_role === 1 || $user->user_role === 2) {
                                                        // TC Admin/Head can see schedules approved by Exam Cell
                                                        return $schedule->status === 'exam_cell_approved';
                                                    } elseif($user->user_role === 4) {
                                                        // Assessment Agency can see schedules approved by TC Admin
                                                        return $schedule->status === 'tc_admin_approved';
                                                    }
                                                    return false;
                                                });
                                            @endphp
                                                                                         @foreach($pendingSchedules as $index => $schedule)
                                             <tr>
                                                 <td class="text-center">
                                                     <span class="badge bg-primary">{{ $loop->iteration }}</span>
                                                 </td>
                                                 <td>
                                                     <div class="d-flex flex-column">
                                                         <strong class="text-primary">{{ $schedule->course_name }}</strong>
                                                         <small class="text-muted">{{ $schedule->program_number ?? 'N/A' }}</small>
                                                     </div> 
                                        </td>
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
                                            <span class="badge {{ $statusColors[$schedule->status] ?? 'bg-secondary' }}">
                                                {{ $statusLabels[$schedule->status] ?? $schedule->status }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $stageLabels = [
                                                    'faculty' => 'Faculty',
                                                    'exam_cell' => 'Exam Cell',
                                                    'tc_admin' => 'TC Admin',
                                                    'aa' => 'Assessment Agency',
                                                    'completed' => 'Completed'
                                                ];
                                            @endphp
                                            <span class="badge bg-light text-dark">
                                                {{ $stageLabels[$schedule->current_stage] ?? $schedule->current_stage }}
                                            </span>
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
                                                @if($user->user_role === 5)
                                                <a href="{{ route('admin.faculty.exam-schedules.show', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-primary" title="View Details">
                                                             <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.faculty.exam-schedules.fullview', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-secondary" target="_blank" title="Print View">
                                                             <i class="bi bi-printer"></i>
                                                </a>
                                                @elseif($user->user_role === 3)
                                                <a href="{{ route('admin.exam-cell.exam-schedules.show', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-primary" title="View Details">
                                                             <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.exam-cell.exam-schedules.fullview', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-secondary" target="_blank" title="Print View">
                                                             <i class="bi bi-printer"></i>
                                                </a>
                                                @elseif($user->user_role === 1)
                                                <a href="{{ route('admin.tc-admin.exam-schedules.show', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-primary" title="View Details">
                                                             <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.tc-admin.exam-schedules.fullview', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-secondary" target="_blank" title="Print View">
                                                             <i class="bi bi-printer"></i>
                                                </a>
                                                @elseif($user->user_role === 2)
                                                <a href="{{ route('admin.tc-head.exam-schedules.show', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-primary" title="View Details">
                                                             <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.tc-head.exam-schedules.fullview', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-secondary" target="_blank" title="Print View">
                                                             <i class="bi bi-printer"></i>
                                                </a>
                                                @elseif($user->user_role === 4)
                                                <a href="{{ route('admin.aa.exam-schedules.show', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-primary" title="View Details">
                                                             <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.aa.exam-schedules.fullview', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-secondary" target="_blank" title="Print View">
                                                             <i class="bi bi-printer"></i>
                                                </a>
                                                @endif
                                                
                                                @if($user->user_role === 5 && in_array($schedule->status, ['draft', 'hold']))
                                                <a href="{{ route('admin.faculty.exam-schedules.edit', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-warning" title="Edit">
                                                             <i class="bi bi-pencil"></i>
                                                </a>
                                                @endif
                                                
                                                @if($user->user_role === 5 && $schedule->status === 'draft')
                                                <button type="button" class="btn btn-sm btn-outline-success submit-schedule-btn"
                                                        data-schedule-id="{{ $schedule->id }}"
                                                                 data-schedule-name="{{ $schedule->course_name }}"
                                                                 title="Submit">
                                                             <i class="bi bi-send"></i>
                                                </button>
                                                @endif
                                                
                                                @if($user->user_role === 3 && $schedule->status === 'submitted')
                                                <button type="button" class="btn btn-sm btn-outline-success approve-schedule-btn"
                                                        data-schedule-id="{{ $schedule->id }}"
                                                        data-schedule-name="{{ $schedule->course_name }}"
                                                        data-role="3"
                                                                 data-tc-code="{{ $schedule->tc_code }}"
                                                                 title="Approve">
                                                             <i class="bi bi-check-circle"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-warning hold-schedule-btn"
                                                        data-schedule-id="{{ $schedule->id }}"
                                                        data-schedule-name="{{ $schedule->course_name }}"
                                                                 data-role="exam-cell"
                                                                 title="Hold">
                                                             <i class="bi bi-pause-circle"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger reject-schedule-btn"
                                                        data-schedule-id="{{ $schedule->id }}"
                                                        data-schedule-name="{{ $schedule->course_name }}"
                                                                 data-role="exam-cell"
                                                                 title="Reject">
                                                             <i class="bi bi-x-circle"></i>
                                                </button>
                                                @endif
                                                
                                                @if($user->user_role === 1 && $schedule->status === 'exam_cell_approved')
                                                <button type="button" class="btn btn-sm btn-outline-success approve-schedule-btn"
                                                        data-schedule-id="{{ $schedule->id }}"
                                                        data-schedule-name="{{ $schedule->course_name }}"
                                                        data-role="1"
                                                                 data-tc-code="{{ $schedule->tc_code }}"
                                                                 title="Approve">
                                                             <i class="bi bi-check-circle"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-warning hold-schedule-btn"
                                                        data-schedule-id="{{ $schedule->id }}"
                                                        data-schedule-name="{{ $schedule->course_name }}"
                                                                 data-role="tc-admin"
                                                                 title="Hold">
                                                             <i class="bi bi-pause-circle"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger reject-schedule-btn"
                                                        data-schedule-id="{{ $schedule->id }}"
                                                        data-schedule-name="{{ $schedule->course_name }}"
                                                                 data-role="tc-admin"
                                                                 title="Reject">
                                                             <i class="bi bi-x-circle"></i>
                                                </button>
                                                @endif
                                                
                                                @if($user->user_role === 2 && $schedule->status === 'exam_cell_approved')
                                                <button type="button" class="btn btn-sm btn-outline-success approve-schedule-btn"
                                                        data-schedule-id="{{ $schedule->id }}"
                                                        data-schedule-name="{{ $schedule->course_name }}"
                                                        data-role="2"
                                                                 data-tc-code="{{ $schedule->tc_code }}"
                                                                 title="Approve">
                                                             <i class="bi bi-check-circle"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-warning hold-schedule-btn"
                                                        data-schedule-id="{{ $schedule->id }}"
                                                        data-schedule-name="{{ $schedule->course_name }}"
                                                                 data-role="tc-head"
                                                                 title="Hold">
                                                             <i class="bi bi-pause-circle"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger reject-schedule-btn"
                                                        data-schedule-id="{{ $schedule->id }}"
                                                        data-schedule-name="{{ $schedule->course_name }}"
                                                                 data-role="tc-head"
                                                                 title="Reject">
                                                             <i class="bi bi-x-circle"></i>
                                                </button>
                                                @endif
                                                
                                                @if($user->user_role === 4 && $schedule->status === 'tc_admin_approved')
                                                <button type="button" class="btn btn-sm btn-outline-success approve-schedule-btn"
                                                        data-schedule-id="{{ $schedule->id }}"
                                                        data-schedule-name="{{ $schedule->course_name }}"
                                                        data-role="4"
                                                                 data-tc-code="{{ $schedule->tc_code }}"
                                                                 title="Approve">
                                                             <i class="bi bi-check-circle"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-warning hold-schedule-btn"
                                                        data-schedule-id="{{ $schedule->id }}"
                                                        data-schedule-name="{{ $schedule->course_name }}"
                                                                 data-role="aa"
                                                                 title="Hold">
                                                             <i class="bi bi-pause-circle"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger reject-schedule-btn"
                                                        data-schedule-id="{{ $schedule->id }}"
                                                        data-schedule-name="{{ $schedule->course_name }}"
                                                                 data-role="aa"
                                                                 title="Reject">
                                                             <i class="bi bi-x-circle"></i>
                                                </button>
                                                @endif
                                                
                                                <!-- Hold Approval Actions - Only the person who put it on hold can approve -->
                                                @if($schedule->status === 'hold' && $schedule->held_by === $user->id)
                                                <button type="button" class="btn btn-sm btn-outline-success approve-schedule-btn"
                                                        data-schedule-id="{{ $schedule->id }}"
                                                        data-schedule-name="{{ $schedule->course_name }}"
                                                        data-role="{{ $user->user_role }}"
                                                                 data-tc-code="{{ $schedule->tc_code }}"
                                                                 title="Resume">
                                                             <i class="bi bi-play-circle"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                                 @if($pendingSchedules->count() === 0)
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
                                                     <div class="col-md-2">
                                                         <select class="form-select form-select-sm" name="exam_type">
                                                             <option value="">All Types</option>
                                                             <option value="Internal" {{ request('exam_type') === 'Internal' ? 'selected' : '' }}>Internal</option>
                                                             <option value="External" {{ request('exam_type') === 'External' ? 'selected' : '' }}>External</option>
                                                         </select>
                        </div>
                                                     @if($user->user_role === 2 || $user->user_role === 3 || $user->user_role === 5)
                                                     <div class="col-md-2">
                                                         <select class="form-select form-select-sm" name="approved_by">
                                                             <option value="">All Approvers</option>
                                                             @if($user->user_role === 2)
                                                                 <option value="exam_cell" {{ request('approved_by') === 'exam_cell' ? 'selected' : '' }}>Exam Cell</option>
                                                             @elseif($user->user_role === 3)
                                                                 <option value="tc_head" {{ request('approved_by') === 'tc_head' ? 'selected' : '' }}>TC Head</option>
                                                             @elseif($user->user_role === 5)
                                                                 <option value="exam_cell" {{ request('approved_by') === 'exam_cell' ? 'selected' : '' }}>Exam Cell</option>
                                                                 <option value="tc_head" {{ request('approved_by') === 'tc_head' ? 'selected' : '' }}>TC Head</option>
                                                                 <option value="tc_admin" {{ request('approved_by') === 'tc_admin' ? 'selected' : '' }}>TC Admin</option>
                                                             @endif
                                                         </select>
                                                     </div>
                                                     @endif
                                                     <div class="col-md-2">
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
                                             @php
                                                 $approvedSchedules = $examSchedules->filter(function($schedule) use ($user) {
                                                     if($user->user_role === 5) {
                                                         // Faculty can see their own approved schedules
                                                         return in_array($schedule->status, ['exam_cell_approved', 'tc_admin_approved', 'received']) && $schedule->created_by === $user->id;
                                                     } elseif($user->user_role === 3) {
                                                         // Exam Cell can see schedules they approved (including those that moved to next stage)
                                                         return in_array($schedule->status, ['exam_cell_approved', 'tc_admin_approved', 'received']);
                                                     } elseif($user->user_role === 1 || $user->user_role === 2) {
                                                         // TC Admin/Head can see schedules they approved
                                                         return $schedule->status === 'tc_admin_approved';
                                                     } elseif($user->user_role === 4) {
                                                         // Assessment Agency can see schedules they approved
                                                         return $schedule->status === 'received';
                                                     }
                                                     return false;
                                                 });
                                             @endphp
                                             @foreach($approvedSchedules as $index => $schedule)
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
                                                     @if($schedule->approvedByUser)
                                                         <div class="text-success small">
                                                             <i class="bi bi-check-circle me-1"></i>
                                                             {{ $schedule->approvedByUser->name }}
                                                             <br>
                                                             <small>{{ $schedule->approved_at ? $schedule->approved_at->format('d/m/Y H:i') : 'N/A' }}</small>
                                                         </div>
                                                     @else
                                                         <span class="text-muted small">-</span>
                                                     @endif
                                                 </td>
                                                 <td>
                                                     <div class="btn-group" role="group">
                                                         @if($user->user_role === 5)
                                                         <a href="{{ route('admin.faculty.exam-schedules.show', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-primary" title="View Details">
                                                             <i class="bi bi-eye"></i>
                                                         </a>
                                                         <a href="{{ route('admin.faculty.exam-schedules.fullview', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-secondary" target="_blank" title="Print View">
                                                             <i class="bi bi-printer"></i>
                                                         </a>
                                                         @elseif($user->user_role === 3)
                                                         <a href="{{ route('admin.exam-cell.exam-schedules.show', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-primary" title="View Details">
                                                             <i class="bi bi-eye"></i>
                                                         </a>
                                                         <a href="{{ route('admin.exam-cell.exam-schedules.fullview', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-secondary" target="_blank" title="Print View">
                                                             <i class="bi bi-printer"></i>
                                                         </a>
                                                         @elseif($user->user_role === 1)
                                                         <a href="{{ route('admin.tc-admin.exam-schedules.show', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-primary" title="View Details">
                                                             <i class="bi bi-eye"></i>
                                                         </a>
                                                         <a href="{{ route('admin.tc-admin.exam-schedules.fullview', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-secondary" target="_blank" title="Print View">
                                                             <i class="bi bi-printer"></i>
                                                         </a>
                                                         @elseif($user->user_role === 2)
                                                         <a href="{{ route('admin.tc-head.exam-schedules.show', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-primary" title="View Details">
                                                             <i class="bi bi-eye"></i>
                                                         </a>
                                                         <a href="{{ route('admin.tc-head.exam-schedules.fullview', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-secondary" target="_blank" title="Print View">
                                                             <i class="bi bi-printer"></i>
                                                         </a>
                                                         @elseif($user->user_role === 4)
                                                         <a href="{{ route('admin.aa.exam-schedules.show', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-primary" title="View Details">
                                                             <i class="bi bi-eye"></i>
                                                         </a>
                                                         <a href="{{ route('admin.aa.exam-schedules.fullview', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-secondary" target="_blank" title="Print View">
                                                             <i class="bi bi-printer"></i>
                                                         </a>
                                                         @endif
                                                     </div>
                                                 </td>
                                             </tr>
                                             @endforeach
                                         </tbody>
                                     </table>
                                 </div>
                                 
                                 @if($approvedSchedules->count() === 0)
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
                                                     <div class="col-md-2">
                                                         <select class="form-select form-select-sm" name="exam_type">
                                                             <option value="">All Types</option>
                                                             <option value="Internal" {{ request('exam_type') === 'Internal' ? 'selected' : '' }}>Internal</option>
                                                             <option value="External" {{ request('exam_type') === 'External' ? 'selected' : '' }}>External</option>
                                                         </select>
                                                     </div>
                                                     @if($user->user_role === 2 || $user->user_role === 3 || $user->user_role === 5)
                                                     <div class="col-md-2">
                                                         <select class="form-select form-select-sm" name="approved_by">
                                                             <option value="">All Approvers</option>
                                                             @if($user->user_role === 2)
                                                                 <option value="exam_cell" {{ request('approved_by') === 'exam_cell' ? 'selected' : '' }}>Exam Cell</option>
                                                             @elseif($user->user_role === 3)
                                                                 <option value="tc_head" {{ request('approved_by') === 'tc_head' ? 'selected' : '' }}>TC Head</option>
                                                             @elseif($user->user_role === 5)
                                                                 <option value="exam_cell" {{ request('approved_by') === 'exam_cell' ? 'selected' : '' }}>Exam Cell</option>
                                                                 <option value="tc_head" {{ request('approved_by') === 'tc_head' ? 'selected' : '' }}>TC Head</option>
                                                                 <option value="tc_admin" {{ request('approved_by') === 'tc_admin' ? 'selected' : '' }}>TC Admin</option>
                                                             @endif
                                                         </select>
                                                     </div>
                                                     @endif
                                                     <div class="col-md-2">
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
                                                 <th>Batch Code</th>
                                                 <th>Semester</th>
                                                 <th>Exam Type</th>
                                                 <th>Hold Date</th>
                                                 <th>Action By</th>
                                                 <th>Actions</th>
                                             </tr>
                                         </thead>
                                         <tbody>
                                             @php
                                                 $holdSchedules = $examSchedules->filter(function($schedule) use ($user) {
                                                     if($user->user_role === 5) {
                                                         // Faculty can see their own hold schedules
                                                         return $schedule->status === 'hold' && $schedule->created_by === $user->id;
                                                     } elseif($user->user_role === 3) {
                                                         // Exam Cell can see all hold schedules (for oversight)
                                                         return $schedule->status === 'hold';
                                                     } elseif($user->user_role === 1 || $user->user_role === 2) {
                                                         // TC Admin/Head can see hold schedules they put on hold
                                                         return $schedule->status === 'hold' && $schedule->held_by === $user->id;
                                                     } elseif($user->user_role === 4) {
                                                         // Assessment Agency can see hold schedules they put on hold
                                                         return $schedule->status === 'hold' && $schedule->held_by === $user->id;
                                                     }
                                                     return false;
                                                 });
                                             @endphp
                                             @foreach($holdSchedules as $index => $schedule)
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
                                @if($user->user_role === 5)
                                                         <a href="{{ route('admin.faculty.exam-schedules.show', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-primary" title="View Details">
                                                             <i class="bi bi-eye"></i>
                                                         </a>
                                                         <a href="{{ route('admin.faculty.exam-schedules.fullview', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-secondary" target="_blank" title="Print View">
                                                             <i class="bi bi-printer"></i>
                                                         </a>
                                                         @elseif($user->user_role === 3)
                                                         <a href="{{ route('admin.exam-cell.exam-schedules.show', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-primary" title="View Details">
                                                             <i class="bi bi-eye"></i>
                                                         </a>
                                                         <a href="{{ route('admin.exam-cell.exam-schedules.fullview', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-secondary" target="_blank" title="Print View">
                                                             <i class="bi bi-printer"></i>
                                                         </a>
                                                         @elseif($user->user_role === 1)
                                                         <a href="{{ route('admin.tc-admin.exam-schedules.show', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-primary" title="View Details">
                                                             <i class="bi bi-eye"></i>
                                                         </a>
                                                         <a href="{{ route('admin.tc-admin.exam-schedules.fullview', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-secondary" target="_blank" title="Print View">
                                                             <i class="bi bi-printer"></i>
                                                         </a>
                                                         @elseif($user->user_role === 2)
                                                         <a href="{{ route('admin.tc-head.exam-schedules.show', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-primary" title="View Details">
                                                             <i class="bi bi-eye"></i>
                                                         </a>
                                                         <a href="{{ route('admin.tc-head.exam-schedules.fullview', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-secondary" target="_blank" title="Print View">
                                                             <i class="bi bi-printer"></i>
                                                         </a>
                                                         @elseif($user->user_role === 4)
                                                         <a href="{{ route('admin.aa.exam-schedules.show', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-primary" title="View Details">
                                                             <i class="bi bi-eye"></i>
                                                         </a>
                                                         <a href="{{ route('admin.aa.exam-schedules.fullview', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-secondary" target="_blank" title="Print View">
                                                             <i class="bi bi-printer"></i>
                                                         </a>
                                @endif
                                                         
                                                         @if($schedule->held_by === $user->id)
                                                         <button type="button" class="btn btn-sm btn-outline-success approve-schedule-btn"
                                                                 data-schedule-id="{{ $schedule->id }}"
                                                                 data-schedule-name="{{ $schedule->course_name }}"
                                                                 data-role="{{ $user->user_role }}"
                                                                 data-tc-code="{{ $schedule->tc_code }}"
                                                                 title="Resume">
                                                             <i class="bi bi-play-circle"></i>
                                                         </button>
                                                         @endif
                                                     </div>
                                                 </td>
                                             </tr>
                                             @endforeach
                                         </tbody>
                                     </table>
                                 </div>
                                 
                                 @if($holdSchedules->count() === 0)
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
                                                     <div class="col-md-2">
                                                         <select class="form-select form-select-sm" name="exam_type">
                                                             <option value="">All Types</option>
                                                             <option value="Internal" {{ request('exam_type') === 'Internal' ? 'selected' : '' }}>Internal</option>
                                                             <option value="External" {{ request('exam_type') === 'External' ? 'selected' : '' }}>External</option>
                                                         </select>
                                                     </div>
                                                     @if($user->user_role === 2 || $user->user_role === 3 || $user->user_role === 5)
                                                     <div class="col-md-2">
                                                         <select class="form-select form-select-sm" name="approved_by">
                                                             <option value="">All Approvers</option>
                                                             @if($user->user_role === 2)
                                                                 <option value="exam_cell" {{ request('approved_by') === 'exam_cell' ? 'selected' : '' }}>Exam Cell</option>
                                                             @elseif($user->user_role === 3)
                                                                 <option value="tc_head" {{ request('approved_by') === 'tc_head' ? 'selected' : '' }}>TC Head</option>
                                                             @elseif($user->user_role === 5)
                                                                 <option value="exam_cell" {{ request('approved_by') === 'exam_cell' ? 'selected' : '' }}>Exam Cell</option>
                                                                 <option value="tc_head" {{ request('approved_by') === 'tc_head' ? 'selected' : '' }}>TC Head</option>
                                                                 <option value="tc_admin" {{ request('approved_by') === 'tc_admin' ? 'selected' : '' }}>TC Admin</option>
                                                             @endif
                                                         </select>
                                                     </div>
                                                     @endif
                                                     <div class="col-md-2">
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
                                                 <th>Batch Code</th>
                                                 <th>Semester</th>
                                                 <th>Exam Type</th>
                                                 <th>Rejection Date</th>
                                                 <th>Action By</th>
                                                 <th>Actions</th>
                                             </tr>
                                         </thead>
                                         <tbody>
                                             @php
                                                 $rejectedSchedules = $examSchedules->filter(function($schedule) use ($user) {
                                                     if($user->user_role === 5) {
                                                         // Faculty can see their own rejected schedules
                                                         return $schedule->status === 'rejected' && $schedule->created_by === $user->id;
                                                     } elseif($user->user_role === 3) {
                                                         // Exam Cell can see all rejected schedules (for oversight)
                                                         return $schedule->status === 'rejected';
                                                     } elseif($user->user_role === 1 || $user->user_role === 2) {
                                                         // TC Admin/Head can see rejected schedules they rejected OR from their TC
                                                         return $schedule->status === 'rejected' && ($schedule->rejected_by === $user->id || $schedule->tc_code === $user->tc_code);
                                                     } elseif($user->user_role === 4) {
                                                         // Assessment Agency can see all rejected schedules
                                                         return $schedule->status === 'rejected';
                                                     }
                                                     return false;
                                                 });
                                                 
                                                 // Debug information for Exam Cell
                                                 if($user->user_role === 3) {
                                                     $allRejected = $examSchedules->where('status', 'rejected');
                                                     $rejectedByExamCell = $examSchedules->where('status', 'rejected')->where('rejected_by', 3);
                                                     echo "<!-- DEBUG: Total rejected schedules: " . $allRejected->count() . " -->";
                                                     echo "<!-- DEBUG: Rejected by Exam Cell: " . $rejectedByExamCell->count() . " -->";
                                                     foreach($allRejected as $rejected) {
                                                         echo "<!-- DEBUG: Schedule ID " . $rejected->id . " - rejected_by: " . $rejected->rejected_by . " -->";
                                                     }
                                                 }
                                             @endphp
                                             @foreach($rejectedSchedules as $index => $schedule)
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
                                                     @if($schedule->rejectedByUser)
                                                         <div class="text-danger small">
                                                             <i class="bi bi-x-circle me-1"></i>
                                                             {{ $schedule->rejectedByUser->name }}
                                                             <br>
                                                             <small>{{ $schedule->rejected_at ? $schedule->rejected_at->format('d/m/Y H:i') : 'N/A' }}</small>
                                                         </div>
                                                     @else
                                                         <span class="text-muted small">-</span>
                                                     @endif
                                                 </td>
                                                 <td>
                                                     <div class="btn-group" role="group">
                            @if($user->user_role === 5)
                                                         <a href="{{ route('admin.faculty.exam-schedules.show', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-primary" title="View Details">
                                                             <i class="bi bi-eye"></i>
                                                         </a>
                                                         <a href="{{ route('admin.faculty.exam-schedules.fullview', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-secondary" target="_blank" title="Print View">
                                                             <i class="bi bi-printer"></i>
                                                         </a>
                                                         @elseif($user->user_role === 3)
                                                         <a href="{{ route('admin.exam-cell.exam-schedules.show', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-primary" title="View Details">
                                                             <i class="bi bi-eye"></i>
                                                         </a>
                                                         <a href="{{ route('admin.exam-cell.exam-schedules.fullview', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-secondary" target="_blank" title="Print View">
                                                             <i class="bi bi-printer"></i>
                                                         </a>
                                                         @elseif($user->user_role === 1)
                                                         <a href="{{ route('admin.tc-admin.exam-schedules.show', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-primary" title="View Details">
                                                             <i class="bi bi-eye"></i>
                                                         </a>
                                                         <a href="{{ route('admin.tc-admin.exam-schedules.fullview', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-secondary" target="_blank" title="Print View">
                                                             <i class="bi bi-printer"></i>
                                                         </a>
                                                         @elseif($user->user_role === 2)
                                                         <a href="{{ route('admin.tc-head.exam-schedules.show', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-primary" title="View Details">
                                                             <i class="bi bi-eye"></i>
                                                         </a>
                                                         <a href="{{ route('admin.tc-head.exam-schedules.fullview', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-secondary" target="_blank" title="Print View">
                                                             <i class="bi bi-printer"></i>
                                                         </a>
                                                         @elseif($user->user_role === 4)
                                                         <a href="{{ route('admin.aa.exam-schedules.show', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-primary" title="View Details">
                                                             <i class="bi bi-eye"></i>
                                                         </a>
                                                         <a href="{{ route('admin.aa.exam-schedules.fullview', $schedule->id) }}" 
                                                            class="btn btn-sm btn-outline-secondary" target="_blank" title="Print View">
                                                             <i class="bi bi-printer"></i>
                            </a>
                            @endif
                                                     </div>
                                                 </td>
                                             </tr>
                                             @endforeach
                                         </tbody>
                                     </table>
                                 </div>
                                 
                                 @if($rejectedSchedules->count() === 0)
                                 <div class="text-center py-5">
                                     <i class="bi bi-x-circle display-1 text-muted"></i>
                                     <h4 class="mt-3 text-muted">No Rejected Schedules</h4>
                                     <p class="text-muted">No exam schedules have been rejected.</p>
                                     
                                     @if($user->user_role === 3)
                                     <!-- Debug section for Exam Cell -->
                                     <div class="mt-4 p-3 bg-light rounded">
                                         <h6 class="text-muted">Debug Information:</h6>
                                         @php
                                             $allRejected = $examSchedules->where('status', 'rejected');
                                             $rejectedByExamCell = $examSchedules->where('status', 'rejected')->where('rejected_by', 3);
                                         @endphp
                                         <p class="mb-1"><strong>Total rejected schedules:</strong> {{ $allRejected->count() }}</p>
                                         <p class="mb-1"><strong>Rejected by Exam Cell:</strong> {{ $rejectedByExamCell->count() }}</p>
                                         @if($allRejected->count() > 0)
                                         <p class="mb-2"><strong>Rejected schedules details:</strong></p>
                                         <ul class="text-left">
                                             @foreach($allRejected as $rejected)
                                             <li>ID: {{ $rejected->id }} - Course: {{ $rejected->course_name }} - rejected_by: {{ $rejected->rejected_by ?? 'NULL' }}</li>
                                             @endforeach
                                         </ul>
                                         @endif
                                     </div>
                                     @endif
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

<!-- Regular Approval Modal (for non-Assessment Agency) -->
<div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approvalModalLabel">Approve Exam Schedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="approvalForm">
                <div class="modal-body">
                    <p>Are you sure you want to approve the exam schedule "<strong id="approvalScheduleName"></strong>"?</p>
                    
                    <div class="mb-3">
                        <label for="approvalComment" class="form-label">Comment (Optional)</label>
                        <textarea class="form-control" id="approvalComment" name="comment" rows="3" 
                                  placeholder="Add any comments or notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <span id="approvalText">Approve</span>
                        <span id="approvalLoader" class="spinner-border spinner-border-sm d-none ms-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assessment Agency Approval Modal (Separate) -->
<div class="modal fade" id="aaApprovalModal" tabindex="-1" aria-labelledby="aaApprovalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="aaApprovalModalLabel">
                    <i class="bi bi-shield-check me-2"></i>
                    Assessment Agency Approval
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="aaApprovalForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="text-primary mb-3">
                                <i class="bi bi-file-earmark-text me-2"></i>
                                Exam Schedule Details
                            </h6>
                            <p>Are you sure you want to approve the exam schedule "<strong id="aaApprovalScheduleName"></strong>"?</p>
                            
                            <div class="mb-3">
                                <label for="aaApprovalComment" class="form-label">Comment (Optional)</label>
                                <textarea class="form-control" id="aaApprovalComment" name="comment" rows="3" 
                                          placeholder="Add any comments or notes..."></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">
                                        <i class="bi bi-file-earmark-text me-2"></i>
                                        File Number Assignment
                                    </h6>
                                </div>
                                <div class="card-body text-center">
                                    <p class="text-muted mb-2">Upon approval, this file number will be assigned:</p>
                                    <div class="bg-light p-3 rounded">
                                        <span id="aaGeneratedFileNumber" class="badge bg-success text-white font-monospace fs-6 p-2">
                                            <span class="spinner-border spinner-border-sm me-2" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </span>
                                            Loading...
                                        </span>
                                    </div>
                                    <small class="text-muted mt-2 d-block">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Format: FN[FY][TC][Date][Serial]
                                    </small>
                                    <small class="text-success mt-1 d-block">
                                        <i class="bi bi-check-circle me-1"></i>
                                        This is the actual file number that will be assigned.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-shield-check me-2"></i>
                        <span id="aaApprovalText">Approve & Assign File Number</span>
                        <span id="aaApprovalLoader" class="spinner-border spinner-border-sm d-none ms-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Reject Exam Schedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm">
                <div class="modal-body">
                    <p>Are you sure you want to reject the exam schedule "<strong id="rejectScheduleName"></strong>"?</p>
                    <div class="mb-3">
                        <label for="rejectComment" class="form-label">Reason for Rejection *</label>
                        <textarea class="form-control" id="rejectComment" name="comment" rows="3" 
                                  placeholder="Please provide a reason for rejection..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <span id="rejectText">Reject</span>
                        <span id="rejectLoader" class="spinner-border spinner-border-sm d-none ms-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hold Modal -->
<div class="modal fade" id="holdModal" tabindex="-1" aria-labelledby="holdModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="holdModalLabel">Hold Exam Schedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="holdForm">
                <div class="modal-body">
                    <p>Are you sure you want to put the exam schedule "<strong id="holdScheduleName"></strong>" on hold for reschedule?</p>
                    <div class="mb-3">
                        <label for="holdComment" class="form-label">Reason for Hold *</label>
                        <textarea class="form-control" id="holdComment" name="comment" rows="3" 
                                  placeholder="Please provide a reason for putting on hold..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <span id="holdText">Hold</span>
                        <span id="holdLoader" class="spinner-border spinner-border-sm d-none ms-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Submit Modal -->
<div class="modal fade" id="submitModal" tabindex="-1" aria-labelledby="submitModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="submitModalLabel">Submit Exam Schedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to submit the exam schedule "<strong id="submitScheduleName"></strong>" for approval?</p>
                <p class="text-muted"><small>Once submitted, you cannot edit the schedule unless requested by the Exam Cell.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmSubmit">
                    <span id="submitText">Submit</span>
                    <span id="submitLoader" class="spinner-border spinner-border-sm d-none ms-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Enhanced styling for dashboard */
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

/* Ensure modals have proper z-index and positioning */
.modal {
    z-index: 1050 !important;
}

.modal-backdrop {
    z-index: 1040 !important;
}

/* Assessment Agency Modal specific styling */
#aaApprovalModal {
    z-index: 1060 !important;
}

#aaApprovalModal .modal-backdrop {
    z-index: 1055 !important;
}

#aaApprovalModal .modal-dialog {
    max-width: 800px;
}

#aaGeneratedFileNumber {
    font-family: 'Courier New', monospace;
    letter-spacing: 1px;
    font-size: 1.1rem !important;
}

/* Ensure file number section is properly styled */
#fileNumberSection .alert {
    border-left: 4px solid #17a2b8;
}

#generatedFileNumber {
    font-family: 'Courier New', monospace;
    letter-spacing: 1px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Exam Schedules page JavaScript loaded');
    
    // Initialize modals once at the beginning
    let approvalModal, rejectModal, holdModal, submitModal, aaApprovalModal;
    
    // Initialize Bootstrap modals
    try {
        approvalModal = new bootstrap.Modal(document.getElementById('approvalModal'));
        rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));
        holdModal = new bootstrap.Modal(document.getElementById('holdModal'));
        submitModal = new bootstrap.Modal(document.getElementById('submitModal'));
        aaApprovalModal = new bootstrap.Modal(document.getElementById('aaApprovalModal'));
        
        console.log('All modals initialized successfully');
    } catch (error) {
        console.error('Error initializing modals:', error);
    }
    
    // CSRF token setup
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Filter functionality for each tab
    const filterForms = ['pendingFilterForm', 'approvedFilterForm', 'holdFilterForm', 'rejectedFilterForm'];
    
    filterForms.forEach(formId => {
        const form = document.getElementById(formId);
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                applyFilters(this);
            });
        }
    });
    
    function clearFilters(tabName) {
        const form = document.getElementById(tabName + 'FilterForm');
        if (form) {
            form.reset();
            // Clear all form fields
            const inputs = form.querySelectorAll('input, select');
            inputs.forEach(input => {
                if (input.type === 'text') {
                    input.value = '';
                } else if (input.tagName === 'SELECT') {
                    input.selectedIndex = 0;
                }
            });
            // Redirect to base URL without any filters
            window.location.href = window.location.pathname;
        }
    }
    
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
                @if($user->user_role === 5)
                    params.append('status', 'draft,hold');
                @elseif($user->user_role === 3)
                    params.append('status', 'submitted');
                @elseif($user->user_role === 1 || $user->user_role === 2)
                    params.append('status', 'exam_cell_approved');
                @elseif($user->user_role === 4)
                    params.append('status', 'tc_admin_approved');
                @endif
                break;
            case 'approved':
                @if($user->user_role === 5)
                    params.append('status', 'exam_cell_approved,tc_admin_approved,received');
                @elseif($user->user_role === 3)
                    params.append('status', 'exam_cell_approved');
                @elseif($user->user_role === 1 || $user->user_role === 2)
                    params.append('status', 'tc_admin_approved');
                @elseif($user->user_role === 4)
                    params.append('status', 'received');
                @endif
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
        
        // Determine export route based on user role
        let exportRoute = '';
        @if($user->user_role === 5)
            exportRoute = '/admin/faculty/exam-schedules/export';
        @elseif($user->user_role === 3)
            exportRoute = '/admin/exam-cell/exam-schedules/export';
        @elseif($user->user_role === 1)
            exportRoute = '/admin/tc-admin/exam-schedules/export';
        @elseif($user->user_role === 2)
            exportRoute = '/admin/tc-head/exam-schedules/export';
        @elseif($user->user_role === 4)
            exportRoute = '/admin/aa/exam-schedules/export';
        @endif
        
        window.open(`${exportRoute}?${params.toString()}`, '_blank');
    });
    
    // Refresh functionality
    document.getElementById('refreshBtn').addEventListener('click', function() {
        window.location.reload();
    });
    
    // Global function for clearing filters
    window.clearFilters = clearFilters;
    
    // Submit Schedule
    document.querySelectorAll('.submit-schedule-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const scheduleId = this.getAttribute('data-schedule-id');
            const scheduleName = this.getAttribute('data-schedule-name');
            
            document.getElementById('submitScheduleName').textContent = scheduleName;
            document.getElementById('confirmSubmit').setAttribute('data-schedule-id', scheduleId);
            
            if (submitModal) {
                submitModal.show();
            }
        });
    });
    
    // Confirm Submit
    document.getElementById('confirmSubmit').addEventListener('click', function() {
        const scheduleId = this.getAttribute('data-schedule-id');
        const submitText = document.getElementById('submitText');
        const submitLoader = document.getElementById('submitLoader');
        
        // Show loading state
        submitText.textContent = 'Submitting...';
        submitLoader.classList.remove('d-none');
        
        fetch(`/admin/faculty/exam-schedules/${scheduleId}/submit`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Refresh page
                window.location.reload();
            } else {
                alert(data.message || 'Failed to submit exam schedule');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while submitting the exam schedule');
        })
        .finally(() => {
            // Reset loading state
            submitText.textContent = 'Submit';
            submitLoader.classList.add('d-none');
        });
    });
    
    // Approval Modal
    document.querySelectorAll('.approve-schedule-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            console.log('Approval button clicked');
            const scheduleId = this.getAttribute('data-schedule-id');
            const scheduleName = this.getAttribute('data-schedule-name');
            const role = this.getAttribute('data-role'); // Get the role from data-role
            const tcCode = this.getAttribute('data-tc-code'); // Get TC code for file number generation
            
            console.log('Schedule ID:', scheduleId, 'Role:', role, 'TC Code:', tcCode);
            
            if (role === '4') { // Assessment Agency - Use separate modal
                // Set up Assessment Agency modal
                document.getElementById('aaApprovalScheduleName').textContent = scheduleName;
                document.getElementById('aaApprovalForm').setAttribute('data-schedule-id', scheduleId);
                document.getElementById('aaApprovalForm').setAttribute('data-action', 'approve');
                document.getElementById('aaApprovalForm').setAttribute('data-role', role);
                document.getElementById('aaApprovalForm').setAttribute('data-tc-code', tcCode);
                
                // Fetch actual file number preview from server
                fetch(`/api/file-number-preview/${scheduleId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('aaGeneratedFileNumber').innerHTML = data.file_number;
                            // Store the approval date for use in the approval process
                            document.getElementById('aaApprovalForm').setAttribute('data-approval-date', data.approval_date);
                            document.getElementById('aaApprovalForm').setAttribute('data-file-number', data.file_number);
                            console.log('File number preview loaded:', data.file_number);
                            console.log('Approval date:', data.approval_date);
                        } else {
                            console.error('Failed to load file number preview:', data.error);
                            document.getElementById('aaGeneratedFileNumber').innerHTML = '<span class="text-danger">Error loading preview</span>';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching file number preview:', error);
                        document.getElementById('aaGeneratedFileNumber').innerHTML = '<span class="text-danger">Error loading preview</span>';
                    });
                
                if (aaApprovalModal) {
                    console.log('Showing Assessment Agency approval modal');
                    aaApprovalModal.show();
                } else {
                    console.error('Assessment Agency approval modal not initialized');
                }
            } else { // Other roles - Use regular modal
                // Set up regular approval modal
            document.getElementById('approvalScheduleName').textContent = scheduleName;
            document.getElementById('approvalForm').setAttribute('data-schedule-id', scheduleId);
            document.getElementById('approvalForm').setAttribute('data-action', 'approve');
                document.getElementById('approvalForm').setAttribute('data-role', role);
                document.getElementById('approvalForm').setAttribute('data-tc-code', tcCode);
                
                if (approvalModal) {
                    console.log('Showing regular approval modal');
                    approvalModal.show();
                } else {
                    console.error('Regular approval modal not initialized');
                }
            }
        });
    });
    

    
    // Reject Modal
    document.querySelectorAll('.reject-schedule-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const scheduleId = this.getAttribute('data-schedule-id');
            const scheduleName = this.getAttribute('data-schedule-name');
            const role = this.getAttribute('data-role'); // Get the role from data-role
            
            document.getElementById('rejectScheduleName').textContent = scheduleName;
            document.getElementById('rejectForm').setAttribute('data-schedule-id', scheduleId);
            document.getElementById('rejectForm').setAttribute('data-role', role); // Set role for reject
            
            if (rejectModal) {
                rejectModal.show();
            }
        });
    });
    
    // Hold Modal
    document.querySelectorAll('.hold-schedule-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const scheduleId = this.getAttribute('data-schedule-id');
            const scheduleName = this.getAttribute('data-schedule-name');
            const role = this.getAttribute('data-role'); // Get the role from data-role
            
            document.getElementById('holdScheduleName').textContent = scheduleName;
            document.getElementById('holdForm').setAttribute('data-schedule-id', scheduleId);
            document.getElementById('holdForm').setAttribute('data-role', role); // Set role for hold
            
            if (holdModal) {
                holdModal.show();
            }
        });
    });
    
    // Handle regular approval form submissions
    document.getElementById('approvalForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const scheduleId = this.getAttribute('data-schedule-id');
        const action = this.getAttribute('data-action');
        const formData = new FormData(this);
        const approvalText = document.getElementById('approvalText');
        const approvalLoader = document.getElementById('approvalLoader');
        
        // Determine the correct route based on user role
        let routePrefix = '';
        @if($user->user_role === 3)
            routePrefix = '/admin/exam-cell/exam-schedules';
        @elseif($user->user_role === 1)
            routePrefix = '/admin/tc-admin/exam-schedules';
        @elseif($user->user_role === 2)
            routePrefix = '/admin/tc-head/exam-schedules';
        @elseif($user->user_role === 4)
            routePrefix = '/admin/aa/exam-schedules';
        @endif
        
        // Show loading state
        approvalText.textContent = 'Approving...';
        approvalLoader.classList.remove('d-none');
        
        fetch(`${routePrefix}/${scheduleId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Refresh page
                window.location.reload();
            } else {
                alert(data.message || 'Failed to approve exam schedule');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while approving the exam schedule');
        })
        .finally(() => {
            // Reset loading state
            approvalText.textContent = 'Approve';
            approvalLoader.classList.add('d-none');
        });
    });
    
    document.getElementById('rejectForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const scheduleId = this.getAttribute('data-schedule-id');
        const formData = new FormData(this);
        const rejectText = document.getElementById('rejectText');
        const rejectLoader = document.getElementById('rejectLoader');
        
        // Determine the correct route based on user role
        let routePrefix = '';
        @if($user->user_role === 3)
            routePrefix = '/admin/exam-cell/exam-schedules';
        @elseif($user->user_role === 1)
            routePrefix = '/admin/tc-admin/exam-schedules';
        @elseif($user->user_role === 2)
            routePrefix = '/admin/tc-head/exam-schedules';
        @elseif($user->user_role === 4)
            routePrefix = '/admin/aa/exam-schedules';
        @endif
        
        // Show loading state
        rejectText.textContent = 'Rejecting...';
        rejectLoader.classList.remove('d-none');
        
        fetch(`${routePrefix}/${scheduleId}/reject`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Refresh page
                window.location.reload();
            } else {
                alert(data.message || 'Failed to reject exam schedule');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while rejecting the exam schedule');
        })
        .finally(() => {
            // Reset loading state
            rejectText.textContent = 'Reject';
            rejectLoader.classList.add('d-none');
        });
    });
    
    document.getElementById('holdForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const scheduleId = this.getAttribute('data-schedule-id');
        const formData = new FormData(this);
        const holdText = document.getElementById('holdText');
        const holdLoader = document.getElementById('holdLoader');
        
        // Determine the correct route based on user role
        let routePrefix = '';
        @if($user->user_role === 3)
            routePrefix = '/admin/exam-cell/exam-schedules';
        @elseif($user->user_role === 1)
            routePrefix = '/admin/tc-admin/exam-schedules';
        @elseif($user->user_role === 2)
            routePrefix = '/admin/tc-head/exam-schedules';
        @elseif($user->user_role === 4)
            routePrefix = '/admin/aa/exam-schedules';
        @endif
        
        // Show loading state
        holdText.textContent = 'Holding...';
        holdLoader.classList.remove('d-none');
        
        fetch(`${routePrefix}/${scheduleId}/hold`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Refresh page
                window.location.reload();
            } else {
                alert(data.message || 'Failed to hold exam schedule');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while holding the exam schedule');
        })
        .finally(() => {
            // Reset loading state
            holdText.textContent = 'Hold';
            holdLoader.classList.add('d-none');
        });
    });
    
    // Handle Assessment Agency approval form submissions
    document.getElementById('aaApprovalForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const scheduleId = this.getAttribute('data-schedule-id');
        const action = this.getAttribute('data-action');
        const fileNumber = this.getAttribute('data-file-number');
        const approvalDate = this.getAttribute('data-approval-date');
        const formData = new FormData(this);
        const aaApprovalText = document.getElementById('aaApprovalText');
        const aaApprovalLoader = document.getElementById('aaApprovalLoader');
        
        // Add the file number and approval date to the form data
        formData.append('file_number', fileNumber);
        formData.append('approval_date', approvalDate);
        
        // Assessment Agency route
        const routePrefix = '/admin/aa/exam-schedules';
        
        // Show loading state
        aaApprovalText.textContent = 'Approving...';
        aaApprovalLoader.classList.remove('d-none');
        
        fetch(`${routePrefix}/${scheduleId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Refresh page
                window.location.reload();
            } else {
                alert(data.message || 'Failed to approve exam schedule');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while approving the exam schedule');
        })
        .finally(() => {
            // Reset loading state
            aaApprovalText.textContent = 'Approve & Assign File Number';
            aaApprovalLoader.classList.add('d-none');
        });
    });
    
    console.log('Exam Schedules page JavaScript initialization complete');
});
</script>
@endpush
@endsection