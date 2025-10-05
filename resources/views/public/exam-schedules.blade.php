@extends('layouts.goi-meta')

@section('content')
<div class="container-fluid py-4">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <!-- Navigation -->
                <div class="goi-navigation mb-4">
                    <nav class="navbar navbar-expand-lg navbar-light bg-light rounded">
                        <div class="container-fluid">
                            <a class="navbar-brand" href="{{ route('home') }}">
                                <i class="bi bi-house me-2"></i>Home
                            </a>
                            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <div class="collapse navbar-collapse" id="navbarNav">
                                <ul class="navbar-nav ms-auto">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('home') }}">
                                            <i class="bi bi-house me-1"></i>Home
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('public.lms.index') }}">
                                            <i class="bi bi-book me-1"></i>LMS
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link active" href="{{ route('public.exam-schedules') }}">
                                            <i class="bi bi-calendar-event me-1"></i>Exam Schedules
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('admin.login') }}">
                                            <i class="bi bi-shield-check me-1"></i>Admin Login
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('student.login') }}">
                                            <i class="bi bi-mortarboard me-1"></i>Student Login
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                </div>

                <!-- <div class="goi-page-header">
                    <h1 class="goi-page-title">Exam Schedules</h1>
                    <p class="goi-page-subtitle">View approved exam schedules for internal and final examinations</p>
                </div> -->

                <!-- Tab Navigation -->
                <ul class="nav nav-tabs goi-tabs" id="examTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="final-tab" data-bs-toggle="tab" data-bs-target="#final" type="button" role="tab" aria-controls="final" aria-selected="true">
                            <i class="fas fa-graduation-cap me-2"></i>Final Examinations
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="internal-tab" data-bs-toggle="tab" data-bs-target="#internal" type="button" role="tab" aria-controls="internal" aria-selected="false">
                            <i class="fas fa-clipboard-check me-2"></i>Internal Examinations
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content goi-tab-content" id="examTabsContent">
                    <!-- Final Examinations Tab -->
                    <div class="tab-pane fade show active" id="final" role="tabpanel" aria-labelledby="final-tab">
                        <div class="goi-card">
                            <!-- <div class="goi-card-header">
                                <h3 class="goi-card-title">
                                    <i class="fas fa-graduation-cap me-2"></i>Final Examinations
                                </h3>
                                <p class="goi-card-subtitle">Approved by Assessment Agency with File Numbers</p>
                            </div> -->
                            <div class="goi-card-body" style="min-height: 400px;">
                                <!-- Loading Spinner -->
                                <div id="final-loading" class="text-center py-5" style="display: flex; flex-direction: column; justify-content: center; align-items: center; min-height: 300px;">
                                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="text-muted">Loading exam schedules...</p>
                                </div>

                                <!-- Mobile Scroll Indicator -->
                                <div class="mobile-scroll-indicator d-md-none">
                                    <i class="fas fa-arrows-alt-h me-2"></i>
                                    <span>Scroll horizontally to view all columns</span>
                                </div>
                                
                                <!-- Final Examinations Table -->
                                <div id="final-exam-table" style="display: none;">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover goi-exam-table">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>S.No.</th>
                                                    <th>Course Name</th>
                                                    <th>Batch Code</th>
                                                    <th>Exam Period</th>
                                                    <th>TC Name</th>
                                                    <th>Centre</th>
                                                    <th>File Number</th>
                                                    <th>Type</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="final-exam-tbody">
                                                <!-- Data will be loaded via AJAX -->
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Pagination -->
                                    <div id="final-pagination" class="mt-4">
                                        <!-- Pagination will be loaded via AJAX -->
                                    </div>
                                </div>

                                <!-- Empty State -->
                                <div id="final-empty-state" class="goi-empty-state" style="display: none; min-height: 300px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                    <i class="fas fa-graduation-cap fa-3x mb-3"></i>
                                    <h4>No Final Examinations Available</h4>
                                    <p>There are currently no final examinations scheduled.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Internal Examinations Tab -->
                    <div class="tab-pane fade" id="internal" role="tabpanel" aria-labelledby="internal-tab">
                        <div class="goi-card">
                            <!-- <div class="goi-card-header">
                                <h3 class="goi-card-title">
                                    <i class="fas fa-clipboard-check me-2"></i>Internal Examinations
                                </h3>
                                <p class="goi-card-subtitle">Approved by TC Head</p>
                            </div> -->
                            <div class="goi-card-body" style="min-height: 400px;">
                                <!-- Loading Spinner -->
                                <div id="internal-loading" class="text-center py-5" style="display: none; flex-direction: column; justify-content: center; align-items: center; min-height: 300px;">
                                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="text-muted">Loading exam schedules...</p>
                                </div>

                                <!-- Mobile Scroll Indicator -->
                                <div class="mobile-scroll-indicator d-md-none">
                                    <i class="fas fa-arrows-alt-h me-2"></i>
                                    <span>Scroll horizontally to view all columns</span>
                                </div>
                                
                                <!-- Internal Examinations Table -->
                                <div id="internal-exam-table" style="display: none;">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover goi-exam-table">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>S.No.</th>
                                                    <th>Course Name</th>
                                                    <th>Batch Code</th>
                                                    <th>Exam Period</th>
                                                    <th>TC Name</th>
                                                    <th>Centre</th>
                                                    <th>Type</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="internal-exam-tbody">
                                                <!-- Data will be loaded via AJAX -->
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Pagination -->
                                    <div id="internal-pagination" class="mt-4">
                                        <!-- Pagination will be loaded via AJAX -->
                                    </div>
                                </div>

                                <!-- Empty State -->
                                <div id="internal-empty-state" class="goi-empty-state" style="display: none; min-height: 300px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                    <i class="fas fa-clipboard-check fa-3x mb-3"></i>
                                    <h4>No Internal Examinations Available</h4>
                                    <p>There are currently no internal examinations scheduled.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.goi-page-header {
    text-align: center;
    margin-bottom: 2rem;
    padding: 2rem 0;
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    color: white;
    border-radius: 10px;
    margin-bottom: 2rem;
}

.goi-page-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.goi-page-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin: 0;
}

.goi-tabs {
    border-bottom: 3px solid #e9ecef;
    margin-bottom: 2rem;
}

.goi-tabs .nav-link {
    border: none;
    border-radius: 0;
    padding: 1rem 2rem;
    font-weight: 600;
    color: #6c757d;
    background: transparent;
    transition: all 0.3s ease;
}

.goi-tabs .nav-link:hover {
    color: #1e3c72;
    background: #f8f9fa;
}

.goi-tabs .nav-link.active {
    color: #1e3c72;
    background: white;
    border-bottom: 3px solid #1e3c72;
}

.goi-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.goi-card-header {
    background: #f8f9fa;
    padding: 1.5rem;
    border-bottom: 1px solid #e9ecef;
}

.goi-card-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1e3c72;
    margin: 0;
}

.goi-card-subtitle {
    color: #6c757d;
    margin: 0.5rem 0 0 0;
    font-size: 0.9rem;
}

.goi-card-body {
    padding: 1.5rem;
}

.goi-schedule-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
}

.goi-schedule-card:hover {
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.goi-schedule-header {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    color: white;
    padding: 1rem;
}

.goi-schedule-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0 0 0.5rem 0;
}

.goi-schedule-batch {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 500;
}

.goi-schedule-body {
    padding: 1rem;
}

.goi-schedule-info {
    margin-bottom: 1rem;
}

.goi-info-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.goi-info-item i {
    color: #1e3c72;
    width: 16px;
}

.goi-info-item strong {
    margin-right: 0.5rem;
    color: #495057;
}

.goi-file-number {
    background: #e3f2fd;
    color: #1976d2;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-weight: 600;
    font-size: 0.8rem;
}

.goi-exam-type {
    background: #f3e5f5;
    color: #7b1fa2;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-weight: 600;
    font-size: 0.8rem;
}

.goi-status-approved {
    background: #e8f5e8;
    color: #2e7d32;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-weight: 600;
    font-size: 0.8rem;
}

.goi-schedule-actions {
    text-align: center;
}

.goi-btn {
    background: #1e3c72;
    border: none;
    padding: 0.5rem 1.5rem;
    border-radius: 25px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.goi-btn:hover {
    background: #2a5298;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(30, 60, 114, 0.3);
}

.goi-empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #6c757d;
}

.goi-empty-state i {
    color: #dee2e6;
}

.goi-empty-state h4 {
    color: #495057;
    margin: 1rem 0 0.5rem 0;
}

/* Table Styling */
.goi-exam-table {
    margin-bottom: 0;
    font-size: 0.8rem;
    table-layout: fixed;
    min-width: 800px;
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.goi-exam-table thead th {
    background-color: #1e3c72 !important;
    color: white !important;
    font-weight: 600;
    text-align: center;
    vertical-align: middle;
    padding: 0.5rem 0.25rem;
    border: none;
    font-size: 0.8rem;
    white-space: nowrap;
}

.goi-exam-table tbody td {
    vertical-align: middle;
    padding: 0.5rem 0.25rem;
    border-color: #e9ecef;
    font-size: 0.75rem;
    word-wrap: break-word;
    white-space: normal;
    line-height: 1.2;
}

.goi-exam-table tbody tr:hover {
    background-color: #f8f9fa;
}

.goi-exam-table tbody tr:nth-child(even) {
    background-color: #f8f9fa;
}

.goi-exam-table tbody tr:nth-child(even):hover {
    background-color: #e9ecef;
}

/* Badge Styling */
.badge {
    font-size: 0.75rem;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
}

.badge.bg-primary {
    background-color: #1e3c72 !important;
}

.badge.bg-success {
    background-color: #28a745 !important;
}

.badge.bg-info {
    background-color: #17a2b8 !important;
}

/* Button Styling */
.btn-primary {
    background-color: #1e3c72;
    border-color: #1e3c72;
    font-size: 0.8rem;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-weight: 600;
}

.btn-primary:hover {
    background-color: #2a5298;
    border-color: #2a5298;
    transform: translateY(-1px);
}

/* Text Styling */
.text-primary {
    color: #1e3c72 !important;
    font-weight: 600;
}

.text-success {
    color: #28a745 !important;
}

.fw-bold {
    font-weight: 600 !important;
}

/* Responsive Table */
.table-responsive {
    border-radius: 10px;
    overflow-x: auto;
    overflow-y: visible;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
    scrollbar-color: #1e3c72 #f1f1f1;
}

.table-responsive::-webkit-scrollbar {
    height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #1e3c72;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #2a5298;
}

/* Mobile Scroll Indicator */
.mobile-scroll-indicator {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    text-align: center;
    font-size: 0.8rem;
    font-weight: 500;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    animation: pulse 2s infinite;
}

.mobile-scroll-indicator i {
    color: #FFD700;
}

@keyframes pulse {
    0% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
    100% {
        opacity: 1;
    }
}

/* Column Widths for Compact Display */
.goi-exam-table th:nth-child(1),
.goi-exam-table td:nth-child(1) {
    width: 4%;
    text-align: center;
}

.goi-exam-table th:nth-child(2),
.goi-exam-table td:nth-child(2) {
    width: 18%;
    text-align: left;
}

.goi-exam-table th:nth-child(3),
.goi-exam-table td:nth-child(3) {
    width: 8%;
    text-align: center;
}

.goi-exam-table th:nth-child(4),
.goi-exam-table td:nth-child(4) {
    width: 12%;
    text-align: center;
}

.goi-exam-table th:nth-child(5),
.goi-exam-table td:nth-child(5) {
    width: 20%;
    text-align: left;
}

.goi-exam-table th:nth-child(6),
.goi-exam-table td:nth-child(6) {
    width: 15%;
    text-align: left;
}

.goi-exam-table th:nth-child(7),
.goi-exam-table td:nth-child(7) {
    width: 12%;
    text-align: center;
}

.goi-exam-table th:nth-child(8),
.goi-exam-table td:nth-child(8) {
    width: 6%;
    text-align: center;
}

.goi-exam-table th:nth-child(9),
.goi-exam-table td:nth-child(9) {
    width: 5%;
    text-align: center;
}

/* Mobile Responsive Design */
@media (max-width: 1200px) {
    .goi-exam-table {
        min-width: 900px;
        font-size: 0.75rem;
    }
    
    .goi-exam-table thead th {
        padding: 0.5rem 0.3rem;
        font-size: 0.75rem;
    }
    
    .goi-exam-table tbody td {
        padding: 0.5rem 0.3rem;
        font-size: 0.7rem;
    }
}

@media (max-width: 992px) {
    .goi-exam-table {
        min-width: 1000px;
        font-size: 0.8rem;
    }
    
    .goi-page-title {
        font-size: 2rem;
    }
    
    .goi-tabs .nav-link {
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
    }
    
    .goi-schedule-card {
        margin-bottom: 1rem;
    }
}

@media (max-width: 768px) {
    .goi-exam-table {
        min-width: 1100px;
        font-size: 0.75rem;
    }
    
    .goi-exam-table thead th {
        padding: 0.4rem 0.25rem;
        font-size: 0.7rem;
    }
    
    .goi-exam-table tbody td {
        padding: 0.4rem 0.25rem;
        font-size: 0.65rem;
    }
    
    .badge {
        font-size: 0.6rem;
        padding: 0.2rem 0.4rem;
    }
    
    .btn-sm {
        font-size: 0.6rem;
        padding: 0.2rem 0.4rem;
    }
    
    .goi-page-title {
        font-size: 1.8rem;
    }
    
    .goi-tabs .nav-link {
        padding: 0.6rem 0.8rem;
        font-size: 0.85rem;
    }
}

@media (max-width: 576px) {
    .goi-exam-table {
        min-width: 1200px;
        font-size: 0.7rem;
    }
    
    .goi-exam-table thead th {
        padding: 0.3rem 0.2rem;
        font-size: 0.65rem;
    }
    
    .goi-exam-table tbody td {
        padding: 0.3rem 0.2rem;
        font-size: 0.6rem;
    }
    
    .badge {
        font-size: 0.5rem;
        padding: 0.15rem 0.3rem;
    }
    
    .btn-sm {
        font-size: 0.5rem;
        padding: 0.15rem 0.3rem;
    }
    
    .goi-page-title {
        font-size: 1.5rem;
    }
    
    .goi-tabs .nav-link {
        padding: 0.5rem 0.6rem;
        font-size: 0.8rem;
    }
    
    .container {
        padding-left: 10px;
        padding-right: 10px;
    }
}

/* Extra small devices (phones, 480px and down) */
@media (max-width: 480px) {
    .goi-exam-table {
        min-width: 1300px;
        font-size: 0.65rem;
    }
    
    .goi-exam-table thead th {
        padding: 0.25rem 0.15rem;
        font-size: 0.6rem;
    }
    
    .goi-exam-table tbody td {
        padding: 0.25rem 0.15rem;
        font-size: 0.55rem;
    }
    
    .badge {
        font-size: 0.45rem;
        padding: 0.1rem 0.25rem;
    }
    
    .btn-sm {
        font-size: 0.45rem;
        padding: 0.1rem 0.25rem;
    }
    
    .goi-page-title {
        font-size: 1.3rem;
    }
    
    .goi-tabs .nav-link {
        padding: 0.4rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .mobile-scroll-indicator {
        font-size: 0.7rem;
        padding: 0.4rem 0.8rem;
    }
}

/* Touch device improvements */
@media (hover: none) and (pointer: coarse) {
    .table-responsive {
        -webkit-overflow-scrolling: touch;
        scroll-behavior: smooth;
    }
    
    .goi-exam-table tbody tr {
        border-bottom: 2px solid #e9ecef;
    }
    
    .btn-sm {
        min-height: 32px;
        min-width: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
}

/* Google-style Pagination */
.google-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    margin: 20px 0;
    flex-wrap: wrap;
}

.pagination-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    padding: 8px 12px;
    border: 1px solid #dadce0;
    background-color: #fff;
    color: #3c4043;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s ease;
    user-select: none;
}

.pagination-btn:hover {
    background-color: #f8f9fa;
    border-color: #dadce0;
    color: #3c4043;
    text-decoration: none;
}

.pagination-btn.active {
    background-color: #1a73e8;
    border-color: #1a73e8;
    color: #fff;
}

.pagination-btn.active:hover {
    background-color: #1557b0;
    border-color: #1557b0;
    color: #fff;
}

.pagination-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}

.pagination-ellipsis {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    color: #5f6368;
    font-size: 14px;
    font-weight: 500;
}

.prev-btn, .next-btn {
    min-width: 40px;
}

.prev-btn i, .next-btn i {
    font-size: 12px;
}

/* Loading spinner */
.spinner-border {
    width: 2rem;
    height: 2rem;
    border-width: 0.2em;
    animation: spin 1s linear infinite;
}

/* Enhanced loading animation */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Smooth transitions for content switching */
.goi-card-body > div {
    transition: opacity 0.3s ease-in-out;
}

/* Loading state styling */
#final-loading, #internal-loading {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 8px;
    margin: 10px 0;
}

/* Empty state styling */
.goi-empty-state {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 8px;
    margin: 10px 0;
    padding: 40px 20px;
}

/* Responsive pagination */
@media (max-width: 576px) {
    .google-pagination {
        gap: 4px;
    }
    
    .pagination-btn {
        min-width: 36px;
        height: 36px;
        padding: 6px 10px;
        font-size: 13px;
    }
    
    .pagination-ellipsis {
        min-width: 36px;
        height: 36px;
        font-size: 13px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show initial loading state for final tab
    showLoadingState('final');
    
    // Load initial data
    loadExamSchedules('final', 1);
    
    // Tab click handlers
    document.getElementById('final-tab').addEventListener('click', function() {
        showLoadingState('final');
        loadExamSchedules('final', 1);
    });
    
    document.getElementById('internal-tab').addEventListener('click', function() {
        showLoadingState('internal');
        loadExamSchedules('internal', 1);
    });
    
    // Pagination click handlers
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('pagination-btn') && !e.target.classList.contains('active')) {
            const page = parseInt(e.target.getAttribute('data-page'));
            const type = e.target.getAttribute('data-type');
            loadExamSchedules(type, page);
        }
    });
    
    function showLoadingState(type) {
        const loadingId = type + '-loading';
        const tableId = type + '-exam-table';
        const emptyStateId = type + '-empty-state';
        
        // Show loading, hide others
        document.getElementById(loadingId).style.display = 'flex';
        document.getElementById(tableId).style.display = 'none';
        document.getElementById(emptyStateId).style.display = 'none';
    }
    
    function loadExamSchedules(type, page) {
        const loadingId = type + '-loading';
        const tableId = type + '-exam-table';
        const tbodyId = type + '-exam-tbody';
        const paginationId = type + '-pagination';
        const emptyStateId = type + '-empty-state';
        
        // Show loading
        document.getElementById(loadingId).style.display = 'flex';
        document.getElementById(tableId).style.display = 'none';
        document.getElementById(emptyStateId).style.display = 'none';
        
        // Make AJAX request
        fetch(`{{ route('public.exam-schedules.ajax') }}?type=${type}&page=${page}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hide loading
                    document.getElementById(loadingId).style.display = 'none';
                    
                    if (data.data.length > 0) {
                        // Show table and populate data
                        document.getElementById(tableId).style.display = 'block';
                        populateTable(tbodyId, data.data, type, page);
                        document.getElementById(paginationId).innerHTML = data.pagination.html;
                    } else {
                        // Show empty state
                        document.getElementById(emptyStateId).style.display = 'flex';
                        document.getElementById(paginationId).innerHTML = '';
                    }
                } else {
                    throw new Error('Failed to load data');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById(loadingId).style.display = 'none';
                document.getElementById(emptyStateId).style.display = 'flex';
                document.getElementById(emptyStateId).innerHTML = `
                    <div class="alert alert-danger text-center">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error loading exam schedules. Please try again.
                    </div>
                `;
            });
    }
    
    function populateTable(tbodyId, schedules, type, currentPage) {
        const tbody = document.getElementById(tbodyId);
        const perPage = 10;
        const startIndex = (currentPage - 1) * perPage;
        
        tbody.innerHTML = '';
        
        schedules.forEach((schedule, index) => {
            const row = document.createElement('tr');
            const serialNumber = startIndex + index + 1;
            
            let statusCell = '';
            if (type === 'internal') {
                statusCell = '<td><span class="text-success fw-bold">Approved by TC Head</span></td>';
            }
            
            row.innerHTML = `
                <td>${serialNumber}</td>
                <td><strong>${schedule.course_name}</strong></td>
                <td><span class="badge bg-primary">${schedule.batch_code}</span></td>
                <td><i class="fas fa-calendar-alt me-1"></i> ${formatDate(schedule.exam_start_date)} - ${formatDate(schedule.exam_end_date)}</td>
                <td><i class="fas fa-university me-1"></i> ${schedule.tc_name || 'N/A'}</td>
                <td><i class="fas fa-building me-1"></i> ${schedule.centre ? schedule.centre.centre_name : 'N/A'}</td>
                ${type === 'final' ? `<td><span class="text-primary fw-bold">${schedule.file_no}</span></td>` : ''}
                <td><span class="badge ${type === 'final' ? 'bg-success' : 'bg-info'}">${schedule.exam_type}</span></td>
                ${statusCell}
                <td>
                    <a href="/exam-schedules/${schedule.id}/view" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye me-1"></i>
                    </a>
                </td>
            `;
            
            tbody.appendChild(row);
        });
    }
    
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }
});
</script>
@endsection
