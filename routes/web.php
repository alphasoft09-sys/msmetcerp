<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardDataController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ForgotPasswordController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminManagementController;
use App\Http\Controllers\TcManagementController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\StudentManagementController;
use App\Http\Controllers\QualificationController;
use App\Http\Controllers\QualificationModuleController;
use Illuminate\Http\Request;
use App\Http\Controllers\ExamScheduleController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\TcHeaderLayoutController;
use App\Http\Controllers\TcCentreController;
use App\Http\Controllers\TranslationController;
use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\LmsDepartmentController;
use App\Http\Controllers\TcLmsController;
use App\Http\Controllers\PublicLmsController;
use App\Http\Controllers\LmsMediaController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');



// File number preview API for Assessment Agency modal
Route::get('/api/file-number-preview/{scheduleId}', function ($scheduleId) {
    try {
        $schedule = \App\Models\ExamSchedule::find($scheduleId);
        
        if (!$schedule) {
            return response()->json(['error' => 'Schedule not found'], 404);
        }
        
        // Use current date for approval date
        $approvalDate = \Carbon\Carbon::now();
        
        // Generate file number components with current date - same logic as controller
        $components = \App\Services\FileNumberService::getComponents($schedule, $approvalDate);
        
        // Construct file number: FN + financial_year + tc_short_code + date_formatted + serial_number
        $fileNumber = "FN{$components['financial_year']}{$components['tc_short_code']}{$components['date_formatted']}{$components['serial_number']}";
        
        // Log the preview generation for debugging
        \Log::info('File number preview generated', [
            'exam_schedule_id' => $scheduleId,
            'file_number' => $fileNumber,
            'components' => $components,
            'approval_date' => $approvalDate->format('Y-m-d')
        ]);
        
        return response()->json([
            'success' => true,
            'file_number' => $fileNumber,
            'approval_date' => $approvalDate->format('Y-m-d')
        ]);
        
    } catch (\Exception $e) {
        \Log::error('File number preview generation failed', [
            'exam_schedule_id' => $scheduleId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        // Fallback: Return a file number with current date if database is not available
        $currentDate = \Carbon\Carbon::now();
        $dateFormatted = $currentDate->format('dmy');
        $financialYear = \App\Services\FileNumberService::getFinancialYear($currentDate);
        
        // Try to get TC short code from the schedule if available
        $tcShortCode = 'BB'; // Default fallback
        if (isset($schedule) && $schedule) {
            try {
                $tcShortCode = \App\Services\FileNumberService::getTcShortCode($schedule->tc_code);
            } catch (\Exception $e) {
                // Use default if TC short code fails
            }
        }
        
        return response()->json([
            'success' => true,
            'file_number' => "FN{$financialYear}{$tcShortCode}{$dateFormatted}0126",
            'approval_date' => $currentDate->format('Y-m-d')
        ]);
    }
})->name('api.file-number-preview');


// Public exam schedules page
Route::get('/exam-schedules', [ExamScheduleController::class, 'publicIndex'])->name('public.exam-schedules');
Route::get('/exam-schedules/ajax', [ExamScheduleController::class, 'ajaxExamSchedules'])->name('public.exam-schedules.ajax');
Route::get('/exam-schedules/{id}/view', [ExamScheduleController::class, 'publicView'])->name('public.exam-schedules.view');

// Public routes for serving files (outside admin middleware)
Route::get('/files/exam-schedules/student-details/{filename}', function ($filename) {
    $path = storage_path('app/public/exam-schedules/student-details/' . $filename);
    
    if (!file_exists($path)) {
        abort(404, 'File not found');
    }
    
    return response()->file($path, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="' . $filename . '"'
    ]);
})->name('files.exam-schedules.student-details');

Route::get('/files/exam-schedules/course-completion/{filename}', function ($filename) {
    $path = storage_path('app/public/exam-schedules/course-completion/' . $filename);
    
    if (!file_exists($path)) {
        abort(404, 'File not found');
    }
    
    return response()->file($path, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="' . $filename . '"'
    ]);
})->name('files.exam-schedules.course-completion');

// Admin routes
Route::prefix('admin')->group(function () {
    // Admin login
    Route::get('/login', function() {
        return view('admin.login-goi');
    })->name('admin.login');
    Route::post('/login', [AdminController::class, 'login'])->middleware('captcha');
    Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
    Route::get('/logout', [AdminController::class, 'logout'])->name('admin.logout.get');
Route::post('/debug-auth', [AdminController::class, 'debugAuth'])->name('admin.debug-auth');

// Test endpoint for debugging login issues
Route::get('/test-login-endpoint', function() {
    return response()->json([
        'success' => true,
        'message' => 'Login endpoint is accessible',
        'timestamp' => now()->toISOString()
    ]);
})->name('test.login.endpoint');

// Translation routes
Route::post('/translate', [TranslationController::class, 'translate'])->name('translate');
Route::get('/languages', [TranslationController::class, 'getSupportedLanguages'])->name('languages');

// CAPTCHA routes
Route::post('/captcha/verify', [CaptchaController::class, 'verify'])->name('captcha.verify');
Route::post('/captcha/check', [CaptchaController::class, 'checkVerification'])->name('captcha.check');
Route::get('/captcha/config', [CaptchaController::class, 'getConfig'])->name('captcha.config');

// CSRF token refresh endpoint (public route)
Route::get('/csrf-token', function() {
    try {
        // Regenerate token to ensure it's fresh
        session()->regenerateToken();
        $token = csrf_token();
        
        if (empty($token)) {
            return response()->json([
                'error' => 'Failed to generate CSRF token'
            ], 500);
        }
        
        return response()->json([
            'token' => $token,
            'timestamp' => now()->toISOString(),
            'session_id' => session()->getId()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'CSRF token generation failed: ' . $e->getMessage()
        ], 500);
    }
})->name('csrf.token');

// Session test endpoints
Route::get('/test-session', [App\Http\Controllers\SessionTestController::class, 'testSession'])->name('test.session');
Route::post('/test-csrf', [App\Http\Controllers\SessionTestController::class, 'testCsrf'])->name('test.csrf');
Route::get('/clear-session', [App\Http\Controllers\SessionTestController::class, 'clearSession'])->name('clear.session');

// Session health check endpoint
Route::get('/session-health', function() {
    try {
        $sessionId = session()->getId();
        $csrfToken = csrf_token();
        $sessionData = session()->all();
        
        return response()->json([
            'success' => true,
            'session_id' => $sessionId,
            'csrf_token' => $csrfToken,
            'session_driver' => config('session.driver'),
            'session_lifetime' => config('session.lifetime'),
            'session_data_keys' => array_keys($sessionData),
            'timestamp' => now()->toISOString()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'session_driver' => config('session.driver'),
            'session_lifetime' => config('session.lifetime')
        ], 500);
    }
})->name('session.health');

    // Admin forgot password routes
    Route::get('/forgot-password', function() {
        return view('admin.forgot-password-clean');
    })->name('admin.password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendAdminResetLink'])->name('admin.password.email')->middleware('captcha');
    Route::get('/reset-password', [ForgotPasswordController::class, 'showAdminResetForm'])->name('admin.password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetAdminPassword'])->name('admin.password.update');

    // OTP verification routes
    Route::get('/verify-otp', function() {
        return view('admin.verify-otp-clean');
    })->name('admin.verify-otp');
    Route::post('/verify-otp', [OtpController::class, 'verifyOtp'])->name('admin.verify-otp.post')->middleware('captcha');
    Route::post('/resend-otp', [OtpController::class, 'resendOtp'])->name('admin.resend-otp');

    // Protected admin routes
    Route::middleware(['admin.auth'])->group(function () {
        // Profile management routes
        Route::get('/profile/change-password-email', [ProfileController::class, 'showChangeForm'])->name('admin.profile.change');
        Route::post('/profile/send-otp', [ProfileController::class, 'sendChangeOtp'])->name('admin.profile.send-otp');
        Route::post('/profile/verify-and-update', [ProfileController::class, 'verifyAndUpdate'])->name('admin.profile.verify-update');

        // Admin Profile routes
        Route::get('/profile', [AdminProfileController::class, 'showForm'])->name('admin.profile.form');
        Route::post('/profile', [AdminProfileController::class, 'storeOrUpdate'])->name('admin.profile.store');
        Route::get('/profile/view', [AdminProfileController::class, 'view'])->name('admin.profile.view');
        // Route removed - no longer needed since we show all modules statically

        // Admin Management routes (only for TC Admin - role 1 and TC Head - role 2)
        Route::prefix('management')->middleware('role:1,2')->group(function () {
            Route::get('/', [AdminManagementController::class, 'index'])->name('admin.management.index');
            Route::get('/create', [AdminManagementController::class, 'create'])->name('admin.management.create');
            Route::post('/', [AdminManagementController::class, 'store'])->name('admin.management.store');
            Route::get('/{id}/edit', [AdminManagementController::class, 'edit'])->name('admin.management.edit');
            Route::put('/{id}', [AdminManagementController::class, 'update'])->name('admin.management.update');
            Route::delete('/{id}', [AdminManagementController::class, 'destroy'])->name('admin.management.destroy');
        });

        // Centres Management routes (only for TC Admin - role 1 and TC Head - role 2)
        Route::prefix('centres')->middleware('role:1,2')->group(function () {
            Route::get('/', [TcCentreController::class, 'index'])->name('admin.centres.index');
            Route::get('/create', [TcCentreController::class, 'create'])->name('admin.centres.create');
            Route::post('/', [TcCentreController::class, 'store'])->name('admin.centres.store');
            Route::get('/{id}/edit', [TcCentreController::class, 'edit'])->name('admin.centres.edit');
            Route::put('/{id}', [TcCentreController::class, 'update'])->name('admin.centres.update');
            Route::delete('/{id}', [TcCentreController::class, 'destroy'])->name('admin.centres.destroy');
            Route::get('/get-centres-for-tc', [TcCentreController::class, 'getCentresForTc'])->name('admin.centres.get-for-tc');
        });

        // TC Management routes (only for Assessment Agency - role 4)
        Route::prefix('tc-management')->middleware('role:4')->group(function () {
            Route::get('/', [TcManagementController::class, 'index'])->name('admin.tc-management.index');
            Route::get('/create', [TcManagementController::class, 'create'])->name('admin.tc-management.create');
            Route::post('/', [TcManagementController::class, 'store'])->name('admin.tc-management.store');
            Route::get('/{id}/edit', [TcManagementController::class, 'edit'])->name('admin.tc-management.edit');
            Route::put('/{id}', [TcManagementController::class, 'update'])->name('admin.tc-management.update');
            Route::delete('/{id}', [TcManagementController::class, 'destroy'])->name('admin.tc-management.destroy');
        });

        // Qualification Management Routes - Read access for all roles, CRUD for Assessment Agency only
        Route::get('/qualifications', [QualificationController::class, 'index'])->name('admin.qualifications.index');
        Route::post('/qualifications/ajax', [QualificationController::class, 'ajaxIndex'])->name('admin.qualifications.ajax');
        
        // View modules - All roles can view
        Route::get('/qualifications/{qualification}/modules/view', [QualificationController::class, 'getModulesForViewing'])->name('admin.qualifications.modules.view');
        
        // CRUD operations - Assessment Agency only
        Route::middleware('role:4')->group(function () {
            Route::post('/qualifications', [QualificationController::class, 'store'])->name('admin.qualifications.store');
            Route::put('/qualifications/{qualification}', [QualificationController::class, 'update'])->name('admin.qualifications.update');
            Route::delete('/qualifications/{qualification}', [QualificationController::class, 'destroy'])->name('admin.qualifications.destroy');
            Route::get('/qualifications/{qualification}/modules', [QualificationController::class, 'getModulesForMapping'])->name('admin.qualifications.modules');
            Route::get('/qualifications/{qualification}/modules/map', [QualificationController::class, 'getModulesForMappingHtml'])->name('admin.qualifications.modules.map');
            Route::post('/qualifications/{qualification}/modules', [QualificationController::class, 'updateModuleMappings'])->name('admin.qualifications.map-modules');
            Route::get('/qualifications/{qualification}/modules/search', [QualificationController::class, 'searchModulesForMapping'])->name('admin.qualifications.modules.search');
        });
        
        // View mapped modules - All roles can view
        Route::get('/qualifications/{qualification}/mapped-modules', [QualificationController::class, 'getMappedModules'])->name('admin.qualifications.mapped-modules');

        // Qualification Upload Routes (Assessment Agency only)
        Route::post('/qualifications/upload-excel', [QualificationController::class, 'uploadExcel'])->name('admin.qualifications.upload-excel');
        Route::get('/qualifications/download-template', [QualificationController::class, 'downloadTemplate'])->name('admin.qualifications.download-template');

        // Qualification Module Management Routes
        Route::get('/qualification-modules', [QualificationModuleController::class, 'index'])->name('admin.qualification-modules.index');
Route::post('/qualification-modules/ajax', [QualificationModuleController::class, 'ajaxIndex'])->name('admin.qualification-modules.ajax');
Route::post('/qualification-modules', [QualificationModuleController::class, 'store'])->name('admin.qualification-modules.store');
Route::put('/qualification-modules/{module}', [QualificationModuleController::class, 'update'])->name('admin.qualification-modules.update');
Route::delete('/qualification-modules/{module}', [QualificationModuleController::class, 'destroy'])->name('admin.qualification-modules.destroy');
Route::post('/qualification-modules/upload-excel', [QualificationModuleController::class, 'uploadExcel'])->name('admin.qualification-modules.upload-excel');
Route::get('/qualification-modules/download-template', [QualificationModuleController::class, 'downloadTemplate'])->name('admin.qualification-modules.download-template');

// Test route for import functionality (remove in production)
Route::get('/test-import', function() {
    try {
        $import = new \App\Imports\QualificationModulesImport(true);
        $testData = [
            ['Module Name' => 'Test Module 1', 'NOS Code' => 'TEST001', 'Is Optional' => 'No', 'Hours' => '40', 'Credit' => '2.5'],
            ['Module Name' => 'Test Module 2', 'NOS Code' => 'TEST002', 'Is Optional' => 'Yes', 'Hours' => '60', 'Credit' => '4.0'],
        ];
        
        $collection = new \Illuminate\Support\Collection($testData);
        $import->collection($collection);
        
        $stats = $import->getStats();
        
        return response()->json([
            'success' => true,
            'message' => 'Test import completed',
            'stats' => $stats
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Test import failed: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

// Database test route (remove in production)
Route::get('/test-database', [QualificationModuleController::class, 'testDatabase'])->name('admin.qualification-modules.test-database');

        // TC Admin Dashboard (role 1)
        Route::get('/tc-admin/dashboard', [DashboardController::class, 'tcAdminDashboard'])
            ->name('admin.tc-admin.dashboard')
            ->middleware('role:1');

        // Dashboard Data API endpoints (only for TC Admin - role 1)
        Route::prefix('tc-admin')->middleware('role:1')->group(function () {
            Route::get('/chart-data/registrations', [DashboardDataController::class, 'getStudentRegistrationData'])->name('admin.tc-admin.chart.registrations');
            Route::get('/chart-data/distribution', [DashboardDataController::class, 'getStudentDistributionData'])->name('admin.tc-admin.chart.distribution');
            Route::get('/chart-data/students', [DashboardDataController::class, 'getStudentChartData'])->name('admin.tc-admin.chart.students');
            Route::get('/stats', [DashboardDataController::class, 'getDashboardStats'])->name('admin.tc-admin.stats');
        });

        // TC Head Dashboard (role 2)
        Route::get('/tc-head/dashboard', [DashboardController::class, 'tcHeadDashboard'])
            ->name('admin.tc-head.dashboard')
            ->middleware('role:2');

        // Exam Cell Dashboard (role 3)
        Route::get('/exam-cell/dashboard', [DashboardController::class, 'examCellDashboard'])
            ->name('admin.exam-cell.dashboard')
            ->middleware('role:3');

        // Assessment Agency Dashboard (role 4)
        Route::get('/aa/dashboard', [DashboardController::class, 'aaDashboard'])
            ->name('admin.aa.dashboard')
            ->middleware('role:4');

        // TC Faculty Dashboard (role 5)
        Route::get('/tc-faculty/dashboard', [DashboardController::class, 'tcFacultyDashboard'])
            ->name('admin.tc-faculty.dashboard')
            ->middleware('role:5');

        // Faculty Management Routes (only for TC Faculty - role 5)
        Route::prefix('faculty')->middleware('role:5')->group(function () {
            // Subjects
            Route::get('/subjects', [FacultyController::class, 'subjects'])->name('admin.faculty.subjects');
            Route::get('/subjects/create', [FacultyController::class, 'createSubject'])->name('admin.faculty.subjects.create');
            Route::post('/subjects', [FacultyController::class, 'storeSubject'])->name('admin.faculty.subjects.store');

            // Schedules
            Route::get('/schedules', [FacultyController::class, 'schedules'])->name('admin.faculty.schedules');
            Route::post('/schedules', [FacultyController::class, 'storeSchedule'])->name('admin.faculty.schedules.store');

            // Attendance
            Route::get('/attendance', [FacultyController::class, 'attendance'])->name('admin.faculty.attendance');
            Route::post('/attendance', [FacultyController::class, 'takeAttendance'])->name('admin.faculty.attendance.store');

            // Student Progress
            Route::get('/progress', [FacultyController::class, 'studentProgress'])->name('admin.faculty.progress');
            Route::post('/progress', [FacultyController::class, 'storeProgress'])->name('admin.faculty.progress.store');

            // Messages
            Route::get('/messages', [FacultyController::class, 'messages'])->name('admin.faculty.messages');
            Route::post('/messages', [FacultyController::class, 'sendMessage'])->name('admin.faculty.messages.send');

            // Exam Schedules (Faculty)
            Route::get('/exam-schedules', [ExamScheduleController::class, 'index'])->name('admin.faculty.exam-schedules.index');
            Route::get('/exam-schedules/create', [ExamScheduleController::class, 'create'])->name('admin.faculty.exam-schedules.create');
            Route::post('/exam-schedules', [ExamScheduleController::class, 'store'])->name('admin.faculty.exam-schedules.store');
            Route::get('/exam-schedules/{id}', [ExamScheduleController::class, 'show'])->name('admin.faculty.exam-schedules.show');
            Route::get('/exam-schedules/{id}/fullview', [ExamScheduleController::class, 'fullview'])->name('admin.faculty.exam-schedules.fullview');
            Route::get('/exam-schedules/{id}/edit', [ExamScheduleController::class, 'edit'])->name('admin.faculty.exam-schedules.edit');
            Route::put('/exam-schedules/{id}', [ExamScheduleController::class, 'update'])->name('admin.faculty.exam-schedules.update');
            Route::post('/exam-schedules/{id}/submit', [ExamScheduleController::class, 'submit'])->name('admin.faculty.exam-schedules.submit');
            Route::get('/exam-schedules/{id}/download-eligible-students', [ExamScheduleController::class, 'downloadEligibleStudents'])->name('admin.faculty.exam-schedules.download-eligible-students');
            Route::get('/exam-schedules/students/by-program', [ExamScheduleController::class, 'getStudentsByProgram'])->name('admin.faculty.exam-schedules.students');
            Route::get('/exam-schedules/modules/by-qualification', [ExamScheduleController::class, 'getModulesByQualification'])->name('admin.faculty.exam-schedules.modules');
            
            // Centres access for faculty
            Route::get('/centres/get-centres-for-tc', [TcCentreController::class, 'getCentresForTc'])->name('admin.faculty.centres.get-for-tc');
        });

        // Exam Cell Routes (role 3)
        Route::prefix('exam-cell')->middleware('role:3')->group(function () {
            Route::get('/exam-schedules', [ExamScheduleController::class, 'index'])->name('admin.exam-cell.exam-schedules.index');
            Route::get('/exam-schedules/{id}', [ExamScheduleController::class, 'show'])->name('admin.exam-cell.exam-schedules.show');
            Route::get('/exam-schedules/{id}/fullview', [ExamScheduleController::class, 'fullview'])->name('admin.exam-cell.exam-schedules.fullview');
            Route::get('/exam-schedules/{id}/download-eligible-students', [ExamScheduleController::class, 'downloadEligibleStudents'])->name('admin.exam-cell.exam-schedules.download-eligible-students');
            Route::post('/exam-schedules/{id}/approve', [ExamScheduleController::class, 'approve'])->name('admin.exam-cell.exam-schedules.approve');
            Route::post('/exam-schedules/{id}/reject', [ExamScheduleController::class, 'reject'])->name('admin.exam-cell.exam-schedules.reject');
            Route::post('/exam-schedules/{id}/hold', [ExamScheduleController::class, 'hold'])->name('admin.exam-cell.exam-schedules.hold');
        });

        // TC Admin Routes (role 1)
        Route::prefix('tc-admin')->middleware('role:1')->group(function () {
            Route::get('/exam-schedules', [ExamScheduleController::class, 'index'])->name('admin.tc-admin.exam-schedules.index');
            Route::get('/exam-schedules/{id}', [ExamScheduleController::class, 'show'])->name('admin.tc-admin.exam-schedules.show');
            Route::get('/exam-schedules/{id}/fullview', [ExamScheduleController::class, 'fullview'])->name('admin.tc-admin.exam-schedules.fullview');
            Route::get('/exam-schedules/{id}/download-eligible-students', [ExamScheduleController::class, 'downloadEligibleStudents'])->name('admin.tc-admin.exam-schedules.download-eligible-students');
            Route::post('/exam-schedules/{id}/approve', [ExamScheduleController::class, 'approve'])->name('admin.tc-admin.exam-schedules.approve');
            Route::post('/exam-schedules/{id}/reject', [ExamScheduleController::class, 'reject'])->name('admin.tc-admin.exam-schedules.reject');
            Route::post('/exam-schedules/{id}/hold', [ExamScheduleController::class, 'hold'])->name('admin.tc-admin.exam-schedules.hold');
        });

        // TC Head Routes (role 2)
        Route::prefix('tc-head')->middleware('role:2')->group(function () {
            Route::get('/exam-schedules', [ExamScheduleController::class, 'index'])->name('admin.tc-head.exam-schedules.index');
            Route::get('/exam-schedules/{id}', [ExamScheduleController::class, 'show'])->name('admin.tc-head.exam-schedules.show');
            Route::get('/exam-schedules/{id}/fullview', [ExamScheduleController::class, 'fullview'])->name('admin.tc-head.exam-schedules.fullview');
            Route::get('/exam-schedules/{id}/download-eligible-students', [ExamScheduleController::class, 'downloadEligibleStudents'])->name('admin.tc-head.exam-schedules.download-eligible-students');
            Route::post('/exam-schedules/{id}/approve', [ExamScheduleController::class, 'approve'])->name('admin.tc-head.exam-schedules.approve');
            Route::post('/exam-schedules/{id}/reject', [ExamScheduleController::class, 'reject'])->name('admin.tc-head.exam-schedules.reject');
            Route::post('/exam-schedules/{id}/hold', [ExamScheduleController::class, 'hold'])->name('admin.tc-head.exam-schedules.hold');
        });

        // Assessment Agency Routes (role 4)
        Route::prefix('aa')->middleware('role:4')->group(function () {
            Route::get('/exam-schedules', [ExamScheduleController::class, 'index'])->name('admin.aa.exam-schedules.index');
            Route::get('/exam-schedules/export', [ExamScheduleController::class, 'export'])->name('admin.aa.exam-schedules.export');
            Route::get('/exam-schedules/{id}', [ExamScheduleController::class, 'show'])->name('admin.aa.exam-schedules.show');
            Route::get('/exam-schedules/{id}/fullview', [ExamScheduleController::class, 'fullview'])->name('admin.aa.exam-schedules.fullview');
            Route::get('/exam-schedules/{id}/download', [ExamScheduleController::class, 'download'])->name('admin.aa.exam-schedules.download');
            Route::get('/exam-schedules/{id}/download-excel', [ExamScheduleController::class, 'downloadExcel'])->name('admin.aa.exam-schedules.download-excel');
            Route::get('/exam-schedules/{id}/download-eligible-students', [ExamScheduleController::class, 'downloadEligibleStudents'])->name('admin.aa.exam-schedules.download-eligible-students');
            Route::post('/exam-schedules/{id}/approve', [ExamScheduleController::class, 'approve'])->name('admin.aa.exam-schedules.approve');
            Route::post('/exam-schedules/{id}/reject', [ExamScheduleController::class, 'reject'])->name('admin.aa.exam-schedules.reject');
            Route::post('/exam-schedules/{id}/hold', [ExamScheduleController::class, 'hold'])->name('admin.aa.exam-schedules.hold');
            
            // Protected signature route
            Route::get('/exam-schedules/protected-signature/{type}/{token}', [ExamScheduleController::class, 'serveProtectedSignature'])
                ->name('admin.exam-schedules.protected-signature');
        });

        // TC Header Layout Management Routes (Assessment Agency only)
        Route::prefix('tc-header-layouts')->middleware('role:4')->group(function () {
            Route::get('/', [TcHeaderLayoutController::class, 'index'])->name('admin.tc-header-layouts.index');
            Route::post('/', [TcHeaderLayoutController::class, 'store'])->name('admin.tc-header-layouts.store');
            Route::post('/test', [TcHeaderLayoutController::class, 'testUpload'])->name('admin.tc-header-layouts.test');
            Route::get('/test/storage', [TcHeaderLayoutController::class, 'testStorage'])->name('admin.tc-header-layouts.storage-test');
            Route::get('/{tcId}', [TcHeaderLayoutController::class, 'show'])->name('admin.tc-header-layouts.show');
            Route::delete('/{tcId}', [TcHeaderLayoutController::class, 'destroy'])->name('admin.tc-header-layouts.destroy');
        });

        // Student Management Routes (only for TC Admin - role 1, TC Head - role 2, Exam Cell - role 3, and TC Faculty - role 5)
        Route::prefix('students')->middleware('role:1,2,3,5')->group(function () {
            Route::get('/', [StudentManagementController::class, 'index'])->name('admin.students.index');
            Route::get('/create', [StudentManagementController::class, 'create'])->name('admin.students.create');
            Route::post('/', [StudentManagementController::class, 'store'])->name('admin.students.store');
            
            // Excel/CSV Upload routes (must come before {id} routes)
            Route::get('/upload', [StudentManagementController::class, 'showUploadForm'])->name('admin.students.upload');
            Route::post('/upload', [StudentManagementController::class, 'upload'])->name('admin.students.upload.store');
            Route::get('/download-template', [StudentManagementController::class, 'downloadTemplate'])->name('admin.students.template');
            
            // Individual student routes (must come after upload routes)
            Route::get('/{id}', [StudentManagementController::class, 'show'])->name('admin.students.show');
            Route::get('/{id}/edit', [StudentManagementController::class, 'edit'])->name('admin.students.edit');
            Route::put('/{id}', [StudentManagementController::class, 'update'])->name('admin.students.update');
            Route::delete('/{id}', [StudentManagementController::class, 'destroy'])->name('admin.students.destroy');
        });

        // LMS Department Management Routes (Assessment Agency only - Role 4)
        Route::prefix('lms-departments')->middleware('role:4')->group(function () {
            Route::get('/', [LmsDepartmentController::class, 'index'])->name('admin.lms-departments.index');
            Route::get('/create', [LmsDepartmentController::class, 'create'])->name('admin.lms-departments.create');
            Route::post('/', [LmsDepartmentController::class, 'store'])->name('admin.lms-departments.store');
            Route::get('/{lmsDepartment}', [LmsDepartmentController::class, 'show'])->name('admin.lms-departments.show');
            Route::get('/{lmsDepartment}/edit', [LmsDepartmentController::class, 'edit'])->name('admin.lms-departments.edit');
            Route::put('/{lmsDepartment}', [LmsDepartmentController::class, 'update'])->name('admin.lms-departments.update');
            Route::delete('/{lmsDepartment}', [LmsDepartmentController::class, 'destroy'])->name('admin.lms-departments.destroy');
            Route::post('/{lmsDepartment}/toggle-status', [LmsDepartmentController::class, 'toggleStatus'])->name('admin.lms-departments.toggle-status');
            Route::get('/api/departments', [LmsDepartmentController::class, 'getDepartments'])->name('admin.lms-departments.api');
        });

        // LMS Site Management Routes (Faculty - Role 5)
        Route::prefix('tc-lms')->middleware('role:5')->group(function () {
            Route::get('/', [TcLmsController::class, 'index'])->name('admin.tc-lms.index');
            Route::get('/create', [TcLmsController::class, 'create'])->name('admin.tc-lms.create');
            Route::post('/', [TcLmsController::class, 'store'])->name('admin.tc-lms.store');
            Route::post('/check-title-duplicate', [TcLmsController::class, 'checkTitleDuplicate'])->name('admin.tc-lms.check-title-duplicate');
            Route::get('/{tcLm}', [TcLmsController::class, 'show'])->name('admin.tc-lms.show');
            Route::get('/{tcLm}/edit', [TcLmsController::class, 'edit'])->name('admin.tc-lms.edit');
            Route::put('/{tcLm}', [TcLmsController::class, 'update'])->name('admin.tc-lms.update');
            Route::delete('/{tcLm}', [TcLmsController::class, 'destroy'])->name('admin.tc-lms.destroy');
            Route::post('/{tcLm}/submit', [TcLmsController::class, 'submit'])->name('admin.tc-lms.submit');
            Route::get('/{tcLm}/preview', [TcLmsController::class, 'preview'])->name('admin.tc-lms.preview');
            
            // Media management routes
            Route::post('/{tcLm}/media/upload-image', [LmsMediaController::class, 'uploadImage'])->name('admin.tc-lms.upload-image');
            Route::post('/{tcLm}/media/add-video', [LmsMediaController::class, 'addVideo'])->name('admin.tc-lms.add-video');
            Route::get('/{tcLm}/media', [LmsMediaController::class, 'getMedia'])->name('admin.tc-lms.get-media');
            Route::delete('/{tcLm}/media/{media}', [LmsMediaController::class, 'deleteMedia'])->name('admin.tc-lms.delete-media');
        });

        // LMS Site Approval Routes (Assessment Agency - Role 4)
        Route::prefix('tc-lms-admin')->middleware('role:4')->group(function () {
            Route::get('/', [TcLmsController::class, 'adminIndex'])->name('admin.tc-lms.admin-index');
            Route::post('/{tcLm}/approve', [TcLmsController::class, 'approve'])->name('admin.tc-lms.approve');
            Route::post('/{tcLm}/reject', [TcLmsController::class, 'reject'])->name('admin.tc-lms.reject');
            Route::post('/{tcLm}/grant-edit-permission', [TcLmsController::class, 'grantEditPermission'])->name('admin.tc-lms.grant-edit-permission');
            Route::post('/{tcLm}/revoke-edit-permission', [TcLmsController::class, 'revokeEditPermission'])->name('admin.tc-lms.revoke-edit-permission');
            Route::delete('/{tcLm}', [TcLmsController::class, 'adminDestroy'])->name('admin.tc-lms-admin.destroy');
            Route::get('/{tcLm}/preview-ajax', [TcLmsController::class, 'getPreview'])->name('admin.tc-lms.preview-ajax');
        });
    });
});

// Public LMS Routes (no authentication required)
Route::prefix('lms')->group(function () {
    Route::match(['GET', 'POST'], '/', [App\Http\Controllers\PublicLmsController::class, 'index'])->name('public.lms.index');
    Route::post('/load-more', [App\Http\Controllers\PublicLmsController::class, 'loadMore'])->name('public.lms.load-more');
    Route::get('/search', [App\Http\Controllers\PublicLmsController::class, 'search'])->name('public.lms.search');
    Route::get('/{departmentSlug}', [App\Http\Controllers\PublicLmsController::class, 'department'])->name('public.lms.department');
    Route::get('/{departmentSlug}/{siteUrl}', [App\Http\Controllers\PublicLmsController::class, 'show'])->name('public.lms.show');
    Route::get('/{departmentSlug}/{siteUrl}/preview', [App\Http\Controllers\PublicLmsController::class, 'preview'])->name('public.lms.preview');
    Route::get('/{departmentSlug}/{siteUrl}/content', [App\Http\Controllers\PublicLmsController::class, 'getContent'])->name('public.lms.content');
});

// Public routes to serve images (no authentication required)
Route::get('/images/header-layouts/{filename}', [TcHeaderLayoutController::class, 'serveImage'])
    ->where('filename', '.*')
    ->name('images.header-layouts');

Route::get('/images/admin-profiles/{type}/{filename}', [AdminProfileController::class, 'serveImage'])
    ->where('filename', '.*')
    ->name('images.admin-profiles');

Route::get('/images/students/{filename}', [StudentManagementController::class, 'serveImage'])
    ->where('filename', '.*')
    ->name('images.students');

// Student routes
Route::prefix('student')->group(function () {
    // Student login
    Route::get('/login', function() {
        return view('student.login-goi');
    })->name('student.login');
    Route::post('/login', [StudentController::class, 'login'])->middleware('captcha');
    Route::post('/logout', [StudentController::class, 'logout'])->name('student.logout');
    Route::get('/logout', [StudentController::class, 'logout'])->name('student.logout.get');

    // Student forgot password routes
    Route::get('/forgot-password', function() {
        return view('student.forgot-password-clean');
    })->name('student.password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendStudentResetLink'])->name('student.password.email')->middleware('captcha');
    Route::get('/reset-password', [ForgotPasswordController::class, 'showStudentResetForm'])->name('student.password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetStudentPassword'])->name('student.password.update');

    // Protected student routes
    Route::middleware(['student.auth'])->group(function () {
        // Profile management routes
        Route::get('/profile/change-password-email', [ProfileController::class, 'showChangeForm'])->name('student.profile.change');
        Route::post('/profile/send-otp', [ProfileController::class, 'sendChangeOtp'])->name('student.profile.send-otp');
        Route::post('/profile/verify-and-update', [ProfileController::class, 'verifyAndUpdate'])->name('student.profile.verify-update');

        Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
    });

    // Student OTP routes (outside middleware for resend functionality)
    Route::post('/resend-otp', [OtpController::class, 'resendOtp'])->name('student.resend-otp');
});

// SEO Routes
Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index']);
Route::get('/sitemap-lms.xml', [App\Http\Controllers\SitemapController::class, 'lms']);

// Public LMS View
Route::get('/lms/{slug}', function($slug) {
    $lmsSite = \App\Models\TcLms::where('seo_slug', $slug)
        ->where('status', 'approved')
        ->where('is_approved', true)
        ->with('faculty')
        ->firstOrFail();
    
    return view('lms.public-view', compact('lmsSite'));
})->name('lms.public-view');
