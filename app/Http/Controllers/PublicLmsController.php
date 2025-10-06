<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TcLms;
use App\Models\LmsDepartment;

class PublicLmsController extends Controller
{
    /**
     * Display the main LMS page
     */
    public function index(Request $request)
    {
        // Only show approved and published content
        $query = TcLms::where('status', 'approved')
            ->where('is_approved', 1)
            ->whereNotNull('site_title')
            ->where('site_title', '!=', '')
            ->with(['faculty' => function($q) {
                $q->select('email', 'name');
            }]);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('site_title', 'like', "%{$search}%")
                  ->orWhere('site_description', 'like', "%{$search}%")
                  ->orWhere('seo_keywords', 'like', "%{$search}%");
            });
        }

        // Filter by multiple departments
        if ($request->filled('departments')) {
            $departments = is_array($request->departments) ? $request->departments : [$request->departments];
            $query->whereIn('site_department', $departments);
        }

        // Filter by faculty (using faculty names instead of emails for privacy)
        if ($request->filled('faculty')) {
            $facultyNames = is_array($request->faculty) ? $request->faculty : [$request->faculty];
            $query->whereHas('faculty', function($q) use ($facultyNames) {
                $q->whereIn('name', $facultyNames);
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sort options
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['created_at', 'site_title', 'site_department'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Set per page limit for initial load
        $perPage = $request->get('per_page', 6);
        $lmsSites = $query->paginate($perPage);
        
        // Get unique departments from actual data
        $departments = TcLms::where('status', 'approved')
            ->where('is_approved', 1)
            ->whereNotNull('site_department')
            ->distinct()
            ->pluck('site_department')
            ->map(function($dept) {
                return (object)[
                    'department_name' => $dept,
                    'department_slug' => \Str::slug($dept),
                    'count' => TcLms::where('status', 'approved')
                        ->where('is_approved', 1)
                        ->where('site_department', $dept)
                        ->count()
                ];
            });

        // Get unique faculty from actual data (using names for privacy)
        $faculty = TcLms::where('status', 'approved')
            ->where('is_approved', 1)
            ->whereNotNull('faculty_code')
            ->with(['faculty' => function($q) {
                $q->select('email', 'name');
            }])
            ->get()
            ->groupBy('faculty_code')
            ->map(function($sites, $facultyCode) {
                $firstSite = $sites->first();
                return (object)[
                    'faculty_code' => $facultyCode, // Keep for internal reference
                    'faculty_name' => $firstSite->faculty->name ?? 'Unknown Faculty',
                    'faculty_display_name' => $firstSite->faculty->name ?? 'Unknown Faculty', // Use name as value
                    'count' => $sites->count()
                ];
            });

        // Get statistics from actual data
        $stats = [
            'total_courses' => TcLms::where('status', 'approved')->where('is_approved', 1)->whereNotNull('site_title')->where('site_title', '!=', '')->count(),
            'total_departments' => $departments->count(),
            'total_faculty' => $faculty->count(),
        ];

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->isMethod('post')) {
            // Debug pagination info
            \Log::info('Index AJAX Response', [
                'totalItems' => $lmsSites->total(),
                'currentPage' => $lmsSites->currentPage(),
                'lastPage' => $lmsSites->lastPage(),
                'hasMorePages' => $lmsSites->hasMorePages(),
                'itemsOnCurrentPage' => $lmsSites->count(),
                'perPage' => $perPage
            ]);
            
            return response()->json([
                'success' => true,
                'html' => view('lms.partials.content-cards', compact('lmsSites'))->render(),
                'hasMorePages' => $lmsSites->hasMorePages(),
                'currentPage' => $lmsSites->currentPage(),
                'totalPages' => $lmsSites->lastPage(),
                'totalItems' => $lmsSites->total(),
                'stats' => $stats
            ]);
        }

        // Pass URL parameters to view for checkbox checking
        $urlParams = $request->all();
        
        return view('lms.index', compact('lmsSites', 'departments', 'faculty', 'stats', 'urlParams'));
    }

    /**
     * Search LMS content
     */
    public function search(Request $request)
    {
        $query = TcLms::where('status', 'approved')
            ->where('is_approved', true)
            ->with('faculty');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('site_title', 'like', "%{$search}%")
                  ->orWhere('site_description', 'like', "%{$search}%")
                  ->orWhere('seo_keywords', 'like', "%{$search}%");
            });
        }

        $results = $query->orderBy('created_at', 'desc')->paginate(12);

        return view('lms.search-results', compact('results'));
    }

    /**
     * Load more content via AJAX
     */
    public function loadMore(Request $request)
    {
        try {
            // Log the request for debugging
            \Log::info('Load More Request', [
                'request_data' => $request->all(),
                'csrf_token' => $request->header('X-CSRF-TOKEN'),
                'ajax' => $request->ajax(),
                'method' => $request->method()
            ]);
            
            // Only show approved and published content
        $query = TcLms::where('status', 'approved')
            ->where('is_approved', 1)
            ->whereNotNull('site_title')
            ->where('site_title', '!=', '')
            ->with(['faculty' => function($q) {
                $q->select('email', 'name');
            }]);

        // Apply same filters as index method
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('site_title', 'like', "%{$search}%")
                  ->orWhere('site_description', 'like', "%{$search}%")
                  ->orWhere('seo_keywords', 'like', "%{$search}%");
            });
        }

        if ($request->filled('departments')) {
            $departments = is_array($request->departments) ? $request->departments : [$request->departments];
            $query->whereIn('site_department', $departments);
        }

        if ($request->filled('faculty')) {
            $facultyNames = is_array($request->faculty) ? $request->faculty : [$request->faculty];
            $query->whereHas('faculty', function($q) use ($facultyNames) {
                $q->whereIn('name', $facultyNames);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['created_at', 'site_title', 'site_department'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Load more content (6 more items)
        $page = $request->get('page', 2);
        $perPage = 6;
        
        $lmsSites = $query->paginate($perPage, ['*'], 'page', $page);
        
        // Debug information
        \Log::info('Load More Debug', [
            'page' => $page,
            'perPage' => $perPage,
            'totalItems' => $lmsSites->total(),
            'currentPage' => $lmsSites->currentPage(),
            'lastPage' => $lmsSites->lastPage(),
            'hasMorePages' => $lmsSites->hasMorePages(),
            'itemsOnCurrentPage' => $lmsSites->count(),
            'requestData' => $request->all()
        ]);
        
        // Generate HTML content
        $html = '';
        if ($lmsSites->count() > 0) {
            $html = view('lms.partials.content-cards', compact('lmsSites'))->render();
            \Log::info('Load More HTML Generated', [
                'html_length' => strlen($html),
                'html_preview' => substr($html, 0, 200) . '...',
                'items_count' => $lmsSites->count()
            ]);
        }
        
        // Always return success, but with appropriate data
        return response()->json([
            'success' => true,
            'html' => $html,
            'hasMorePages' => $lmsSites->hasMorePages(),
            'nextPage' => $lmsSites->currentPage() + 1,
            'currentPage' => $lmsSites->currentPage(),
            'totalPages' => $lmsSites->lastPage(),
            'totalItems' => $lmsSites->total(),
            'itemsOnCurrentPage' => $lmsSites->count(),
            'noMoreContent' => $lmsSites->count() === 0
        ]);
        } catch (\Exception $e) {
            \Log::error('Load More Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while loading content',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show department-specific content
     */
    public function department($departmentSlug)
    {
        // Convert slug back to department name
        $departmentName = str_replace('-', ' ', $departmentSlug);
        $departmentName = ucwords($departmentName);
        
        // Check if department exists in actual data
        $existingDepartments = TcLms::where('status', 'approved')
            ->where('is_approved', 1)
            ->whereNotNull('site_department')
            ->distinct()
            ->pluck('site_department')
            ->toArray();
        
        // Try to find exact match first
        $department = null;
        foreach ($existingDepartments as $dept) {
            if (strtolower(str_replace(' ', '-', $dept)) === $departmentSlug) {
                $department = $dept;
                break;
            }
        }
        
        // If no exact match, try case-insensitive
        if (!$department) {
            foreach ($existingDepartments as $dept) {
                if (strtolower(str_replace(' ', '-', $dept)) === strtolower($departmentSlug)) {
                    $department = $dept;
                    break;
                }
            }
        }
        
        if (!$department) {
            abort(404, 'Department not found');
        }

        $lmsSites = TcLms::where('status', 'approved')
            ->where('is_approved', 1)
            ->where('site_department', $department)
            ->whereNotNull('site_title')
            ->where('site_title', '!=', '')
            ->with(['faculty' => function($q) {
                $q->select('email', 'name');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Get all departments for filter
        $departments = TcLms::where('status', 'approved')
            ->where('is_approved', 1)
            ->whereNotNull('site_department')
            ->distinct()
            ->pluck('site_department')
            ->map(function($dept) {
                return (object)[
                    'department_name' => $dept,
                    'department_slug' => \Str::slug($dept),
                    'count' => TcLms::where('status', 'approved')
                        ->where('is_approved', 1)
                        ->where('site_department', $dept)
                        ->count()
                ];
            });

        // Get faculty for filter (using names for privacy)
        $faculty = TcLms::where('status', 'approved')
            ->where('is_approved', 1)
            ->whereNotNull('faculty_code')
            ->with(['faculty' => function($q) {
                $q->select('email', 'name');
            }])
            ->get()
            ->groupBy('faculty_code')
            ->map(function($sites, $facultyCode) {
                $firstSite = $sites->first();
                return (object)[
                    'faculty_code' => $facultyCode, // Keep for internal reference
                    'faculty_name' => $firstSite->faculty->name ?? 'Unknown Faculty',
                    'faculty_display_name' => $firstSite->faculty->name ?? 'Unknown Faculty', // Use name as value
                    'count' => $sites->count()
                ];
            });

        // Get statistics
        $stats = [
            'total_courses' => $lmsSites->total(),
            'total_departments' => $departments->count(),
            'total_faculty' => $faculty->count(),
        ];

        // Return JSON for AJAX requests
        if (request()->ajax() || request()->isMethod('post')) {
            return response()->json([
                'success' => true,
                'html' => view('lms.partials.content-grid', compact('lmsSites'))->render(),
                'pagination' => $lmsSites->links()->render(),
                'stats' => $stats
            ]);
        }

        // Pass URL parameters to view for checkbox checking
        $urlParams = request()->all();
        
        // If this is a department page, automatically check the department
        if ($department) {
            $urlParams['departments'] = [$department];
        }
        
        return view('lms.index', compact('lmsSites', 'departments', 'faculty', 'stats', 'urlParams'));
    }

    /**
     * Show individual LMS site
     */
    public function show($departmentSlug, $siteUrl)
    {
        $lmsSite = TcLms::where('site_url', $siteUrl)
            ->where('status', 'approved')
            ->where('is_approved', 1)
            ->whereNotNull('site_title')
            ->where('site_title', '!=', '')
            ->with(['faculty' => function($q) {
                $q->select('email', 'name');
            }])
            ->first();

        if (!$lmsSite) {
            abort(404, 'Content not found or not approved for public access.');
        }
        
        // Fix image URLs in content if needed
        if ($lmsSite->site_contents) {
            $lmsSite->site_contents = $this->fixImageUrls($lmsSite->site_contents);
        }

        return view('lms.show', compact('lmsSite'));
    }
    
    /**
     * Fix image URLs in content to ensure they work on the server
     */
    private function fixImageUrls($content)
    {
        if (empty($content)) {
            return $content;
        }
        
        \Log::info('Fixing image URLs in content');
        
        // Get the server's domain
        $serverUrl = request()->getSchemeAndHttpHost();
        
        // Find image tags with src attributes
        $pattern = '/<img[^>]*src=["\']([^"\']*)["\'][^>]*>/i';
        
        $fixedContent = preg_replace_callback($pattern, function($matches) use ($serverUrl) {
            $originalUrl = $matches[1];
            $fixedUrl = $originalUrl;
            
            \Log::info('Found image URL: ' . $originalUrl);
            
            // If it's a relative URL starting with /storage
            if (strpos($originalUrl, '/storage/') === 0) {
                $fixedUrl = $serverUrl . $originalUrl;
                \Log::info('Fixed to absolute URL: ' . $fixedUrl);
            }
            
            // Replace the URL in the img tag
            return str_replace($originalUrl, $fixedUrl, $matches[0]);
        }, $content);
        
        return $fixedContent;
    }

    /**
     * Preview LMS site (for faculty)
     */
    public function preview($departmentSlug, $siteUrl)
    {
        $lmsSite = TcLms::where('site_url', $siteUrl)
            ->with('faculty')
            ->firstOrFail();
            
        // Fix image URLs in content if needed
        if ($lmsSite->site_contents) {
            $lmsSite->site_contents = $this->fixImageUrls($lmsSite->site_contents);
        }

        return view('lms.preview', compact('lmsSite'));
    }

    /**
     * Get LMS content via AJAX
     */
    public function getContent($departmentSlug, $siteUrl)
    {
        $lmsSite = TcLms::where('site_url', $siteUrl)
            ->where('status', 'approved')
            ->where('is_approved', true)
            ->firstOrFail();
            
        // Fix image URLs in content if needed
        $content = $lmsSite->site_contents;
        if ($content) {
            $content = $this->fixImageUrls($content);
        }

        return response()->json([
            'success' => true,
            'content' => $content,
            'title' => $lmsSite->site_title,
            'description' => $lmsSite->site_description
        ]);
    }
}