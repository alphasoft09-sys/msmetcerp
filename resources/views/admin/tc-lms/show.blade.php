@extends('admin.layout')

@section('title', 'LMS Site Details')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1 text-primary">
                        <i class="bi bi-globe me-2"></i>
                        {{ $tcLm->site_title }}
                    </h1>
                    <p class="text-muted mb-0">
                        LMS Site Details and Management
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <!-- <a href="{{ route('admin.tc-lms.preview', $tcLm) }}" class="btn btn-outline-info" target="_blank">
                        <i class="bi bi-eye me-2"></i>
                        Preview
                    </a> -->
                    <a href="{{ route('admin.tc-lms.edit', $tcLm) }}" class="btn btn-outline-warning">
                        <i class="bi bi-pencil me-2"></i>
                        Edit
                    </a>
                    <a href="{{ route('admin.tc-lms.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Back to Sites
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Site Information -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Site Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Site Title</label>
                                <p class="form-control-plaintext">{{ $tcLm->site_title }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Department</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-info">{{ $tcLm->site_department }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Site URL</label>
                                <p class="form-control-plaintext">
                                    <code>{{ $tcLm->site_url }}</code>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <p class="form-control-plaintext">
                                    @switch($tcLm->status)
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
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($tcLm->site_description)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <p class="form-control-plaintext">{{ $tcLm->site_description }}</p>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Created</label>
                                <p class="form-control-plaintext">{{ $tcLm->created_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Last Updated</label>
                                <p class="form-control-plaintext">{{ $tcLm->updated_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    @if($tcLm->is_approved && $tcLm->approvedBy)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Approved By</label>
                                    <p class="form-control-plaintext">{{ $tcLm->approvedBy->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Approved At</label>
                                    <p class="form-control-plaintext">{{ $tcLm->approved_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Faculty Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Faculty Information</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar me-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 50px; height: 50px;">
                                {{ substr($tcLm->faculty->name, 0, 1) }}
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-1">{{ $tcLm->faculty->name }}</h6>
                            <small class="text-muted">{{ $tcLm->faculty->email }}</small>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <strong>TC Code:</strong> {{ $tcLm->tc_code }}
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <!-- <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.tc-lms.preview', $tcLm) }}" class="btn btn-outline-info" target="_blank">
                            <i class="bi bi-eye me-2"></i>
                            Preview Site
                        </a>
                        
                        <a href="{{ route('admin.tc-lms.edit', $tcLm) }}" class="btn btn-outline-warning">
                            <i class="bi bi-pencil me-2"></i>
                            Edit Site
                        </a>

                        @if($tcLm->status === 'draft')
                            <form method="POST" action="{{ route('admin.tc-lms.submit', $tcLm) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success w-100" 
                                        onclick="return confirm('Are you sure you want to submit this site for approval?')">
                                    <i class="bi bi-send me-2"></i>
                                    Submit for Approval
                                </button>
                            </form>
                        @endif

                        @if($tcLm->status === 'draft')
                            <form method="POST" action="{{ route('admin.tc-lms.destroy', $tcLm) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100" 
                                        onclick="return confirm('Are you sure you want to delete this site?')">
                                    <i class="bi bi-trash me-2"></i>
                                    Delete Site
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div> -->
        </div>
    </div>

    <!-- Site Content Preview -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Site Content</h5>
                </div>
                <div class="card-body">
                    @if($tcLm->site_contents && !empty(trim($tcLm->site_contents)))
                        <div class="site-content-preview">
                            {!! $tcLm->site_contents !!}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-file-text display-1 text-muted"></i>
                            <h4 class="mt-3 text-muted">No Content Available</h4>
                            <p class="text-muted">This site doesn't have any content yet.</p>
                            <a href="{{ route('admin.tc-lms.edit', $tcLm) }}" class="btn btn-primary">
                                <i class="bi bi-pencil me-2"></i>
                                Add Content
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.site-content-preview {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    background: #f8f9fa;
    max-height: 600px;
    overflow-y: auto;
}

.site-content-preview img {
    max-width: 100%;
    height: auto;
    border-radius: 4px;
    margin: 10px 0;
}

.site-content-preview table {
    width: 100%;
    border-collapse: collapse;
    margin: 15px 0;
}

.site-content-preview table, 
.site-content-preview th, 
.site-content-preview td {
    border: 1px solid #dee2e6;
}

.site-content-preview th, 
.site-content-preview td {
    padding: 8px 12px;
    text-align: left;
}

.site-content-preview h1, 
.site-content-preview h2, 
.site-content-preview h3, 
.site-content-preview h4, 
.site-content-preview h5, 
.site-content-preview h6 {
    margin: 20px 0 10px 0;
    color: #333;
}

.site-content-preview p {
    margin: 10px 0;
    line-height: 1.6;
}

.site-content-preview ul, 
.site-content-preview ol {
    margin: 10px 0;
    padding-left: 20px;
}
</style>
@endpush
@endsection
