<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\TcHeaderLayout;
use App\Models\User;

class TcHeaderLayoutController extends Controller
{
    /**
     * Display the header layout management page
     */
    public function index()
    {
        $user = Auth::user();
        
        // Only Assessment Agency (role 4) can access this
        if ($user->user_role !== 4) {
            abort(403, 'Unauthorized access');
        }

        // Get all unique TCs from users table
        $tcs = User::select('from_tc')
            ->whereNotNull('from_tc')
            ->where('from_tc', '!=', '')
            ->distinct()
            ->orderBy('from_tc')
            ->get();

        // Get existing header layouts
        $headerLayouts = TcHeaderLayout::all()->keyBy('tc_id');

        return view('admin.tc-header-layouts.index', compact('tcs', 'headerLayouts', 'user'));
    }

    /**
     * Upload/update header layout for a TC
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

            // Manual validation to avoid fileinfo dependency
            if (!$request->has('tc_id') || empty($request->tc_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'TC ID is required'
                ], 422);
            }

            if (!$request->hasFile('header_layout')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Header layout file is required'
                ], 422);
            }

            $file = $request->file('header_layout');
            
            // Check file size (2MB max)
            if ($file->getSize() > 2 * 1024 * 1024) {
                return response()->json([
                    'success' => false,
                    'message' => 'File size must be less than 2MB'
                ], 422);
            }

            $tcId = $request->tc_id;
            $file = $request->file('header_layout');
            
            // Debug logging
            \Log::info('Header Layout Upload - TC ID: ' . $tcId);
            \Log::info('Header Layout Upload - File exists: ' . ($file ? 'Yes' : 'No'));
            if ($file) {
                \Log::info('Header Layout Upload - File name: ' . $file->getClientOriginalName());
                \Log::info('Header Layout Upload - File size: ' . $file->getSize());
                \Log::info('Header Layout Upload - File mime type: ' . $file->getMimeType());
                
                // Manual file type validation - works without fileinfo extension
                $allowedExtensions = ['png', 'jpg', 'jpeg'];
                $fileExtension = strtolower($file->getClientOriginalExtension());
                
                // Check file extension first
                if (!in_array($fileExtension, $allowedExtensions)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid file type. Only PNG, JPG, and JPEG files are allowed.'
                    ], 422);
                }
                
                // Try to get MIME type if fileinfo is available, otherwise skip
                if (extension_loaded('fileinfo') && class_exists('finfo')) {
                    try {
                        $allowedMimes = ['image/png', 'image/jpeg', 'image/jpg'];
                        $fileMime = $file->getMimeType();
                        if (!in_array($fileMime, $allowedMimes)) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Invalid file type. Only PNG, JPG, and JPEG files are allowed.'
                            ], 422);
                        }
                    } catch (Exception $e) {
                        \Log::warning('Fileinfo not available, using extension validation only: ' . $e->getMessage());
                    }
                } else {
                    \Log::info('Fileinfo extension not available, using extension-based validation only');
                }
            }

            // Check if TC exists in users table
            $tcExists = User::where('from_tc', $tcId)->exists();
            if (!$tcExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tool Room (TC) not found'
                ], 404);
            }

            // Generate unique filename
            $filename = 'tc_' . $tcId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = 'header_layouts/' . $filename;

            // Debug logging
            \Log::info('Header Layout Upload - Filename: ' . $filename);
            \Log::info('Header Layout Upload - Path: ' . $path);
            \Log::info('Header Layout Upload - Storage disk: public');

            // Store the file
            try {
            $file->storeAs('header_layouts', $filename, 'public');
                \Log::info('Header Layout Upload - File stored successfully');
            } catch (\Exception $e) {
                \Log::error('Header Layout Upload - File storage error: ' . $e->getMessage());
                throw $e;
            }

            // Check if header layout already exists for this TC
            $existingLayout = TcHeaderLayout::where('tc_id', $tcId)->first();

            if ($existingLayout) {
                // Delete old file if exists
                if ($existingLayout->storage_path && Storage::disk('public')->exists($existingLayout->storage_path)) {
                    Storage::disk('public')->delete($existingLayout->storage_path);
                }

                // Update existing record
                $existingLayout->update([
                    'header_layout_url' => $path
                ]);

                $headerLayout = $existingLayout;
            } else {
                // Create new record
                $headerLayout = TcHeaderLayout::create([
                    'tc_id' => $tcId,
                    'header_layout_url' => $path
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Header layout uploaded successfully',
                'header_layout' => $headerLayout,
                'image_url' => $headerLayout->header_layout_url
            ]);

        } catch (\Exception $e) {
            \Log::error('Header Layout Upload Error: ' . $e->getMessage());
            \Log::error('Header Layout Upload Error Stack: ' . $e->getTraceAsString());
            \Log::error('Header Layout Upload Error File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload header layout. Please try again. Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete header layout for a TC
     */
    public function destroy($tcId)
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

            $headerLayout = TcHeaderLayout::where('tc_id', $tcId)->first();

            if (!$headerLayout) {
                return response()->json([
                    'success' => false,
                    'message' => 'Header layout not found'
                ], 404);
            }

            // Delete file from storage
            if ($headerLayout->storage_path && Storage::disk('public')->exists($headerLayout->storage_path)) {
                Storage::disk('public')->delete($headerLayout->storage_path);
            }

            // Delete record from database
            $headerLayout->delete();

            return response()->json([
                'success' => true,
                'message' => 'Header layout deleted successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Header Layout Delete Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete header layout. Please try again.'
            ], 500);
        }
    }

    /**
     * Get header layout for a specific TC
     */
    public function show($tcId)
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

            $headerLayout = TcHeaderLayout::where('tc_id', $tcId)->first();

            if (!$headerLayout) {
                return response()->json([
                    'success' => false,
                    'message' => 'Header layout not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'header_layout' => $headerLayout,
                'image_url' => $headerLayout->header_layout_url
            ]);

        } catch (\Exception $e) {
            \Log::error('Header Layout Show Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve header layout'
            ], 500);
        }
    }

    /**
     * Test file upload without validation
     */
    public function testUpload(Request $request)
    {
        try {
            \Log::info('Test upload called');
            
            if (!$request->hasFile('header_layout')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No file uploaded'
                ], 422);
            }

            $file = $request->file('header_layout');
            \Log::info('File received: ' . $file->getClientOriginalName());
            \Log::info('File size: ' . $file->getSize());
            \Log::info('File extension: ' . $file->getClientOriginalExtension());

            return response()->json([
                'success' => true,
                'message' => 'File received successfully',
                'filename' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'extension' => $file->getClientOriginalExtension()
            ]);

        } catch (\Exception $e) {
            \Log::error('Test upload error: ' . $e->getMessage());
            \Log::error('Test upload stack: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test storage setup
     */
    public function testStorage()
    {
        try {
            $storagePath = storage_path('app/public');
            $publicPath = public_path('storage');
            $headerLayoutsPath = storage_path('app/public/header_layouts');
            
            $results = [
                'storage_path_exists' => file_exists($storagePath),
                'public_storage_exists' => file_exists($publicPath),
                'header_layouts_exists' => file_exists($headerLayoutsPath),
                'storage_path' => $storagePath,
                'public_path' => $publicPath,
                'header_layouts_path' => $headerLayoutsPath,
                'app_url' => config('app.url'),
                'filesystem_disk' => config('filesystems.default'),
                'public_disk_url' => config('filesystems.disks.public.url'),
            ];
            
            // Test file creation
            $testFile = $headerLayoutsPath . '/test.txt';
            if (is_writable($headerLayoutsPath)) {
                file_put_contents($testFile, 'test');
                $results['test_file_created'] = file_exists($testFile);
                unlink($testFile);
            } else {
                $results['test_file_created'] = false;
                $results['header_layouts_writable'] = false;
            }
            
            return response()->json([
                'success' => true,
                'storage_info' => $results
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'storage_info' => [
                    'storage_path' => storage_path('app/public'),
                    'public_path' => public_path('storage'),
                    'app_url' => config('app.url')
                ]
            ], 500);
        }
    }

    /**
     * Serve header layout image
     */
    public function serveImage($filename)
    {
        try {
            $filePath = storage_path('app/public/header_layouts/' . $filename);
            
            if (!file_exists($filePath)) {
                abort(404, 'Image not found');
            }
            
            $fileInfo = pathinfo($filePath);
            $extension = strtolower($fileInfo['extension']);
            
            // Set appropriate content type
            $contentTypes = [
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'gif' => 'image/gif'
            ];
            
            $contentType = $contentTypes[$extension] ?? 'application/octet-stream';
            
            return response()->file($filePath, [
                'Content-Type' => $contentType,
                'Cache-Control' => 'public, max-age=31536000', // Cache for 1 year
                'Access-Control-Allow-Origin' => '*'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Image serve error: ' . $e->getMessage());
            abort(404, 'Image not found');
        }
    }
}
