@extends('admin.layout')

@section('title', 'Exam Cell Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Exam Cell Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">Schedule Exam</button>
            <button type="button" class="btn btn-sm btn-outline-secondary">Generate Report</button>
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
                            Total Students
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
                            Upcoming Exams
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUpcomingExams }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-calendar-event-fill fa-2x text-success"></i>
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
                            Pending Exams
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPendingExams }}</div>
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
                            Average Performance
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($averagePerformance, 1) }}%</div>
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
                <h6 class="m-0 font-weight-bold text-primary">Exam Performance Trends</h6>
            </div>
            <div class="card-body">
                <canvas id="performanceChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Students by Class</h6>
            </div>
            <div class="card-body">
                <canvas id="classPieChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Exams Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Recent Exams</h6>
        <button class="btn btn-primary btn-sm">Schedule New Exam</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Exam Name</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Duration</th>
                        <th>Total Students</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentExams as $exam)
                    <tr>
                        <td>{{ $exam->course_name }}</td>
                        <td>{{ $exam->exam_type ?? 'N/A' }}</td>
                        <td>{{ $exam->exam_start_date ? $exam->exam_start_date->format('d/m/Y') : 'N/A' }}</td>
                        <td>{{ $exam->exam_end_date && $exam->exam_start_date ? $exam->exam_start_date->diffInHours($exam->exam_end_date) . ' hours' : 'N/A' }}</td>
                        <td>{{ $exam->students()->count() }}</td>
                        <td><span class="badge {{ $exam->current_stage === 'completed' ? 'bg-success' : ($exam->current_stage === 'aa' ? 'bg-primary' : 'bg-warning') }}">{{ ucfirst($exam->current_stage) }}</span></td>
                        <td>
                            <a href="{{ url('admin/exam-schedules/' . $exam->id . '/fullview') }}" class="btn btn-sm btn-outline-primary">View Details</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No recent exams found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Student Performance Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Top Performing Students</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Student Name</th>
                        <th>Class</th>
                        <th>TC Code</th>
                        <th>Average Score</th>
                        <th>Exams Taken</th>
                        <th>Performance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($studentPerformance as $index => $student)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $student['student_name'] }}</td>
                        <td>{{ $student['class'] }}</td>
                        <td>{{ $student['tc_code'] }}</td>
                        <td>{{ number_format($student['average_score'], 1) }}%</td>
                        <td>{{ $student['exams_taken'] }}</td>
                        <td><span class="badge {{ $student['average_score'] >= 90 ? 'bg-success' : ($student['average_score'] >= 80 ? 'bg-primary' : ($student['average_score'] >= 70 ? 'bg-warning' : 'bg-danger')) }}">{{ $student['performance'] }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No student performance data available</td>
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
    
    // Prepare real exam trends data
    const trendLabels = [];
    const trendData = [];
    
    @foreach($examTrends as $month => $count)
        trendLabels.push('{{ $month }}');
        trendData.push({{ $count }});
    @endforeach
    
    new Chart(performanceCtx, {
        type: 'line',
        data: {
            labels: trendLabels.length > 0 ? trendLabels : ['No Data'],
            datasets: [{
                label: 'Completed Exams',
                data: trendData.length > 0 ? trendData : [0],
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
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

    // Class Distribution Pie Chart with real data
    const classPieCtx = document.getElementById('classPieChart').getContext('2d');
    
    // Prepare real student class distribution data
    const classLabels = [];
    const classData = [];
    const classColors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'];
    
    @foreach($studentsByClass as $index => $class)
        classLabels.push('{{ $class->class ?? "Unknown" }}');
        classData.push({{ $class->count }});
    @endforeach
    
    new Chart(classPieCtx, {
        type: 'doughnut',
        data: {
            labels: classLabels.length > 0 ? classLabels : ['No Data'],
            datasets: [{
                data: classData.length > 0 ? classData : [0],
                backgroundColor: classColors.slice(0, classLabels.length)
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