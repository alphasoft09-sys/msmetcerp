<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TcLms;
use App\Models\LmsDepartment;
use App\Services\ContentImageProcessor;

class TcLmsController extends Controller
{
    /**
     * Display a listing of LMS sites for faculty
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->user_role !== 5) {
            abort(403, 'Unauthorized access. Only Faculty can manage LMS sites.');
        }

        $query = TcLms::where('faculty_code', $user->email);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department')) {
            $query->where('site_department', $request->department);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('site_title', 'like', "%{$search}%")
                  ->orWhere('site_url', 'like', "%{$search}%")
                  ->orWhere('site_department', 'like', "%{$search}%");
            });
        }

        $lmsSites = $query->with('tcShotCode')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $departments = LmsDepartment::active()->orderBy('department_name')->get();

        return view('admin.tc-lms.index', compact('lmsSites', 'departments'));
    }

    /**
     * Show the form for creating a new LMS site
     */
    public function create()
    {
        $user = Auth::user();
        
        if ($user->user_role !== 5) {
            abort(403, 'Unauthorized access. Only Faculty can create LMS sites.');
        }

        $departments = LmsDepartment::active()->orderBy('department_name')->get();
        
        return view('admin.tc-lms.create', compact('departments'));
    }

    /**
     * Store a newly created LMS site
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if ($user->user_role !== 5) {
            abort(403, 'Unauthorized access. Only Faculty can create LMS sites.');
        }

        $request->validate([
            'site_title' => 'required|string|max:255|unique:all_tc_lms,site_title,NULL,id,faculty_code,' . $user->email,
            'site_department' => 'required|string|exists:lms_departments,department_name',
            'site_description' => 'nullable|string|max:1000',
            'site_contents' => 'nullable|string',
            'seo_title' => 'nullable|string|max:60',
            'seo_description' => 'nullable|string|max:160',
            'seo_keywords' => 'nullable|string|max:500',
        ]);

        $siteUrl = TcLms::generateUniqueSiteUrl($request->site_title, $user->from_tc);

        // Generate SEO slug
        $seoSlug = \Str::slug($request->site_title);
        
        // Generate structured data for educational content
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'EducationalOccupationalProgram',
            'name' => $request->site_title,
            'description' => $request->site_description,
            'provider' => [
                '@type' => 'Organization',
                'name' => 'Educational LMS System'
            ],
            'educationalLevel' => 'Undergraduate',
            'occupationalCategory' => $request->site_department,
            'url' => url('/lms/' . $seoSlug)
        ];

        $lmsSite = TcLms::create([
            'tc_code' => $user->from_tc,
            'faculty_code' => $user->email,
            'site_url' => $siteUrl,
            'site_department' => $request->site_department,
            'site_title' => $request->site_title,
            'site_description' => $request->site_description,
            'site_contents' => $request->site_contents, // Store as string directly
            'seo_title' => $request->seo_title ?: $request->site_title,
            'seo_description' => $request->seo_description ?: $request->site_description,
            'seo_keywords' => $request->seo_keywords,
            'seo_slug' => $seoSlug,
            'structured_data' => json_encode($structuredData),
            'status' => 'draft',
        ]);

        return redirect()->route('admin.tc-lms.edit', $lmsSite)
            ->with('success', 'LMS site created successfully. You can now design your site.');
    }

    /**
     * Check if site title is duplicate
     */
    public function checkTitleDuplicate(Request $request)
    {
        $user = Auth::user();
        
        if ($user->user_role !== 5) {
            return response()->json(['available' => false, 'message' => 'Unauthorized access'], 403);
        }

        $request->validate([
            'title' => 'required|string|min:3|max:255'
        ]);

        $title = trim($request->title);
        
        // Check if title already exists for this faculty
        $exists = TcLms::where('faculty_code', $user->email)
                       ->where('site_title', $title)
                       ->exists();

        if ($exists) {
            return response()->json([
                'available' => false,
                'message' => 'This title is already taken. Please choose a different one.'
            ]);
        }

        return response()->json([
            'available' => true,
            'message' => 'Title is available'
        ]);
    }

    /**
     * Display the specified LMS site
     */
    public function show(TcLms $tcLm)
    {
        \Log::info('=== SHOW METHOD CALLED ===');
        \Log::info('Request Method: ' . request()->method());
        \Log::info('Request URL: ' . request()->fullUrl());
        \Log::info('Is AJAX: ' . (request()->ajax() ? 'true' : 'false'));
        \Log::info('LMS Site ID: ' . $tcLm->id);
        \Log::info('=== SHOW METHOD DEBUG END ===');
        
        $user = Auth::user();
        
        if ($user->user_role !== 5) {
            abort(403, 'Unauthorized access. Only Faculty can view LMS sites.');
        }

        if ($tcLm->faculty_code !== $user->email) {
            abort(403, 'Unauthorized access. You can only view your own LMS sites.');
        }

        return view('admin.tc-lms.show', compact('tcLm'));
    }

    /**
     * Show the form for editing the specified LMS site
     */
    public function edit(TcLms $tcLm)
    {
        $user = Auth::user();
        
        if ($user->user_role !== 5) {
            abort(403, 'Unauthorized access. Only Faculty can edit LMS sites.');
        }

        if ($tcLm->faculty_code !== $user->email) {
            abort(403, 'Unauthorized access. You can only edit your own LMS sites.');
        }

        // Check if site can be edited
        if (!$tcLm->canBeEditedByFaculty()) {
            return redirect()->route('admin.tc-lms.index')
                ->with('error', 'This site cannot be edited. Only draft sites or approved sites with admin permission can be edited.');
        }

        $departments = LmsDepartment::active()->orderBy('department_name')->get();
        $tcLm->load('media');

        return view('admin.tc-lms.edit', compact('tcLm', 'departments'));
    }

    /**
     * Update the specified LMS site
     */
    public function update(Request $request, TcLms $tcLm)
    {
        $user = Auth::user();
        
        // Debug: Log comprehensive request information
        \Log::info('=== LMS UPDATE REQUEST DEBUG START ===');
        \Log::info('Timestamp: ' . now());
        \Log::info('User: ' . ($user ? $user->email : 'NULL'));
        \Log::info('User Role: ' . ($user ? $user->user_role : 'NULL'));
        \Log::info('User ID: ' . ($user ? $user->id : 'NULL'));
        \Log::info('LMS Site ID: ' . $tcLm->id);
        \Log::info('Faculty Code: ' . $tcLm->faculty_code);
        \Log::info('Request URL: ' . $request->fullUrl());
        \Log::info('Request Method: ' . $request->method());
        \Log::info('Is AJAX: ' . ($request->ajax() ? 'true' : 'false'));
        \Log::info('Request Headers: ' . json_encode($request->headers->all()));
        \Log::info('Request IP: ' . $request->ip());
        \Log::info('User Agent: ' . $request->userAgent());
        \Log::info('Session ID: ' . $request->session()->getId());
        \Log::info('CSRF Token: ' . $request->header('X-CSRF-TOKEN'));
        \Log::info('=== LMS UPDATE REQUEST DEBUG END ===');
        
        if ($user->user_role !== 5) {
            \Log::warning('User role mismatch - Expected: 5, Got: ' . $user->user_role);
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access. Only Faculty can update LMS sites.'], 403);
            }
            abort(403, 'Unauthorized access. Only Faculty can update LMS sites.');
        }

        if ($tcLm->faculty_code !== $user->email) {
            \Log::warning('Faculty code mismatch - Site faculty: ' . $tcLm->faculty_code . ', User email: ' . $user->email);
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access. You can only update your own LMS sites.'], 403);
            }
            abort(403, 'Unauthorized access. You can only update your own LMS sites.');
        }

        // Check if site can be edited
        if (!$tcLm->canBeEditedByFaculty()) {
            // Log the current site status for debugging
            \Log::info('Site cannot be edited. Status: ' . $tcLm->status . ', Can edit after approval: ' . ($tcLm->can_edit_after_approval ? 'true' : 'false'));
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'This site cannot be edited. Only draft sites or approved sites with admin permission can be edited.'], 400);
            }
            return redirect()->back()->with('error', 'This site cannot be edited. Only draft sites or approved sites with admin permission can be edited.');
        }

        // Debug: Log request data
        \Log::info('Request data received:');
        \Log::info('site_title: ' . ($request->site_title ?? 'NULL'));
        \Log::info('site_department: ' . ($request->site_department ?? 'NULL'));
        \Log::info('site_description: ' . ($request->site_description ?? 'NULL'));
        \Log::info('status: ' . ($request->status ?? 'NULL'));
        \Log::info('is_ajax: ' . ($request->ajax() ? 'true' : 'false'));
        \Log::info('CSRF token: ' . ($request->header('X-CSRF-TOKEN') ?? 'NULL'));
        \Log::info('Request method: ' . $request->method());
        
        $request->validate([
            'site_title' => 'required|string|max:255',
            'site_department' => 'required|string|exists:lms_departments,department_name',
            'site_description' => 'nullable|string|max:1000',
            'site_contents' => 'nullable|string|max:16777215', // MEDIUMTEXT column limit (16MB)
            'status' => 'nullable|string|in:draft,submitted', // Allow status update
        ]);

        try {
            // Clean old JSON data if present
            $siteContents = $request->site_contents;
            
            // Debug: Log what we're receiving
            \Log::info('Received content length: ' . strlen($siteContents));
            \Log::info('Content preview: ' . substr($siteContents, 0, 200));
            
            if (is_string($siteContents) && strpos($siteContents, 'component_') !== false && strpos($siteContents, '"type":"heading"') !== false) {
                \Log::info('Detected old JSON format, clearing content');
                $siteContents = ''; // Clear old JSON data
            }
            
            // Process images in content (extract base64 images to files)
            $imageProcessor = new ContentImageProcessor();
            
            // Process content first to get the final content with all images
            $siteContents = $imageProcessor->processContent($siteContents, $tcLm->id);
            
            // Then clean up unused images (images not referenced in the final content)
            $imageProcessor->cleanupUnusedImages($tcLm->id, $siteContents);
            
            // Check content size
            if ($imageProcessor->isContentTooLarge($siteContents)) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Content is too large. Please reduce the number of images or image sizes.'], 400);
                }
                return redirect()->back()
                    ->with('error', 'Content is too large. Please reduce the number of images or image sizes.')
                    ->withInput();
            }
            
            $updateData = [
                'site_title' => $request->site_title,
                'site_department' => $request->site_department,
                'site_description' => $request->site_description,
                'site_contents' => $siteContents, // Store as string directly, not JSON
            ];
            
            // Update status if provided
            if ($request->has('status')) {
                $updateData['status'] = $request->status;
            }
            
            // Retry database operation if connection fails
            $maxRetries = 3;
            $retryCount = 0;
            
            while ($retryCount < $maxRetries) {
                try {
                    $tcLm->update($updateData);
                    break; // Success, exit retry loop
                } catch (\Illuminate\Database\QueryException $e) {
                    $retryCount++;
                    
                    // Check if it's a connection issue
                    if (strpos($e->getMessage(), 'MySQL server has gone away') !== false || 
                        strpos($e->getMessage(), 'Lost connection') !== false) {
                        
                        if ($retryCount < $maxRetries) {
                            // Wait before retry
                            usleep(500000); // 0.5 seconds
                            continue;
                        }
                    }
                    
                    throw $e; // Re-throw if not a connection issue or max retries reached
                }
            }
            
            // Return JSON response for AJAX requests
            if ($request->ajax()) {
                \Log::info('Sending success response for AJAX request');
                return response()->json(['success' => true, 'message' => 'LMS site updated successfully.']);
            }
            
            return redirect()->route('admin.tc-lms.index')
                ->with('success', 'LMS site updated successfully.');
                
        } catch (\Exception $e) {
            // Handle any general exceptions
            \Log::error('=== LMS UPDATE EXCEPTION DEBUG START ===');
            \Log::error('Exception Type: ' . get_class($e));
            \Log::error('Exception Message: ' . $e->getMessage());
            \Log::error('Exception File: ' . $e->getFile() . ':' . $e->getLine());
            \Log::error('Exception Trace: ' . $e->getTraceAsString());
            \Log::error('Request URL: ' . $request->fullUrl());
            \Log::error('Request Method: ' . $request->method());
            \Log::error('User: ' . (Auth::user() ? Auth::user()->email : 'NULL'));
            \Log::error('=== LMS UPDATE EXCEPTION DEBUG END ===');
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
            }
            
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle database-specific errors
            \Log::error('Database QueryException: ' . $e->getMessage());
            \Log::error('Database QueryException trace: ' . $e->getTraceAsString());
            
            $errorMessage = 'Database error occurred. ';
            
            if (strpos($e->getMessage(), 'MySQL server has gone away') !== false) {
                $errorMessage .= 'Please try again. The database connection was lost.';
            } elseif (strpos($e->getMessage(), 'Lost connection') !== false) {
                $errorMessage .= 'Database connection lost. Please try again.';
            } else {
                $errorMessage .= $e->getMessage();
            }
            
            if ($request->ajax()) {
                \Log::error('Sending database error response for AJAX request: ' . $errorMessage);
                return response()->json(['success' => false, 'message' => $errorMessage], 500);
            }
            
            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
                
        } catch (\Exception $e) {
            // Handle other errors
            \Log::error('General Exception: ' . $e->getMessage());
            \Log::error('General Exception trace: ' . $e->getTraceAsString());
            
            if ($request->ajax()) {
                \Log::error('Sending general error response for AJAX request: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Error saving content: ' . $e->getMessage()], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Error saving content: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Submit LMS site for approval
     */
    public function submit(TcLms $tcLm)
    {
        $user = Auth::user();
        
        if ($user->user_role !== 5) {
            abort(403, 'Unauthorized access. Only Faculty can submit LMS sites.');
        }

        if ($tcLm->faculty_code !== $user->email) {
            abort(403, 'Unauthorized access. You can only submit your own LMS sites.');
        }

        if (empty($tcLm->site_contents)) {
            return redirect()->route('admin.tc-lms.edit', $tcLm)
                ->with('error', 'Please add content to your site before submitting for approval.');
        }

        $tcLm->update([
            'status' => 'submitted'
        ]);

        return redirect()->route('admin.tc-lms.index')
            ->with('success', 'LMS site submitted for approval successfully.');
    }

    /**
     * Preview LMS site
     */
    public function preview(TcLms $tcLm)
    {
        $user = Auth::user();
        
        if ($user->user_role !== 5) {
            abort(403, 'Unauthorized access. Only Faculty can preview LMS sites.');
        }

        if ($tcLm->faculty_code !== $user->email) {
            abort(403, 'Unauthorized access. You can only preview your own LMS sites.');
        }

        $tcLm->load(['tcShotCode', 'faculty']);

        return view('admin.tc-lms.preview', compact('tcLm'));
    }

    /**
     * Soft delete LMS site
     */
    public function destroy(TcLms $tcLm)
    {
        $user = Auth::user();
        
        if ($user->user_role !== 5) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access. Only Faculty can delete LMS sites.'], 403);
            }
            abort(403, 'Unauthorized access. Only Faculty can delete LMS sites.');
        }

        if ($tcLm->faculty_code !== $user->email) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access. You can only delete your own LMS sites.'], 403);
            }
            abort(403, 'Unauthorized access. You can only delete your own LMS sites.');
        }

        // Check if site can be deleted
        if (!$tcLm->canBeDeletedByFaculty()) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'This site cannot be deleted. Only draft sites can be deleted.'], 400);
            }
            return redirect()->back()->with('error', 'This site cannot be deleted. Only draft sites can be deleted.');
        }

        try {
            // Soft delete the site
            $tcLm->delete();

            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'LMS site deleted successfully.']);
            }

            return redirect()->route('admin.tc-lms.index')
                ->with('success', 'LMS site deleted successfully.');
                
        } catch (\Exception $e) {
            \Log::error('Error deleting LMS site: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error deleting site: ' . $e->getMessage()], 500);
            }
            
            return redirect()->back()->with('error', 'Error deleting site: ' . $e->getMessage());
        }
    }

    /**
     * Admin index for approval (Assessment Agency - Role 4)
     */
    public function adminIndex(Request $request)
    {
        $user = Auth::user();
        
        if ($user->user_role !== 4) {
            abort(403, 'Unauthorized access. Only Assessment Agency can approve LMS sites.');
        }

        $query = TcLms::with(['faculty', 'tcShotCode']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department')) {
            $query->where('site_department', $request->department);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('site_title', 'like', "%{$search}%")
                  ->orWhere('site_url', 'like', "%{$search}%")
                  ->orWhere('site_department', 'like', "%{$search}%");
            });
        }

        $lmsSites = $query->orderBy('created_at', 'desc')->paginate(15);
        $departments = LmsDepartment::active()->orderBy('department_name')->get();

        return view('admin.tc-lms.admin-index', compact('lmsSites', 'departments'));
    }

    /**
     * Approve LMS site (Assessment Agency - Role 4)
     */
    public function approve(Request $request, TcLms $tcLm)
    {
        $user = Auth::user();
        
        if ($user->user_role !== 4) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access. Only Assessment Agency can approve LMS sites.'], 403);
            }
            abort(403, 'Unauthorized access. Only Assessment Agency can approve LMS sites.');
        }

        try {
            $tcLm->update([
                'is_approved' => true,
                'approved_by' => $user->id,
                'approved_at' => now(),
                'status' => 'approved'
            ]);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'LMS site approved successfully!']);
            }

            return redirect()->route('admin.tc-lms.admin-index')
                ->with('success', 'LMS site approved successfully.');
        } catch (\Exception $e) {
            \Log::error('Error approving LMS site: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error approving site: ' . $e->getMessage()], 500);
            }
            
            return redirect()->back()->with('error', 'Error approving site: ' . $e->getMessage());
        }
    }

    /**
     * Reject LMS site (Assessment Agency - Role 4)
     */
    public function reject(Request $request, TcLms $tcLm)
    {
        $user = Auth::user();
        
        if ($user->user_role !== 4) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access. Only Assessment Agency can reject LMS sites.'], 403);
            }
            abort(403, 'Unauthorized access. Only Assessment Agency can reject LMS sites.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        try {
            $tcLm->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'rejected_by' => $user->id,
                'rejected_at' => now(),
                'site_description' => $tcLm->site_description . "\n\nRejection Reason: " . $request->rejection_reason
            ]);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'LMS site rejected successfully!']);
            }

            return redirect()->route('admin.tc-lms.admin-index')
                ->with('success', 'LMS site rejected successfully.');
        } catch (\Exception $e) {
            \Log::error('Error rejecting LMS site: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error rejecting site: ' . $e->getMessage()], 500);
            }
            
            return redirect()->back()->with('error', 'Error rejecting site: ' . $e->getMessage());
        }
    }

    /**
     * Grant edit permission to faculty for approved site (Assessment Agency - Role 4)
     */
    public function grantEditPermission(Request $request, TcLms $tcLm)
    {
        $user = Auth::user();
        
        if ($user->user_role !== 4) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access. Only Assessment Agency can grant edit permissions.'], 403);
            }
            abort(403, 'Unauthorized access. Only Assessment Agency can grant edit permissions.');
        }

        try {
            $tcLm->update([
                'can_edit_after_approval' => true
            ]);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Edit permission granted successfully! Faculty can now edit this site.']);
            }

            return redirect()->route('admin.tc-lms.admin-index')
                ->with('success', 'Edit permission granted successfully! Faculty can now edit this site.');
        } catch (\Exception $e) {
            \Log::error('Error granting edit permission: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error granting edit permission: ' . $e->getMessage()], 500);
            }
            
            return redirect()->back()->with('error', 'Error granting edit permission: ' . $e->getMessage());
        }
    }

    /**
     * Revoke edit permission from faculty for approved site (Assessment Agency - Role 4)
     */
    public function revokeEditPermission(Request $request, TcLms $tcLm)
    {
        $user = Auth::user();
        
        if ($user->user_role !== 4) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access. Only Assessment Agency can revoke edit permissions.'], 403);
            }
            abort(403, 'Unauthorized access. Only Assessment Agency can revoke edit permissions.');
        }

        try {
            $tcLm->update([
                'can_edit_after_approval' => false
            ]);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Edit permission revoked successfully! Faculty can no longer edit this site.']);
            }

            return redirect()->route('admin.tc-lms.admin-index')
                ->with('success', 'Edit permission revoked successfully! Faculty can no longer edit this site.');
        } catch (\Exception $e) {
            \Log::error('Error revoking edit permission: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error revoking edit permission: ' . $e->getMessage()], 500);
            }
            
            return redirect()->back()->with('error', 'Error revoking edit permission: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete LMS site (Assessment Agency - Role 4)
     */
    public function adminDestroy(TcLms $tcLm)
    {
        $user = Auth::user();
        
        if ($user->user_role !== 4) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access. Only Assessment Agency can delete LMS sites.'], 403);
            }
            abort(403, 'Unauthorized access. Only Assessment Agency can delete LMS sites.');
        }

        try {
            // Soft delete the site
            $tcLm->delete();

            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'LMS site deleted successfully.']);
            }

            return redirect()->route('admin.tc-lms.admin-index')
                ->with('success', 'LMS site deleted successfully.');
                
        } catch (\Exception $e) {
            \Log::error('Error deleting LMS site: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error deleting site: ' . $e->getMessage()], 500);
            }
            
            return redirect()->back()->with('error', 'Error deleting site: ' . $e->getMessage());
        }
    }

    /**
     * Get preview content for AJAX
     */
    public function getPreview(Request $request, TcLms $tcLm)
    {
        try {
            $content = $tcLm->site_contents ?? '';
            
            // If content is old JSON format, return empty
            if (is_string($content) && strpos($content, 'component_') !== false && strpos($content, '"type":"heading"') !== false) {
                $content = '';
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'site_title' => $tcLm->site_title,
                    'site_department' => $tcLm->site_department,
                    'site_description' => $tcLm->site_description,
                    'content' => $content,
                    'status' => $tcLm->status,
                    'created_at' => $tcLm->created_at->format('M d, Y H:i'),
                    'faculty_name' => $tcLm->faculty->name,
                    'faculty_email' => $tcLm->faculty->email,
                    'tc_code' => $tcLm->tc_code,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting preview: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading preview: ' . $e->getMessage()
            ], 500);
        }
    }
}