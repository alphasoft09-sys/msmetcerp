@extends('admin.layout')

@section('title', 'LMS Departments Management')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1 text-primary">
                        <i class="bi bi-building me-2"></i>
                        LMS Departments Management
                    </h1>
                    <p class="text-muted mb-0">
                        Manage departments for LMS sites
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.lms-departments.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle me-2"></i>
                        Create Department
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Departments Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Departments</h5>
                </div>
                <div class="card-body">
                    @if($departments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Department Name</th>
                                        <th>Slug</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Created By</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($departments as $department)
                                        <tr>
                                            <td>
                                                <strong>{{ $department->department_name }}</strong>
                                            </td>
                                            <td>
                                                <code>{{ $department->department_slug }}</code>
                                            </td>
                                            <td>
                                                {{ Str::limit($department->description, 50) ?: 'No description' }}
                                            </td>
                                            <td>
                                                @if($department->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $department->creator->name }}
                                            </td>
                                            <td>
                                                {{ $department->created_at->format('M d, Y') }}
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.lms-departments.show', $department) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.lms-departments.edit', $department) }}" 
                                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.lms-departments.toggle-status', $department) }}" 
                                                          class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-{{ $department->is_active ? 'secondary' : 'success' }}" 
                                                                title="{{ $department->is_active ? 'Deactivate' : 'Activate' }}">
                                                            <i class="bi bi-{{ $department->is_active ? 'pause' : 'play' }}"></i>
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.lms-departments.destroy', $department) }}" 
                                                          class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                title="Delete" 
                                                                onclick="return confirm('Are you sure you want to delete this department?')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $departments->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-building display-1 text-muted"></i>
                            <h4 class="mt-3 text-muted">No Departments Found</h4>
                            <p class="text-muted">You haven't created any departments yet.</p>
                            <a href="{{ route('admin.lms-departments.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>
                                Create Your First Department
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
