<!-- Enhanced Approval Modal for Assessment Agency -->
<div class="modal fade" id="aaApprovalModal" tabindex="-1" aria-labelledby="aaApprovalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
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
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Important:</strong> Upon approval, a unique file number will be automatically assigned to this exam schedule.
                            </div>
                            
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
                                    <p class="text-muted mb-3">File number to be assigned:</p>
                                    <div class="file-number-container">
                                        <span id="aaGeneratedFileNumber" class="file-number-display">
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

<!-- Enhanced Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectModalLabel">
                    <i class="bi bi-x-circle me-2"></i>
                    Reject Exam Schedule
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> This action will reject the exam schedule and notify the Training Center.
                    </div>
                    
                    <p>Are you sure you want to reject the exam schedule "<strong id="rejectScheduleName"></strong>"?</p>
                    <div class="mb-3">
                        <label for="rejectComment" class="form-label">Reason for Rejection *</label>
                        <textarea class="form-control" id="rejectComment" name="comment" rows="3" 
                                  placeholder="Please provide a detailed reason for rejection..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-2"></i>
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

<!-- Enhanced Hold Modal -->
<div class="modal fade" id="holdModal" tabindex="-1" aria-labelledby="holdModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="holdModalLabel">
                    <i class="bi bi-pause-circle me-2"></i>
                    Hold Exam Schedule
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="holdForm">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Note:</strong> This will put the exam schedule on hold for rescheduling.
                    </div>
                    
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
                        <i class="bi bi-pause-circle me-2"></i>
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

<style>
/* Enhanced File Number Display Styles */
.file-number-container {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px solid #dee2e6;
    border-radius: 12px;
    padding: 20px;
    margin: 15px 0;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
}

.file-number-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #28a745, #20c997, #17a2b8);
}

.file-number-display {
    display: inline-block;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    font-family: 'Courier New', 'Monaco', 'Consolas', monospace;
    font-size: 1.1rem;
    font-weight: 600;
    letter-spacing: 1.5px;
    padding: 12px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.2);
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.file-number-display::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
}

.file-number-display:hover::before {
    left: 100%;
}

.file-number-display:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(40, 167, 69, 0.4);
}

.file-number-display i {
    margin-right: 8px;
    font-size: 1.2rem;
    vertical-align: middle;
}

/* Loading state styling */
.file-number-display .spinner-border {
    width: 1rem;
    height: 1rem;
    border-width: 0.15em;
}

/* Error state styling */
.file-number-display.error {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
}

.file-number-display.error:hover {
    box-shadow: 0 6px 12px rgba(220, 53, 69, 0.4);
} 

/* Success state styling */
.file-number-display.success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    animation: successPulse 0.6s ease-in-out;
}

@keyframes successPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .file-number-display {
        font-size: 0.95rem;
        padding: 10px 16px;
        letter-spacing: 1px;
    }
    
    .file-number-container {
        padding: 15px;
        margin: 10px 0;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .file-number-container {
        background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
        border-color: #4a5568;
    }
}
</style> 