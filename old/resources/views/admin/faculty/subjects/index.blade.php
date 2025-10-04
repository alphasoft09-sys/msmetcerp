@extends('admin.layout')

@section('title', 'Faculty Subjects')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">My Subjects</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('admin.faculty.subjects.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle me-1"></i>Add New Subject
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Subjects
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $subjects->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-book-fill fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Active Subjects
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $subjects->where('is_active', true)->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-check-circle-fill fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Class Levels
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $subjects->pluck('class_level')->unique()->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-layers-fill fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            TC Code
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $user->from_tc }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-building-fill fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Subjects Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Subject List</h6>
        <div class="btn-group">
            <button type="button" class="btn btn-sm btn-outline-primary">Export</button>
            <button type="button" class="btn btn-sm btn-outline-secondary">Print</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>Class Level</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $subject)
                    <tr>
                        <td>
                            <span class="badge bg-primary">{{ $subject->code }}</span>
                        </td>
                        <td>
                            <strong>{{ $subject->name }}</strong>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $subject->class_level }}</span>
                        </td>
                        <td>
                            {{ Str::limit($subject->description, 50) }}
                        </td>
                        <td>
                            @if($subject->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="btn btn-outline-success" title="Edit Subject">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-outline-info" title="View Schedule">
                                    <i class="bi bi-calendar3"></i>
                                </button>
                                <button type="button" class="btn btn-outline-warning" title="View Students">
                                    <i class="bi bi-people"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            <div class="py-4">
                                <i class="bi bi-book text-muted" style="font-size: 3rem;"></i>
                                <h5 class="mt-3 text-muted">No subjects found</h5>
                                <p class="text-muted">You haven't created any subjects yet.</p>
                                <a href="{{ route('admin.faculty.subjects.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-1"></i>Create Your First Subject
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Class Level Distribution -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Subjects by Class Level</h6>
            </div>
            <div class="card-body">
                <canvas id="classLevelChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.faculty.subjects.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Add New Subject
                    </a>
                    <a href="{{ route('admin.faculty.schedules') }}" class="btn btn-success">
                        <i class="bi bi-calendar-plus me-2"></i>Manage Schedules
                    </a>
                    <a href="{{ route('admin.faculty.attendance') }}" class="btn btn-info">
                        <i class="bi bi-check2-square me-2"></i>Take Attendance
                    </a>
                    <a href="{{ route('admin.faculty.progress') }}" class="btn btn-warning">
                        <i class="bi bi-graph-up me-2"></i>View Progress
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Class Level Distribution Chart
    const classLevelCtx = document.getElementById('classLevelChart').getContext('2d');
    
    // Get class level data
    const classLevelData = @json($subjects->groupBy('class_level')->map->count());
    const labels = Object.keys(classLevelData);
    const data = Object.values(classLevelData);
    
    new Chart(classLevelCtx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endsection 