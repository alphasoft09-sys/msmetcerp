<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\ExamSchedule;
use Illuminate\Support\Str;
use App\Models\ExamScheduleStudent;
use App\Models\ExamScheduleModule;
use App\Models\Qualification;
use App\Models\QualificationModule;
use App\Models\User;
use App\Services\FileNumberService;
use App\Mail\ExamScheduleSubmitted;
use App\Mail\ExamScheduleApprovedByExamCell;
use App\Mail\ExamScheduleApprovedByTCHead;
use App\Mail\ExamScheduleFinalApproved;
use Dompdf\Dompdf;
use Dompdf\Options;

class ExamScheduleController extends Controller
{
    /**
     * Display the exam schedule list for faculty
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Start with base query
        $query = ExamSchedule::query();
        
        // Apply filters based on request parameters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('tc_code')) {
            $query->where('tc_code', $request->tc_code);
        }
        
        if ($request->filled('exam_type')) {
            $query->where('exam_type', $request->exam_type);
        }
        
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('course_name', 'like', "%{$search}%")
                  ->orWhere('batch_code', 'like', "%{$search}%")
                  ->orWhere('exam_coordinator', 'like', "%{$search}%")
                  ->orWhere('tc_code', 'like', "%{$search}%");
            });
        }
        
        // Apply date range filters
        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                    break;
                case 'quarter':
                    $query->whereBetween('created_at', [now()->startOfQuarter(), now()->endOfQuarter()]);
                    break;
            }
        }
        
        // Check user role and set appropriate query with comprehensive visibility rules
        switch ($user->user_role) {
            case 5: // Faculty - can see their own schedules and schedules held/rejected by higher authorities from their TC
                $query->where(function($q) use ($user) {
                    $q->where('created_by', $user->id)
                      ->orWhere(function($subQ) use ($user) {
                          $subQ->where('tc_code', $user->from_tc)
                               ->whereIn('status', ['hold', 'rejected'])
                               ->where(function($actionQ) use ($user) {
                                   // Can see if held/rejected by themselves, Exam Cell (3), TC Admin (1), or TC Head (2)
                                   $actionQ->where('held_by', $user->id) // Schedules held by current user
                                   ->orWhere('rejected_by', $user->id) // Schedules rejected by current user
                                   ->orWhereIn('held_by', function($heldQ) use ($user) {
                                       $heldQ->select('id')->from('users')
                                             ->where('from_tc', $user->from_tc)
                                             ->whereIn('user_role', [1, 2, 3]);
                                   })
                                   ->orWhereIn('rejected_by', function($rejectedQ) use ($user) {
                                       $rejectedQ->select('id')->from('users')
                                                ->where('from_tc', $user->from_tc)
                                                ->whereIn('user_role', [1, 2, 3]);
                                   });
                               });
                      });
                });
                break;
                
            case 3: // Exam Cell - can see submitted schedules and schedules held/rejected by higher authorities from their TC
                $query->where('tc_code', $user->from_tc)
                      ->where(function($q) use ($user) {
                          $q->whereIn('status', ['submitted', 'exam_cell_approved', 'tc_admin_approved', 'received'])
                            ->orWhere(function($subQ) use ($user) {
                                $subQ->whereIn('status', ['hold', 'rejected'])
                                     ->where(function($actionQ) use ($user) {
                                         // Can see if held/rejected by themselves, TC Admin (1), TC Head (2), or AA (4)
                                         $actionQ->where('held_by', $user->id) // Schedules held by current user
                                         ->orWhere('rejected_by', $user->id) // Schedules rejected by current user
                                         ->orWhereIn('held_by', function($heldQ) use ($user) {
                                             $heldQ->select('id')->from('users')
                                                   ->where(function($userQ) use ($user) {
                                                       $userQ->where('from_tc', $user->from_tc)
                                                             ->whereIn('user_role', [1, 2])
                                                             ->orWhere('user_role', 4); // AA can hold/reject any
                                                   });
                                         })
                                         ->orWhereIn('rejected_by', function($rejectedQ) use ($user) {
                                             $rejectedQ->select('id')->from('users')
                                                      ->where(function($userQ) use ($user) {
                                                          $userQ->where('from_tc', $user->from_tc)
                                                                ->whereIn('user_role', [1, 2])
                                                                ->orWhere('user_role', 4); // AA can hold/reject any
                                                      });
                                         });
                                     });
                            });
                      });
                break;
                
            case 1: // TC Admin - can see exam cell approved schedules and schedules held/rejected by higher authorities from their TC
                $query->where('tc_code', $user->from_tc)
                      ->where(function($q) use ($user) {
                          $q->whereIn('status', ['exam_cell_approved', 'tc_admin_approved', 'received'])
                            ->orWhere(function($subQ) use ($user) {
                                $subQ->whereIn('status', ['hold', 'rejected'])
                                     ->where(function($actionQ) use ($user) {
                                         // Can see if held/rejected by themselves, TC Head (2), or AA (4)
                                         $actionQ->where('held_by', $user->id) // Schedules held by current user
                                         ->orWhere('rejected_by', $user->id) // Schedules rejected by current user
                                         ->orWhereIn('held_by', function($heldQ) use ($user) {
                                             $heldQ->select('id')->from('users')
                                                   ->where(function($userQ) use ($user) {
                                                       $userQ->where('from_tc', $user->from_tc)
                                                             ->where('user_role', 2)
                                                             ->orWhere('user_role', 4); // AA can hold/reject any
                                                   });
                                         })
                                         ->orWhereIn('rejected_by', function($rejectedQ) use ($user) {
                                             $rejectedQ->select('id')->from('users')
                                                      ->where(function($userQ) use ($user) {
                                                          $userQ->where('from_tc', $user->from_tc)
                                                                ->where('user_role', 2)
                                                                ->orWhere('user_role', 4); // AA can hold/reject any
                                                      });
                                         });
                                     });
                            });
                      });
                break;
                
            case 2: // TC Head - can see exam cell approved schedules and schedules held/rejected by AA from their TC
                $query->where(function($q) use ($user) {
                    $q->where('tc_code', $user->from_tc)
                      ->whereIn('status', ['submitted', 'exam_cell_approved', 'tc_admin_approved', 'received'])
                      ->orWhere(function($subQ) use ($user) {
                          $subQ->whereIn('status', ['hold', 'rejected'])
                               ->where(function($actionQ) use ($user) {
                                   // Can see if held/rejected by themselves
                                   $actionQ->where('held_by', $user->id) // Schedules held by current user
                                   ->orWhere('rejected_by', $user->id) // Schedules rejected by current user
                                   ->orWhere(function($aaActionQ) use ($user) {
                                       // Can see AA actions on schedules from their TC
                                       $aaActionQ->where('tc_code', $user->from_tc)
                                                ->where(function($aaUserQ) {
                                                    $aaUserQ->whereIn('held_by', function($heldQ) {
                                                        $heldQ->select('id')->from('users')
                                                              ->where('user_role', 4); // AA can hold/reject any
                                                    })
                                                    ->orWhereIn('rejected_by', function($rejectedQ) {
                                                        $rejectedQ->select('id')->from('users')
                                                                 ->where('user_role', 4); // AA can hold/reject any
                                                    });
                                                });
                                   });
                               });
                      });
                });
                break;
                
            case 4: // Assessment Agency - can see TC approved schedules, completed Internal exams, and schedules they held/rejected
                $query->where(function($q) use ($user) {
                    $q->whereIn('status', ['tc_admin_approved', 'received'])
                      ->whereIn('current_stage', ['aa', 'completed'])
                      ->orWhere(function($subQ) use ($user) {
                          $subQ->whereIn('status', ['rejected', 'hold'])
                               ->where(function($actionQ) use ($user) {
                                   // Can see if held/rejected by themselves
                                   $actionQ->where('held_by', $user->id) // Schedules held by current AA user
                                   ->orWhere('rejected_by', $user->id) // Schedules rejected by current AA user
                                   ->orWhereIn('current_stage', ['aa', 'completed']); // Or any schedule in AA stage
                               });
                      });
                });
                break;
                
            default:
                abort(403, 'Unauthorized access');
        }
        
        // Order by created_at desc
        $query->with(['faculty', 'centre', 'heldByUser', 'rejectedByUser', 'approvedByUser'])->orderBy('created_at', 'desc');

        // Paginate for all roles except Assessment Agency, which gets all results
        if ($user->user_role === 4) {
            $examSchedules = $query->get();
        } else {
            $examSchedules = $query->paginate(15);
        }
        
        // For Assessment Agency, use the enhanced view
        if ($user->user_role === 4) {
            return view('admin.exam-schedules.aa-index', compact('examSchedules', 'user'));
        }
        
        return view('admin.exam-schedules.index', compact('examSchedules', 'user'));
    }

    /**
     * Show the form for creating a new exam schedule
     */
    public function create()
    {
        $user = Auth::user();
        
        // Only faculty (role 5) can access this
        if ($user->user_role !== 5) {
            abort(403, 'Unauthorized access');
        }

        $qualifications = Qualification::orderBy('qf_name')->get();
        $semesters = range(1, 8); // Assuming max 8 semesters

        return view('admin.exam-schedules.create', compact('qualifications', 'semesters', 'user'));
    }

    /**
     * Store a newly created exam schedule
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Only faculty (role 5) can access this
            if ($user->user_role !== 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $request->validate([
                'course_name' => 'required|string|max:255',
                'batch_code' => 'required|string|max:10|regex:/^[A-Za-z0-9]+$/',
                'semester' => 'required|string|max:10|regex:/^[1-8]$/',
                'exam_type' => 'required|in:Internal,Final,Special Final',
                'exam_start_date' => 'required|date|after_or_equal:today',
                'exam_end_date' => 'required|date|after_or_equal:exam_start_date',
                'program_number' => 'required|string|max:255|regex:/^[A-Z0-9\-\/]+$/',
                'centre_id' => 'required|exists:tc_centres,id',
                'students' => 'required|array|min:1',
                'students.*.student_roll_no' => 'required|string|max:50',
                'students.*.is_selected' => 'boolean',
                'modules' => 'required|array|min:1',
                'modules.*.nos_code' => 'required|string|max:50',
                'modules.*.is_theory' => 'boolean',
                'modules.*.venue' => 'required|string|max:255',
                'modules.*.invigilator' => 'required|string|max:255',
                'modules.*.exam_date' => 'required|date|after_or_equal:exam_start_date|before_or_equal:exam_end_date',
                'modules.*.start_time' => 'required|date_format:H:i',
                'modules.*.end_time' => 'required|date_format:H:i|after:modules.*.start_time',
                'terms_accepted' => 'required|boolean|accepted',
            ], [
                'course_name.required' => 'Please select a course/qualification.',
                'course_name.max' => 'Course name cannot exceed 255 characters.',
                'batch_code.required' => 'Batch code is required.',
                'batch_code.max' => 'Batch code cannot exceed 10 characters.',
                'batch_code.regex' => 'Batch code can contain only letters and numbers (e.g., BATCH2024).',
                'semester.required' => 'Please select a semester.',
                'semester.regex' => 'Semester must be a number between 1 and 8.',
                'exam_type.required' => 'Please select an exam type.',
                'exam_type.in' => 'Exam type must be Internal, Final, or Special Final.',
                'exam_start_date.required' => 'Exam start date is required.',
                'exam_start_date.after_or_equal' => 'Exam start date must be today or a future date.',
                'exam_end_date.required' => 'Exam end date is required.',
                'exam_end_date.after_or_equal' => 'Exam end date must be on or after the start date.',
                'program_number.required' => 'Program number is required.',
                'program_number.regex' => 'Program number can only contain letters, numbers, hyphens, and forward slashes.',
                'centre_id.required' => 'Please select a centre.',
                'centre_id.exists' => 'Selected centre is invalid.',
                'students.required' => 'At least one student must be selected.',
                'students.min' => 'At least one student must be selected.',
                'students.*.student_roll_no.required' => 'Student roll number is required.',
                'students.*.student_roll_no.max' => 'Student roll number cannot exceed 50 characters.',
                'modules.required' => 'At least one module must be selected.',
                'modules.min' => 'At least one module must be selected.',
                'modules.*.nos_code.required' => 'Module NOS code is required.',
                'modules.*.nos_code.max' => 'Module NOS code cannot exceed 50 characters.',
                'modules.*.venue.required' => 'Module venue is required.',
                'modules.*.venue.max' => 'Module venue cannot exceed 255 characters.',
                'modules.*.invigilator.required' => 'Module invigilator is required.',
                'modules.*.invigilator.max' => 'Module invigilator name cannot exceed 255 characters.',
                'modules.*.exam_date.required' => 'Module exam date is required.',
                'modules.*.exam_date.after_or_equal' => 'Module exam date must be on or after the exam start date.',
                'modules.*.exam_date.before_or_equal' => 'Module exam date must be on or before the exam end date.',
                'modules.*.start_time.required' => 'Module start time is required.',
                'modules.*.start_time.date_format' => 'Module start time must be in HH:MM format (e.g., 09:00).',
                'modules.*.end_time.required' => 'Module end time is required.',
                'modules.*.end_time.date_format' => 'Module end time must be in HH:MM format (e.g., 11:00).',
                'modules.*.end_time.after' => 'Module end time must be after the start time.',
                'terms_accepted.required' => 'You must accept the terms and conditions.',
                'terms_accepted.accepted' => 'You must accept the terms and conditions to proceed.',
            ]);

            // Handle file uploads
            $courseCompletionFile = null;
            $studentDetailsFile = null;

            if ($request->hasFile('course_completion_file')) {
                $courseCompletionFile = $request->file('course_completion_file')->store('exam-schedules/course-completion', 'public');
            }

            if ($request->hasFile('student_details_file')) {
                $studentDetailsFile = $request->file('student_details_file')->store('exam-schedules/student-details', 'public');
            }

            // Create exam schedule
            $examSchedule = ExamSchedule::create([
                'created_by' => $user->id,
                'tc_code' => $user->from_tc,
                'course_name' => $request->course_name,
                'batch_code' => $request->batch_code,
                'semester' => $request->semester,
                'exam_type' => $request->exam_type,
                'exam_coordinator' => $user->name,
                'exam_start_date' => $request->exam_start_date,
                'exam_end_date' => $request->exam_end_date,
                'program_number' => $request->program_number,
                'centre_id' => $request->centre_id,
                'status' => 'draft',
                'current_stage' => 'faculty',
                'course_completion_file' => $courseCompletionFile,
                'student_details_file' => $studentDetailsFile,
                'terms_accepted' => $request->terms_accepted,
            ]);

            // Create student records
            $studentRollNumbers = [];
            foreach ($request->students as $student) {
                if (isset($student['student_roll_no']) && !empty($student['student_roll_no'])) {
                    $studentRollNumbers[] = $student['student_roll_no'];
                }
            }
            
            // Create single student record with all roll numbers
            if (!empty($studentRollNumbers)) {
                ExamScheduleStudent::create([
                    'exam_schedule_id' => $examSchedule->id,
                    'student_roll_numbers' => $studentRollNumbers,
                ]);
            }

            // Create module records
            foreach ($request->modules as $module) {
                ExamScheduleModule::create([
                    'exam_schedule_id' => $examSchedule->id,
                    'nos_code' => $module['nos_code'],
                    'is_theory' => $module['is_theory'] ?? true,
                    'venue' => $module['venue'],
                    'invigilator' => $module['invigilator'],
                    'exam_date' => $module['exam_date'],
                    'start_time' => $module['start_time'],
                    'end_time' => $module['end_time'],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Exam schedule created successfully',
                'exam_schedule_id' => $examSchedule->id
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Exam Schedule Validation Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Please check the form and try again.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Exam Schedule Store Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create exam schedule. Please try again.'
            ], 500);
        }
    }

    /**
     * Display the specified exam schedule
     */
    public function show($id)
    {
        $user = Auth::user();
        
        // If user is not authenticated, redirect to login
        if (!$user) {
            return redirect()->route('admin.login')->with('error', 'Please login to access this page.');
        }
        
        // Check access based on role
        $examSchedule = ExamSchedule::with(['students', 'modules', 'faculty', 'qualification', 'centre'])->findOrFail($id);
        
        // Check access permissions based on user role and status
        switch ($user->user_role) {
            case 5: // Faculty can only see their own schedules (all statuses)
                if ($examSchedule->created_by !== $user->id) {
                    return redirect()->route('admin.login')->with('error', 'You do not have permission to access this exam schedule.');
                }
                break;
                
            case 3: // Exam Cell can see submitted and approved schedules from their TC
                if ($examSchedule->tc_code !== $user->from_tc) {
                    return redirect()->route('admin.login')->with('error', 'You do not have permission to access this exam schedule.');
                }
                if (!in_array($examSchedule->status, ['submitted', 'exam_cell_approved', 'tc_admin_approved', 'received', 'rejected', 'hold'])) {
                    return redirect()->route('admin.login')->with('error', 'This exam schedule is not yet submitted for review.');
                }
                break;
                
            case 1: // TC Admin can see exam cell approved schedules from their TC
                if ($examSchedule->tc_code !== $user->from_tc) {
                    return redirect()->route('admin.login')->with('error', 'You do not have permission to access this exam schedule.');
                }
                if (!in_array($examSchedule->status, ['exam_cell_approved', 'tc_admin_approved', 'received', 'rejected', 'hold'])) {
                    return redirect()->route('admin.login')->with('error', 'This exam schedule is not yet approved by Exam Cell.');
                }
                break;
                
            case 2: // TC Head can see exam cell approved schedules and schedules held/rejected by AA from their TC
                if ($examSchedule->tc_code !== $user->from_tc) {
                    return redirect()->route('admin.login')->with('error', 'You do not have permission to access this exam schedule.');
                }
                if (!in_array($examSchedule->status, ['submitted', 'exam_cell_approved', 'tc_admin_approved', 'received', 'rejected', 'hold'])) {
                    return redirect()->route('admin.login')->with('error', 'This exam schedule is not yet submitted for review.');
                }
                break;
                
            case 4: // Assessment Agency can see TC approved schedules only
                if (!in_array($examSchedule->status, ['tc_admin_approved', 'received', 'rejected'])) {
                    return redirect()->route('admin.login')->with('error', 'This exam schedule is not yet approved by TC Admin/Head.');
                }
                break;
                
            default:
                return redirect()->route('admin.login')->with('error', 'You do not have permission to access this page.');
        }

        // Get header layout for the TC if exists
        $headerLayout = null;
        if ($examSchedule->tc_code) {
            $headerLayout = \App\Models\TcHeaderLayout::where('tc_id', $examSchedule->tc_code)->first();
        }

        // Get exam cell user and signature for the TC
        $examCellUser = null;
        $examCellSignature = null;
        if ($examSchedule->tc_code) {
            $examCellUser = \App\Models\User::where('user_role', 3)
                ->where('from_tc', $examSchedule->tc_code)
                ->with('profile')
                ->first();
            
            if ($examCellUser && $examCellUser->profile && $examCellUser->profile->signature) {
                $examCellSignature = '<img style="text-align:center;" src="' . asset('storage/' . $examCellUser->profile->signature) . '" alt="' . $examCellUser->name . '" width="auto" height="50px">';
            }
        }

        // Get TC Head user (Manager) and signature for the TC
        $managerUser = null;
        $managerSignature = null;
        if ($examSchedule->tc_code) {
            $managerUser = \App\Models\User::where('user_role', 2)
                ->where('from_tc', $examSchedule->tc_code)
                ->with('profile')
                ->first();
            
            if ($managerUser && $managerUser->profile && $managerUser->profile->signature) {
                $managerSignature = '<img style="text-align:center;" src="' . asset('storage/' . $managerUser->profile->signature) . '" alt="' . $managerUser->name . '" width="auto" height="50px">';
            }
        }

        // Get qualification modules mapping for module names
        $qualificationModules = [];
        if ($examSchedule->course_name) {
            $qualification = \App\Models\Qualification::where('qf_name', $examSchedule->course_name)->first();
            if ($qualification) {
                $qualificationModules = $qualification->modules()->get()->keyBy('nos_code');
            }
        }

        return view('admin.exam-schedules.show', compact('examSchedule', 'user', 'headerLayout', 'examCellUser', 'examCellSignature', 'managerUser', 'managerSignature', 'qualificationModules'));
    }

    /**
     * Display the full view of exam schedule (printable format)
     */
    public function fullview($id)
    {
        $user = Auth::user();
        
        // If user is not authenticated, redirect to login
        if (!$user) {
            return redirect()->route('admin.login')->with('error', 'Please login to access this page.');
        }
        
        // Check access based on role
        $examSchedule = ExamSchedule::with(['students', 'modules', 'faculty', 'qualification', 'centre'])->findOrFail($id);
        
        // Check access permissions based on user role and status
        switch ($user->user_role) {
            case 5: // Faculty can only see their own schedules (all statuses)
                if ($examSchedule->created_by !== $user->id) {
                    return redirect()->route('admin.login')->with('error', 'You do not have permission to access this exam schedule.');
                }
                break;
                
            case 3: // Exam Cell can see submitted and approved schedules from their TC
                if ($examSchedule->tc_code !== $user->from_tc) {
                    return redirect()->route('admin.login')->with('error', 'You do not have permission to access this exam schedule.');
                }
                if (!in_array($examSchedule->status, ['submitted', 'exam_cell_approved', 'tc_admin_approved', 'received', 'rejected', 'hold'])) {
                    return redirect()->route('admin.login')->with('error', 'This exam schedule is not yet submitted for review.');
                }
                break;
                
            case 1: // TC Admin can see exam cell approved schedules from their TC
                if ($examSchedule->tc_code !== $user->from_tc) {
                    return redirect()->route('admin.login')->with('error', 'You do not have permission to access this exam schedule.');
                }
                if (!in_array($examSchedule->status, ['exam_cell_approved', 'tc_admin_approved', 'received', 'rejected', 'hold'])) {
                    return redirect()->route('admin.login')->with('error', 'This exam schedule is not yet approved by Exam Cell.');
                }
                break;
                
            case 2: // TC Head can see exam cell approved schedules and schedules held/rejected by AA from their TC
                if ($examSchedule->tc_code !== $user->from_tc) {
                    return redirect()->route('admin.login')->with('error', 'You do not have permission to access this exam schedule.');
                }
                if (!in_array($examSchedule->status, ['submitted', 'exam_cell_approved', 'tc_admin_approved', 'received', 'rejected', 'hold'])) {
                    return redirect()->route('admin.login')->with('error', 'This exam schedule is not yet submitted for review.');
                }
                break;
                
            case 4: // Assessment Agency can see TC approved schedules only
                if (!in_array($examSchedule->status, ['tc_admin_approved', 'received', 'rejected'])) {
                    return redirect()->route('admin.login')->with('error', 'This exam schedule is not yet approved by TC Admin/Head.');
                }
                break;
                
            default:
                return redirect()->route('admin.login')->with('error', 'You do not have permission to access this page.');
        }

        // Get header layout for the TC if exists
        $headerLayout = null;
        if ($examSchedule->tc_code) {
            $headerLayout = \App\Models\TcHeaderLayout::where('tc_id', $examSchedule->tc_code)->first();
        }

        // Get exam cell user and signature for the TC
        $examCellUser = null;
        $examCellSignature = null;
        if ($examSchedule->tc_code) {
            $examCellUser = \App\Models\User::where('user_role', 3)
                ->where('from_tc', $examSchedule->tc_code)
                ->with('profile')
                ->first();
            
            if ($examCellUser && $examCellUser->profile && $examCellUser->profile->signature) {
                $examCellSignature = '<img style="text-align:center;" src="' . asset('storage/' . $examCellUser->profile->signature) . '" alt="' . $examCellUser->name . '" width="auto" height="50px">';
            }
        }

        // Get TC Head user (Manager) and signature for the TC
        $managerUser = null;
        $managerSignature = null;
        if ($examSchedule->tc_code) {
            $managerUser = \App\Models\User::where('user_role', 2)
                ->where('from_tc', $examSchedule->tc_code)
                ->with('profile')
                ->first();
            
            if ($managerUser && $managerUser->profile && $managerUser->profile->signature) {
                $managerSignature = '<img style="text-align:center;" src="' . asset('storage/' . $managerUser->profile->signature) . '" alt="' . $managerUser->name . '" width="auto" height="50px">';
            }
        }

        // Get Coordinator user and signature for the TC
        $coordinatorUser = null;
        $coordinatorSignature = null;
        if ($examSchedule->tc_code) {
            // First try to find by created_by
            $coordinatorUser = \App\Models\User::where('user_role', 5)
                ->where('from_tc', $examSchedule->tc_code)
                ->where('id', $examSchedule->created_by)
                ->with('profile')
                ->first();
            
            // If not found, try to find any coordinator from the same TC
            if (!$coordinatorUser) {
                $coordinatorUser = \App\Models\User::where('user_role', 5)
                    ->where('from_tc', $examSchedule->tc_code)
                    ->with('profile')
                    ->first();
            }
            

            
            if ($coordinatorUser && $coordinatorUser->profile && $coordinatorUser->profile->signature) {
                $signatureUrl = $coordinatorUser->profile->signature_url;
                

                
                $coordinatorSignature = '<img style="text-align:center;" src="' . $signatureUrl . '" alt="' . $coordinatorUser->name . '" width="auto" height="50px">';
            }
        }

        // Get qualification modules mapping for module names
        $qualificationModules = [];
        if ($examSchedule->course_name) {
            $qualification = \App\Models\Qualification::where('qf_name', $examSchedule->course_name)->first();
            if ($qualification) {
                $qualificationModules = $qualification->modules()->get()->keyBy('nos_code');
            }
        }

        // Log access for security monitoring
        \Log::info('Exam schedule fullview accessed', [
            'id' => $id,
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()
        ]);

        return view('admin.exam-schedules.fullview', compact('examSchedule', 'user', 'headerLayout', 'examCellUser', 'examCellSignature', 'managerUser', 'managerSignature', 'coordinatorUser', 'coordinatorSignature', 'qualificationModules'));
    }

    /**
     * Generate protected signature URL with session token
     */
    private function generateProtectedSignatureUrl($signaturePath, $type)
    {
        // Generate a unique token for this session
        $token = md5(session()->getId() . $signaturePath . $type . time());
        
        // Store token in session for validation
        session()->put("signature_token_{$type}", $token);
        session()->put("signature_path_{$type}", $signaturePath);
        
        // Return protected URL
        return route('admin.exam-schedules.protected-signature', [
            'type' => $type,
            'token' => $token
        ]);
    }

    /**
     * Serve protected signature with token validation
     */
    public function serveProtectedSignature($type, $token)
    {
        try {
            // Validate token from session
            $storedToken = session()->get("signature_token_{$type}");
            $storedPath = session()->get("signature_path_{$type}");
            
            if (!$storedToken || !$storedPath || $storedToken !== $token) {
                \Log::warning('Invalid signature access attempt', [
                    'type' => $type,
                    'token' => $token,
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]);
                abort(403, 'Invalid signature access');
            }
            
            // Check if user is authenticated and has proper access
            if (!auth()->check()) {
                abort(401, 'Unauthorized');
            }
            
            // Validate file exists
            $filePath = storage_path('app/public/' . $storedPath);
            if (!file_exists($filePath)) {
                abort(404, 'Signature not found');
            }
            
            // Determine content type
            $extension = strtolower(pathinfo($storedPath, PATHINFO_EXTENSION));
            $contentType = match($extension) {
                'jpg', 'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                default => 'application/octet-stream'
            };
            
            // Log successful access
            \Log::info('Protected signature accessed', [
                'type' => $type,
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
                'timestamp' => now()
            ]);
            
            // Serve file with protection headers
            return response()->file($filePath, [
                'Content-Type' => $contentType,
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => '0',
                'X-Content-Type-Options' => 'nosniff',
                'X-Frame-Options' => 'DENY',
                'Content-Disposition' => 'inline'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Protected signature serve error: ' . $e->getMessage());
            abort(500, 'Error serving signature');
        }
    }

    /**
     * Show the form for editing the specified exam schedule
     */
    public function edit($id)
    {
        $user = Auth::user();
        
        // Only faculty (role 5) can edit, and only their own schedules
        if ($user->user_role !== 5) {
            abort(403, 'Unauthorized access');
        }

        $examSchedule = ExamSchedule::with(['students', 'modules', 'centre'])->findOrFail($id);
        
        if ($examSchedule->created_by !== $user->id) {
            abort(403, 'Unauthorized access');
        }

        // Can only edit if status is draft or if exam cell requested reschedule
        if (!in_array($examSchedule->status, ['draft', 'hold'])) {
            abort(403, 'Cannot edit this exam schedule');
        }

        $qualifications = Qualification::orderBy('qf_name')->get();
        $semesters = range(1, 8);

        return view('admin.exam-schedules.edit', compact('examSchedule', 'qualifications', 'semesters', 'user'));
    }

    /**
     * Update the specified exam schedule
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            
            // Only faculty (role 5) can update
            if ($user->user_role !== 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $examSchedule = ExamSchedule::findOrFail($id);
            
            if ($examSchedule->created_by !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Can only update if status is draft or hold
            if (!in_array($examSchedule->status, ['draft', 'hold'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot edit this exam schedule'
                ], 403);
            }

            $request->validate([
                'course_name' => 'required|string|max:255',
                'batch_code' => 'required|string|max:10|regex:/^[A-Za-z0-9]+$/',
                'semester' => 'required|string|max:10|regex:/^[1-8]$/',
                'exam_type' => 'required|in:Internal,Final,Special Final',
                'exam_start_date' => 'required|date|after_or_equal:today',
                'exam_end_date' => 'required|date|after_or_equal:exam_start_date',
                'program_number' => 'required|string|max:255|regex:/^[A-Z0-9\-\/]+$/',
                'centre_id' => 'required|exists:tc_centres,id',
                'students' => 'required|array|min:1',
                'students.*.student_roll_no' => 'required|string|max:50',
                'students.*.is_selected' => 'boolean',
                'modules' => 'required|array|min:1',
                'modules.*.nos_code' => 'required|string|max:50',
                'modules.*.is_theory' => 'boolean',
                'modules.*.venue' => 'required|string|max:255',
                'modules.*.invigilator' => 'required|string|max:255',
                'modules.*.exam_date' => 'required|date|after_or_equal:exam_start_date|before_or_equal:exam_end_date',
                'modules.*.start_time' => 'required|date_format:H:i',
                'modules.*.end_time' => 'required|date_format:H:i|after:modules.*.start_time',
                'terms_accepted' => 'required|boolean|accepted',
            ], [
                'course_name.required' => 'Please select a course/qualification.',
                'course_name.max' => 'Course name cannot exceed 255 characters.',
                'batch_code.required' => 'Batch code is required.',
                'batch_code.max' => 'Batch code cannot exceed 10 characters.',
                'batch_code.regex' => 'Batch code can contain only letters and numbers (e.g., BATCH2024).',
                'semester.required' => 'Please select a semester.',
                'semester.regex' => 'Semester must be a number between 1 and 8.',
                'exam_type.required' => 'Please select an exam type.',
                'exam_type.in' => 'Exam type must be Internal, Final, or Special Final.',
                'exam_start_date.required' => 'Exam start date is required.',
                'exam_start_date.after_or_equal' => 'Exam start date must be today or a future date.',
                'exam_end_date.required' => 'Exam end date is required.',
                'exam_end_date.after_or_equal' => 'Exam end date must be on or after the start date.',
                'program_number.required' => 'Program number is required.',
                'program_number.regex' => 'Program number can only contain letters, numbers, hyphens, and forward slashes.',
                'centre_id.required' => 'Please select a centre.',
                'centre_id.exists' => 'Selected centre is invalid.',
                'students.required' => 'At least one student must be selected.',
                'students.min' => 'At least one student must be selected.',
                'students.*.student_roll_no.required' => 'Student roll number is required.',
                'students.*.student_roll_no.max' => 'Student roll number cannot exceed 50 characters.',
                'modules.required' => 'At least one module must be selected.',
                'modules.min' => 'At least one module must be selected.',
                'modules.*.nos_code.required' => 'Module NOS code is required.',
                'modules.*.nos_code.max' => 'Module NOS code cannot exceed 50 characters.',
                'modules.*.venue.required' => 'Module venue is required.',
                'modules.*.venue.max' => 'Module venue cannot exceed 255 characters.',
                'modules.*.invigilator.required' => 'Module invigilator is required.',
                'modules.*.invigilator.max' => 'Module invigilator name cannot exceed 255 characters.',
                'modules.*.exam_date.required' => 'Module exam date is required.',
                'modules.*.exam_date.after_or_equal' => 'Module exam date must be on or after the exam start date.',
                'modules.*.exam_date.before_or_equal' => 'Module exam date must be on or before the exam end date.',
                'modules.*.start_time.required' => 'Module start time is required.',
                'modules.*.start_time.date_format' => 'Module start time must be in HH:MM format (e.g., 09:00).',
                'modules.*.end_time.required' => 'Module end time is required.',
                'modules.*.end_time.date_format' => 'Module end time must be in HH:MM format (e.g., 11:00).',
                'modules.*.end_time.after' => 'Module end time must be after the start time.',
                'terms_accepted.required' => 'You must accept the terms and conditions.',
                'terms_accepted.accepted' => 'You must accept the terms and conditions to proceed.',
            ]);

            // Handle file uploads
            if ($request->hasFile('course_completion_file')) {
                // Delete old file if exists
                if ($examSchedule->course_completion_file) {
                    Storage::disk('public')->delete($examSchedule->course_completion_file);
                }
                $courseCompletionFile = $request->file('course_completion_file')->store('exam-schedules/course-completion', 'public');
                $examSchedule->course_completion_file = $courseCompletionFile;
            }

            if ($request->hasFile('student_details_file')) {
                // Delete old file if exists
                if ($examSchedule->student_details_file) {
                    Storage::disk('public')->delete($examSchedule->student_details_file);
                }
                $studentDetailsFile = $request->file('student_details_file')->store('exam-schedules/student-details', 'public');
                $examSchedule->student_details_file = $studentDetailsFile;
            }

            // Update exam schedule
            $examSchedule->update([
                'course_name' => $request->course_name,
                'batch_code' => $request->batch_code,
                'semester' => $request->semester,
                'exam_type' => $request->exam_type,
                'exam_start_date' => $request->exam_start_date,
                'exam_end_date' => $request->exam_end_date,
                'program_number' => $request->program_number,
                'centre_id' => $request->centre_id,
                'terms_accepted' => $request->terms_accepted,
            ]);

            // Update students
            $examSchedule->students()->delete();
            $studentRollNumbers = [];
            foreach ($request->students as $student) {
                if (isset($student['student_roll_no']) && !empty($student['student_roll_no'])) {
                    $studentRollNumbers[] = $student['student_roll_no'];
                }
            }
            
            // Create single student record with all roll numbers
            if (!empty($studentRollNumbers)) {
                ExamScheduleStudent::create([
                    'exam_schedule_id' => $examSchedule->id,
                    'student_roll_numbers' => $studentRollNumbers,
                ]);
            }

            // Update modules
            $examSchedule->modules()->delete();
            foreach ($request->modules as $module) {
                ExamScheduleModule::create([
                    'exam_schedule_id' => $examSchedule->id,
                    'nos_code' => $module['nos_code'],
                    'is_theory' => $module['is_theory'] ?? true,
                    'venue' => $module['venue'],
                    'invigilator' => $module['invigilator'],
                    'exam_date' => $module['exam_date'],
                    'start_time' => $module['start_time'],
                    'end_time' => $module['end_time'],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Exam schedule updated successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Exam Schedule Update Validation Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Please check the form and try again.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Exam Schedule Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update exam schedule. Please try again.'
            ], 500);
        }
    }

    /**
     * Submit exam schedule for approval
     */
    public function submit(Request $request, $id)
    {
        try {
            $user = Auth::user();
            
            // Only faculty (role 5) can submit
            if ($user->user_role !== 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $examSchedule = ExamSchedule::findOrFail($id);
            
            if ($examSchedule->created_by !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Can only submit if status is draft
            if ($examSchedule->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Can only submit draft schedules'
                ], 403);
            }

            $examSchedule->update([
                'status' => 'submitted',
                'current_stage' => 'exam_cell',
            ]);

            // Send email notification to Exam Cell
            try {
                $faculty = Auth::user();
                
                // Find Exam Cell user for this TC
                $examCellUser = User::where('user_role', 3)
                    ->where('from_tc', $examSchedule->tc_code)
                    ->first();
                
                if ($examCellUser) {
                    Mail::to($examCellUser->email)
                        ->send(new ExamScheduleSubmitted($examSchedule, $faculty, $examCellUser));
                    
                    \Log::info('Email sent to Exam Cell for exam schedule submission', [
                        'exam_schedule_id' => $examSchedule->id,
                        'exam_cell_email' => $examCellUser->email,
                        'faculty_email' => $faculty->email
                    ]);
                } else {
                    \Log::warning('No Exam Cell user found for TC: ' . $examSchedule->tc_code);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send email notification for exam schedule submission: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Exam schedule submitted for approval'
            ]);

        } catch (\Exception $e) {
            \Log::error('Exam Schedule Submit Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit exam schedule. Please try again.'
            ], 500);
        }
    }

    /**
     * Get students by program number
     */
    public function getStudentsByProgram(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Only faculty (role 5) can access this
            if ($user->user_role !== 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $request->validate([
                'program_number' => 'required|string'
            ]);

            $programNumber = $request->program_number;
            $tcCode = $user->from_tc;

            \Log::info('Fetching students for program number: ' . $programNumber . ' in TC: ' . $tcCode);

            // Check if TC student table exists
            if (!\App\Services\DynamicTableService::tableExists($tcCode)) {
                \Log::warning('Student table does not exist for TC: ' . $tcCode);
                return response()->json([
                    'success' => false,
                    'message' => 'Student table for this TC does not exist'
                ], 400);
            }

            // Get students from TC-specific table
            $tcStudent = \App\Models\TcStudent::forTc($tcCode);
            $tableName = $tcStudent->getTable();

            // Fetch students where ProgName matches the program number
            $students = \Illuminate\Support\Facades\DB::table($tableName)
                ->where(function($query) use ($programNumber) {
                    $query->where('ProgName', 'like', '%' . $programNumber . '%')
                          ->orWhere('ProgName', $programNumber)
                          ->orWhere('ProgName', 'like', '%P' . $programNumber . '%')
                          ->orWhere('ProgName', 'like', '%Program%' . $programNumber . '%');
                })
                ->select('RollNo as roll_no', 'Name as name', 'ProgName as program_name')
                ->orderBy('Name')
                ->get();

            \Log::info('Found ' . $students->count() . ' students for program number: ' . $programNumber . ' in TC table');
            \Log::info('Sample students: ' . $students->take(3)->toJson());

            // If no students found in TC table, also check StudentLogin table
            if ($students->isEmpty()) {
                \Log::info('No students found in TC table, checking StudentLogin table');
                
                $loginStudents = \App\Models\StudentLogin::where('tc_code', $tcCode)
                    ->where(function($query) use ($programNumber) {
                        $query->where('class', 'like', '%' . $programNumber . '%')
                              ->orWhere('class', $programNumber)
                              ->orWhere('class', 'like', '%P' . $programNumber . '%')
                              ->orWhere('class', 'like', '%Program%' . $programNumber . '%');
                    })
                    ->select('roll_number as roll_no', 'name', 'class as program_name')
                    ->orderBy('name')
                    ->get();

                \Log::info('Found ' . $loginStudents->count() . ' students in StudentLogin table');
                \Log::info('Sample login students: ' . $loginStudents->take(3)->toJson());

                $students = $loginStudents;
            }

            // If still no students found, show all students from the TC with a warning
            if ($students->isEmpty()) {
                \Log::warning('No students found for program number: ' . $programNumber . '. Showing all students from TC.');
                
                // Get all students from TC table
                $allStudents = \Illuminate\Support\Facades\DB::table($tableName)
                    ->select('RollNo as roll_no', 'Name as name', 'ProgName as program_name')
                    ->orderBy('Name')
                    ->limit(50) // Limit to prevent overwhelming results
                    ->get();

                if ($allStudents->isEmpty()) {
                    // Get all students from StudentLogin table
                    $allLoginStudents = \App\Models\StudentLogin::where('tc_code', $tcCode)
                        ->select('roll_number as roll_no', 'name', 'class as program_name')
                        ->orderBy('name')
                        ->limit(50)
                        ->get();
                    
                    $students = $allLoginStudents;
                } else {
                    $students = $allStudents;
                }

                \Log::info('Showing ' . $students->count() . ' total students from TC as fallback');
            }

            // Format the response
            $formattedStudents = $students->map(function($student) {
                return [
                    'roll_no' => $student->roll_no ?? 'N/A',
                    'name' => $student->name ?? 'N/A'
                ];
            })->toArray();

            $response = [
                'success' => true,
                'students' => $formattedStudents,
                'count' => count($formattedStudents)
            ];

            // Add warning if showing all students instead of specific program matches
            if (count($formattedStudents) > 0 && $students->first()->program_name && 
                !str_contains(strtolower($students->first()->program_name), strtolower($programNumber))) {
                $response['warning'] = 'No students found for Program Number "' . $programNumber . '". Showing all students from this TC.';
            }

            return response()->json($response);

        } catch (\Exception $e) {
            \Log::error('Get Students Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch students: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get modules by qualification
     */
    public function getModulesByQualification(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Only faculty (role 5) can access this
            if ($user->user_role !== 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $request->validate([
                'qualification_name' => 'required|string'
            ]);

            \Log::info('Fetching modules for qualification: ' . $request->qualification_name);

            $qualification = Qualification::where('qf_name', $request->qualification_name)->first();
            
            if (!$qualification) {
                \Log::warning('Qualification not found: ' . $request->qualification_name);
                return response()->json([
                    'success' => false,
                    'message' => 'Qualification not found'
                ], 404);
            }

            \Log::info('Qualification found: ' . $qualification->id);

            $modules = $qualification->modules;
            
            \Log::info('Modules count: ' . $modules->count());

            return response()->json([
                'success' => true,
                'modules' => $modules
            ]);

        } catch (\Exception $e) {
            \Log::error('Get Modules Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch modules'
            ], 500);
        }
    }

    /**
     * Approve exam schedule (for Exam Cell, TC Admin, TC Head, Assessment Agency)
     */
    public function approve(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $examSchedule = ExamSchedule::findOrFail($id);
            
            $request->validate([
                'comment' => 'nullable|string|max:1000'
            ]);

            // Debug information
            \Log::info('Approval attempt - User Role: ' . $user->user_role . ', Schedule Status: ' . $examSchedule->status . ', Held By: ' . $examSchedule->held_by . ', Current Stage: ' . $examSchedule->current_stage);

            $nextStage = null;
            $nextStatus = null;

            // Check if schedule is on hold
            if ($examSchedule->status === 'hold') {
                \Log::info('Schedule is on hold, checking if user can approve');
                
                // Only the person who put it on hold can approve it
                if ($examSchedule->held_by === null || $examSchedule->held_by !== $user->id) {
                    \Log::warning('User ' . $user->id . ' cannot approve schedule held by ' . ($examSchedule->held_by ?? 'null'));
                    return response()->json([
                        'success' => false,
                        'message' => 'Only the person who put this schedule on hold can approve it'
                    ], 403);
                }

                \Log::info('User can approve held schedule, determining next stage');

                // Determine next stage based on who is approving (the person who put it on hold)
                switch ($user->user_role) {
                    case 3: // Exam Cell - goes to TC Admin/TC Head
                        $nextStage = 'tc_admin';
                        $nextStatus = 'exam_cell_approved';
                        break;
                    case 1: // TC Admin - check exam type
                        if ($examSchedule->exam_type === 'Internal') {
                            $nextStage = 'completed';
                            $nextStatus = 'received';
                        } else {
                        $nextStage = 'aa';
                        $nextStatus = 'tc_admin_approved';
                        }
                        break;
                    case 2: // TC Head - check exam type
                        if ($examSchedule->exam_type === 'Internal') {
                            $nextStage = 'completed';
                            $nextStatus = 'received';
                        } else {
                        $nextStage = 'aa';
                        $nextStatus = 'tc_admin_approved';
                        }
                        break;
                    case 4: // Assessment Agency - final approval
                        // AA can approve any schedule they put on hold, regardless of current stage
                        $nextStage = 'aa';
                        $nextStatus = 'received';
                        break;
                    default:
                        return response()->json([
                            'success' => false,
                            'message' => 'Unauthorized access'
                        ], 403);
                }
            } else {
                \Log::info('Schedule is not on hold, using normal approval flow');
                
                // Normal approval flow (not on hold)
                switch ($user->user_role) {
                    case 3: // Exam Cell
                        if ($examSchedule->current_stage !== 'exam_cell') {
                            return response()->json([
                                'success' => false,
                                'message' => 'Invalid stage for approval'
                            ], 403);
                        }
                        $nextStage = 'tc_admin';
                        $nextStatus = 'exam_cell_approved';
                        break;
                        
                    case 1: // TC Admin
                        if ($examSchedule->current_stage !== 'tc_admin') {
                            return response()->json([
                                'success' => false,
                                'message' => 'Invalid stage for approval'
                            ], 403);
                        }
                        
                        // Check if it's an Internal exam - no Assessment Agency approval needed
                        if ($examSchedule->exam_type === 'Internal') {
                            $nextStage = 'completed';
                            $nextStatus = 'received';
                        } else {
                        $nextStage = 'aa';
                        $nextStatus = 'tc_admin_approved';
                        }
                        break;
                        
                    case 2: // TC Head
                        if ($examSchedule->current_stage !== 'tc_admin') {
                            return response()->json([
                                'success' => false,
                                'message' => 'Invalid stage for approval'
                            ], 403);
                        }
                        
                        // Check if it's an Internal exam - no Assessment Agency approval needed
                        if ($examSchedule->exam_type === 'Internal') {
                            $nextStage = 'completed';
                            $nextStatus = 'received';
                        } else {
                        $nextStage = 'aa';
                        $nextStatus = 'tc_admin_approved';
                        }
                        break;
                        
                    case 4: // Assessment Agency
                        // AA can approve schedules in 'aa' stage or any stage if they put it on hold
                        if ($examSchedule->current_stage !== 'aa' && $examSchedule->held_by !== $user->id) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Invalid stage for approval'
                            ], 403);
                        }
                        $nextStage = 'aa';
                        $nextStatus = 'received';
                        break;
                        
                    default:
                        return response()->json([
                            'success' => false,
                            'message' => 'Unauthorized access'
                        ], 403);
                }
            }

            \Log::info('Approval successful - Next Stage: ' . $nextStage . ', Next Status: ' . $nextStatus);

            // Prepare update data
            $updateData = [
                'status' => $nextStatus,
                'current_stage' => $nextStage,
                'held_by' => null, // Clear the held_by field when approved
                'approved_by' => $user->id, // Track who approved it
                'approved_at' => now(), // Track when it was approved
                'comment' => $request->comment,
            ];

            // Generate file number if Assessment Agency is approving (final approval)
if ($user->user_role === 4 && $nextStatus === 'received') {
    try {
        // Log file number request data for debugging
        \Log::info('Assessment Agency approval request data', [
            'exam_schedule_id' => $examSchedule->id,
            'has_file_number' => $request->has('file_number'),
            'file_number_value' => $request->input('file_number'),
            'approval_date_value' => $request->input('approval_date')
        ]);
        
        if ($request->has('file_number') && $request->file_number) {
            $fileNumber = $request->file_number;
            \Log::info('File number used from preview for exam schedule', [
                'exam_schedule_id' => $examSchedule->id,
                'file_no' => $fileNumber,
                'approval_date' => $request->approval_date ?? 'not provided'
            ]);
        } else {
            // Generate file number manually and ensure uniqueness
            $components = FileNumberService::getComponents($examSchedule); // Assumes this method exists
            $serial = (int) $components['serial_number'];
            $maxAttempts = 10;
            $attempt = 0;
            $fileNumber = null;

            do {
                $serialFormatted = str_pad($serial, 4, '0', STR_PAD_LEFT);
                $generatedFileNumber = 'FN' . $components['financial_year'] . $components['tc_short_code'] . $components['date_formatted'] . $serialFormatted;

                $exists = \App\Models\ExamSchedule::where('file_no', $generatedFileNumber)->exists();

                if (!$exists) {
                    $fileNumber = $generatedFileNumber;

                    \Log::info('File number generated successfully', [
                        'exam_schedule_id' => $examSchedule->id,
                        'file_number' => $fileNumber,
                        'components' => array_merge($components, [
                            'serial_number' => $serialFormatted
                        ])
                    ]);
                    break;
                }

                $serial++;
                $attempt++;
            } while ($attempt < $maxAttempts);

            if (!$fileNumber) {
                \Log::error('Failed to generate unique file number after ' . $maxAttempts . ' attempts', [
                    'exam_schedule_id' => $examSchedule->id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate a unique file number. Please try again later.'
                ], 500);
            }
        }

        // Add the file number to the update data
        $updateData['file_no'] = $fileNumber;

    } catch (\Exception $e) {
        \Log::error('File number generation error: ' . $e->getMessage(), [
            'exam_schedule_id' => $examSchedule->id
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Error generating file number. Please try again.'
        ], 500);
    }
}

// Final update after file number (if applicable) is included
$examSchedule->update($updateData);


            // Send email notifications based on approval stage
            try {
                $this->sendApprovalEmails($examSchedule, $user, $nextStatus, $nextStage);
            } catch (\Exception $e) {
                \Log::error('Failed to send approval emails: ' . $e->getMessage());
            }

            $message = 'Exam schedule approved successfully';
            if (isset($updateData['file_no'])) {
                $message .= '. File number assigned: ' . $updateData['file_no'];
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            \Log::error('Exam Schedule Approve Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve exam schedule. Please try again.'
            ], 500);
        }
    }

    /**
     * Reject exam schedule
     */
    public function reject(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $examSchedule = ExamSchedule::findOrFail($id);
            
            $request->validate([
                'comment' => 'required|string|max:1000'
            ]);

            $examSchedule->update([
                'status' => 'rejected',
                'comment' => $request->comment,
                'rejected_by' => $user->id,
                'rejected_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Exam schedule rejected'
            ]);

        } catch (\Exception $e) {
            \Log::error('Exam Schedule Reject Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject exam schedule. Please try again.'
            ], 500);
        }
    }

    /**
     * Hold exam schedule for reschedule
     */
    public function hold(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $examSchedule = ExamSchedule::findOrFail($id);
            
            $request->validate([
                'comment' => 'required|string|max:1000'
            ]);

            // Determine the stage to return to based on who is putting it on hold
            $returnStage = null;
            switch ($user->user_role) {
                case 3: // Exam Cell - return to faculty
                    $returnStage = 'faculty';
                    break;
                case 1: // TC Admin - return to exam_cell
                    $returnStage = 'exam_cell';
                    break;
                case 2: // TC Head - return to exam_cell
                    $returnStage = 'exam_cell';
                    break;
                case 4: // Assessment Agency - return to tc_admin
                    $returnStage = 'tc_admin';
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized access'
                    ], 403);
            }

            $examSchedule->update([
                'status' => 'hold',
                'current_stage' => $returnStage,
                'held_by' => $user->id, // Store the user ID who put it on hold
                'held_at' => now(),
                'comment' => $request->comment,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Exam schedule put on hold for reschedule'
            ]);

        } catch (\Exception $e) {
            \Log::error('Exam Schedule Hold Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to hold exam schedule. Please try again.'
            ], 500);
        }
    }

    /**
     * Download exam schedule as PDF
     */
    public function download($id)
    {
        try {
            $user = Auth::user();
            
            // Only Assessment Agency (role 4) can access this
            if ($user->user_role !== 4) {
                abort(403, 'Unauthorized access');
            }

            $examSchedule = ExamSchedule::with(['modules', 'centre', 'qualification'])->findOrFail($id);
            
            // Get header layout and other required variables (same as fullview method)
            $headerLayout = null;
            if ($examSchedule->tc_code) {
                $headerLayout = \App\Models\TcHeaderLayout::where('tc_id', $examSchedule->tc_code)->first();
            }
            
            $examCellUser = null;
            $managerUser = null;
            $coordinatorUser = null;
            $examCellSignature = null;
            $managerSignature = null;
            $coordinatorSignature = null;
            
            if($examSchedule->exam_cell_approved_by) {
                $examCellUser = \App\Models\User::with('profile')->find($examSchedule->exam_cell_approved_by);
            }
            
            if($examSchedule->tc_admin_approved_by) {
                $managerUser = \App\Models\User::with('profile')->find($examSchedule->tc_admin_approved_by);
            }
            
            // Get signatures if available
            if($examCellUser && $examCellUser->profile && $examCellUser->profile->signature) {
                $examCellSignature = '<img style="text-align:center;" src="' . $examCellUser->profile->signature_url . '" alt="' . $examCellUser->name . '" width="auto" height="50px">';
            }
            
            if($managerUser && $managerUser->profile && $managerUser->profile->signature) {
                $managerSignature = '<img style="text-align:center;" src="' . $managerUser->profile->signature_url . '" alt="' . $managerUser->name . '" width="auto" height="50px">';
            }
            
            // Get coordinator user and signature
            if($examSchedule->created_by) {
                $coordinatorUser = \App\Models\User::with('profile')->find($examSchedule->created_by);
                if($coordinatorUser && $coordinatorUser->profile && $coordinatorUser->profile->signature) {
                    $coordinatorSignature = '<img style="text-align:center;" src="' . $coordinatorUser->profile->signature_url . '" alt="' . $coordinatorUser->name . '" width="auto" height="50px">';
                }
            }
            
            // Get qualification modules mapping for module names
            $qualificationModules = [];
            if ($examSchedule->course_name) {
                $qualification = \App\Models\Qualification::where('qf_name', $examSchedule->course_name)->first();
                if ($qualification) {
                    $qualificationModules = $qualification->modules()->get()->keyBy('nos_code');
                }
            }
            
            // Generate PDF using the exact same view as fullview
            $html = view('admin.exam-schedules.fullview', compact('examSchedule', 'headerLayout', 'examCellUser', 'examCellSignature', 'managerUser', 'managerSignature', 'coordinatorUser', 'coordinatorSignature', 'qualificationModules'))->render();
            
            // Create DomPDF instance
            $dompdf = new Dompdf();
            
            // Set options
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'Arial');
            $options->set('chroot', public_path());
            $dompdf->setOptions($options);
            
            // Load HTML
            $dompdf->loadHtml($html);
            
            // Set paper size - flexible orientation
            $dompdf->setPaper('A4');
            
            // Render PDF
            $dompdf->render();
            
            $filename = "Exam_Schedule_{$examSchedule->course_name}_{$examSchedule->batch_code}.pdf";
            
            // Output PDF
            return $dompdf->stream($filename, ['Attachment' => true]);

        } catch (\Exception $e) {
            \Log::error('Download Exam Schedule Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Download eligible student list as Excel (Name and Roll Number only)
     */
    public function downloadEligibleStudents($id)
    {
        try {
            $user = Auth::user();
            
            // Check access permissions
            $examSchedule = ExamSchedule::with(['students', 'centre'])->findOrFail($id);
            
            // Check access based on role
            switch ($user->user_role) {
                case 5: // Faculty can only see their own schedules
                    if ($examSchedule->created_by !== $user->id) {
                        abort(403, 'Unauthorized access');
                    }
                    break;
                    
                case 3: // Exam Cell can see schedules from their TC
                    if ($examSchedule->tc_code !== $user->from_tc) {
                        abort(403, 'Unauthorized access');
                    }
                    break;
                    
                case 1: // TC Admin can see schedules from their TC
                    if ($examSchedule->tc_code !== $user->from_tc) {
                        abort(403, 'Unauthorized access');
                    }
                    break;
                    
                case 2: // TC Head can see schedules from their TC
                    if ($examSchedule->tc_code !== $user->from_tc) {
                        abort(403, 'Unauthorized access');
                    }
                    break;
                    
                case 4: // Assessment Agency can see approved schedules
                    if (!in_array($examSchedule->status, ['tc_admin_approved', 'received'])) {
                        abort(403, 'Unauthorized access');
                    }
                    break;
                    
                default:
                    abort(403, 'Unauthorized access');
            }

            // Get student roll numbers
            $studentRecord = $examSchedule->students()->first();
            if (!$studentRecord || empty($studentRecord->student_roll_numbers)) {
                return response()->json(['error' => 'No eligible students found for this exam schedule'], 404);
            }

            $rollNumbers = $studentRecord->student_roll_numbers;
            
            // Get student names from TC student table
            $studentData = [];
            try {
                $tcStudentModel = \App\Models\TcStudent::forTc($examSchedule->tc_code);
                $tableName = $tcStudentModel->getTable();
                
                foreach ($rollNumbers as $rollNumber) {
                    $student = DB::table($tableName)
                        ->where('RollNo', $rollNumber)
                        ->select('Name', 'RollNo')
                        ->first();
                    
                    if ($student) {
                        $studentData[] = [
                            'name' => $student->Name,
                            'roll_number' => $student->RollNo
                        ];
                    } else {
                        // If student not found, use roll number as name
                        $studentData[] = [
                            'name' => 'Student (Roll: ' . $rollNumber . ')',
                            'roll_number' => $rollNumber
                        ];
                    }
                }
            } catch (\Exception $e) {
                // If TC student table doesn't exist or error occurs, use roll numbers only
                foreach ($rollNumbers as $rollNumber) {
                    $studentData[] = [
                        'name' => 'Student (Roll: ' . $rollNumber . ')',
                        'roll_number' => $rollNumber
                    ];
                }
            }
            
            // Create CSV file with only Name and Roll Number
            $filename = "Eligible_Students_{$examSchedule->course_name}_{$examSchedule->batch_code}.csv";
            
            // Set headers for CSV download
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            // Create output stream
            $output = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel display
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add headers
            fputcsv($output, ['S.No', 'Student Name', 'Roll Number']);
            
            // Add data
            foreach ($studentData as $index => $student) {
                fputcsv($output, [
                    $index + 1,
                    $student['name'],
                    $student['roll_number']
                ]);
            }
            
            fclose($output);
            exit;

        } catch (\Exception $e) {
            \Log::error('Download Eligible Students Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Download exam schedule as Excel
     */
    public function downloadExcel($id)
    {
        try {
            $user = Auth::user();
            
            // Only Assessment Agency (role 4) can access this
            if ($user->user_role !== 4) {
                abort(403, 'Unauthorized access');
            }

            $examSchedule = ExamSchedule::with(['modules', 'centre', 'qualification'])->findOrFail($id);
            
            // Get qualification modules mapping for module names
            $qualificationModules = [];
            if ($examSchedule->course_name) {
                $qualification = \App\Models\Qualification::where('qf_name', $examSchedule->course_name)->first();
                if ($qualification) {
                    $qualificationModules = $qualification->modules()->get()->keyBy('nos_code');
                }
            }
            
            // Generate Excel content with better handling of theory/practical modules
            $theoryModules = $examSchedule->modules->filter(function($module) {
                return $module->is_theory === true;
            });
            $practicalModules = $examSchedule->modules->filter(function($module) {
                return $module->is_theory === false;
            });
            
            // If no modules are categorized, show all modules in one section
            if ($theoryModules->count() === 0 && $practicalModules->count() === 0) {
                $allModules = $examSchedule->modules;
            }
            
            $csvContent = "EXAMINATION SCHEDULE\n";
            $csvContent .= "Course: {$examSchedule->course_name}\n";
            $csvContent .= "Batch: {$examSchedule->batch_code}\n\n";
            $csvContent .= "Examination Type,{$examSchedule->exam_type} SEM: {$examSchedule->semester}\n";
            $csvContent .= "Examination Period," . \Carbon\Carbon::parse($examSchedule->exam_start_date)->format('d/m/Y') . " - " . \Carbon\Carbon::parse($examSchedule->exam_end_date)->format('d/m/Y') . "\n";
            $csvContent .= "Center," . ($examSchedule->centre ? $examSchedule->centre->centre_name : 'Not specified') . "\n";
            $csvContent .= "File Number," . ($examSchedule->file_no ?? 'N/A') . "\n";
            $csvContent .= "Program Number," . ($examSchedule->program_number ?? 'N/A') . "\n";
            $csvContent .= "Total Students," . ($examSchedule->total_students ?? 'N/A') . "\n\n";
            
            if (isset($allModules) && $allModules->count() > 0) {
                $csvContent .= "ALL EXAMINATIONS\n";
                $csvContent .= "S.No.,Date,NOS Code,Subject,Venue,Timing,Invigilator\n";
                foreach ($allModules as $index => $module) {
                    $csvContent .= ($index + 1) . ",";
                    $csvContent .= ($module->exam_date ? $module->exam_date->format('d/m/Y') : '') . ",";
                    $csvContent .= $module->nos_code . ",";
                    $csvContent .= (isset($qualificationModules[$module->nos_code]) ? $qualificationModules[$module->nos_code]->module_name : $module->nos_code) . ",";
                    $csvContent .= $module->venue . ",";
                    $csvContent .= ($module->start_time ? $module->start_time->format('H:i') : '') . " - " . ($module->end_time ? $module->end_time->format('H:i') : '') . ",";
                    $csvContent .= $module->invigilator . "\n";
                }
            } else {
                if ($theoryModules->count() > 0) {
                    $csvContent .= "THEORY EXAMINATIONS\n";
                    $csvContent .= "S.No.,Date,NOS Code,Subject,Venue,Timing,Invigilator\n";
                    foreach ($theoryModules as $index => $module) {
                        $csvContent .= ($index + 1) . ",";
                        $csvContent .= ($module->exam_date ? $module->exam_date->format('d/m/Y') : '') . ",";
                        $csvContent .= $module->nos_code . ",";
                        $csvContent .= (isset($qualificationModules[$module->nos_code]) ? $qualificationModules[$module->nos_code]->module_name : $module->nos_code) . ",";
                        $csvContent .= $module->venue . ",";
                        $csvContent .= ($module->start_time ? $module->start_time->format('H:i') : '') . " - " . ($module->end_time ? $module->end_time->format('H:i') : '') . ",";
                        $csvContent .= $module->invigilator . "\n";
                    }
                    $csvContent .= "\n";
                }
                
                if ($practicalModules->count() > 0) {
                    $csvContent .= "PRACTICAL EXAMINATIONS\n";
                    $csvContent .= "S.No.,Date,NOS Code,Subject,Venue,Timing,Invigilator\n";
                    foreach ($practicalModules as $index => $module) {
                        $csvContent .= ($index + 1) . ",";
                        $csvContent .= ($module->exam_date ? $module->exam_date->format('d/m/Y') : '') . ",";
                        $csvContent .= $module->nos_code . ",";
                        $csvContent .= (isset($qualificationModules[$module->nos_code]) ? $qualificationModules[$module->nos_code]->module_name : $module->nos_code) . ",";
                        $csvContent .= $module->venue . ",";
                        $csvContent .= ($module->start_time ? $module->start_time->format('H:i') : '') . " - " . ($module->end_time ? $module->end_time->format('H:i') : '') . ",";
                        $csvContent .= $module->invigilator . "\n";
                    }
                }
            }
            
            $csvContent .= "\n\nGenerated on: " . \Carbon\Carbon::now()->format('d/m/Y H:i:s');
            
            $filename = "Exam_Schedule_{$examSchedule->course_name}_{$examSchedule->batch_code}.csv";
            
            return response($csvContent)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            \Log::error('Download Excel Exam Schedule Error: ' . $e->getMessage());
            abort(500, 'Failed to download exam schedule');
        }
    }

    /**
     * Export exam schedules data for Assessment Agency
     */
    public function export(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Check if user has permission to export
            if ($user->user_role !== 4) {
                abort(403, 'Unauthorized access');
            }
            
            // Start with base query
            $query = ExamSchedule::query();
            
            // Apply filters based on request parameters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->filled('tc_code')) {
                $query->where('tc_code', $request->tc_code);
            }
            
            if ($request->filled('exam_type')) {
                $query->where('exam_type', $request->exam_type);
            }
            
            if ($request->filled('semester')) {
                $query->where('semester', $request->semester);
            }
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('course_name', 'like', "%{$search}%")
                      ->orWhere('batch_code', 'like', "%{$search}%")
                      ->orWhere('exam_coordinator', 'like', "%{$search}%")
                      ->orWhere('tc_code', 'like', "%{$search}%");
                });
            }
            
            // Apply date range filters
            if ($request->filled('date_range')) {
                switch ($request->date_range) {
                    case 'today':
                        $query->whereDate('created_at', today());
                        break;
                    case 'week':
                        $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'month':
                        $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                        break;
                    case 'quarter':
                        $query->whereBetween('created_at', [now()->startOfQuarter(), now()->endOfQuarter()]);
                        break;
                }
            }
            
            // Assessment Agency can see TC approved schedules and completed Internal exams
            $query->whereIn('status', ['tc_admin_approved', 'received', 'rejected', 'hold']);
            
            // Get data with relationships
            $examSchedules = $query->with(['faculty', 'centre'])
                                   ->orderBy('created_at', 'desc')
                                   ->get();
            
            // Generate CSV content
            $csvContent = $this->generateExportCsv($examSchedules);
            
            $filename = "exam_schedules_export_" . date('Y-m-d_H-i-s') . ".csv";
            
            return response($csvContent)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to export data: ' . $e->getMessage());
        }
    }

    /**
     * Generate CSV content for export
     */
    private function generateExportCsv($examSchedules)
    {
        $headers = [
            'ID',
            'Course Name',
            'TC Code',
            'Batch Code',
            'Semester',
            'Exam Type',
            'Exam Coordinator',
            'Exam Start Date',
            'Exam End Date',
            'Status',
            'File Number',
            'Created At',
            'Updated At',
            'Faculty Name',
            'Centre Name'
        ];
        
        $csv = fopen('php://temp', 'r+');
        fputcsv($csv, $headers);
        
        foreach ($examSchedules as $schedule) {
            $row = [
                $schedule->id,
                $schedule->course_name,
                $schedule->tc_code,
                $schedule->batch_code,
                $schedule->semester,
                $schedule->exam_type,
                $schedule->exam_coordinator,
                $schedule->exam_start_date ? $schedule->exam_start_date->format('Y-m-d') : '',
                $schedule->exam_end_date ? $schedule->exam_end_date->format('Y-m-d') : '',
                $schedule->status,
                $schedule->file_no ?? '',
                $schedule->created_at ? $schedule->created_at->format('Y-m-d H:i:s') : '',
                $schedule->updated_at ? $schedule->updated_at->format('Y-m-d H:i:s') : '',
                $schedule->faculty ? $schedule->faculty->name : '',
                $schedule->centre ? $schedule->centre->name : ''
            ];
            
            fputcsv($csv, $row);
        }
        
        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);
        
        return $content;
    }

    /**
     * Send approval emails based on the approval stage
     */
    private function sendApprovalEmails($examSchedule, $approvingUser, $nextStatus, $nextStage)
    {
        try {
            // Get all relevant users
            $faculty = User::find($examSchedule->created_by);
            $examCellUser = User::where('user_role', 3)->where('from_tc', $examSchedule->tc_code)->first();
            $tcHeadUser = User::where('user_role', 2)->where('from_tc', $examSchedule->tc_code)->first();
            $assessmentAgencyUser = User::where('user_role', 4)->first();

            // Send emails based on approval stage
            switch ($approvingUser->user_role) {
                case 3: // Exam Cell approved
                    if ($tcHeadUser) {
                        Mail::to($tcHeadUser->email)
                            ->send(new ExamScheduleApprovedByExamCell($examSchedule, $approvingUser, $tcHeadUser));
                        
                        \Log::info('Email sent to TC Head for exam cell approval', [
                            'exam_schedule_id' => $examSchedule->id,
                            'tc_head_email' => $tcHeadUser->email
                        ]);
                    }
                    break;

                case 1: // TC Admin approved
                case 2: // TC Head approved
                    if ($nextStatus === 'received' && $examSchedule->exam_type === 'Internal') {
                        // Internal exam - send final approval email to TC Head
                        if ($tcHeadUser) {
                            Mail::to($tcHeadUser->email)
                                ->cc([$examCellUser->email, $faculty->email])
                                ->send(new ExamScheduleFinalApproved($examSchedule, $approvingUser, $faculty, $examCellUser, $tcHeadUser));
                            
                            \Log::info('Final approval email sent for Internal exam', [
                                'exam_schedule_id' => $examSchedule->id,
                                'tc_head_email' => $tcHeadUser->email,
                                'cc_emails' => [$examCellUser->email, $faculty->email]
                            ]);
                        }
                    } else {
                        // Final/Special Final exam - send to Assessment Agency
                        if ($assessmentAgencyUser) {
                            Mail::to($assessmentAgencyUser->email)
                                ->send(new ExamScheduleApprovedByTCHead($examSchedule, $approvingUser, $assessmentAgencyUser));
                            
                            \Log::info('Email sent to Assessment Agency for TC approval', [
                                'exam_schedule_id' => $examSchedule->id,
                                'assessment_agency_email' => $assessmentAgencyUser->email
                            ]);
                        }
                    }
                    break;

                case 4: // Assessment Agency approved
                    // Send final approval email to TC Head with CC to others
                    if ($tcHeadUser) {
                        Mail::to($tcHeadUser->email)
                            ->cc([$examCellUser->email, $faculty->email])
                            ->send(new ExamScheduleFinalApproved($examSchedule, $approvingUser, $faculty, $examCellUser, $tcHeadUser));
                        
                        \Log::info('Final approval email sent by Assessment Agency', [
                            'exam_schedule_id' => $examSchedule->id,
                            'tc_head_email' => $tcHeadUser->email,
                            'cc_emails' => [$examCellUser->email, $faculty->email],
                            'file_number' => $examSchedule->file_no
                        ]);
                    }
                    break;
            }
        } catch (\Exception $e) {
            \Log::error('Error sending approval emails: ' . $e->getMessage());
            throw $e;
        }
    }
} 
