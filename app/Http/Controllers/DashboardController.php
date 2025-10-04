<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\StudentLogin;
use App\Models\Attendance;
use App\Models\ClassSchedule;
use App\Models\Subject;
use App\Models\FacultyMessage;
use App\Models\StudentProgress;
use App\Models\ExamSchedule;

class DashboardController extends Controller
{
    public function tcAdminDashboard()
    {
        try {
            $user = Auth::user();
            
            // Ensure user has TC Admin role
            if ($user->user_role !== 1) {
                \Log::warning('Unauthorized access attempt to TC Admin Dashboard by user: ' . $user->email);
                abort(403, 'Unauthorized access');
            }

            // Get basic statistics for TC Admin
            $totalStudents = StudentLogin::where('tc_code', $user->from_tc)->count();
            $recentStudents = StudentLogin::where('tc_code', $user->from_tc)
                ->latest()
                ->take(5)
                ->get();

            // Get exam schedule statistics
            $totalExams = ExamSchedule::where('tc_code', $user->from_tc)->count();
            $pendingExams = ExamSchedule::where('tc_code', $user->from_tc)
                ->whereIn('current_stage', ['faculty', 'exam_cell'])
                ->count();
            $approvedExams = ExamSchedule::where('tc_code', $user->from_tc)
                ->whereIn('current_stage', ['tc_admin', 'aa', 'completed'])
                ->count();

            // Get center statistics
            $totalCenters = \App\Models\TcCentre::where('tc_code', $user->from_tc)->count();

            // Get student distribution by class
            $studentDistribution = StudentLogin::where('tc_code', $user->from_tc)
                ->select('class', \DB::raw('count(*) as count'))
                ->groupBy('class')
                ->get();

            // Get monthly registration data for the last 6 months
            $monthlyData = [];
            $monthlyLabels = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = \Carbon\Carbon::now()->subMonths($i);
                $count = StudentLogin::where('tc_code', $user->from_tc)
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();
                $monthlyData[] = $count;
                $monthlyLabels[] = $date->format('M Y');
            }

            // Get recent exam schedules
            $recentExams = ExamSchedule::where('tc_code', $user->from_tc)
                ->with(['centre', 'faculty'])
                ->latest()
                ->take(10)
                ->get();

            // Get recent activities
            $recentActivities = collect();
            
            // Add recent student registrations
            $recentStudentRegistrations = StudentLogin::where('tc_code', $user->from_tc)
                ->where('created_at', '>=', now()->subDays(7))
                ->latest()
                ->take(5)
                ->get()
                ->map(function($student) {
                    return [
                        'type' => 'student_registration',
                        'title' => 'New Student Registered',
                        'description' => $student->name . ' - ' . ($student->class ?? 'Unknown Class'),
                        'time' => $student->created_at,
                        'badge' => 'Registered',
                        'badge_class' => 'bg-success'
                    ];
                });
            
            $recentActivities = $recentActivities->merge($recentStudentRegistrations);
            
            // Add recent exam schedule updates
            $recentExamUpdates = ExamSchedule::where('tc_code', $user->from_tc)
                ->where('updated_at', '>=', now()->subDays(7))
                ->with(['centre', 'faculty'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function($exam) {
                    return [
                        'type' => 'exam_schedule',
                        'title' => 'Exam Schedule Updated',
                        'description' => $exam->course_name . ' - ' . ($exam->centre->centre_name ?? 'Unknown Centre'),
                        'time' => $exam->updated_at,
                        'badge' => ucfirst($exam->current_stage),
                        'badge_class' => $this->getStageBadgeClass($exam->current_stage)
                    ];
                });
            
            $recentActivities = $recentActivities->merge($recentExamUpdates);
            
            // Sort by time and take top 5
            $recentActivities = $recentActivities->sortByDesc('time')->take(5);

            // Calculate performance metrics
            $activeStudents = $totalStudents; // All students are considered active
            $completionRate = $totalExams > 0 ? ($approvedExams / $totalExams) * 100 : 0;

            // Get student performance data
            $topStudents = StudentProgress::with('student')
                ->whereHas('student', function($query) use ($user) {
                    $query->where('tc_code', $user->from_tc);
                })
                ->orderBy('score', 'desc')
                ->take(5)
                ->get()
                ->map(function($progress) {
                    return [
                        'name' => $progress->student->name ?? 'Unknown Student',
                        'class' => $progress->student->class ?? 'N/A',
                        'score' => $progress->score ?? 0,
                        'performance' => $this->getPerformanceLevel($progress->score ?? 0)
                    ];
                });

            return view('admin.dashboards.tc-admin', compact(
                'user', 
                'totalStudents', 
                'recentStudents', 
                'studentDistribution',
                'monthlyData',
                'monthlyLabels',
                'totalExams',
                'pendingExams',
                'approvedExams',
                'totalCenters',
                'activeStudents',
                'completionRate',
                'recentExams',
                'recentActivities',
                'topStudents'
            ));
        } catch (\Exception $e) {
            \Log::error('TC Admin Dashboard error: ' . $e->getMessage());
            abort(500, 'Internal server error');
        }
    }

    public function tcHeadDashboard()
    {
        try {
        $user = Auth::user();
        
        // Ensure user has TC Head role
        if ($user->user_role !== 2) {
            abort(403, 'Unauthorized access');
        }

            // Get basic statistics
        $totalAdmins = User::where('user_role', 1)->count();
        $totalStudents = StudentLogin::count();
        $tcAdmins = User::where('user_role', 1)->get();

            // Get center statistics
            $totalCenters = \App\Models\TcCentre::count();
            $activeCenters = $totalCenters; // All centers are considered active since there's no is_active field

            // Get TC-wise performance data
            $tcPerformance = StudentLogin::selectRaw('tc_code, count(*) as student_count')
                ->groupBy('tc_code')
                ->orderBy('student_count', 'desc')
                ->get();

            // Get admin distribution by status
            $activeAdmins = $totalAdmins; // All admins are considered active since there's no is_active field
            $inactiveAdmins = 0; // No inactive admins since there's no is_active field

            // Get recent exam schedules for TC Head approval
            $pendingApprovals = ExamSchedule::where('current_stage', 'tc_admin')
                ->with(['centre', 'faculty'])
                ->latest()
                ->take(10)
                ->get();

            // Get recent activities
            $recentActivities = collect();
            
            // Add recent admin activities
            $recentAdminActivities = User::where('user_role', 1)
                ->where('updated_at', '>=', now()->subDays(7))
                ->latest()
                ->take(5)
                ->get()
                ->map(function($admin) {
                    return [
                        'type' => 'admin',
                        'title' => 'Admin Profile Updated',
                        'description' => $admin->name . ' - ' . ($admin->from_tc ?? 'Unknown TC'),
                        'time' => $admin->updated_at,
                        'badge' => 'Active', // All admins are considered active
                        'badge_class' => 'bg-success'
                    ];
                });
            
            $recentActivities = $recentActivities->merge($recentAdminActivities);
            
            // Add recent exam approvals
            $recentExamApprovals = ExamSchedule::where('current_stage', 'tc_admin')
                ->where('updated_at', '>=', now()->subDays(7))
                ->with(['centre', 'faculty'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function($exam) {
                    return [
                        'type' => 'exam_approval',
                        'title' => 'Exam Pending Approval',
                        'description' => $exam->course_name . ' - ' . ($exam->centre->centre_name ?? 'Unknown Centre'),
                        'time' => $exam->updated_at,
                        'badge' => 'Pending',
                        'badge_class' => 'bg-warning'
                    ];
                });
            
            $recentActivities = $recentActivities->merge($recentExamApprovals);
            
            // Sort by time and take top 5
            $recentActivities = $recentActivities->sortByDesc('time')->take(5);

            // Calculate overall performance score
            $totalExams = ExamSchedule::count();
            $completedExams = ExamSchedule::where('current_stage', 'completed')->count();
            $performanceScore = $totalExams > 0 ? ($completedExams / $totalExams) * 100 : 0;

            // Get admin management data
            $adminManagement = $tcAdmins->map(function($admin) {
                $studentCount = StudentLogin::where('tc_code', $admin->from_tc)->count();
                $examCount = ExamSchedule::where('tc_code', $admin->from_tc)->count();
                
                return [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'email' => $admin->email,
                    'tc_code' => $admin->from_tc ?? 'N/A',
                    'status' => 'Active', // All admins are considered active
                    'students_managed' => $studentCount,
                    'exams_managed' => $examCount,
                    'last_active' => $admin->updated_at,
                    'status_badge_class' => 'bg-success'
                ];
            });

            return view('admin.dashboards.tc-head', compact(
                'user',
                'totalAdmins',
                'totalStudents',
                'tcAdmins',
                'totalCenters',
                'activeCenters',
                'tcPerformance',
                'activeAdmins',
                'inactiveAdmins',
                'pendingApprovals',
                'recentActivities',
                'performanceScore',
                'adminManagement'
            ));
        } catch (\Exception $e) {
            \Log::error('TC Head Dashboard error: ' . $e->getMessage());
            abort(500, 'Internal server error');
        }
    }

    public function examCellDashboard()
    {
        try {
        $user = Auth::user();
        
        // Ensure user has Exam Cell role
        if ($user->user_role !== 3) {
            abort(403, 'Unauthorized access');
        }

        // Get statistics for Exam Cell
        $totalStudents = StudentLogin::count();
        $studentsByClass = StudentLogin::selectRaw('class, count(*) as count')
            ->groupBy('class')
            ->get();

            // Get exam schedule data
            $today = now()->toDateString();
            $upcomingExams = ExamSchedule::with(['centre', 'faculty'])
                ->whereDate('exam_start_date', '>', $today)
                ->orderBy('exam_start_date')
                ->get();
            
            $recentExams = ExamSchedule::with(['centre', 'faculty'])
                ->whereDate('exam_start_date', '<=', $today)
                ->orderBy('exam_start_date', 'desc')
                ->take(10)
                ->get();

            // Get exam statistics
            $totalUpcomingExams = $upcomingExams->count();
            $totalCompletedExams = ExamSchedule::where('current_stage', 'completed')->count();
            $totalPendingExams = ExamSchedule::whereIn('current_stage', ['faculty', 'exam_cell', 'tc_admin', 'aa'])->count();

            // Get student performance data
            $studentPerformance = StudentProgress::with('student')
                ->orderBy('score', 'desc')
                ->take(10)
                ->get()
                ->map(function($progress) {
                    return [
                        'student_name' => $progress->student->name ?? 'Unknown Student',
                        'class' => $progress->student->class ?? 'N/A',
                        'tc_code' => $progress->student->tc_code ?? 'N/A',
                        'average_score' => $progress->score ?? 0,
                        'exams_taken' => StudentProgress::where('student_id', $progress->student_id)->count(),
                        'performance' => $this->getPerformanceLevel($progress->score ?? 0)
                    ];
                });

            // Get exam performance trends (last 6 months)
            $examTrends = ExamSchedule::where('exam_start_date', '>=', now()->subMonths(6))
                ->where('current_stage', 'completed')
                ->orderBy('exam_start_date')
                ->get()
                ->groupBy(function($exam) {
                    return $exam->exam_start_date->format('M Y');
                })
                ->map(function($monthExams) {
                    return $monthExams->count();
                });

            // Get recent activities
            $recentActivities = collect();
            
            // Add recent exam schedule updates
            $recentExamUpdates = ExamSchedule::where('updated_at', '>=', now()->subDays(7))
                ->with(['centre', 'faculty'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function($exam) {
                    return [
                        'type' => 'exam_schedule',
                        'title' => 'Exam Schedule Updated',
                        'description' => $exam->course_name . ' - ' . ($exam->centre->centre_name ?? 'Unknown Centre'),
                        'time' => $exam->updated_at,
                        'badge' => ucfirst($exam->current_stage),
                        'badge_class' => $this->getStageBadgeClass($exam->current_stage)
                    ];
                });
            
            $recentActivities = $recentActivities->merge($recentExamUpdates);
            
            // Add recent student progress updates
            $recentProgressUpdates = StudentProgress::where('updated_at', '>=', now()->subDays(7))
                ->with('student')
                ->latest()
                ->take(5)
                ->get()
                ->map(function($progress) {
                    return [
                        'type' => 'progress',
                        'title' => 'Student Progress Updated',
                        'description' => ($progress->student->name ?? 'Student') . ' - Score: ' . ($progress->score ?? 0) . '%',
                        'time' => $progress->updated_at,
                        'badge' => $this->getPerformanceLevel($progress->score ?? 0),
                        'badge_class' => $this->getPerformanceBadgeClass($progress->score ?? 0)
                    ];
                });
            
            $recentActivities = $recentActivities->merge($recentProgressUpdates);
            
            // Sort by time and take top 5
            $recentActivities = $recentActivities->sortByDesc('time')->take(5);

            // Calculate average performance
            $averagePerformance = StudentProgress::avg('score') ?? 0;

            return view('admin.dashboards.exam-cell', compact(
                'user',
                'totalStudents',
                'studentsByClass',
                'upcomingExams',
                'recentExams',
                'totalUpcomingExams',
                'totalCompletedExams',
                'totalPendingExams',
                'studentPerformance',
                'examTrends',
                'recentActivities',
                'averagePerformance'
            ));
        } catch (\Exception $e) {
            \Log::error('Exam Cell Dashboard error: ' . $e->getMessage());
            abort(500, 'Internal server error');
        }
    }

    public function aaDashboard()
    {
        $user = Auth::user();
        
        // Ensure user has AA role
        if ($user->user_role !== 4) {
            abort(403, 'Unauthorized access');
        }

        // TC-wise student count
        $studentsByTC = \App\Models\StudentLogin::selectRaw('tc_code, count(*) as student_count')
            ->groupBy('tc_code')
            ->get();

        // TC-wise faculty count
        $facultyByTC = \App\Models\User::where('user_role', 5)
            ->selectRaw('from_tc as tc_code, count(*) as faculty_count')
            ->groupBy('from_tc')
            ->get()
            ->keyBy('tc_code');

        // Total qualifications
        $totalQualifications = \App\Models\Qualification::count();
        // Total modules
        $totalModules = \App\Models\QualificationModule::count();
        // User role counts
        $totalFaculty = \App\Models\User::where('user_role', 5)->count();
        $totalHeads = \App\Models\User::where('user_role', 2)->count();
        $totalExamCells = \App\Models\User::where('user_role', 3)->count();

        // Total students (all TCs)
        $totalStudents = \App\Models\StudentLogin::count();
        // Total admins (role <= 3)
        $totalAdmins = \App\Models\User::where('user_role', '<=', 3)->count();

        // Exam schedule data for dashboard
        $today = \Carbon\Carbon::today()->toDateString();
        $todayExams = \App\Models\ExamSchedule::with(['centre'])
            ->whereDate('exam_start_date', $today)
            ->orderBy('exam_start_date')
            ->get();
        $upcomingExams = \App\Models\ExamSchedule::with(['centre'])
            ->whereDate('exam_start_date', '>', $today)
            ->orderBy('exam_start_date')
            ->get();

        return view('admin.dashboards.aa', compact(
            'user',
            'totalStudents',
            'totalAdmins',
            'studentsByTC',
            'facultyByTC',
            'totalQualifications',
            'totalModules',
            'totalFaculty',
            'totalHeads',
            'totalExamCells',
            'todayExams',
            'upcomingExams'
        ));
    }

    public function tcFacultyDashboard()
    {
        try {
            $user = Auth::user();
            
            // Ensure user has TC Faculty role
            if ($user->user_role !== 5) {
                \Log::warning('Unauthorized access attempt to TC Faculty Dashboard by user: ' . $user->email);
                abort(403, 'Unauthorized access');
            }

            // Get faculty-specific data
            $assignedStudents = StudentLogin::where('tc_code', $user->from_tc)
                ->orderBy('name')
                ->get();
            
            $totalStudents = $assignedStudents->count();
            $recentStudents = $assignedStudents->take(5);
            
            // Get real attendance data for today
            $today = now()->toDateString();
            $todayAttendance = Attendance::where('faculty_id', $user->id)
                ->where('date', $today)
                ->get();
            
            $presentCount = $todayAttendance->where('status', 'present')->count();
            $absentCount = $todayAttendance->where('status', 'absent')->count();
            
            $attendanceData = [
                'present' => $presentCount,
                'absent' => $absentCount,
                'total' => $totalStudents
            ];
            
            // Get attendance history for the last 7 days
            $attendanceHistory = Attendance::where('faculty_id', $user->id)
                ->where('date', '>=', now()->subDays(7))
                ->get()
                ->groupBy('date')
                ->map(function($dayAttendance) {
                    return [
                        'present' => $dayAttendance->where('status', 'present')->count(),
                        'absent' => $dayAttendance->where('status', 'absent')->count(),
                        'total' => $dayAttendance->count()
                    ];
                });
            
            // Get real academic schedule
            $academicSchedule = ClassSchedule::with('subject')
                ->where('faculty_id', $user->id)
                ->where('is_active', true)
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->get()
                ->map(function($schedule) {
                    return [
                        'day' => $schedule->day_of_week,
                        'subject' => $schedule->subject->name ?? 'Unknown Subject',
                        'time' => $schedule->time_range,
                        'class' => $schedule->class_level,
                        'schedule_id' => $schedule->id
                    ];
                })
                ->toArray();

            // Get subjects taught by this faculty
            $subjects = Subject::where('faculty_id', $user->id)
                ->where('is_active', true)
                ->get();

            // Get recent messages
            $recentMessages = FacultyMessage::where('faculty_id', $user->id)
                ->latest()
                ->take(5)
                ->get();

            // Get student progress data
            $studentProgress = StudentProgress::whereIn('student_id', $assignedStudents->pluck('id'))
                ->where('faculty_id', $user->id)
                ->latest()
                ->take(10)
                ->get();

            // Get recent activities (combine attendance, messages, and progress updates)
            $recentActivities = collect();
            
            // Add recent attendance updates
            $recentAttendance = Attendance::where('faculty_id', $user->id)
                ->where('date', '>=', now()->subDays(3))
                ->with('student')
                ->latest()
                ->take(5)
                ->get()
                ->map(function($attendance) {
                    return [
                        'type' => 'attendance',
                        'title' => 'Attendance Updated',
                        'description' => $attendance->student->name ?? 'Student' . ' - ' . ucfirst($attendance->status),
                        'time' => $attendance->created_at,
                        'badge' => $attendance->status === 'present' ? 'Present' : 'Absent',
                        'badge_class' => $attendance->status === 'present' ? 'bg-success' : 'bg-warning'
                    ];
                });
            
            $recentActivities = $recentActivities->merge($recentAttendance);
            
            // Add recent messages
            $recentMessageActivities = $recentMessages->map(function($message) {
                return [
                    'type' => 'message',
                    'title' => 'Message Sent',
                    'description' => 'Message sent to students',
                    'time' => $message->created_at,
                    'badge' => 'Sent',
                    'badge_class' => 'bg-info'
                ];
            });
            
            $recentActivities = $recentActivities->merge($recentMessageActivities);
            
            // Sort by time and take top 5
            $recentActivities = $recentActivities->sortByDesc('time')->take(5);

            // Calculate attendance rate for the week
            $weeklyAttendanceRate = 0;
            if ($attendanceHistory->count() > 0) {
                $totalPresent = $attendanceHistory->sum('present');
                $totalPossible = $attendanceHistory->sum('total');
                $weeklyAttendanceRate = $totalPossible > 0 ? ($totalPresent / $totalPossible) * 100 : 0;
            }

            return view('admin.dashboards.tc-faculty', compact(
                'user', 
                'assignedStudents', 
                'totalStudents', 
                'recentStudents', 
                'attendanceData', 
                'attendanceHistory',
                'academicSchedule',
                'subjects',
                'recentMessages',
                'studentProgress',
                'recentActivities',
                'weeklyAttendanceRate'
            ));
        } catch (\Exception $e) {
            \Log::error('TC Faculty Dashboard error: ' . $e->getMessage());
            abort(500, 'Internal server error');
        }
    }

    // Helper methods for performance levels and badge classes
    private function getPerformanceLevel($score)
    {
        if ($score >= 90) return 'Excellent';
        if ($score >= 80) return 'Good';
        if ($score >= 70) return 'Average';
        if ($score >= 60) return 'Below Average';
        return 'Poor';
    }

    private function getPerformanceBadgeClass($score)
    {
        if ($score >= 90) return 'bg-success';
        if ($score >= 80) return 'bg-primary';
        if ($score >= 70) return 'bg-warning';
        if ($score >= 60) return 'bg-info';
        return 'bg-danger';
    }

    private function getStageBadgeClass($stage)
    {
        switch ($stage) {
            case 'completed': return 'bg-success';
            case 'aa': return 'bg-primary';
            case 'tc_admin': return 'bg-info';
            case 'exam_cell': return 'bg-warning';
            case 'faculty': return 'bg-secondary';
            default: return 'bg-secondary';
        }
    }
}
