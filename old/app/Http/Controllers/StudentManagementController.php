<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\StudentLogin;
use App\Models\TcStudent;
use App\Services\DynamicTableService;

class StudentManagementController extends Controller
{
    /**
     * Display a listing of students for the current TC
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Only TC Admin (role 1), TC Head (role 2), Exam Cell (role 3), and TC Faculty (role 5) can access this
            if (!in_array($user->user_role, [1, 2, 3, 5])) {
                abort(403, 'Unauthorized access');
            }

            $tcCode = $user->from_tc;
            $search = $request->get('search', '');
            $perPage = $request->get('per_page', 15);

            // Check if TC student table exists
            if (!DynamicTableService::tableExists($tcCode)) {
                return view('admin.students.index', [
                    'students' => collect([])->paginate($perPage),
                    'user' => $user,
                    'tcCode' => $tcCode,
                    'search' => $search,
                    'tableExists' => false
                ]);
            }

            // Get students from the TC-specific table
            $students = TcStudent::getStudentsForTc($tcCode, $perPage);
            
            if ($search) {
                $students = TcStudent::searchStudentsForTc($tcCode, $search, $perPage);
            }

            return view('admin.students.index', [
                'students' => $students,
                'user' => $user,
                'tcCode' => $tcCode,
                'search' => $search,
                'tableExists' => true
            ]);

        } catch (\Exception $e) {
            \Log::error('Student Management Index Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load students. Please try again.');
        }
    }

    /**
     * Show the form for creating a new student
     */
    public function create()
    {
        $user = Auth::user();
        
        // Only TC Admin (role 1), TC Head (role 2), Exam Cell (role 3), and TC Faculty (role 5) can access this
        if (!in_array($user->user_role, [1, 2, 3, 5])) {
            abort(403, 'Unauthorized access');
        }

        $tcCode = $user->from_tc;
        
        // Check if TC student table exists
        if (!DynamicTableService::tableExists($tcCode)) {
            return back()->with('error', 'Student table for this TC does not exist. Please contact the administrator.');
        }

        return view('admin.students.create', compact('user', 'tcCode'));
    }

    /**
     * Display the specified student
     */
    public function show($id)
    {
        try {
            $user = Auth::user();
            
            // Only TC Admin (role 1), TC Head (role 2), Exam Cell (role 3), and TC Faculty (role 5) can access this
            if (!in_array($user->user_role, [1, 2, 3, 5])) {
                abort(403, 'Unauthorized access');
            }

            $tcCode = $user->from_tc;
            
            // Check if TC student table exists
            if (!DynamicTableService::tableExists($tcCode)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student table for this TC does not exist'
                ], 400);
            }

            // Get student from the TC-specific table
            $tcStudent = TcStudent::forTc($tcCode);
            $student = DB::table($tcStudent->getTable())->find($id);
            
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ], 404);
            }

            // Return HTML view for modal
            return view('admin.students.show', compact('student', 'user', 'tcCode'));

        } catch (\Exception $e) {
            \Log::error('Student Show Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load student details'
            ], 500);
        }
    }

    /**
     * Store a newly created student
     */
    public function store(Request $request)
    {
        try {
            \Log::info('Student store request received');
            \Log::info('Request method: ' . $request->method());
            \Log::info('Request headers: ' . json_encode($request->headers->all()));
            \Log::info('Request data: ' . json_encode($request->all()));
            \Log::info('Request content type: ' . $request->header('Content-Type'));
            
            $user = Auth::user();
            
            // Only TC Admin (role 1), TC Head (role 2), Exam Cell (role 3), and TC Faculty (role 5) can access this
            if (!in_array($user->user_role, [1, 2, 3, 5])) {
                \Log::warning('Unauthorized access attempt to student store by user: ' . $user->email);
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $tcCode = $user->from_tc;
            \Log::info('TC Code: ' . $tcCode);
            
            // Check if TC student table exists
            if (!DynamicTableService::tableExists($tcCode)) {
                \Log::error('Student table does not exist for TC: ' . $tcCode);
                return response()->json([
                    'success' => false,
                    'message' => 'Student table for this TC does not exist'
                ], 400);
            }

            // Validate the request
            $validator = Validator::make($request->all(), [
                'ProgName' => 'required|string|max:255',
                'RefNo' => 'required|string|max:255',
                'RollNo' => 'required|string|max:255',
                'Name' => 'required|string|max:255',
                'FatherName' => 'required|string|max:255',
                'DOB' => 'required|date',
                'Gender' => 'required|in:Male,Female,Other',
                'Category' => 'required|string|max:50',
                'Minority' => 'boolean',
                'MinorityType' => 'nullable|string|max:100',
                'EducationName' => 'required|string|max:255',
                'Address' => 'required|string',
                'City' => 'required|string|max:100',
                'State' => 'required|string|max:100',
                'District' => 'required|string|max:100',
                'Country' => 'required|string|max:100',
                'Pincode' => 'required|string|max:10',
                'MobileNo' => 'required|string|max:15',
                'PhoneNo' => 'nullable|string|max:15',
                'Email' => 'nullable|email|max:255',
                'TraineeFee' => 'nullable|numeric|min:0',
                'Photo' => 'nullable|file|max:50', // 50KB limit - using file instead of image to avoid fileinfo dependency
            ]);

            if ($validator->fails()) {
                \Log::warning('Validation failed', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check for duplicate RefNo and RollNo
            $tcStudent = TcStudent::forTc($tcCode);
            
            $existingRefNo = DB::table($tcStudent->getTable())
                ->where('RefNo', $request->RefNo)
                ->exists();
                
            if ($existingRefNo) {
                \Log::warning('Duplicate RefNo found: ' . $request->RefNo);
                return response()->json([
                    'success' => false,
                    'message' => 'Reference Number already exists'
                ], 422);
            }

            $existingRollNo = DB::table($tcStudent->getTable())
                ->where('RollNo', $request->RollNo)
                ->exists();
                
            if ($existingRollNo) {
                \Log::warning('Duplicate RollNo found: ' . $request->RollNo);
                return response()->json([
                    'success' => false,
                    'message' => 'Roll Number already exists'
                ], 422);
            }

            // Check for duplicate email if provided
            if ($request->Email) {
                $existingEmail = DB::table($tcStudent->getTable())
                    ->where('Email', $request->Email)
                    ->exists();
                    
                if ($existingEmail) {
                    \Log::warning('Duplicate Email found: ' . $request->Email);
                    return response()->json([
                        'success' => false,
                        'message' => 'Email address already exists'
                    ], 422);
                }

                // Also check in StudentLogin table
                $existingLoginEmail = StudentLogin::where('email', $request->Email)->exists();
                if ($existingLoginEmail) {
                    \Log::warning('Duplicate Email found in login table: ' . $request->Email);
                    return response()->json([
                        'success' => false,
                        'message' => 'Email address already exists in login system'
                    ], 422);
                }
            }

            // Create student record
            $studentData = $request->all();
            $studentData['Minority'] = $request->boolean('Minority');
            
            // Extract program number from ProgName
            $studentData['ProgName'] = $this->extractProgramNumber($request->ProgName);
            
            // Handle photo upload
            if ($request->hasFile('Photo')) {
                $photo = $request->file('Photo');
                
                // Manual file type validation
                $allowedExtensions = ['png', 'jpg', 'jpeg', 'gif'];
                $fileExtension = strtolower($photo->getClientOriginalExtension());
                
                if (!in_array($fileExtension, $allowedExtensions)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid photo file type. Only PNG, JPG, JPEG, and GIF files are allowed.'
                    ], 422);
                }
                
                // Validate file size (50KB)
                if ($photo->getSize() > 50 * 1024) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Photo size must be less than 50KB'
                    ], 422);
                }
                
                // Generate unique filename
                $filename = 'student_' . time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                
                // Create directory if it doesn't exist
                $uploadPath = public_path('STUDENTS-PHOTO');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                // Move uploaded file
                $photo->move($uploadPath, $filename);
                
                // Update photo path in database
                $studentData['Photo'] = 'STUDENTS-PHOTO/' . $filename;
            }
            
            // Remove CSRF token and other non-database fields
            unset($studentData['_token']);
            
            \Log::info('Inserting student data', $studentData);
            \Log::info('Table name: ' . $tcStudent->getTable());
            
            try {
                DB::table($tcStudent->getTable())->insert($studentData);
                \Log::info('Student data inserted successfully');
            } catch (\Exception $e) {
                \Log::error('Database insert error: ' . $e->getMessage());
                \Log::error('SQL: ' . $e->getSql());
                throw $e;
            }

            // Create login credentials if email is provided
            if ($request->Email) {
                $password = Hash::make('password123'); // Default password
                
                StudentLogin::create([
                    'name' => $request->Name,
                    'email' => $request->Email,
                    'password' => $password,
                    'tc_code' => $tcCode,
                    'phone' => $request->MobileNo,
                    'roll_number' => $request->RollNo,
                    'class' => $request->ProgName
                ]);
                
                \Log::info('Student login created for email: ' . $request->Email);
            }

            \Log::info('Student added successfully');
            return response()->json([
                'success' => true,
                'message' => 'Student added successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Student Store Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add student. Please try again.'
            ], 500);
        }
    }

    /**
     * Show the form for editing a student
     */
    public function edit($id)
    {
        $user = Auth::user();
        
        // Only TC Admin (role 1), TC Head (role 2), Exam Cell (role 3), and TC Faculty (role 5) can access this
        if (!in_array($user->user_role, [1, 2, 3, 5])) {
            abort(403, 'Unauthorized access');
        }

        $tcCode = $user->from_tc;
        
        // Check if TC student table exists
        if (!DynamicTableService::tableExists($tcCode)) {
            return back()->with('error', 'Student table for this TC does not exist.');
        }

        // Get student from TC-specific table
        $tcStudent = TcStudent::forTc($tcCode);
        $student = DB::table($tcStudent->getTable())->find($id);
        
        if (!$student) {
            return back()->with('error', 'Student not found.');
        }

        return view('admin.students.edit', compact('student', 'user', 'tcCode'));
    }

    /**
     * Update the specified student
     */
    public function update(Request $request, $id)
    {
        try {
            \Log::info('Student update request received for ID: ' . $id);
            \Log::info('Request method: ' . $request->method());
            \Log::info('Request data: ' . json_encode($request->all()));
            
            $user = Auth::user();
            
            // Only TC Admin (role 1), TC Head (role 2), Exam Cell (role 3), and TC Faculty (role 5) can access this
            if (!in_array($user->user_role, [1, 2, 3, 5])) {
                \Log::warning('Unauthorized access attempt to student update by user: ' . $user->email);
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $tcCode = $user->from_tc;
            \Log::info('TC Code: ' . $tcCode);
            
            // Check if TC student table exists
            if (!DynamicTableService::tableExists($tcCode)) {
                \Log::error('Student table does not exist for TC: ' . $tcCode);
                return response()->json([
                    'success' => false,
                    'message' => 'Student table for this TC does not exist'
                ], 400);
            }

            // Get request data
            $data = $request->all();
            \Log::info('Processing data: ' . json_encode($data));
            
            // Validate the request
            $validator = Validator::make($data, [
                'ProgName' => 'required|string|max:255',
                'RefNo' => 'required|string|max:255',
                'RollNo' => 'required|string|max:255',
                'Name' => 'required|string|max:255',
                'FatherName' => 'required|string|max:255',
                'DOB' => 'required|date',
                'Gender' => 'required|in:Male,Female,Other',
                'Category' => 'required|string|max:50',
                'Minority' => 'boolean',
                'MinorityType' => 'nullable|string|max:100',
                'EducationName' => 'required|string|max:255',
                'Address' => 'required|string',
                'City' => 'required|string|max:100',
                'State' => 'required|string|max:100',
                'District' => 'required|string|max:100',
                'Country' => 'required|string|max:100',
                'Pincode' => 'required|string|max:10',
                'MobileNo' => 'required|string|max:15',
                'PhoneNo' => 'nullable|string|max:15',
                'Email' => 'nullable|email|max:255',
                'TraineeFee' => 'nullable|numeric|min:0',
                'Photo' => 'nullable|file|max:50', // 50KB limit - using file instead of image to avoid fileinfo dependency
            ]);

            if ($validator->fails()) {
                \Log::warning('Validation failed', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check for duplicate RefNo and RollNo (excluding current student)
            $tcStudent = TcStudent::forTc($tcCode);
            
            $existingRefNo = DB::table($tcStudent->getTable())
                ->where('RefNo', $data['RefNo'])
                ->where('id', '!=', $id)
                ->exists();
                
            if ($existingRefNo) {
                \Log::warning('Duplicate RefNo found: ' . $data['RefNo']);
                return response()->json([
                    'success' => false,
                    'message' => 'Reference Number already exists'
                ], 422);
            }

            $existingRollNo = DB::table($tcStudent->getTable())
                ->where('RollNo', $data['RollNo'])
                ->where('id', '!=', $id)
                ->exists();
                
            if ($existingRollNo) {
                \Log::warning('Duplicate RollNo found: ' . $data['RollNo']);
                return response()->json([
                    'success' => false,
                    'message' => 'Roll Number already exists'
                ], 422);
            }

            // Check for duplicate email if provided
            if (!empty($data['Email'])) {
                $existingEmail = DB::table($tcStudent->getTable())
                    ->where('Email', $data['Email'])
                    ->where('id', '!=', $id)
                    ->exists();
                    
                if ($existingEmail) {
                    \Log::warning('Duplicate Email found: ' . $data['Email']);
                    return response()->json([
                        'success' => false,
                        'message' => 'Email address already exists'
                    ], 422);
                }

                // Also check in StudentLogin table (excluding current student's login)
                $existingLoginEmail = StudentLogin::where('email', $data['Email'])
                    ->where('roll_number', '!=', $data['RollNo'])
                    ->exists();
                if ($existingLoginEmail) {
                    \Log::warning('Duplicate Email found in login table: ' . $data['Email']);
                    return response()->json([
                        'success' => false,
                        'message' => 'Email address already exists in login system'
                    ], 422);
                }
            }

            // Update student record
            $studentData = $data;
            $studentData['Minority'] = isset($data['Minority']) ? (bool)$data['Minority'] : false;
            
            // Extract program number from ProgName
            $studentData['ProgName'] = $this->extractProgramNumber($data['ProgName']);
            
            // Handle photo upload
            if ($request->hasFile('Photo')) {
                $photo = $request->file('Photo');
                
                // Manual file type validation
                $allowedExtensions = ['png', 'jpg', 'jpeg', 'gif'];
                $fileExtension = strtolower($photo->getClientOriginalExtension());
                
                if (!in_array($fileExtension, $allowedExtensions)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid photo file type. Only PNG, JPG, JPEG, and GIF files are allowed.'
                    ], 422);
                }
                
                // Validate file size (50KB)
                if ($photo->getSize() > 50 * 1024) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Photo size must be less than 50KB'
                    ], 422);
                }
                
                // Generate unique filename
                $filename = 'student_' . $id . '_' . time() . '.' . $photo->getClientOriginalExtension();
                
                // Create directory if it doesn't exist
                $uploadPath = public_path('STUDENTS-PHOTO');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                // Move uploaded file
                $photo->move($uploadPath, $filename);
                
                // Update photo path in database
                $studentData['Photo'] = 'STUDENTS-PHOTO/' . $filename;
                
                // Delete old photo if exists
                if (!empty($data['Photo']) && file_exists(public_path($data['Photo']))) {
                    unlink(public_path($data['Photo']));
                }
            }
            
            // Remove CSRF token and other non-database fields
            unset($studentData['_token']);
            unset($studentData['_method']);
            
            \Log::info('Updating student data: ' . json_encode($studentData));
            \Log::info('Table name: ' . $tcStudent->getTable());
            \Log::info('Student ID: ' . $id);
            
            try {
                DB::table($tcStudent->getTable())
                    ->where('id', $id)
                    ->update($studentData);
                \Log::info('Database update successful');
            } catch (\Exception $e) {
                \Log::error('Database update error: ' . $e->getMessage());
                throw $e;
            }

            // Update login credentials if email is provided
            if (!empty($data['Email'])) {
                $existingLogin = StudentLogin::where('roll_number', $data['RollNo'])
                    ->where('tc_code', $tcCode)
                    ->first();
                
                if ($existingLogin) {
                    $existingLogin->update([
                        'name' => $data['Name'],
                        'email' => $data['Email'],
                        'phone' => $data['MobileNo'],
                        'class' => $data['ProgName']
                    ]);
                } else {
                    // Create new login if doesn't exist
                    $password = Hash::make('password123');
                    StudentLogin::create([
                        'name' => $data['Name'],
                        'email' => $data['Email'],
                        'password' => $password,
                        'tc_code' => $tcCode,
                        'phone' => $data['MobileNo'],
                        'roll_number' => $data['RollNo'],
                        'class' => $data['ProgName']
                    ]);
                }
            }

            \Log::info('Student update completed successfully for ID: ' . $id);
            return response()->json([
                'success' => true,
                'message' => 'Student updated successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Student Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update student. Please try again.'
            ], 500);
        }
    }

    /**
     * Remove the specified student
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            
            // Only TC Admin (role 1), TC Head (role 2), Exam Cell (role 3), and TC Faculty (role 5) can access this
            if (!in_array($user->user_role, [1, 2, 3, 5])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $tcCode = $user->from_tc;
            
            // Check if TC student table exists
            if (!DynamicTableService::tableExists($tcCode)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student table for this TC does not exist'
                ], 400);
            }

            // Get student details before deletion
            $tcStudent = TcStudent::forTc($tcCode);
            $student = DB::table($tcStudent->getTable())->find($id);
            
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ], 404);
            }

            // Delete from TC-specific table
            DB::table($tcStudent->getTable())->where('id', $id)->delete();

            // Delete from login table if exists
            if ($student->RollNo) {
                StudentLogin::where('roll_number', $student->RollNo)
                    ->where('tc_code', $tcCode)
                    ->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Student deleted successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Student Delete Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete student. Please try again.'
            ], 500);
        }
    }

    /**
     * Show the Excel/CSV upload form
     */
    public function showUploadForm()
    {
        $user = Auth::user();
        
        // Only TC Admin (role 1), TC Head (role 2), Exam Cell (role 3), and TC Faculty (role 5) can access this
        if (!in_array($user->user_role, [1, 2, 3, 5])) {
            abort(403, 'Unauthorized access');
        }

        $tcCode = $user->from_tc;
        
        // Check if TC student table exists
        if (!DynamicTableService::tableExists($tcCode)) {
            return back()->with('error', 'Student table for this TC does not exist. Please contact the administrator.');
        }

        return view('admin.students.upload', compact('user', 'tcCode'));
    }

    /**
     * Handle Excel/CSV upload
     */
    public function upload(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Only TC Admin (role 1), TC Head (role 2), Exam Cell (role 3), and TC Faculty (role 5) can access this
            if (!in_array($user->user_role, [1, 2, 3, 5])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $tcCode = $user->from_tc;
            
            \Log::info('Student upload started', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'tc_code' => $tcCode,
                'file_name' => $request->file('file')->getClientOriginalName()
            ]);
            
            // Check if TC student table exists
            if (!DynamicTableService::tableExists($tcCode)) {
                \Log::error('Student table does not exist for TC', ['tc_code' => $tcCode]);
                return response()->json([
                    'success' => false,
                    'message' => 'Student table for this TC does not exist'
                ], 400);
            }

            // Validate file upload
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max, CSV only for now
            ]);

            if ($validator->fails()) {
                \Log::warning('File validation failed', ['errors' => $validator->errors()->toArray()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Please upload a valid CSV file (max 10MB)'
                ], 422);
            }

            // Process the CSV file
            $results = $this->processCsvFile($request->file('file'), $tcCode);

            \Log::info('Student upload completed', [
                'results' => $results,
                'tc_code' => $tcCode
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'results' => $results
            ]);

        } catch (\Exception $e) {
            \Log::error('Student Upload Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload file. Please check the file format and try again. Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process CSV file
     */
    protected function processCsvFile($file, $tcCode)
    {
        $results = [
            'total' => 0,
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        $handle = fopen($file->getPathname(), 'r');
        if (!$handle) {
            throw new \Exception('Could not open file');
        }

        // Read headers
        $headers = fgetcsv($handle);
        if (!$headers) {
            throw new \Exception('Invalid CSV file - no headers found');
        }

        // Clean headers
        $headers = array_map('trim', $headers);
        $headers = array_map('strtolower', $headers);

        $rowNumber = 1; // Start from 1 since we already read headers

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            $results['total']++;

            try {
                // Combine headers with row data
                $data = array_combine($headers, $row);
                
                // Process the row
                $this->processStudentRow($data, $tcCode);
                $results['success']++;

            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'row' => $rowNumber,
                    'error' => $e->getMessage(),
                    'data' => $row
                ];
            }
        }

        fclose($handle);
        return $results;
    }

    /**
     * Process a single student row
     */
    protected function processStudentRow($data, $tcCode)
    {
        // Clean and validate the data
        $studentData = $this->cleanRowData($data);
        
        // Log the cleaned data for debugging
        \Log::info('Processing student row', [
            'original_data' => $data,
            'cleaned_data' => $studentData,
            'tc_code' => $tcCode
        ]);
        
        // Validate the data
        $validator = Validator::make($studentData, [
            'ProgName' => 'required|string|max:255',
            'RefNo' => 'required|string|max:255',
            'RollNo' => 'required|string|max:255',
            'Name' => 'required|string|max:255',
            'FatherName' => 'required|string|max:255',
            'DOB' => 'required|date',
            'Gender' => 'required|in:Male,Female,Other',
            'Category' => 'required|string|max:50',
            'Minority' => 'boolean',
            'MinorityType' => 'nullable|string|max:100',
            'EducationName' => 'required|string|max:255',
            'Address' => 'required|string',
            'City' => 'required|string|max:100',
            'State' => 'required|string|max:100',
            'District' => 'required|string|max:100',
            'Country' => 'required|string|max:100',
            'Pincode' => 'required|string|max:10',
            'MobileNo' => 'required|string|max:15',
            'PhoneNo' => 'nullable|string|max:15',
            'Email' => 'nullable|email|max:255',
            'TraineeFee' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            \Log::error('Student row validation failed', [
                'errors' => $validator->errors()->toArray(),
                'data' => $studentData
            ]);
            throw new \Exception('Validation failed: ' . $validator->errors()->first());
        }

        // Check for duplicate RefNo and RollNo
        $tcStudent = TcStudent::forTc($tcCode);
        
        $existingRefNo = DB::table($tcStudent->getTable())
            ->where('RefNo', $studentData['RefNo'])
            ->exists();
            
        if ($existingRefNo) {
            throw new \Exception("Reference Number '{$studentData['RefNo']}' already exists");
        }

        $existingRollNo = DB::table($tcStudent->getTable())
            ->where('RollNo', $studentData['RollNo'])
            ->exists();
            
        if ($existingRollNo) {
            throw new \Exception("Roll Number '{$studentData['RollNo']}' already exists");
        }

        // Check for duplicate email if provided
        if (!empty($studentData['Email'])) {
            $existingEmail = DB::table($tcStudent->getTable())
                ->where('Email', $studentData['Email'])
                ->exists();
                
            if ($existingEmail) {
                throw new \Exception("Email address '{$studentData['Email']}' already exists");
            }

            // Also check in StudentLogin table
            $existingLoginEmail = StudentLogin::where('email', $studentData['Email'])->exists();
            if ($existingLoginEmail) {
                throw new \Exception("Email address '{$studentData['Email']}' already exists in login system");
            }
        }

        // Insert the student record
        \Log::info('Inserting student record', [
            'tc_code' => $tcCode,
            'table' => $tcStudent->getTable(),
            'data' => $studentData
        ]);
        
        DB::table($tcStudent->getTable())->insert($studentData);

        // Create login credentials if email is provided
        if (!empty($studentData['Email'])) {
            $password = Hash::make('password123'); // Default password
            
            StudentLogin::create([
                'name' => $studentData['Name'],
                'email' => $studentData['Email'],
                'password' => $password,
                'tc_code' => $tcCode,
                'phone' => $studentData['MobileNo'],
                'roll_number' => $studentData['RollNo'],
                'class' => $studentData['ProgName']
            ]);
        }
    }

    /**
     * Clean and format row data
     */
    protected function cleanRowData($row)
    {
        $data = [];
        
        // Map CSV column names to database column names
        $columnMapping = [
            'progname' => 'ProgName',
            'refno' => 'RefNo',
            'rollno' => 'RollNo',
            'name' => 'Name',
            'fathername' => 'FatherName',
            'dob' => 'DOB',
            'gender' => 'Gender',
            'category' => 'Category',
            'minority' => 'Minority',
            'minoritytype' => 'MinorityType',
            'minority type' => 'MinorityType',
            'educationname' => 'EducationName',
            'address' => 'Address',
            'city' => 'City',
            'state' => 'State',
            'district' => 'District',
            'country' => 'Country',
            'pincode' => 'Pincode',
            'mobileno' => 'MobileNo',
            'phoneno' => 'PhoneNo',
            'email' => 'Email',
            'traineefee' => 'TraineeFee'
        ];

        foreach ($row as $key => $value) {
            $cleanKey = strtolower(trim($key));
            
            if (isset($columnMapping[$cleanKey])) {
                $dbColumn = $columnMapping[$cleanKey];
                $data[$dbColumn] = $this->cleanValue($value, $dbColumn);
            }
        }

        return $data;
    }

    /**
     * Clean individual values based on column type
     */
    protected function cleanValue($value, $column)
    {
        $value = trim($value);
        
        \Log::info('Cleaning value', [
            'column' => $column,
            'original_value' => $value,
            'is_empty' => empty($value)
        ]);
        
        // Special handling for TraineeFee - return 0.00 for empty values
        if ($column === 'TraineeFee' && empty($value)) {
            \Log::info('TraineeFee is empty, returning 0.00');
            return 0.00;
        }
        
        // Special handling for Minority - return false for empty values
        if ($column === 'Minority' && empty($value)) {
            \Log::info('Minority is empty, returning false');
            return false;
        }
        
        // Special handling for Country - return 'India' for empty values
        if ($column === 'Country' && empty($value)) {
            \Log::info('Country is empty, returning India');
            return 'India';
        }
        
        // For other columns, return null for empty values
        if (empty($value)) {
            \Log::info('Value is empty, returning null', ['column' => $column]);
            return null;
        }

        switch ($column) {
            case 'ProgName':
                // Extract program number before "--"
                if (strpos($value, '--') !== false) {
                    $programNumber = trim(explode('--', $value)[0]);
                    
                    // Validate that it contains a P number
                    if (!preg_match('/^P\d+$/', $programNumber)) {
                        throw new \Exception("Invalid program number format: '{$programNumber}'. Must start with 'P' followed by numbers.");
                    }
                    
                    return $programNumber;
                } else {
                    // If no "--" found, check if the entire value is a valid P number
                    if (!preg_match('/^P\d+$/', $value)) {
                        throw new \Exception("Invalid program format: '{$value}'. Must contain a valid program number (e.g., P102023--Program Name).");
                    }
                    return $value;
                }
                
            case 'DOB':
                // Convert various date formats to Y-m-d
                
                \Log::info('Processing DOB value', ['original_value' => $value]);
                
                // Handle DD-MM-YYYY format (e.g., 29-11-2003)
                if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $value, $matches)) {
                    $day = intval($matches[1]);
                    $month = intval($matches[2]);
                    $year = intval($matches[3]);
                    
                    \Log::info('DD-MM-YYYY format detected', [
                        'day' => $day,
                        'month' => $month,
                        'year' => $year
                    ]);
                    
                    // Validate date
                    if (checkdate($month, $day, $year)) {
                        $formattedDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
                        \Log::info('Date converted successfully', ['formatted_date' => $formattedDate]);
                        return $formattedDate;
                    } else {
                        throw new \Exception("Invalid date: '{$value}'. Please use DD-MM-YYYY format.");
                    }
                }
                
                // Handle DD/MMM/YYYY format (e.g., 29/Nov/2003)
                if (preg_match('/(\d{1,2})\/(\w+)\/(\d{4})/', $value, $matches)) {
                    $day = $matches[1];
                    $month = $matches[2];
                    $year = $matches[3];
                    
                    $monthMap = [
                        'january' => 1, 'jan' => 1,
                        'february' => 2, 'feb' => 2,
                        'march' => 3, 'mar' => 3,
                        'april' => 4, 'apr' => 4,
                        'may' => 5,
                        'june' => 6, 'jun' => 6,
                        'july' => 7, 'jul' => 7,
                        'august' => 8, 'aug' => 8,
                        'september' => 9, 'sep' => 9,
                        'october' => 10, 'oct' => 10,
                        'november' => 11, 'nov' => 11,
                        'december' => 12, 'dec' => 12
                    ];
                    
                    $monthNum = $monthMap[strtolower($month)] ?? 1;
                    return sprintf('%04d-%02d-%02d', $year, $monthNum, $day);
                }
                
                // Handle YYYY-MM-DD format (already correct)
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                    return $value;
                }
                
                // If none of the above formats match, try to parse with Carbon
                try {
                    $date = \Carbon\Carbon::parse($value);
                    return $date->format('Y-m-d');
                } catch (\Exception $e) {
                    throw new \Exception("Invalid date format: '{$value}'. Please use DD-MM-YYYY, DD/MMM/YYYY, or YYYY-MM-DD format.");
                }
                
            case 'Gender':
                $gender = strtolower($value);
                if (in_array($gender, ['male', 'm'])) return 'Male';
                if (in_array($gender, ['female', 'f'])) return 'Female';
                if (in_array($gender, ['other', 'o'])) return 'Other';
                return 'Male'; // Default
                
            case 'Minority':
                $minority = strtolower($value);
                if (in_array($minority, ['yes', 'y', 'true', '1'])) return true;
                if (in_array($minority, ['no', 'n', 'false', '0'])) return false;
                return false; // Default
                
            case 'TraineeFee':
                // Remove currency symbols and convert to numeric
                $fee = preg_replace('/[^\d.]/', '', $value);
                return floatval($fee) ?: 0.00;
                
            case 'MobileNo':
            case 'PhoneNo':
                // Remove non-numeric characters
                return preg_replace('/[^\d]/', '', $value);
                
            case 'Pincode':
                // Remove non-numeric characters
                return preg_replace('/[^\d]/', '', $value);
                
            default:
                return $value;
        }
    }

    /**
     * Download sample CSV template
     */
    public function downloadTemplate()
    {
        $user = Auth::user();
        
        // Only TC Admin (role 1), TC Head (role 2), Exam Cell (role 3), and TC Faculty (role 5) can access this
        if (!in_array($user->user_role, [1, 2, 3, 5])) {
            abort(403, 'Unauthorized access');
        }

        // Create sample data based on the Excel file provided
        $sampleData = [
            [
                'ProgName' => 'P102023--SAP Business ONE',
                'RefNo' => 'T25036860',
                'RollNo' => 'BBST982506000419',
                'Name' => 'BINOD KUMAR BEHERA',
                'FatherName' => 'NARAYAN BEHERA',
                'DOB' => '2003-11-28',
                'Gender' => 'Male',
                'Category' => 'SC',
                'Minority' => 'No',
                'MinorityType' => '',
                'EducationName' => 'Graduate(Tech) (Persuing)',
                'Address' => 'PLOT NO-126 CTC ROAD BUDHESWARI COLONY',
                'City' => 'BHUBANESWAR',
                'State' => 'ODISHA',
                'District' => 'KHORDHA',
                'Country' => 'India',
                'Pincode' => '751006',
                'MobileNo' => '8658162615',
                'PhoneNo' => '',
                'Email' => '',
                'TraineeFee' => '0.00'
            ]
        ];

        // Create CSV content
        $headers = [
            'ProgName', 'RefNo', 'RollNo', 'Name', 'FatherName', 'DOB', 'Gender', 
            'Category', 'Minority', 'MinorityType', 'EducationName', 'Address', 
            'City', 'State', 'District', 'Country', 'Pincode', 'MobileNo', 
            'PhoneNo', 'Email', 'TraineeFee'
        ];
        
        $csv = implode(',', $headers) . "\n";
        
        foreach ($sampleData as $row) {
            $csvRow = [];
            foreach ($headers as $header) {
                $value = $row[$header] ?? '';
                // Escape commas and quotes
                if (strpos($value, ',') !== false || strpos($value, '"') !== false) {
                    $value = '"' . str_replace('"', '""', $value) . '"';
                }
                $csvRow[] = $value;
            }
            $csv .= implode(',', $csvRow) . "\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="student_template.csv"'
        ]);
    }

    /**
     * Extract and validate program number from ProgName
     */
    protected function extractProgramNumber($progName)
    {
        $progName = trim($progName);
        
        if (empty($progName)) {
            throw new \Exception('Program name is required');
        }
        
        // Extract program number before "--"
        if (strpos($progName, '--') !== false) {
            $programNumber = trim(explode('--', $progName)[0]);
            
            // Validate that it contains a P number
            if (!preg_match('/^P\d+$/', $programNumber)) {
                throw new \Exception("Invalid program number format: '{$programNumber}'. Must start with 'P' followed by numbers.");
            }
            
            return $programNumber;
        } else {
            // If no "--" found, check if the entire value is a valid P number
            if (!preg_match('/^P\d+$/', $progName)) {
                throw new \Exception("Invalid program format: '{$progName}'. Must contain a valid program number (e.g., P102023--Program Name).");
            }
            return $progName;
        }
    }

    /**
     * Serve student photos
     */
    public function serveImage($filename)
    {
        try {
            $filePath = public_path('STUDENTS-PHOTO/' . $filename);
            
            if (!file_exists($filePath)) {
                abort(404, 'Image not found');
            }

            // Determine content type based on file extension
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $contentType = match($extension) {
                'jpg', 'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                default => 'application/octet-stream'
            };

            return response()->file($filePath, [
                'Content-Type' => $contentType,
                'Cache-Control' => 'public, max-age=31536000',
                'Access-Control-Allow-Origin' => '*'
            ]);
        } catch (\Exception $e) {
            \Log::error('Student Image Serve Error: ' . $e->getMessage());
            abort(404, 'Image not found');
        }
    }
} 