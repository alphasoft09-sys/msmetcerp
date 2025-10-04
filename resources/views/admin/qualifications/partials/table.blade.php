@if($qualifications->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th style="width: 60px;">#</th>
                    <th class="sortable" data-sort="qf_name">
                        Qualification Name
                        <i class="bi bi-arrow-down-up sort-icon"></i>
                    </th>
                    <th class="sortable" data-sort="nqr_no">
                        NQR Number
                        <i class="bi bi-arrow-down-up sort-icon"></i>
                    </th>
                    <th class="sortable" data-sort="sector">
                        Sector
                        <i class="bi bi-arrow-down-up sort-icon"></i>
                    </th>
                    <th class="sortable" data-sort="level">
                        Level
                        <i class="bi bi-arrow-down-up sort-icon"></i>
                    </th>
                    <th class="sortable" data-sort="qf_type">
                        Type
                        <i class="bi bi-arrow-down-up sort-icon"></i>
                    </th>
                    <th class="sortable" data-sort="qf_total_hour">
                        Total Hours
                        <i class="bi bi-arrow-down-up sort-icon"></i>
                    </th>
                    <th>Modules</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($qualifications as $index => $qualification)
                <tr>
                    <td class="text-center">
                        <span class="badge bg-secondary">{{ $qualifications->firstItem() + $index }}</span>
                    </td>
                    <td>
                        <strong>{{ $qualification->qf_name }}</strong>
                    </td>
                    <td>{{ $qualification->nqr_no }}</td>
                    <td>{{ $qualification->sector }}</td>
                    <td>{{ $qualification->level }}</td>
                    <td>{{ $qualification->qf_type }}</td>
                    <td>{{ $qualification->qf_total_hour }}</td>
                    <td>
                        <span class="badge bg-primary">{{ $qualification->modules_count }} modules</span>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-info view-modules-btn" 
                                    data-qualification-id="{{ $qualification->id }}"
                                    data-qualification-name="{{ $qualification->qf_name }}">
                                <i class="bi bi-eye me-1"></i>
                                View Modules
                            </button>
                            @if($user->user_role === 4) {{-- Only show mapping actions for Assessment Agency --}}
                            <button type="button" class="btn btn-sm btn-outline-success map-modules-btn"
                                    data-qualification-id="{{ $qualification->id }}"
                                    data-qualification-name="{{ $qualification->qf_name }}">
                                <i class="bi bi-link me-1"></i>
                                Map Modules
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-warning edit-qualification-btn"
                                    data-qualification="{{ json_encode($qualification) }}">
                                <i class="bi bi-pencil me-1"></i>
                                Edit
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-qualification-btn"
                                    data-qualification-id="{{ $qualification->id }}"
                                    data-qualification-name="{{ $qualification->qf_name }}">
                                <i class="bi bi-trash me-1"></i>
                                Delete
                            </button>
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
        <i class="bi bi-award display-1 text-muted"></i>
        <h4 class="mt-3 text-muted">No Qualifications Found</h4>
        <p class="text-muted">
            @if(request('search') || request('sector') || request('level') || request('qf_type') || request('modules_count'))
                Try adjusting your search criteria.
            @else
                @if($user->user_role === 4)
                    Start by adding your first qualification.
                @else
                    No qualifications are available at the moment.
                @endif
            @endif
        </p>
        @if(!request('search') && !request('sector') && !request('level') && !request('qf_type') && !request('modules_count') && $user->user_role === 4)
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQualificationModal">
                <i class="bi bi-plus-circle me-2"></i>
                Add First Qualification
            </button>
        @endif
    </div>
@endif 