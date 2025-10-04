@extends('admin.layout')

@section('title', 'TC Faculty Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">TC Faculty Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">Export Report</button>
            <button type="button" class="btn btn-sm btn-outline-secondary">Print Schedule</button>
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
                            Assigned Students
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-white">{{ $totalStudents }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-people-fill fa-2x text-white-50"></i>
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
                            Weekly Attendance Rate
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($weeklyAttendanceRate, 1) }}%
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-check-circle-fill fa-2x text-success"></i>
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
                            Today's Attendance
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $attendanceData['present'] }}/{{ $attendanceData['total'] }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-clock-fill fa-2x text-warning"></i>
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
                            TC Code
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $user->from_tc }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-building-fill fa-2x text-info"></i>
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
                <h6 class="m-0 font-weight-bold text-primary">Student Attendance Overview</h6>
            </div>
            <div class="card-body">
                <canvas id="attendanceChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Subjects Taught</h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @forelse($subjects as $subject)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $subject->name }}</strong>
                            <br><small class="text-muted">{{ $subject->description ?? 'No description' }}</small>
                        </div>
                        <span class="badge bg-primary rounded-pill">{{ $subject->is_active ? 'Active' : 'Inactive' }}</span>
                    </div>
                    @empty
                    <div class="list-group-item text-center text-muted">
                        No subjects assigned yet
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Academic Schedule -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Academic Schedule</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Day</th>
                        <th>Subject</th>
                        <th>Time</th>
                        <th>Class</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($academicSchedule as $schedule)
                    <tr>
                        <td>{{ $schedule['day'] }}</td>
                        <td>{{ $schedule['subject'] }}</td>
                        <td>{{ $schedule['time'] }}</td>
                        <td>{{ $schedule['class'] }}</td>
                        <td>
                            <button class="btn btn-sm btn-primary">Take Attendance</button>
                            <button class="btn btn-sm btn-outline-secondary">View Class</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Assigned Students Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Assigned Students</h6>
        <div class="btn-group">
            <button type="button" class="btn btn-sm btn-outline-primary">Export List</button>
            <button type="button" class="btn btn-sm btn-outline-success">Send Message</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Class</th>
                        <th>Roll Number</th>
                        <th>Phone</th>
                        <th>Attendance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignedStudents as $student)
                    <tr>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->email }}</td>
                        <td>{{ $student->class ?? 'N/A' }}</td>
                        <td>{{ $student->roll_number ?? 'N/A' }}</td>
                        <td>{{ $student->phone ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-success">Present</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary" title="View Profile">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="btn btn-outline-success" title="Send Message">
                                    <i class="bi bi-chat"></i>
                                </button>
                                <button type="button" class="btn btn-outline-info" title="View Progress">
                                    <i class="bi bi-graph-up"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No students assigned yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-primary">Take Today's Attendance</button>
                    <button class="btn btn-success">Schedule New Class</button>
                    <button class="btn btn-info">Send Class Announcement</button>
                    <button class="btn btn-warning">Generate Progress Report</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card shadow">
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
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Attendance Chart with real data
    const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
    
    // Prepare real attendance data
    const attendanceLabels = [];
    const attendanceData = [];
    
    @foreach($attendanceHistory as $date => $data)
        attendanceLabels.push('{{ \Carbon\Carbon::parse($date)->format("M d") }}');
        attendanceData.push({{ $data['present'] }});
    @endforeach
    
    new Chart(attendanceCtx, {
        type: 'line',
        data: {
            labels: attendanceLabels.length > 0 ? attendanceLabels : ['No Data'],
            datasets: [{
                label: 'Present Students',
                data: attendanceData.length > 0 ? attendanceData : [0],
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: {{ $totalStudents > 0 ? $totalStudents : 10 }}
                }
            }
        }
    });
});
</script>
@endsection 