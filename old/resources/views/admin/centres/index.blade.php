@extends('admin.layout')

@section('title', 'Manage Centres')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="bi bi-building me-2"></i>
                        Manage Centres
                    </h1>
                    <p class="text-muted">Add and manage centres for your Tool Room (TC)</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCentreModal">
                        <i class="bi bi-plus-circle me-2"></i>
                        Add Centre
                    </button>
                </div>
            </div>

            <!-- Alert Messages -->
            <div id="alertContainer"></div>

            <!-- Centres Table -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-ul me-2"></i>
                        Centres List
                    </h5>
                </div>
                <div class="card-body">
                    @if($centres->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Centre Name</th>
                                        <th>Address</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($centres as $centre)
                                    <tr>
                                        <td>
                                            <strong>{{ $centre->centre_name }}</strong>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ Str::limit($centre->address, 100) }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $centre->created_at->format('M d, Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-info edit-centre-btn"
                                                        data-centre-id="{{ $centre->id }}"
                                                        data-centre-name="{{ $centre->centre_name }}"
                                                        data-centre-address="{{ $centre->address }}">
                                                    <i class="bi bi-pencil me-1"></i>
                                                    Edit
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-centre-btn"
                                                        data-centre-id="{{ $centre->id }}"
                                                        data-centre-name="{{ $centre->centre_name }}">
                                                    <i class="bi bi-trash me-1"></i>
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-building display-1 text-muted"></i>
                            <h4 class="mt-3 text-muted">No Centres Found</h4>
                            <p class="text-muted">Start by adding your first centre.</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCentreModal">
                                <i class="bi bi-plus-circle me-2"></i>
                                Add First Centre
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Centre Modal -->
<div class="modal fade" id="addCentreModal" tabindex="-1" aria-labelledby="addCentreModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCentreModalLabel">Add New Centre</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addCentreForm" action="{{ route('admin.centres.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div id="addCentreModalError" class="alert alert-danger d-none"></div>
                    <div class="mb-3">
                        <label for="centre_name" class="form-label">Centre Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="centre_name" name="centre_name" required>
                        <div class="invalid-feedback" id="centre_name_error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                        <div class="invalid-feedback" id="address_error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <span id="addBtnText">Add Centre</span>
                        <span id="addBtnLoader" class="spinner-border spinner-border-sm d-none ms-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Centre Modal -->
<div class="modal fade" id="editCentreModal" tabindex="-1" aria-labelledby="editCentreModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCentreModalLabel">Edit Centre</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCentreForm" action="#" method="POST">
                @csrf
                <input type="hidden" id="edit_centre_id" name="centre_id">
                <div class="modal-body">
                    <div id="editCentreModalError" class="alert alert-danger d-none"></div>
                    <div class="mb-3">
                        <label for="edit_centre_name" class="form-label">Centre Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_centre_name" name="centre_name" required>
                        <div class="invalid-feedback" id="edit_centre_name_error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_address" class="form-label">Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_address" name="address" rows="3" required></textarea>
                        <div class="invalid-feedback" id="edit_address_error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <span id="editBtnText">Update Centre</span>
                        <span id="editBtnLoader" class="spinner-border spinner-border-sm d-none ms-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hide error message when opening Add Centre Modal
    $('#addCentreModal').on('show.bs.modal', function() {
        $('#addCentreModalError').addClass('d-none').text('');
    });
    // Hide error message when opening Edit Centre Modal
    $('#editCentreModal').on('show.bs.modal', function() {
        $('#editCentreModalError').addClass('d-none').text('');
    });

    // Add Centre Form
    $('#addCentreForm').on('submit', function(e) {
        e.preventDefault();
        
        // Reset validation
        resetValidation();
        
        // Show loading
        $('#addBtnText').addClass('d-none');
        $('#addBtnLoader').removeClass('d-none');
        $('#addCentreModalError').addClass('d-none').text('');
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("admin.centres.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(data) {
                if (data.success) {
                    showAlert('success', data.message);
                    $('#addCentreModal').modal('hide');
                    $('#addCentreForm')[0].reset();
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    let errorMessage = data.message || 'An error occurred. Please try again.';
                    if (data.errors) {
                        errorMessage += '\n' + Object.values(data.errors).join('\n');
                    }
                    if (data.usage_details) {
                        errorMessage += '\n\nUsage Details:';
                        errorMessage += `\n• Total schedules using this centre: ${data.usage_details.total_schedules}`;
                        if (data.usage_details.schedules && data.usage_details.schedules.length > 0) {
                            errorMessage += '\n• Sample schedules:';
                            data.usage_details.schedules.forEach(function(schedule) {
                                errorMessage += `\n  - Schedule #${schedule.id}: ${schedule.program_name} (${schedule.current_stage}) - Created: ${schedule.created_at}`;
                            });
                        }
                    }
                    $('#addCentreModalError').removeClass('d-none').text(errorMessage);
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = 'An error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                    if (xhr.responseJSON.usage_details) {
                        errorMessage += '\n\nUsage Details:';
                        errorMessage += `\n• Total schedules using this centre: ${xhr.responseJSON.usage_details.total_schedules}`;
                        if (xhr.responseJSON.usage_details.schedules && xhr.responseJSON.usage_details.schedules.length > 0) {
                            errorMessage += '\n• Sample schedules:';
                            xhr.responseJSON.usage_details.schedules.forEach(function(schedule) {
                                errorMessage += `\n  - Schedule #${schedule.id}: ${schedule.program_name} (${schedule.current_stage}) - Created: ${schedule.created_at}`;
                            });
                        }
                    }
                }
                $('#addCentreModalError').removeClass('d-none').text(errorMessage);
            },
            complete: function() {
                $('#addBtnText').removeClass('d-none');
                $('#addBtnLoader').addClass('d-none');
            }
        });
    });

    // Edit Centre Buttons
    document.querySelectorAll('.edit-centre-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const centreId = this.dataset.centreId;
            const centreName = this.dataset.centreName;
            const centreAddress = this.dataset.centreAddress;
            
            document.getElementById('edit_centre_id').value = centreId;
            document.getElementById('edit_centre_name').value = centreName;
            document.getElementById('edit_address').value = centreAddress;
            
            $('#editCentreModal').modal('show');
        });
    });

    // Edit Centre Form
    $('#editCentreForm').on('submit', function(e) {
        e.preventDefault();
        
        // Reset validation
        resetValidation();
        
        // Show loading
        $('#editBtnText').addClass('d-none');
        $('#editBtnLoader').removeClass('d-none');
        $('#editCentreModalError').addClass('d-none').text('');
        
        const centreId = $('#edit_centre_id').val();
        const formData = new FormData(this);
        formData.append('_method', 'PUT');
        
        $.ajax({
            url: `{{ url('admin/centres') }}/${centreId}`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(data) {
                if (data.success) {
                    showAlert('success', data.message);
                    $('#editCentreModal').modal('hide');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    let errorMessage = data.message || 'An error occurred. Please try again.';
                    if (data.errors) {
                        errorMessage += '\n' + Object.values(data.errors).join('\n');
                    }
                    if (data.usage_details) {
                        errorMessage += '\n\nUsage Details:';
                        errorMessage += `\n• Total schedules using this centre: ${data.usage_details.total_schedules}`;
                        if (data.usage_details.schedules && data.usage_details.schedules.length > 0) {
                            errorMessage += '\n• Sample schedules:';
                            data.usage_details.schedules.forEach(function(schedule) {
                                errorMessage += `\n  - Schedule #${schedule.id}: ${schedule.program_name} (${schedule.current_stage}) - Created: ${schedule.created_at}`;
                            });
                        }
                    }
                    $('#editCentreModalError').removeClass('d-none').text(errorMessage);
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = 'An error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                    if (xhr.responseJSON.usage_details) {
                        errorMessage += '\n\nUsage Details:';
                        errorMessage += `\n• Total schedules using this centre: ${xhr.responseJSON.usage_details.total_schedules}`;
                        if (xhr.responseJSON.usage_details.schedules && xhr.responseJSON.usage_details.schedules.length > 0) {
                            errorMessage += '\n• Sample schedules:';
                            xhr.responseJSON.usage_details.schedules.forEach(function(schedule) {
                                errorMessage += `\n  - Schedule #${schedule.id}: ${schedule.program_name} (${schedule.current_stage}) - Created: ${schedule.created_at}`;
                            });
                        }
                    }
                }
                $('#editCentreModalError').removeClass('d-none').text(errorMessage);
            },
            complete: function() {
                $('#editBtnText').removeClass('d-none');
                $('#editBtnLoader').addClass('d-none');
            }
        });
    });

    // Delete Centre Buttons
    document.querySelectorAll('.delete-centre-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const centreId = this.dataset.centreId;
            const centreName = this.dataset.centreName;
            
            if (confirm(`Are you sure you want to delete the centre "${centreName}"?`)) {
                deleteCentre(centreId);
            }
        });
    });

    function deleteCentre(centreId) {
        $.ajax({
            url: `/admin/centres/${centreId}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(data) {
                if (data.success) {
                    showAlert('success', data.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    let errorMessage = data.message || 'An error occurred. Please try again.';
                    
                    // If there are usage details, show them in a more detailed alert
                    if (data.usage_details) {
                        errorMessage += '\n\nUsage Details:';
                        errorMessage += `\n• Total schedules using this centre: ${data.usage_details.total_schedules}`;
                        if (data.usage_details.schedules && data.usage_details.schedules.length > 0) {
                            errorMessage += '\n• Sample schedules:';
                            data.usage_details.schedules.forEach(function(schedule) {
                                errorMessage += `\n  - Schedule #${schedule.id}: ${schedule.program_name} (${schedule.current_stage}) - Created: ${schedule.created_at}`;
                            });
                        }
                    }
                    
                    showAlert('danger', errorMessage);
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = 'An error occurred. Please try again.';
                
                // Try to parse error response for more details
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                    
                    // If there are usage details in error response
                    if (xhr.responseJSON.usage_details) {
                        errorMessage += '\n\nUsage Details:';
                        errorMessage += `\n• Total schedules using this centre: ${xhr.responseJSON.usage_details.total_schedules}`;
                        if (xhr.responseJSON.usage_details.schedules && xhr.responseJSON.usage_details.schedules.length > 0) {
                            errorMessage += '\n• Sample schedules:';
                            xhr.responseJSON.usage_details.schedules.forEach(function(schedule) {
                                errorMessage += `\n  - Schedule #${schedule.id}: ${schedule.program_name} (${schedule.current_stage}) - Created: ${schedule.created_at}`;
                            });
                        }
                    }
                }
                
                showAlert('danger', errorMessage);
            }
        });
    }

    function showAlert(type, message) {
        const alertContainer = document.getElementById('alertContainer');
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        alertContainer.innerHTML = alertHtml;
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            const alert = alertContainer.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }

    function resetValidation() {
        document.querySelectorAll('.is-invalid').forEach(element => {
            element.classList.remove('is-invalid');
        });
        document.querySelectorAll('.invalid-feedback').forEach(element => {
            element.textContent = '';
        });
    }

    function showValidationErrors(errors, prefix = '') {
        // Note: dot notation in error keys may not match element IDs if field names use underscores
        Object.keys(errors).forEach(field => {
            const input = document.getElementById(prefix + field);
            const errorDiv = document.getElementById(prefix + field + '_error');
            if (input && errorDiv) {
                input.classList.add('is-invalid');
                errorDiv.textContent = errors[field][0];
            }
        });
    }
});
</script>
@endpush 