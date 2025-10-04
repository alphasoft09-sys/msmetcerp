@extends('admin.layout')

@section('title', 'TC Head Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">TC Head Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">Export Report</button>
            <button type="button" class="btn btn-sm btn-outline-secondary">Generate Summary</button>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white-50 text-uppercase mb-1">
                            Total TC Admins
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-white">{{ $totalAdmins }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-person-badge-fill fa-2x text-white-50"></i>
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
                            Total Students
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalStudents }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-people-fill fa-2x text-success"></i>
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
                            Active Centers
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeCenters }}/{{ $totalCenters }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-building-fill fa-2x text-warning"></i>
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
                            Performance Score
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($performanceScore, 1) }}%</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-graph-up fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Center Performance Overview</h6>
            </div>
            <div class="card-body">
                <canvas id="performanceChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Admin Distribution</h6>
            </div>
            <div class="card-body">
                <canvas id="adminPieChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- TC Admins Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">TC Admins Management</h6>
        <button class="btn btn-primary btn-sm">Add New Admin</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>TC Code</th>
                        <th>Status</th>
                        <th>Students Managed</th>
                        <th>Last Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($adminManagement as $admin)
                    <tr>
                        <td>{{ $admin['name'] }}</td>
                        <td>{{ $admin['email'] }}</td>
                        <td>{{ $admin['tc_code'] }}</td>
                        <td>
                            <span class="badge {{ $admin['status_badge_class'] }}">{{ $admin['status'] }}</span>
                        </td>
                        <td>{{ $admin['students_managed'] }}</td>
                        <td>{{ $admin['last_active']->format('M d, Y H:i') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-outline-warning" title="Edit Admin">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-outline-info" title="View Performance">
                                    <i class="bi bi-graph-up"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No TC Admins found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pending Approvals -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Pending Exam Approvals</h6>
        <a href="{{ url('admin/exam-schedules') }}" class="btn btn-primary btn-sm">View All</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>Centre</th>
                        <th>Exam Date</th>
                        <th>Faculty</th>
                        <th>Students</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingApprovals as $exam)
                    <tr>
                        <td>{{ $exam->course_name }}</td>
                        <td>{{ $exam->centre->centre_name ?? 'N/A' }}</td>
                        <td>{{ $exam->exam_start_date ? $exam->exam_start_date->format('d/m/Y') : 'N/A' }}</td>
                        <td>{{ $exam->faculty->name ?? 'N/A' }}</td>
                        <td>N/A</td>
                        <td><span class="badge bg-warning">Pending Approval</span></td>
                        <td>
                            <a href="{{ url('admin/exam-schedules/' . $exam->id . '/fullview') }}" class="btn btn-sm btn-outline-primary">Review</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No pending approvals</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Recent Activities</h6>
    </div>
    <div class="card-body">
        <div class="list-group list-group-flush">
            @forelse($recentActivities as $activity)
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $activity['title'] }}</strong>
                    <br><small class="text-muted">{{ $activity['description'] }} - {{ $activity['time']->diffForHumans() }}</small>
                </div>
                <span class="badge {{ $activity['badge_class'] }} rounded-pill">{{ $activity['badge'] }}</span>
            </div>
            @empty
            <div class="list-group-item text-center text-muted">
                No recent activities
            </div>
            @endforelse
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Performance Chart with real data
    const performanceCtx = document.getElementById('performanceChart').getContext('2d');
    
    // Prepare real TC performance data
    const tcLabels = [];
    const tcData = [];
    
    @foreach($tcPerformance as $tc)
        tcLabels.push('{{ $tc->tc_code }}');
        tcData.push({{ $tc->student_count }});
    @endforeach
    
    new Chart(performanceCtx, {
        type: 'bar',
        data: {
            labels: tcLabels.length > 0 ? tcLabels : ['No Data'],
            datasets: [{
                label: 'Student Count',
                data: tcData.length > 0 ? tcData : [0],
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgb(75, 192, 192)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Admin Distribution Pie Chart with real data
    const adminPieCtx = document.getElementById('adminPieChart').getContext('2d');
    
    new Chart(adminPieCtx, {
        type: 'doughnut',
        data: {
            labels: ['Active Admins', 'Inactive Admins'],
            datasets: [{
                data: [{{ $activeAdmins }}, {{ $inactiveAdmins }}],
                backgroundColor: [
                    '#28a745',
                    '#dc3545'
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