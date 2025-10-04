@if($modules->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Module Name</th>
                    <th>NOS Code</th>
                    <th>Hours</th>
                    <th>Credits</th>
                    <th>Exam Type</th>
                    <th>Marks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($modules as $index => $module)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $module->module_name }}</strong>
                    </td>
                    <td>
                        <span class="badge bg-info">{{ $module->nos_code }}</span>
                    </td>
                    <td>
                        <span class="badge bg-primary">{{ $module->hour ?? 0 }} hours</span>
                    </td>
                    <td>
                        <span class="badge bg-success">{{ $module->credit ?? 0 }} credits</span>
                    </td>
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
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="mt-3">
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Total Modules:</strong> {{ $modules->count() }} modules mapped to this qualification.
        </div>
    </div>
@else
    <div class="text-center py-4">
        <i class="bi bi-puzzle display-4 text-muted"></i>
        <h5 class="mt-3 text-muted">No Modules Mapped</h5>
        <p class="text-muted">
            This qualification doesn't have any modules mapped to it yet.
            @if(Auth::user()->user_role === 4)
                <br>Click "Map Modules" to add modules to this qualification.
            @endif
        </p>
    </div>
@endif 