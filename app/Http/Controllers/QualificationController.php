<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Qualification;
use App\Models\QualificationModule;

class QualificationController extends Controller
{
    /**
     * Display the qualification list page
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Allow access for Assessment Agency (role 4) and read-only for others
        if (!in_array($user->user_role, [1, 2, 3, 4, 5])) {
            abort(403, 'Unauthorized access');
        }

        $query = Qualification::withCount('modules');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('qf_name', 'like', "%{$search}%")
                  ->orWhere('nqr_no', 'like', "%{$search}%")
                  ->orWhere('sector', 'like', "%{$search}%")
                  ->orWhere('level', 'like', "%{$search}%")
                  ->orWhere('qf_type', 'like', "%{$search}%");
            });
        }

        // Filter by sector
        if ($request->filled('sector')) {
            $query->where('sector', $request->get('sector'));
        }

        // Filter by level
        if ($request->filled('level')) {
            $query->where('level', $request->get('level'));
        }

        // Filter by type
        if ($request->filled('qf_type')) {
            $query->where('qf_type', $request->get('qf_type'));
        }

        // Filter by modules count
        if ($request->filled('modules_count')) {
            $modulesCount = $request->get('modules_count');
            if ($modulesCount === 'with_modules') {
                $query->has('modules');
            } elseif ($modulesCount === 'without_modules') {
                $query->doesntHave('modules');
            }
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['qf_name', 'nqr_no', 'sector', 'level', 'qf_type', 'qf_total_hour', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $qualifications = $query->paginate(15)->withQueryString();

        // Get unique values for filters
        $sectors = Qualification::distinct()->pluck('sector')->sort();
        $levels = Qualification::distinct()->pluck('level')->sort();
        $types = Qualification::distinct()->pluck('qf_type')->sort();

        return view('admin.qualifications.index', compact('qualifications', 'user', 'sectors', 'levels', 'types'));
    }

    /**
     * Handle AJAX request for qualifications with search and filters
     */
    public function ajaxIndex(Request $request)
    {
        $user = Auth::user();
        
        // Allow access for Assessment Agency (role 4) and read-only for others
        if (!in_array($user->user_role, [1, 2, 3, 4, 5])) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        $query = Qualification::withCount('modules');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('qf_name', 'like', "%{$search}%")
                  ->orWhere('nqr_no', 'like', "%{$search}%")
                  ->orWhere('sector', 'like', "%{$search}%")
                  ->orWhere('level', 'like', "%{$search}%")
                  ->orWhere('qf_type', 'like', "%{$search}%");
            });
        }

        // Filter by sectors (multiple)
        if ($request->filled('sectors')) {
            $sectors = $request->get('sectors');
            if (is_array($sectors) && !empty($sectors)) {
                $query->whereIn('sector', $sectors);
            }
        }

        // Filter by levels (multiple)
        if ($request->filled('levels')) {
            $levels = $request->get('levels');
            if (is_array($levels) && !empty($levels)) {
                $query->whereIn('level', $levels);
            }
        }

        // Filter by types (multiple)
        if ($request->filled('qf_types')) {
            $types = $request->get('qf_types');
            if (is_array($types) && !empty($types)) {
                $query->whereIn('qf_type', $types);
            }
        }

        // Filter by hours range
        if ($request->filled('hours_min') || $request->filled('hours_max')) {
            if ($request->filled('hours_min')) {
                $query->where('qf_total_hour', '>=', $request->get('hours_min'));
            }
            if ($request->filled('hours_max')) {
                $query->where('qf_total_hour', '<=', $request->get('hours_max'));
            }
        }

        // Filter by modules count
        if ($request->filled('modules_count')) {
            $modulesCount = $request->get('modules_count');
            if ($modulesCount === 'with_modules') {
                $query->has('modules');
            } elseif ($modulesCount === 'without_modules') {
                $query->doesntHave('modules');
            }
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['qf_name', 'nqr_no', 'sector', 'level', 'qf_type', 'qf_total_hour', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $qualifications = $query->paginate(15);

        // Get unique values for filters
        $sectors = Qualification::distinct()->pluck('sector')->sort();
        $levels = Qualification::distinct()->pluck('level')->sort();
        $types = Qualification::distinct()->pluck('qf_type')->sort();

        $html = view('admin.qualifications.partials.table', compact('qualifications', 'user'))->render();
        $pagination = view('admin.qualifications.partials.pagination', compact('qualifications'))->render();
        $filters = view('admin.qualifications.partials.filters', compact('sectors', 'levels', 'types'))->render();

        return response()->json([
            'success' => true,
            'html' => $html,
            'pagination' => $pagination,
            'filters' => $filters,
            'total' => $qualifications->total(),
            'current_page' => $qualifications->currentPage(),
            'last_page' => $qualifications->lastPage(),
            'per_page' => $qualifications->perPage(),
            'from' => $qualifications->firstItem(),
            'to' => $qualifications->lastItem()
        ]);
    }

    /**
     * Show the form for creating a new qualification
     */
    public function create()
    {
        $user = Auth::user();
        
        // Only Assessment Agency (role 4) can access this
        if ($user->user_role !== 4) {
            abort(403, 'Unauthorized access');
        }

        return view('admin.qualifications.create', compact('user'));
    }

    /**
     * Store a newly created qualification
     */
    public function store(Request $request)
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

            $request->validate([
                'qf_name' => 'required|string|max:255',
                'nqr_no' => 'required|string|max:255|unique:qualifications,nqr_no',
                'sector' => 'required|string|max:255',
                'level' => 'required|string|max:255',
                'qf_type' => 'required|string|max:255',
                'qf_total_hour' => 'required|integer|min:1',
            ]);

            $qualification = Qualification::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Qualification created successfully',
                'qualification' => $qualification
            ]);

        } catch (\Exception $e) {
            \Log::error('Qualification Store Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create qualification. Please try again.'
            ], 500);
        }
    }

    /**
     * Display the specified qualification
     */
    public function show($id)
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

            $qualification = Qualification::with('modules')->find($id);
            
            if (!$qualification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Qualification not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'qualification' => $qualification,
                'modules' => $qualification->modules
            ]);

        } catch (\Exception $e) {
            \Log::error('Qualification Show Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load qualification details'
            ], 500);
        }
    }

    /**
     * Show the form for editing a qualification
     */
    public function edit($id)
    {
        $user = Auth::user();
        
        // Only Assessment Agency (role 4) can access this
        if ($user->user_role !== 4) {
            abort(403, 'Unauthorized access');
        }

        $qualification = Qualification::findOrFail($id);

        return view('admin.qualifications.edit', compact('qualification', 'user'));
    }

    /**
     * Update the specified qualification
     */
    public function update(Request $request, $id)
    {
        try {
            \Log::info('Qualification Update Request', [
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

            $qualification = Qualification::findOrFail($id);
            \Log::info('Qualification found', ['qualification' => $qualification->toArray()]);

            $request->validate([
                'qf_name' => 'required|string|max:255',
                'nqr_no' => 'required|string|max:255|unique:qualifications,nqr_no,' . $id,
                'sector' => 'required|string|max:255',
                'level' => 'required|string|max:255',
                'qf_type' => 'required|string|max:255',
                'qf_total_hour' => 'required|integer|min:1',
            ]);

            \Log::info('Validation passed, updating qualification');
            $qualification->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Qualification updated successfully',
                'qualification' => $qualification
            ]);

        } catch (\Exception $e) {
            \Log::error('Qualification Update Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::error('Request data: ' . json_encode($request->all()));
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update qualification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified qualification
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

            $qualification = Qualification::findOrFail($id);
            $qualification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Qualification deleted successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Qualification Delete Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete qualification. Please try again.'
            ], 500);
        }
    }

    /**
     * Get modules for mapping to a qualification
     */
    public function getModulesForMapping($qualificationId)
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

            $qualification = Qualification::findOrFail($qualificationId);
            $allModules = QualificationModule::orderBy('module_name')->get();
            $mappedModuleIds = $qualification->modules->pluck('id')->toArray();

            return response()->json([
                'success' => true,
                'allModules' => $allModules,
                'mappedModuleIds' => $mappedModuleIds,
                'qualification' => $qualification
            ]);

        } catch (\Exception $e) {
            \Log::error('Get Modules for Mapping Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load modules for mapping'
            ], 500);
        }
    }

    /**
     * Update module mappings for a qualification
     */
    public function updateModuleMappings(Request $request, $qualificationId)
    {
        try {
            \Log::info('Update Module Mappings Request', [
                'qualification_id' => $qualificationId,
                'request_data' => $request->all(),
                'module_ids' => $request->module_ids
            ]);

            $user = Auth::user();
            
            // Only Assessment Agency (role 4) can access this
            if ($user->user_role !== 4) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Handle module_ids - could be array or string
            $moduleIds = $request->module_ids;
            if (is_string($moduleIds)) {
                $moduleIds = json_decode($moduleIds, true);
            }
            
            if (!is_array($moduleIds)) {
                $moduleIds = [];
            }

            \Log::info('Processed module IDs', ['module_ids' => $moduleIds]);

            // Validate module IDs exist
            if (!empty($moduleIds)) {
                $existingModules = QualificationModule::whereIn('id', $moduleIds)->pluck('id')->toArray();
                $invalidIds = array_diff($moduleIds, $existingModules);
                
                if (!empty($invalidIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Some module IDs are invalid: ' . implode(', ', $invalidIds)
                    ], 422);
                }
            }

            $qualification = Qualification::findOrFail($qualificationId);
            
            \Log::info('Qualification found', [
                'qualification_id' => $qualification->id,
                'qualification_name' => $qualification->qf_name
            ]);
            
            // Sync the module mappings (this will add new ones and remove old ones)
            $qualification->modules()->sync($moduleIds);

            \Log::info('Module mappings synced successfully', [
                'module_ids' => $moduleIds
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Module mappings updated successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Update Module Mappings Error: ' . $e->getMessage(), [
                'qualification_id' => $qualificationId,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update module mappings. Please try again.'
            ], 500);
        }
    }

    /**
     * Search modules for mapping to a qualification
     */
    public function searchModulesForMapping(Request $request, $qualificationId)
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

            $qualification = Qualification::findOrFail($qualificationId);
            $search = $request->get('search', '');
            
            $query = QualificationModule::orderBy('module_name');
            
            // Apply search filter
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('module_name', 'like', "%{$search}%")
                      ->orWhere('nos_code', 'like', "%{$search}%");
                });
            }
            
            $allModules = $query->get();
            $mappedModuleIds = $qualification->modules->pluck('id')->toArray();

            return response()->json([
                'success' => true,
                'allModules' => $allModules,
                'mappedModuleIds' => $mappedModuleIds,
                'qualification' => $qualification
            ]);

        } catch (\Exception $e) {
            \Log::error('Search Modules for Mapping Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to search modules for mapping'
            ], 500);
        }
    }

    /**
     * Get mapped modules for a qualification
     */
    public function getMappedModules($qualificationId)
    {
        try {
            $user = Auth::user();
            
            // Allow access for all roles (TC Admin, TC Head, Exam Cell, Assessment Agency, TC Faculty)
            if (!in_array($user->user_role, [1, 2, 3, 4, 5])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $qualification = Qualification::with('modules')->findOrFail($qualificationId);

            return response()->json([
                'success' => true,
                'qualification' => $qualification,
                'modules' => $qualification->modules
            ]);

        } catch (\Exception $e) {
            \Log::error('Get Mapped Modules Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load mapped modules'
            ], 500);
        }
    }

    /**
     * Get modules for viewing (returns HTML)
     */
    public function getModulesForViewing($qualificationId)
    {
        try {
            $user = Auth::user();
            
            // Allow access for all roles that can view qualifications
            if (!in_array($user->user_role, [1, 2, 3, 4, 5])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            \Log::info('Getting modules for viewing', ['qualification_id' => $qualificationId]);
            
            $qualification = Qualification::with('modules')->findOrFail($qualificationId);
            \Log::info('Qualification found', ['qualification_name' => $qualification->qf_name]);
            
            $modules = $qualification->modules()->orderBy('module_name')->get();
            \Log::info('Modules retrieved', ['modules_count' => $modules->count()]);

            $html = view('admin.qualifications.partials.modules-view', compact('qualification', 'modules'))->render();
            \Log::info('HTML rendered successfully', ['html_length' => strlen($html)]);

            return response()->json([
                'success' => true,
                'html' => $html
            ]);

        } catch (\Exception $e) {
            \Log::error('Get Modules for Viewing Error: ' . $e->getMessage(), [
                'qualification_id' => $qualificationId,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to load modules for viewing: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload qualifications from CSV file
     */
    public function uploadExcel(Request $request)
    {
        try {
            $request->validate([
                'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120', // 5MB max
            ]);

            $file = $request->file('excel_file');
            $skipDuplicates = $request->has('skipDuplicates');

            Log::info('Qualification upload started', [
                'filename' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'skipDuplicates' => $skipDuplicates,
                'mime_type' => $file->getMimeType()
            ]);

            // Check if mbstring extension is available
            if (!extension_loaded('mbstring')) {
                throw new \Exception('mbstring extension is required for CSV processing');
            }

            $result = $this->processUploadedFile($file, $skipDuplicates);

            Log::info('Qualification upload completed', $result);

            return response()->json([
                'success' => true,
                'message' => 'Qualifications uploaded successfully',
                'addedCount' => $result['addedCount'],
                'skippedCount' => $result['skippedCount'],
                'errors' => $result['errors']
            ]);

        } catch (\Exception $e) {
            Log::error('Qualification upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $request->file('excel_file') ? $request->file('excel_file')->getClientOriginalName() : 'No file'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload qualifications: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process uploaded CSV file
     */
    private function processUploadedFile($file, $skipDuplicates)
    {
        $addedCount = 0;
        $skippedCount = 0;
        $errors = [];

        if ($file->getClientOriginalExtension() !== 'csv') {
            throw new \Exception('Only CSV files are supported');
        }

        $filePath = $file->getPathname();
        
        // Read file content and handle encoding
        $content = file_get_contents($filePath);
        
        // Remove BOM if present
        $bom = pack('H*','EFBBBF');
        $content = preg_replace("/^$bom/", '', $content);
        
        // Detect encoding and convert to UTF-8 if needed
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        
        if ($encoding && $encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
            \Log::info('Converted file encoding', ['from' => $encoding, 'to' => 'UTF-8']);
        }
        
        // Create temporary file with proper encoding
        $tempFile = tempnam(sys_get_temp_dir(), 'csv_upload_');
        file_put_contents($tempFile, $content);

        $handle = fopen($tempFile, 'r');
        if (!$handle) {
            unlink($tempFile);
            throw new \Exception('Could not open file');
        }

        // Read header row
        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            unlink($tempFile);
            throw new \Exception('Could not read header row');
        }

        // Clean header row
        $header = array_map(function($col) {
            return trim($col);
        }, $header);

        $rowNumber = 1; // Start from 1 since we're processing data rows
        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            
            try {
                $result = $this->processRow($row, $header, $skipDuplicates, $rowNumber);
                
                if ($result['success']) {
                    $addedCount++;
                } elseif ($result['skipped']) {
                    $skippedCount++;
                } else {
                    $errors[] = [
                        'row' => $rowNumber,
                        'message' => $result['error']
                    ];
                }
            } catch (\Exception $e) {
                \Log::error('Error processing qualification row', [
                    'row' => $rowNumber,
                    'error' => $e->getMessage(),
                    'data' => $row
                ]);
                
                $errors[] = [
                    'row' => $rowNumber,
                    'message' => $e->getMessage()
                ];
            }
        }

        fclose($handle);
        unlink($tempFile); // Clean up temporary file

        return [
            'addedCount' => $addedCount,
            'skippedCount' => $skippedCount,
            'errors' => $errors
        ];
    }

    /**
     * Process individual row from CSV
     */
    private function processRow($row, $header, $skipDuplicates, $rowNumber)
    {
        // Clean row data
        $row = array_map(function($value) {
            return trim($value);
        }, $row);
        
        // Combine header and row data
        $rowData = array_combine($header, $row);
        
        // Normalize column names
        $qfName = $this->getColumnValue($rowData, ['qf_name', 'qualification_name', 'name']);
        $nqrNo = $this->getColumnValue($rowData, ['nqr_no', 'nqr_number', 'number']);
        $sector = $this->getColumnValue($rowData, ['sector']);
        $level = $this->getColumnValue($rowData, ['level']);
        $qfType = $this->getColumnValue($rowData, ['qf_type', 'type']);
        $qfTotalHour = $this->getColumnValue($rowData, ['qf_total_hour', 'total_hour', 'hours']);

        // Clean and validate UTF-8 data
        $qfName = $this->cleanUtf8String($qfName);
        $nqrNo = $this->cleanUtf8String($nqrNo);
        $sector = $this->cleanUtf8String($sector);
        $level = $this->cleanUtf8String($level);
        $qfType = $this->cleanUtf8String($qfType);

        // Validate required fields
        if (empty($qfName)) {
            return ['success' => false, 'skipped' => false, 'error' => 'Qualification name is required'];
        }
        if (empty($nqrNo)) {
            return ['success' => false, 'skipped' => false, 'error' => 'NQR number is required'];
        }
        if (empty($sector)) {
            return ['success' => false, 'skipped' => false, 'error' => 'Sector is required'];
        }
        if (empty($level)) {
            return ['success' => false, 'skipped' => false, 'error' => 'Level is required'];
        }
        if (empty($qfType)) {
            return ['success' => false, 'skipped' => false, 'error' => 'Type is required'];
        }

        // Check for duplicate NQR number
        $existingQualification = Qualification::where('nqr_no', $nqrNo)->first();
        if ($existingQualification) {
            if ($skipDuplicates) {
                Log::info('Skipping duplicate qualification', ['nqr_no' => $nqrNo, 'row' => $rowNumber]);
                return ['success' => false, 'skipped' => true, 'error' => 'Duplicate NQR number'];
            } else {
                return ['success' => false, 'skipped' => false, 'error' => 'NQR number already exists'];
            }
        }

        // Validate numeric fields
        if (!empty($qfTotalHour) && !is_numeric($qfTotalHour)) {
            return ['success' => false, 'skipped' => false, 'error' => 'Total hours must be a number'];
        }

        // Create qualification
        try {
            DB::beginTransaction();
            
            $qualification = Qualification::create([
                'qf_name' => $qfName,
                'nqr_no' => $nqrNo,
                'sector' => $sector,
                'level' => $level,
                'qf_type' => $qfType,
                'qf_total_hour' => !empty($qfTotalHour) ? (int)$qfTotalHour : null,
            ]);

            DB::commit();
            
            Log::info('Qualification created successfully', [
                'id' => $qualification->id,
                'nqr_no' => $nqrNo,
                'row' => $rowNumber
            ]);

            return ['success' => true, 'skipped' => false, 'error' => null];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create qualification', [
                'error' => $e->getMessage(),
                'data' => $rowData,
                'row' => $rowNumber
            ]);
            
            return ['success' => false, 'skipped' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }

    /**
     * Get column value with fallback names
     */
    private function getColumnValue($rowData, $possibleNames)
    {
        foreach ($possibleNames as $name) {
            // Try exact match first
            if (isset($rowData[$name])) {
                return $rowData[$name];
            }
            
            // Try case-insensitive match
            foreach ($rowData as $key => $value) {
                if (strtolower($key) === strtolower($name)) {
                    return $value;
                }
            }
            
            // Try partial match
            foreach ($rowData as $key => $value) {
                if (stripos($key, $name) !== false || stripos($name, $key) !== false) {
                    return $value;
                }
            }
        }
        
        return null;
    }

    /**
     * Clean UTF-8 string and remove invalid characters
     */
    private function cleanUtf8String($string)
    {
        if (empty($string)) {
            return $string;
        }
        
        // Remove any invalid UTF-8 characters
        $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
        
        // Remove any remaining invalid characters
        $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $string);
        
        // Trim whitespace
        $string = trim($string);
        
        return $string;
    }

    /**
     * Download CSV template
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="qualifications_template.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add headers
            fputcsv($file, ['qf_name', 'nqr_no', 'sector', 'level', 'qf_type', 'qf_total_hour']);
            
            // Add sample data
            fputcsv($file, [
                'Sample Qualification Name',
                'NQR-001',
                'Information Technology',
                'Level 4',
                'National Certificate',
                '120'
            ]);
            
            fputcsv($file, [
                'Another Qualification',
                'NQR-002',
                'Healthcare',
                'Level 5',
                'National Diploma',
                '180'
            ]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get modules for mapping (returns HTML interface)
     */
    public function getModulesForMappingHtml($qualificationId)
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

            $qualification = Qualification::with('modules')->findOrFail($qualificationId);
            $allModules = QualificationModule::orderBy('module_name')->get();
            $mappedModuleIds = $qualification->modules->pluck('id')->toArray();

            $html = view('admin.qualifications.partials.modules-mapping', compact('qualification', 'allModules', 'mappedModuleIds'))->render();

            return response()->json([
                'success' => true,
                'html' => $html
            ]);

        } catch (\Exception $e) {
            \Log::error('Get Modules for Mapping HTML Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load modules for mapping'
            ], 500);
        }
    }

    /**
     * Get modules for mapping (returns JSON data)
     */
}
