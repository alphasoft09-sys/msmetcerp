@extends('admin.layout')

@section('title', 'LMS Sites Management')

@section('content')
<style>
.table-responsive {
    border-radius: 0.375rem;
}

.table th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    white-space: nowrap;
    position: sticky;
    top: 0;
    z-index: 10;
}

.table td {
    border-bottom: 1px solid #dee2e6;
    white-space: nowrap;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.badge {
    font-size: 0.75em;
    padding: 0.35em 0.65em;
}

.btn-group .btn {
    margin-right: 0.25rem;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
}

/* Custom scrollbar for webkit browsers */
.table-responsive::-webkit-scrollbar {
    height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1 text-primary">
                        <i class="bi bi-globe me-2"></i>
                        LMS Sites Management
                    </h1>
                    <p class="text-muted mb-0">
                        Create and manage your Learning Management System sites
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.tc-lms.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle me-2"></i>
                        Create New Site
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.tc-lms.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Search sites...">
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="department" class="form-label">Department</label>
                            <select class="form-select" id="department" name="department">
                                <option value="">All Departments</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->department_name }}" 
                                            {{ request('department') == $department->department_name ? 'selected' : '' }}>
                                        {{ $department->department_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search me-1"></i>Filter
                                </button>
                                <a href="{{ route('admin.tc-lms.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- LMS Sites Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Your LMS Sites</h5>
                </div>
                <div class="card-body p-0">
                    @if($lmsSites->count() > 0)
                        <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                            <table class="table table-hover mb-0" style="min-width: 1000px;">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th style="min-width: 300px; width: 35%;">Site Title</th>
                                        <th style="min-width: 120px; width: 15%;">Department</th>
                                        <th style="min-width: 100px; width: 12%;">Status</th>
                                        <th style="min-width: 150px; width: 18%;">URL</th>
                                        <th style="min-width: 100px; width: 10%;">Created</th>
                                        <th style="min-width: 200px; width: 20%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lmsSites as $site)
                                        <tr>
                                            <td style="vertical-align: middle;">
                                                <div class="d-flex flex-column">
                                                    <strong class="text-primary mb-1">{{ $site->site_title }}</strong>
                                                    @if($site->site_description)
                                                        <small class="text-muted">{{ Str::limit($site->site_description, 80) }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td style="vertical-align: middle;">
                                                <span class="badge bg-info text-wrap">{{ $site->site_department }}</span>
                                            </td>
                                            <td style="vertical-align: middle;">
                                                @switch($site->status)
                                                    @case('draft')
                                                        <span class="badge bg-secondary">Draft</span>
                                                        @break
                                                    @case('submitted')
                                                        <span class="badge bg-warning">Submitted</span>
                                                        @break
                                                    @case('approved')
                                                        <span class="badge bg-success">Approved</span>
                                                        @break
                                                    @case('rejected')
                                                        <span class="badge bg-danger">Rejected</span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td style="vertical-align: middle;">
                                                <div class="text-truncate" style="max-width: 150px;" title="{{ $site->site_url }}">
                                                    <small class="text-muted">{{ $site->site_url }}</small>
                                                </div>
                                            </td>
                                            <td style="vertical-align: middle;">
                                                <small class="text-muted">{{ $site->created_at->format('M d, Y') }}</small>
                                            </td>
                                            <td style="vertical-align: middle;">
                                                <div class="d-flex flex-wrap gap-1">
                                                    <a href="{{ route('admin.tc-lms.show', $site) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    @if($site->canBeEditedByFaculty())
                                                        <a href="{{ route('admin.tc-lms.edit', $site) }}" 
                                                           class="btn btn-sm btn-outline-warning" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                    @else
                                                        <button class="btn btn-sm btn-outline-secondary" disabled title="Cannot edit - {{ $site->status === 'approved' ? 'No admin permission' : 'Site is ' . $site->status }}">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                    @endif
                                                    @if($site->status === 'draft')
                                                        <form method="POST" action="{{ route('admin.tc-lms.submit', $site) }}" 
                                                              class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-success" 
                                                                    title="Submit for Approval" 
                                                                    onclick="return confirm('Are you sure you want to submit this site for approval?')">
                                                                <i class="bi bi-send"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    @if($site->canBeDeletedByFaculty())
                                                        <form method="POST" action="{{ route('admin.tc-lms.destroy', $site) }}" 
                                                              class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                    title="Delete" 
                                                                    onclick="return confirm('Are you sure you want to delete this site?')">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <button class="btn btn-sm btn-outline-secondary" disabled title="Cannot delete - Only draft sites can be deleted">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $lmsSites->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-globe display-1 text-muted"></i>
                            <h4 class="mt-3 text-muted">No LMS Sites Found</h4>
                            <p class="text-muted">You haven't created any LMS sites yet.</p>
                            <a href="{{ route('admin.tc-lms.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>
                                Create Your First Site
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
