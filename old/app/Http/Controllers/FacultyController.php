<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\StudentLogin;
use App\Models\Subject;
use App\Models\ClassSchedule;
use App\Models\Attendance;
use App\Models\StudentProgress;
use App\Models\FacultyMessage;
use Illuminate\Support\Str;

class FacultyController extends Controller
{
    /**
     * Show faculty subjects
     */
    public function subjects()
    {
        $user = Auth::user();
        $subjects = Subject::where('faculty_id', $user->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.faculty.subjects.index', compact('subjects', 'user'));
    }

    /**
     * Show create subject form
     */
    public function createSubject()
    {
        $user = Auth::user();
        return view('admin.faculty.subjects.create', compact('user'));
    }

    /**
     * Store new subject
     */
    public function storeSubject(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects,code',
            'description' => 'nullable|string',
            'class_level' => 'required|string|max:50',
        ]);

        try {
            Subject::create([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'tc_code' => $user->from_tc,
                'faculty_id' => $user->id,
                'class_level' => $request->class_level,
                'is_active' => true,
            ]);

            return redirect()->route('admin.faculty.subjects')
                ->with('success', 'Subject created successfully!');
        } catch (\Exception $e) {
            \Log::error('Subject creation error: ' . $e->getMessage());
            return back()->with('error', 'Failed to create subject. Please try again.');
        }
    }

    /**
     * Show faculty schedules
     */
    public function schedules()
    {
        $user = Auth::user();
        $schedules = ClassSchedule::with('subject')
            ->where('faculty_id', $user->id)
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $subjects = Subject::where('faculty_id', $user->id)
            ->where('is_active', true)
            ->get();

        return view('admin.faculty.schedules.index', compact('schedules', 'subjects', 'user'));
    }

    /**
     * Store new schedule
     */
    public function storeSchedule(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'day_of_week' => 'required|string|max:20',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room_number' => 'nullable|string|max:50',
        ]);

        try {
            // Check if subject belongs to this faculty
            $subject = Subject::where('id', $request->subject_id)
                ->where('faculty_id', $user->id)
                ->first();

            if (!$subject) {
                return back()->with('error', 'Invalid subject selected.');
            }

            ClassSchedule::create([
                'subject_id' => $request->subject_id,
                'tc_code' => $user->from_tc,
                'class_level' => $subject->class_level,
                'day_of_week' => $request->day_of_week,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'room_number' => $request->room_number,
                'faculty_id' => $user->id,
                'is_active' => true,
            ]);

            return redirect()->route('admin.faculty.schedules')
                ->with('success', 'Schedule created successfully!');
        } catch (\Exception $e) {
            \Log::error('Schedule creation error: ' . $e->getMessage());
            return back()->with('error', 'Failed to create schedule. Please try again.');
        }
    }

    /**
     * Show attendance management
     */
    public function attendance()
    {
        $user = Auth::user();
        $today = now()->toDateString();
        
        // Get today's schedules
        $todaySchedules = ClassSchedule::with('subject')
            ->where('faculty_id', $user->id)
            ->where('is_active', true)
            ->where('day_of_week', now()->format('l'))
            ->orderBy('start_time')
            ->get();

        // Get students for this TC
        $students = StudentLogin::where('tc_code', $user->from_tc)
            ->orderBy('name')
            ->get();

        // Get today's attendance
        $todayAttendance = Attendance::where('faculty_id', $user->id)
            ->where('date', $today)
            ->get()
            ->keyBy('student_id');

        return view('admin.faculty.attendance.index', compact(
            'todaySchedules', 
            'students', 
            'todayAttendance', 
            'user'
        ));
    }

    /**
     * Take attendance for a specific subject
     */
    public function takeAttendance(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:student_logins,id',
            'attendance.*.status' => 'required|in:present,absent,late,excused',
        ]);

        try {
            // Check if subject belongs to this faculty
            $subject = Subject::where('id', $request->subject_id)
                ->where('faculty_id', $user->id)
                ->first();

            if (!$subject) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid subject selected.'
                ], 400);
            }

            foreach ($request->attendance as $record) {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $record['student_id'],
                        'subject_id' => $request->subject_id,
                        'date' => $request->date,
                    ],
                    [
                        'faculty_id' => $user->id,
                        'tc_code' => $user->from_tc,
                        'status' => $record['status'],
                        'remarks' => $record['remarks'] ?? null,
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Attendance recorded successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Attendance recording error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to record attendance. Please try again.'
            ], 500);
        }
    }

    /**
     * Show student progress management
     */
    public function studentProgress()
    {
        $user = Auth::user();
        
        $students = StudentLogin::where('tc_code', $user->from_tc)
            ->orderBy('name')
            ->get();

        $subjects = Subject::where('faculty_id', $user->id)
            ->where('is_active', true)
            ->get();

        $progress = StudentProgress::with(['student', 'subject'])
            ->where('faculty_id', $user->id)
            ->orderBy('assessment_date', 'desc')
            ->get();

        return view('admin.faculty.progress.index', compact('students', 'subjects', 'progress', 'user'));
    }

    /**
     * Store student progress
     */
    public function storeProgress(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'student_id' => 'required|exists:student_logins,id',
            'subject_id' => 'required|exists:subjects,id',
            'assessment_type' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'score' => 'nullable|numeric|min:0',
            'max_score' => 'required|numeric|min:1',
            'assessment_date' => 'required|date',
            'comments' => 'nullable|string',
        ]);

        try {
            // Check if subject belongs to this faculty
            $subject = Subject::where('id', $request->subject_id)
                ->where('faculty_id', $user->id)
                ->first();

            if (!$subject) {
                return back()->with('error', 'Invalid subject selected.');
            }

            // Calculate grade
            $grade = null;
            if ($request->score !== null && $request->max_score > 0) {
                $percentage = ($request->score / $request->max_score) * 100;
                if ($percentage >= 90) $grade = 'A';
                elseif ($percentage >= 80) $grade = 'B';
                elseif ($percentage >= 70) $grade = 'C';
                elseif ($percentage >= 60) $grade = 'D';
                else $grade = 'F';
            }

            StudentProgress::create([
                'student_id' => $request->student_id,
                'subject_id' => $request->subject_id,
                'faculty_id' => $user->id,
                'tc_code' => $user->from_tc,
                'assessment_type' => $request->assessment_type,
                'title' => $request->title,
                'score' => $request->score,
                'max_score' => $request->max_score,
                'assessment_date' => $request->assessment_date,
                'comments' => $request->comments,
                'grade' => $grade,
            ]);

            return redirect()->route('admin.faculty.progress')
                ->with('success', 'Progress recorded successfully!');
        } catch (\Exception $e) {
            \Log::error('Progress recording error: ' . $e->getMessage());
            return back()->with('error', 'Failed to record progress. Please try again.');
        }
    }

    /**
     * Show messaging interface
     */
    public function messages()
    {
        $user = Auth::user();
        
        $students = StudentLogin::where('tc_code', $user->from_tc)
            ->orderBy('name')
            ->get();

        $messages = FacultyMessage::with(['student'])
            ->where('faculty_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.faculty.messages.index', compact('students', 'messages', 'user'));
    }

    /**
     * Send message
     */
    public function sendMessage(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'message_type' => 'required|in:individual,class,broadcast',
            'student_id' => 'required_if:message_type,individual|nullable|exists:student_logins,id',
            'target_class' => 'required_if:message_type,class|nullable|string|max:50',
        ]);

        try {
            if ($request->message_type === 'individual') {
                FacultyMessage::create([
                    'faculty_id' => $user->id,
                    'student_id' => $request->student_id,
                    'tc_code' => $user->from_tc,
                    'subject' => $request->subject,
                    'message' => $request->message,
                    'message_type' => 'individual',
                ]);
            } elseif ($request->message_type === 'class') {
                // Send to all students in the target class
                $classStudents = StudentLogin::where('tc_code', $user->from_tc)
                    ->where('class', $request->target_class)
                    ->get();

                foreach ($classStudents as $student) {
                    FacultyMessage::create([
                        'faculty_id' => $user->id,
                        'student_id' => $student->id,
                        'tc_code' => $user->from_tc,
                        'subject' => $request->subject,
                        'message' => $request->message,
                        'message_type' => 'class',
                        'target_class' => $request->target_class,
                    ]);
                }
            } else { // broadcast
                // Send to all students in the TC
                $allStudents = StudentLogin::where('tc_code', $user->from_tc)->get();

                foreach ($allStudents as $student) {
                    FacultyMessage::create([
                        'faculty_id' => $user->id,
                        'student_id' => $student->id,
                        'tc_code' => $user->from_tc,
                        'subject' => $request->subject,
                        'message' => $request->message,
                        'message_type' => 'broadcast',
                    ]);
                }
            }

            return redirect()->route('admin.faculty.messages')
                ->with('success', 'Message sent successfully!');
        } catch (\Exception $e) {
            \Log::error('Message sending error: ' . $e->getMessage());
            return back()->with('error', 'Failed to send message. Please try again.');
        }
    }
}
