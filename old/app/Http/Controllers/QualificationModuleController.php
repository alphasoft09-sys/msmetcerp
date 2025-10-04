<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\QualificationModule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class QualificationModuleController extends Controller
{
    /**
     * Display a listing of modules
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Only Assessment Agency (role 4) can access this
        if ($user->user_role !== 4) {
            abort(403, 'Unauthorized access');
        }

        $query = QualificationModule::withCount('qualifications');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('module_name', 'like', "%{$search}%")
                  ->orWhere('nos_code', 'like', "%{$search}%");
            });
        }

        // Filter by module type (optional/mandatory)
        if ($request->filled('is_optional')) {
            $isOptional = $request->get('is_optional');
            if ($isOptional === 'optional') {
                $query->where('is_optional', true);
            } elseif ($isOptional === 'mandatory') {
                $query->where('is_optional', false);
            }
        }

        // Filter by hours range
        if ($request->filled('hours_min')) {
            $query->where('hour', '>=', $request->get('hours_min'));
        }
        if ($request->filled('hours_max')) {
            $query->where('hour', '<=', $request->get('hours_max'));
        }

        // Filter by credit range
        if ($request->filled('credit_min')) {
            $query->where('credit', '>=', $request->get('credit_min'));
        }
        if ($request->filled('credit_max')) {
            $query->where('credit', '<=', $request->get('credit_max'));
        }

        // Filter by qualifications count
        if ($request->filled('qualifications_count')) {
            $qualificationsCount = $request->get('qualifications_count');
            if ($qualificationsCount === 'with_qualifications') {
                $query->has('qualifications');
            } elseif ($qualificationsCount === 'without_qualifications') {
                $query->doesntHave('qualifications');
            }
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['module_name', 'nos_code', 'hour', 'credit', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $modules = $query->paginate(15)->withQueryString();

        return view('admin.qualification-modules.index', compact('modules', 'user'));
    }

    /**
     * Handle AJAX request for modules with search and filters
     */
    public function ajaxIndex(Request $request)
    {
        $user = Auth::user();
        
        // Only Assessment Agency (role 4) can access this
        if ($user->user_role !== 4) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        $query = QualificationModule::withCount('qualifications');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('module_name', 'like', "%{$search}%")
                  ->orWhere('nos_code', 'like', "%{$search}%");
            });
        }

        // Filter by module type (optional/mandatory)
        if ($request->filled('is_optional')) {
            $isOptional = $request->get('is_optional');
            if ($isOptional === 'optional') {
                $query->where('is_optional', true);
            } elseif ($isOptional === 'mandatory') {
                $query->where('is_optional', false);
            }
        }

        // Filter by hours range
        if ($request->filled('hours_min')) {
            $query->where('hour', '>=', $request->get('hours_min'));
        }
        if ($request->filled('hours_max')) {
            $query->where('hour', '<=', $request->get('hours_max'));
        }

        // Filter by credit range
        if ($request->filled('credit_min')) {
            $query->where('credit', '>=', $request->get('credit_min'));
        }
        if ($request->filled('credit_max')) {
            $query->where('credit', '<=', $request->get('credit_max'));
        }

        // Filter by qualifications count
        if ($request->filled('qualifications_count')) {
            $qualificationsCount = $request->get('qualifications_count');
            if ($qualificationsCount === 'with_qualifications') {
                $query->has('qualifications');
            } elseif ($qualificationsCount === 'without_qualifications') {
                $query->doesntHave('qualifications');
            }
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['module_name', 'nos_code', 'hour', 'credit', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $modules = $query->paginate(15);

        $html = view('admin.qualification-modules.partials.table', compact('modules', 'user'))->render();
        $pagination = view('admin.qualification-modules.partials.pagination', compact('modules'))->render();

        return response()->json([
            'success' => true,
            'html' => $html,
            'pagination' => $pagination,
            'total' => $modules->total(),
            'current_page' => $modules->currentPage(),
            'last_page' => $modules->lastPage(),
            'per_page' => $modules->perPage(),
            'from' => $modules->firstItem(),
            'to' => $modules->lastItem()
        ]);
    }

    /**
     * Store a newly created module
     */
    public function store(Request $request)
    {
        try {
            \Log::info('Module Store Request', [
                'request_data' => $request->all(),
                'user_id' => Auth::id(),
                'user_role' => Auth::user()->user_role
            ]);
            
            $user = Auth::user();
            
            // Only Assessment Agency (role 4) can access this
            if ($user->user_role !== 4) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $request->validate([
                'module_name' => 'required|string|max:255',
                'nos_code' => [
                    'required',
                    'string',
                    'max:255',
                    function ($attribute, $value, $fail) {
                        // Allow "NA" to be duplicated
                        if (strtoupper(trim($value)) === 'NA') {
                            return;
                        }
                        
                        // Check for uniqueness only for non-NA values
                        $exists = \App\Models\QualificationModule::where('nos_code', $value)->exists();
                        if ($exists) {
                            $fail('The NOS code has already been taken.');
                        }
                    }
                ],
                'is_optional' => 'boolean',
                'hour' => 'required|integer|min:1',
                'credit' => 'required|numeric|min:0',
                'is_viva' => 'boolean',
                'is_practical' => 'boolean',
                'is_theory' => 'boolean',
                'full_mark' => 'nullable|integer|min:0',
                'pass_mark' => 'nullable|integer|min:0',
            ]);

            $module = QualificationModule::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Module created successfully',
                'module' => $module
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Module Store Validation Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Module Store Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::error('Request data: ' . json_encode($request->all()));
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create module: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified module
     */
    public function update(Request $request, $id)
    {
        try {
            \Log::info('Module Update Request', [
                'id' => $id,
                'request_data' => $request->all(),
                'user_id' => Auth::id(),
                'user_role' => Auth::user()->user_role
            ]);
            
            $user = Auth::user();
            
            // Only Assessment Agency (role 4) can access this
            if ($user->user_role !== 4) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $module = QualificationModule::findOrFail($id);

            $request->validate([
                'module_name' => 'required|string|max:255',
                'nos_code' => [
                    'required',
                    'string',
                    'max:255',
                    function ($attribute, $value, $fail) use ($id) {
                        // Allow "NA" to be duplicated
                        if (strtoupper(trim($value)) === 'NA') {
                            return;
                        }
                        
                        // Check for uniqueness only for non-NA values, excluding current record
                        $exists = \App\Models\QualificationModule::where('nos_code', $value)
                            ->where('id', '!=', $id)
                            ->exists();
                        if ($exists) {
                            $fail('The NOS code has already been taken.');
                        }
                    }
                ],
                'is_optional' => 'boolean',
                'hour' => 'required|integer|min:1',
                'credit' => 'required|numeric|min:0',
                'is_viva' => 'boolean',
                'is_practical' => 'boolean',
                'is_theory' => 'boolean',
                'full_mark' => 'nullable|integer|min:0',
                'pass_mark' => 'nullable|integer|min:0',
            ]);

            $module->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Module updated successfully',
                'module' => $module
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Module Update Validation Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Module Update Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::error('Request data: ' . json_encode($request->all()));
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update module: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified module
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            
            // Only Assessment Agency (role 4) can access this
            if ($user->user_role !== 4) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $module = QualificationModule::findOrFail($id);
            $module->delete();

            return response()->json([
                'success' => true,
                'message' => 'Module deleted successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Module Delete Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete module. Please try again.'
            ], 500);
        }
    }

    /**
     * Upload modules from Excel file
     */
    public function uploadExcel(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Only Assessment Agency (role 4) can access this
            if ($user->user_role !== 4) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Validate the request
            $request->validate([
                'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120', // 5MB max
                'skip_duplicates' => 'boolean'
            ]);

            $skipDuplicates = $request->boolean('skip_duplicates', true);

            // Log the upload attempt
            Log::info('File upload started', [
                'user_id' => $user->id,
                'filename' => $request->file('excel_file')->getClientOriginalName(),
                'file_size' => $request->file('excel_file')->getSize(),
                'skip_duplicates' => $skipDuplicates
            ]);

            // Process the file
            $stats = $this->processUploadedFile($request->file('excel_file'), $skipDuplicates);

            // Log the results
            Log::info('File upload completed', [
                'user_id' => $user->id,
                'total_records' => $stats['added_count'] + $stats['skipped_count'],
                'added_count' => $stats['added_count'],
                'skipped_count' => $stats['skipped_count'],
                'error_count' => count($stats['errors'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'total_records' => $stats['added_count'] + $stats['skipped_count'],
                'added_count' => $stats['added_count'],
                'skipped_count' => $stats['skipped_count'],
                'errors' => $stats['errors']
            ]);

        } catch (\Exception $e) {
            Log::error('File Upload Error: ' . $e->getMessage(), [
                'user_id' => $user->id ?? 'unknown',
                'file' => $request->file('excel_file')->getClientOriginalName() ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload file. Please check the file format and try again.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Process uploaded file (CSV or Excel)
     */
    private function processUploadedFile($file, $skipDuplicates)
    {
        $addedCount = 0;
        $skippedCount = 0;
        $errors = [];
        $rowNumber = 0;

        try {
            $filePath = $file->getPathname();
            $fileExtension = strtolower($file->getClientOriginalExtension());

            if ($fileExtension === 'csv') {
                // Process CSV file
                $handle = fopen($filePath, 'r');
                if (!$handle) {
                    throw new \Exception('Could not open CSV file');
                }

                // Read header row
                $header = fgetcsv($handle);
                if (!$header) {
                    throw new \Exception('Could not read CSV header');
                }

                // Process data rows
                while (($row = fgetcsv($handle)) !== false) {
                    $rowNumber++;
                    $result = $this->processRow($row, $header, $skipDuplicates, $rowNumber);
                    
                    if ($result['success']) {
                        $addedCount++;
                    } elseif ($result['skipped']) {
                        $skippedCount++;
                    }
                    
                    if ($result['error']) {
                        $errors[] = $result['error'];
                    }
                }

                fclose($handle);
            } else {
                // For Excel files, we'll convert to CSV first
                // This is a simplified approach - in production you might want to use a proper Excel library
                throw new \Exception('Excel files (.xlsx, .xls) are not supported yet. Please convert to CSV format.');
            }

        } catch (\Exception $e) {
            $errors[] = "File processing error: " . $e->getMessage();
            Log::error('File processing error: ' . $e->getMessage(), [
                'file' => $file->getClientOriginalName(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return [
            'added_count' => $addedCount,
            'skipped_count' => $skippedCount,
            'errors' => $errors
        ];
    }

    /**
     * Process a single row
     */
    private function processRow($row, $header, $skipDuplicates, $rowNumber)
    {
        try {
            // Create associative array from header and row
            $rowData = array_combine($header, $row);
            
            // Normalize column names (handle different possible column names)
            $moduleName = $this->getColumnValue($rowData, ['module_name', 'module name', 'name', 'title', 'Module Name', 'MODULE NAME']);
            $nosCode = $this->getColumnValue($rowData, ['nos_code', 'nos code', 'code', 'NOS Code', 'NOS CODE']);
            $isOptional = $this->getColumnValue($rowData, ['is_optional', 'is optional', 'optional', 'Is Optional', 'IS OPTIONAL']);
            $hours = $this->getColumnValue($rowData, ['hours', 'hour', 'duration', 'Hours', 'HOURS']);
            $credit = $this->getColumnValue($rowData, ['credit', 'credits', 'Credit', 'CREDIT']);
            $isViva = $this->getColumnValue($rowData, ['is_viva', 'is viva', 'viva', 'Is Viva', 'IS VIVA']);
            $isPractical = $this->getColumnValue($rowData, ['is_practical', 'is practical', 'practical', 'Is Practical', 'IS PRACTICAL']);
            $isTheory = $this->getColumnValue($rowData, ['is_theory', 'is theory', 'theory', 'Is Theory', 'IS THEORY']);
            $fullMark = $this->getColumnValue($rowData, ['full_mark', 'full mark', 'fullmark', 'Full Mark', 'FULL MARK']);
            $passMark = $this->getColumnValue($rowData, ['pass_mark', 'pass mark', 'passmark', 'Pass Mark', 'PASS MARK']);

            // Validate required fields
            if (empty($moduleName) || empty($nosCode) || empty($hours) || empty($credit)) {
                return [
                    'success' => false,
                    'skipped' => false,
                    'error' => "Row {$rowNumber}: Missing required fields (Module Name, NOS Code, Hours, Credit)"
                ];
            }

            // Check for duplicate NOS code (allow "NA" to be duplicated)
            $trimmedNosCode = trim($nosCode);
            if ($skipDuplicates && strtoupper($trimmedNosCode) !== 'NA' && QualificationModule::where('nos_code', $trimmedNosCode)->exists()) {
                return [
                    'success' => false,
                    'skipped' => true,
                    'error' => "Row {$rowNumber}: NOS Code '{$nosCode}' already exists - skipped"
                ];
            }

            // Parse boolean values
            $isOptionalBool = $this->parseBoolean($isOptional);
            $isVivaBool = $this->parseBoolean($isViva);
            $isPracticalBool = $this->parseBoolean($isPractical);
            $isTheoryBool = $this->parseBoolean($isTheory);

            // Validate numeric values
            if (!is_numeric($hours) || $hours <= 0) {
                return [
                    'success' => false,
                    'skipped' => false,
                    'error' => "Row {$rowNumber}: Hours must be a positive number, got '{$hours}'"
                ];
            }

            if (!is_numeric($credit) || $credit < 0) {
                return [
                    'success' => false,
                    'skipped' => false,
                    'error' => "Row {$rowNumber}: Credit must be a non-negative number, got '{$credit}'"
                ];
            }

            // Validate mark values if provided
            if (!empty($fullMark) && (!is_numeric($fullMark) || $fullMark <= 0)) {
                return [
                    'success' => false,
                    'skipped' => false,
                    'error' => "Row {$rowNumber}: Full Mark must be a positive number, got '{$fullMark}'"
                ];
            }

            if (!empty($passMark) && (!is_numeric($passMark) || $passMark < 0)) {
                return [
                    'success' => false,
                    'skipped' => false,
                    'error' => "Row {$rowNumber}: Pass Mark must be a non-negative number, got '{$passMark}'"
                ];
            }

            // Validate pass mark is not greater than full mark
            if (!empty($fullMark) && !empty($passMark) && $passMark > $fullMark) {
                return [
                    'success' => false,
                    'skipped' => false,
                    'error' => "Row {$rowNumber}: Pass Mark cannot be greater than Full Mark"
                ];
            }

            // Create the module using DB transaction
            DB::beginTransaction();
            try {
                $module = QualificationModule::create([
                    'module_name' => trim($moduleName),
                    'nos_code' => trim($nosCode),
                    'is_optional' => $isOptionalBool,
                    'hour' => (int) $hours,
                    'credit' => (float) $credit,
                    'is_viva' => $isVivaBool,
                    'is_practical' => $isPracticalBool,
                    'is_theory' => $isTheoryBool,
                    'full_mark' => !empty($fullMark) ? (int) $fullMark : null,
                    'pass_mark' => !empty($passMark) ? (int) $passMark : null,
                ]);

                DB::commit();
                
                Log::info("Module created successfully: {$module->module_name} ({$module->nos_code})", [
                    'module_id' => $module->id,
                    'row_number' => $rowNumber
                ]);

                return ['success' => true, 'skipped' => false, 'error' => null];

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Database error during module creation: ' . $e->getMessage(), [
                    'row' => $row,
                    'row_number' => $rowNumber,
                    'trace' => $e->getTraceAsString()
                ]);
                
                return [
                    'success' => false,
                    'skipped' => false,
                    'error' => "Row {$rowNumber}: Database error - " . $e->getMessage()
                ];
            }

        } catch (\Exception $e) {
            Log::error('Row processing error: ' . $e->getMessage(), [
                'row' => $row,
                'row_number' => $rowNumber,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'skipped' => false,
                'error' => "Row {$rowNumber}: " . $e->getMessage()
            ];
        }
    }

    /**
     * Get column value from multiple possible column names
     */
    private function getColumnValue($rowData, $possibleNames)
    {
        // First try exact matches
        foreach ($possibleNames as $name) {
            if (isset($rowData[$name]) && !empty($rowData[$name])) {
                return $rowData[$name];
            }
        }
        
        // Then try case-insensitive matches
        foreach ($rowData as $key => $value) {
            foreach ($possibleNames as $name) {
                if (strtolower(trim($key)) === strtolower(trim($name)) && !empty($value)) {
                    return $value;
                }
            }
        }
        
        // Finally try partial matches
        foreach ($rowData as $key => $value) {
            foreach ($possibleNames as $name) {
                if (stripos(trim($key), trim($name)) !== false && !empty($value)) {
                    return $value;
                }
            }
        }
        
        return null;
    }

    /**
     * Parse boolean values from various formats
     */
    private function parseBoolean($value)
    {
        if (empty($value)) {
            return false;
        }

        $value = strtolower(trim($value));
        
        if (in_array($value, ['yes', 'y', '1', 'true', 'optional'])) {
            return true;
        }
        
        if (in_array($value, ['no', 'n', '0', 'false', 'mandatory'])) {
            return false;
        }

        // Default to false for unknown values
        return false;
    }

    /**
     * Download Excel template
     */
    public function downloadTemplate()
    {
        try {
            $user = Auth::user();
            
            // Only Assessment Agency (role 4) can access this
            if ($user->user_role !== 4) {
                abort(403, 'Unauthorized access');
            }

            // Create sample data
            $sampleData = [
                ['Module Name', 'NOS Code', 'Is Optional', 'Hours', 'Credit', 'Is Viva', 'Is Practical', 'Is Theory', 'Full Mark', 'Pass Mark'],
                ['Basic Computer Skills', 'NOS001', 'No', '40', '2.5', 'No', 'Yes', 'Yes', '100', '40'],
                ['Advanced Programming', 'NOS002', 'Yes', '60', '4.0', 'Yes', 'Yes', 'No', '100', '50'],
                ['Database Management', 'NOS003', 'No', '50', '3.0', 'No', 'Yes', 'Yes', '100', '40'],
                ['Web Development', 'NOS004', 'Yes', '80', '5.0', 'Yes', 'No', 'Yes', '100', '50'],
                ['Network Administration', 'NOS005', 'No', '70', '4.5', 'No', 'Yes', 'Yes', '100', '40'],
            ];

            // Create CSV content
            $csvContent = '';
            foreach ($sampleData as $row) {
                $csvContent .= implode(',', array_map(function($field) {
                    return '"' . str_replace('"', '""', $field) . '"';
                }, $row)) . "\n";
            }

            return response($csvContent)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="modules_template.csv"');

        } catch (\Exception $e) {
            \Log::error('Template Download Error: ' . $e->getMessage());
            abort(500, 'Failed to generate template');
        }
    }

    /**
     * Test method to verify database connectivity and module creation
     */
    public function testDatabase()
    {
        try {
            $user = Auth::user();
            
            // Only Assessment Agency (role 4) can access this
            if ($user->user_role !== 4) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Test database connection
            DB::connection()->getPdo();
            
            // Test creating a module directly
            $testModule = QualificationModule::create([
                'module_name' => 'Test Module ' . time(),
                'nos_code' => 'TEST' . time(),
                'is_optional' => false,
                'hour' => 40,
                'credit' => 2.5,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Database test successful',
                'module_created' => $testModule->toArray(),
                'database_connected' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Database test failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Database test failed: ' . $e->getMessage(),
                'database_connected' => false
            ], 500);
        }
    }
}
