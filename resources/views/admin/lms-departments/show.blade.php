@extends('admin.layout')

@section('title', 'Department Details')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1 text-primary">
                        <i class="bi bi-building me-2"></i>
                        {{ $lmsDepartment->department_name }}
                    </h1>
                    <p class="text-muted mb-0">
                        Department Details and LMS Sites
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.lms-departments.edit', $lmsDepartment) }}" class="btn btn-outline-warning">
                        <i class="bi bi-pencil me-2"></i>
                        Edit Department
                    </a>
                    <a href="{{ route('admin.lms-departments.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Back to Departments
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Information -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Department Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Department Name</label>
                                <p class="form-control-plaintext">{{ $lmsDepartment->department_name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Department Slug</label>
                                <p class="form-control-plaintext">
                                    <code>{{ $lmsDepartment->department_slug }}</code>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <p class="form-control-plaintext">
                                    @if($lmsDepartment->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Created By</label>
                                <p class="form-control-plaintext">{{ $lmsDepartment->creator->name }}</p>
                            </div>
                        </div>
                    </div>

                    @if($lmsDepartment->description)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <p class="form-control-plaintext">{{ $lmsDepartment->description }}</p>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Created</label>
                                <p class="form-control-plaintext">{{ $lmsDepartment->created_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Last Updated</label>
                                <p class="form-control-plaintext">{{ $lmsDepartment->updated_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Department URL</label>
                        <p class="form-control-plaintext">
                            <a href="{{ $lmsDepartment->department_url }}" target="_blank" class="text-decoration-none">
                                {{ $lmsDepartment->department_url }}
                                <i class="bi bi-box-arrow-up-right ms-1"></i>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Statistics -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h3 class="text-primary">{{ $lmsDepartment->lmsSites->count() }}</h3>
                                <small class="text-muted">Total Sites</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h3 class="text-success">{{ $lmsDepartment->lmsSites->where('is_approved', true)->count() }}</h3>
                            <small class="text-muted">Approved</small>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h3 class="text-warning">{{ $lmsDepartment->lmsSites->where('status', 'submitted')->count() }}</h3>
                                <small class="text-muted">Pending</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h3 class="text-secondary">{{ $lmsDepartment->lmsSites->where('status', 'draft')->count() }}</h3>
                            <small class="text-muted">Draft</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.lms-departments.edit', $lmsDepartment) }}" class="btn btn-outline-warning">
                            <i class="bi bi-pencil me-2"></i>
                            Edit Department
                        </a>
                        
                        <form method="POST" action="{{ route('admin.lms-departments.toggle-status', $lmsDepartment) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-{{ $lmsDepartment->is_active ? 'secondary' : 'success' }} w-100">
                                <i class="bi bi-{{ $lmsDepartment->is_active ? 'pause' : 'play' }} me-2"></i>
                                {{ $lmsDepartment->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>

                        @if($lmsDepartment->lmsSites->count() == 0)
                            <form method="POST" action="{{ route('admin.lms-departments.destroy', $lmsDepartment) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100" 
                                        onclick="return confirm('Are you sure you want to delete this department?')">
                                    <i class="bi bi-trash me-2"></i>
                                    Delete Department
                                </button>
                            </form>
                        @else
                            <button type="button" class="btn btn-outline-danger w-100" disabled 
                                    title="Cannot delete department with existing sites">
                                <i class="bi bi-trash me-2"></i>
                                Delete Department
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- LMS Sites in this Department -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">LMS Sites in this Department</h5>
                </div>
                <div class="card-body">
                    @if($lmsDepartment->lmsSites->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Site Title</th>
                                        <th>Faculty</th>
                                        <th>TC Code</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lmsDepartment->lmsSites as $site)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $site->site_title }}</strong>
                                                    @if($site->site_description)
                                                        <br><small class="text-muted">{{ Str::limit($site->site_description, 50) }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $site->faculty->name }}</strong>
                                                    <br><small class="text-muted">{{ $site->faculty->email }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <code>{{ $site->tc_code }}</code>
                                            </td>
                                            <td>
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
                                            <td>
                                                <small>{{ $site->created_at->format('M d, Y') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.tc-lms.show', $site) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    @if($site->status === 'submitted')
                                                        <form method="POST" action="{{ route('admin.tc-lms.approve', $site) }}" 
                                                              class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-success" 
                                                                    title="Approve" 
                                                                    onclick="return confirm('Are you sure you want to approve this site?')">
                                                                <i class="bi bi-check"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-file-text display-1 text-muted"></i>
                            <h4 class="mt-3 text-muted">No Sites Found</h4>
                            <p class="text-muted">No LMS sites have been created in this department yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
