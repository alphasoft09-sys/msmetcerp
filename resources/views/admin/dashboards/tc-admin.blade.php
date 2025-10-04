@extends('admin.layout')

@section('title', 'TC Admin Dashboard')

@section('content')
<div class="fade-in-up">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>TC Admin Dashboard</h1>
                <p>Welcome back, {{ Auth::user()->name }}!</p>
            </div>
            <div class="btn-group">
                <!-- <a href="{{ route('admin.management.index') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-people me-1"></i>Manage Admins
                </a>
                <button type="button" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-download me-1"></i>Export
                </button>
                <button type="button" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-printer me-1"></i>Print
                </button> -->
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="dashboard-grid">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="stats-label">Total Students</div>
                        <div class="stats-number">{{ $totalStudents }}</div>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="stats-label text-success">Active Students</div>
                        <div class="stats-number text-success">{{ $activeStudents }}</div>
                    </div>
                    <div class="stats-icon" style="background: rgba(46, 125, 50, 0.2); color: var(--success-color);">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="stats-label text-warning">Pending Approvals</div>
                        <div class="stats-number text-warning">{{ $pendingExams }}</div>
                    </div>
                    <div class="stats-icon" style="background: rgba(245, 124, 0, 0.2); color: var(--warning-color);">
                        <i class="bi bi-clock-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="stats-label text-info">Completion Rate</div>
                        <div class="stats-number text-info">{{ number_format($completionRate, 1) }}%</div>
                    </div>
                    <div class="stats-icon" style="background: rgba(25, 118, 210, 0.2); color: var(--info-color);">
                        <i class="bi bi-graph-up"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Student Registration Trends</h5>
                </div>
                <div class="card-body">
                    <canvas id="registrationChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.management.index') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-people me-1"></i>Manage Admins
                        </a>
                        <a href="{{ url('admin/tc-admin/exam-schedules') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-calendar-event me-1"></i>View Exam Schedules
                        </a>
                        <a href="{{ url('admin/centres') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-building me-1"></i>Manage Centers
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Students Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Recent Students</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Class</th>
                            <th>Roll Number</th>
                            <th>Phone</th>
                            <th>Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentStudents as $student)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="stats-icon me-2" style="width: 2rem; height: 2rem; font-size: 0.875rem; background: var(--gray-100); color: var(--gray-600);">
                                        <i class="bi bi-person"></i>
                                    </div>
                                    <strong>{{ $student->name }}</strong>
                                </div>
                            </td>
                            <td>{{ $student->email }}</td>
                            <td>
                                <span class="badge bg-light">{{ $student->class ?? 'N/A' }}</span>
                            </td>
                            <td>{{ $student->roll_number ?? 'N/A' }}</td>
                            <td>{{ $student->phone ?? 'N/A' }}</td>
                            <td>
                                <small class="text-muted">{{ $student->created_at->format('M d, Y') }}</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                    <p class="mt-2 mb-0">No students found</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Exam Schedules -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Recent Exam Schedules</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Course Name</th>
                            <th>Centre</th>
                            <th>Exam Date</th>
                            <th>Faculty</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentExams as $exam)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="stats-icon me-2" style="width: 2rem; height: 2rem; font-size: 0.875rem; background: var(--gray-100); color: var(--gray-600);">
                                        <i class="bi bi-calendar-event"></i>
                                    </div>
                                    <strong>{{ $exam->course_name }}</strong>
                                </div>
                            </td>
                            <td>{{ $exam->centre->centre_name ?? 'N/A' }}</td>
                            <td>{{ $exam->exam_start_date ? $exam->exam_start_date->format('M d, Y') : 'N/A' }}</td>
                            <td>{{ $exam->faculty->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $exam->current_stage === 'completed' ? 'bg-success' : ($exam->current_stage === 'aa' ? 'bg-primary' : 'bg-warning') }}">
                                    {{ ucfirst($exam->current_stage) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ url('admin/tc-admin/exam-schedules/' . $exam->id . '/fullview') }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-calendar-x" style="font-size: 2rem;"></i>
                                    <p class="mt-2 mb-0">No exam schedules found</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Performing Students -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Top Performing Students</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Student Name</th>
                            <th>Class</th>
                            <th>Score</th>
                            <th>Performance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topStudents as $index => $student)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="stats-icon me-2" style="width: 2rem; height: 2rem; font-size: 0.875rem; background: var(--gray-100); color: var(--gray-600);">
                                        <i class="bi bi-person"></i>
                                    </div>
                                    <strong>{{ $student['name'] }}</strong>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light">{{ $student['class'] }}</span>
                            </td>
                            <td>{{ number_format($student['score'], 1) }}%</td>
                            <td>
                                <span class="badge {{ $student['score'] >= 90 ? 'bg-success' : ($student['score'] >= 80 ? 'bg-primary' : ($student['score'] >= 70 ? 'bg-warning' : 'bg-danger')) }}">
                                    {{ $student['performance'] }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-graph-down" style="font-size: 2rem;"></i>
                                    <p class="mt-2 mb-0">No performance data available</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Recent Activities</h5>
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
                    <i class="bi bi-activity" style="font-size: 2rem;"></i>
                    <p class="mt-2 mb-0">No recent activities</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard script loaded');
    
    // Registration Chart with real data
    const registrationCtx = document.getElementById('registrationChart');
    if (registrationCtx) {
        new Chart(registrationCtx, {
            type: 'line',
            data: {
                labels: @json($monthlyLabels),
                datasets: [{
                    label: 'Student Registrations',
                    data: @json($monthlyData),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Monthly Student Registration Trends'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection 