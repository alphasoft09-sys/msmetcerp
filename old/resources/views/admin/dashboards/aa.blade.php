@extends('admin.layout')

@section('title', 'Assessment Agency Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">AA Dashboard</h1>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card stats-card h-100 bg-primary text-white">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-uppercase mb-1">Total Students</div>
                <div class="h5 mb-0 font-weight-bold">{{ $totalStudents }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card stats-card h-100 bg-info text-white">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-uppercase mb-1">Total Faculty</div>
                <div class="h5 mb-0 font-weight-bold">{{ $totalFaculty }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card stats-card h-100 bg-success text-white">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-uppercase mb-1">Total Heads</div>
                <div class="h5 mb-0 font-weight-bold">{{ $totalHeads }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card stats-card h-100 bg-warning text-white">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-uppercase mb-1">Exam Cell Users</div>
                <div class="h5 mb-0 font-weight-bold">{{ $totalExamCells }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card stats-card h-100 bg-secondary text-white">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-uppercase mb-1">Qualifications</div>
                <div class="h5 mb-0 font-weight-bold">{{ $totalQualifications }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card stats-card h-100 bg-dark text-white">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-uppercase mb-1">Modules</div>
                <div class="h5 mb-0 font-weight-bold">{{ $totalModules }}</div>
            </div>
        </div>
    </div>
</div>

<!-- TC-wise Student & Faculty Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">TC-wise Student & Faculty Summary</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>TC Code</th>
                        <th>Student Count</th>
                        <th>Faculty Count</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($studentsByTC as $tc)
                    <tr>
                        <td>{{ $tc->tc_code }}</td>
                        <td>{{ $tc->student_count }}</td>
                        <td>{{ $facultyByTC[$tc->tc_code]->faculty_count ?? 0 }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">No data found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Ongoing & Upcoming Exams -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Today's Ongoing Exams</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>TC Code</th>
                        <th>Program Name</th>
                        <th>Centre Name</th>
                        <th>Status/Stage</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($todayExams as $exam)
                    <tr>
                        <td>{{ $exam->exam_start_date ? $exam->exam_start_date->format('d/m/Y') : '-' }}</td>
                        <td>{{ $exam->tc_code }}</td>
                        <td>{{ $exam->course_name }}</td>
                        <td>{{ $exam->centre->centre_name ?? '-' }}</td>
                        <td><span class="badge bg-info">{{ ucfirst($exam->current_stage) }}</span></td>
                        <td>
                            <a href="{{ url('admin/aa/exam-schedules/' . $exam->id . '/fullview') }}" class="btn btn-sm btn-outline-primary" target="_blank">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No ongoing exams today</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Upcoming Exams</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>TC Code</th>
                        <th>Program Name</th>
                        <th>Centre Name</th>
                        <th>Status/Stage</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($upcomingExams as $exam)
                    <tr>
                        <td>{{ $exam->exam_start_date ? $exam->exam_start_date->format('d/m/Y') : '-' }}</td>
                        <td>{{ $exam->tc_code }}</td>
                        <td>{{ $exam->course_name }}</td>
                        <td>{{ $exam->centre->centre_name ?? '-' }}</td>
                        <td><span class="badge bg-warning">{{ ucfirst($exam->current_stage) }}</span></td>
                        <td>
                            <a href="{{ url('admin/aa/exam-schedules/' . $exam->id . '/fullview') }}" class="btn btn-sm btn-outline-primary" target="_blank">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No upcoming exams</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Center Distribution Chart
    const centerCtx = document.getElementById('centerChart').getContext('2d');
    new Chart(centerCtx, {
        type: 'bar',
        data: {
            labels: @json($studentsByTC->pluck('tc_code')),
            datasets: [{
                label: 'Student Count',
                data: @json($studentsByTC->pluck('count')),
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

    // Compliance Pie Chart
    const compliancePieCtx = document.getElementById('compliancePieChart').getContext('2d');
    new Chart(compliancePieCtx, {
        type: 'doughnut',
        data: {
            labels: ['Compliant', 'Minor Issues', 'Major Issues', 'Under Review'],
            datasets: [{
                data: [75, 15, 5, 5],
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#dc3545',
                    '#17a2b8'
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