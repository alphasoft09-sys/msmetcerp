@extends('admin.layout')

@section('title', 'Preview LMS Site')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1 text-primary">
                        <i class="bi bi-eye me-2"></i>
                        Preview: {{ $tcLm->site_title }}
                    </h1>
                    <p class="text-muted mb-0">
                        Preview your LMS site before submission
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.tc-lms.edit', $tcLm) }}" class="btn btn-outline-warning">
                        <i class="bi bi-pencil me-2"></i>
                        Edit Site
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
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Site Title:</strong><br>
                            <span class="text-muted">{{ $tcLm->site_title }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Department:</strong><br>
                            <span class="badge bg-info">{{ $tcLm->site_department }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Status:</strong><br>
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
                        </div>
                        <div class="col-md-3">
                            <strong>Site URL:</strong><br>
                            <small class="text-muted">{{ $tcLm->site_url }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Container -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-layout-text-window me-2"></i>
                            Site Preview
                        </h5>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="mobile-preview">
                                <i class="bi bi-phone"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="tablet-preview">
                                <i class="bi bi-tablet"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-primary" id="desktop-preview">
                                <i class="bi bi-laptop"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div id="preview-container" class="preview-container">
                        <div id="preview-content" class="preview-content">
                            @if($tcLm->site_contents && !empty(trim($tcLm->site_contents)))
                                {!! $tcLm->site_contents !!}
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-file-text display-1 text-muted"></i>
                                    <h3 class="mt-3 text-muted">No Content Available</h3>
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
    </div>

    <!-- Action Buttons -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted">
                                <i class="bi bi-info-circle me-2"></i>
                                This is a preview of your site. Changes made here won't be saved.
                            </span>
                        </div>
                        <div class="d-flex gap-2">
                            @if($tcLm->status === 'draft')
                                <form method="POST" action="{{ route('admin.tc-lms.submit', $tcLm) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success" 
                                            onclick="return confirm('Are you sure you want to submit this site for approval?')">
                                        <i class="bi bi-send me-2"></i>
                                        Submit for Approval
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('admin.tc-lms.edit', $tcLm) }}" class="btn btn-primary">
                                <i class="bi bi-pencil me-2"></i>
                                Edit Site
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.preview-container {
    min-height: 600px;
    background: #f8f9fa;
    border: 2px dashed #dee2e6;
    position: relative;
    margin: 0 20px;
}

.preview-content {
    min-height: 600px;
    padding: 20px;
    position: relative;
    background: white;
    margin: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

/* Responsive preview */
.preview-content.mobile {
    max-width: 375px;
    margin: 20px auto;
}

.preview-content.tablet {
    max-width: 768px;
    margin: 20px auto;
}

.preview-content.desktop {
    max-width: 100%;
    margin: 20px;
}

/* Content styling */
.preview-content img {
    max-width: 100%;
    height: auto;
    border-radius: 4px;
    margin: 10px 0;
}

.preview-content table {
    width: 100%;
    border-collapse: collapse;
    margin: 15px 0;
}

.preview-content table, 
.preview-content th, 
.preview-content td {
    border: 1px solid #dee2e6;
}

.preview-content th, 
.preview-content td {
    padding: 8px 12px;
    text-align: left;
}

.preview-content h1, 
.preview-content h2, 
.preview-content h3, 
.preview-content h4, 
.preview-content h5, 
.preview-content h6 {
    margin: 20px 0 10px 0;
    color: #333;
}

.preview-content p {
    margin: 10px 0;
    line-height: 1.6;
}

.preview-content ul, 
.preview-content ol {
    margin: 10px 0;
    padding-left: 20px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializePreviewButtons();
});

function initializePreviewButtons() {
    document.getElementById('mobile-preview').addEventListener('click', () => {
        document.getElementById('preview-content').className = 'preview-content mobile';
        updateActiveButton('mobile-preview');
    });
    
    document.getElementById('tablet-preview').addEventListener('click', () => {
        document.getElementById('preview-content').className = 'preview-content tablet';
        updateActiveButton('tablet-preview');
    });
    
    document.getElementById('desktop-preview').addEventListener('click', () => {
        document.getElementById('preview-content').className = 'preview-content desktop';
        updateActiveButton('desktop-preview');
    });
}

function updateActiveButton(activeId) {
    // Remove active class from all buttons
    document.querySelectorAll('#mobile-preview, #tablet-preview, #desktop-preview').forEach(btn => {
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-outline-secondary');
    });
    
    // Add active class to clicked button
    const activeBtn = document.getElementById(activeId);
    activeBtn.classList.remove('btn-outline-secondary');
    activeBtn.classList.add('btn-primary');
}
</script>
@endpush
@endsection
