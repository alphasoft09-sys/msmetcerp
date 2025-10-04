@if($modules->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th style="width: 60px;">#</th>
                    <th class="sortable" data-sort="module_name">
                        Module Name
                        <i class="bi bi-arrow-down-up sort-icon"></i>
                    </th>
                    <th class="sortable" data-sort="nos_code">
                        NOS Code
                        <i class="bi bi-arrow-down-up sort-icon"></i>
                    </th>
                    <th class="sortable" data-sort="is_optional">
                        Type
                        <i class="bi bi-arrow-down-up sort-icon"></i>
                    </th>
                    <th class="sortable" data-sort="hour">
                        Hours
                        <i class="bi bi-arrow-down-up sort-icon"></i>
                    </th>
                    <th class="sortable" data-sort="credit">
                        Credit
                        <i class="bi bi-arrow-down-up sort-icon"></i>
                    </th>
                    <th>Exam Type</th>
                    <th>Marks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($modules as $index => $module)
                <tr>
                    <td class="text-center">
                        <span class="badge bg-secondary">{{ $modules->firstItem() + $index }}</span>
                    </td>
                    <td>
                        <strong>{{ $module->module_name }}</strong>
                    </td>
                    <td>{{ $module->nos_code }}</td>
                    <td>
                        <span class="badge {{ $module->is_optional ? 'bg-warning' : 'bg-success' }}">
                            {{ $module->is_optional ? 'Optional' : 'Mandatory' }}
                        </span>
                    </td>
                    <td>{{ $module->hour }}</td>
                    <td>{{ $module->credit }}</td>
                    <td>
                        <div class="d-flex flex-column gap-1">
                            @if($module->is_theory)
                                <span class="badge bg-primary">Theory</span>
                            @endif
                            @if($module->is_practical)
                                <span class="badge bg-success">Practical</span>
                            @endif
                            @if($module->is_viva)
                                <span class="badge bg-warning">Viva</span>
                            @endif
                            @if(!$module->is_theory && !$module->is_practical && !$module->is_viva)
                                <span class="badge bg-secondary">Not Set</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if($module->full_mark && $module->pass_mark)
                            <div class="d-flex flex-column">
                                <small class="text-muted">Full: {{ $module->full_mark }}</small>
                                <small class="text-muted">Pass: {{ $module->pass_mark }}</small>
                            </div>
                        @else
                            <span class="text-muted">Not Set</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-warning edit-module-btn"
                                    data-module="{{ json_encode($module) }}">
                                <i class="bi bi-pencil me-1"></i>
                                Edit
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-module-btn"
                                    data-module-id="{{ $module->id }}"
                                    data-module-name="{{ $module->module_name }}">
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
        <i class="bi bi-puzzle display-1 text-muted"></i>
        <h4 class="mt-3 text-muted">No Modules Found</h4>
        <p class="text-muted">
            @if(request('search') || request('is_optional') || request('hours_min') || request('hours_max') || request('credit_min') || request('credit_max') || request('qualifications_count'))
                Try adjusting your search criteria.
            @else
                Start by adding your first module.
            @endif
        </p>
        @if(!request('search') && !request('is_optional') && !request('hours_min') && !request('hours_max') && !request('credit_min') && !request('credit_max') && !request('qualifications_count'))
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModuleModal">
                <i class="bi bi-plus-circle me-2"></i>
                Add First Module
            </button>
        @endif
    </div>
@endif 