<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LmsDepartment;
use Illuminate\Support\Str;

class LmsDepartmentController extends Controller
{
    /**
     * Display a listing of departments (Admin only - Role 4)
     */
    public function index()
    {
        $user = Auth::user();
        
        // Check if user is Assessment Agency (Role 4)
        if ($user->user_role !== 4) {
            abort(403, 'Unauthorized access. Only Assessment Agency can manage departments.');
        }

        $departments = LmsDepartment::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.lms-departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new department
     */
    public function create()
    {
        $user = Auth::user();
        
        if ($user->user_role !== 4) {
            abort(403, 'Unauthorized access. Only Assessment Agency can create departments.');
        }

        return view('admin.lms-departments.create');
    }

    /**
     * Store a newly created department
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if ($user->user_role !== 4) {
            abort(403, 'Unauthorized access. Only Assessment Agency can create departments.');
        }

        $request->validate([
            'department_name' => 'required|string|max:255|unique:lms_departments,department_name',
            'description' => 'nullable|string|max:1000',
        ]);

        $department = LmsDepartment::create([
            'department_name' => $request->department_name,
            'department_slug' => LmsDepartment::generateUniqueSlug($request->department_name),
            'description' => $request->description,
            'created_by' => $user->id,
        ]);

        return redirect()->route('admin.lms-departments.index')
            ->with('success', 'Department created successfully.');
    }

    /**
     * Display the specified department
     */
    public function show(LmsDepartment $lmsDepartment)
    {
        $user = Auth::user();
        
        if ($user->user_role !== 4) {
            abort(403, 'Unauthorized access. Only Assessment Agency can view departments.');
        }

        $lmsDepartment->load(['creator', 'lmsSites.faculty']);
        
        return view('admin.lms-departments.show', compact('lmsDepartment'));
    }

    /**
     * Show the form for editing the specified department
     */
    public function edit(LmsDepartment $lmsDepartment)
    {
        $user = Auth::user();
        
        if ($user->user_role !== 4) {
            abort(403, 'Unauthorized access. Only Assessment Agency can edit departments.');
        }

        return view('admin.lms-departments.edit', compact('lmsDepartment'));
    }

    /**
     * Update the specified department
     */
    public function update(Request $request, LmsDepartment $lmsDepartment)
    {
        $user = Auth::user();
        
        if ($user->user_role !== 4) {
            abort(403, 'Unauthorized access. Only Assessment Agency can update departments.');
        }

        $request->validate([
            'department_name' => 'required|string|max:255|unique:lms_departments,department_name,' . $lmsDepartment->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $lmsDepartment->update([
            'department_name' => $request->department_name,
            'department_slug' => LmsDepartment::generateUniqueSlug($request->department_name),
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.lms-departments.index')
            ->with('success', 'Department updated successfully.');
    }

    /**
     * Remove the specified department
     */
    public function destroy(LmsDepartment $lmsDepartment)
    {
        $user = Auth::user();
        
        if ($user->user_role !== 4) {
            abort(403, 'Unauthorized access. Only Assessment Agency can delete departments.');
        }

        // Check if department has any LMS sites
        if ($lmsDepartment->lmsSites()->count() > 0) {
            return redirect()->route('admin.lms-departments.index')
                ->with('error', 'Cannot delete department. It has associated LMS sites.');
        }

        $lmsDepartment->delete();

        return redirect()->route('admin.lms-departments.index')
            ->with('success', 'Department deleted successfully.');
    }

    /**
     * Toggle department active status
     */
    public function toggleStatus(LmsDepartment $lmsDepartment)
    {
        $user = Auth::user();
        
        if ($user->user_role !== 4) {
            abort(403, 'Unauthorized access. Only Assessment Agency can toggle department status.');
        }

        $lmsDepartment->update([
            'is_active' => !$lmsDepartment->is_active
        ]);

        $status = $lmsDepartment->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('admin.lms-departments.index')
            ->with('success', "Department {$status} successfully.");
    }

    /**
     * Get departments for AJAX requests (for faculty use)
     */
    public function getDepartments()
    {
        $departments = LmsDepartment::active()
            ->select('id', 'department_name', 'department_slug')
            ->orderBy('department_name')
            ->get();

        return response()->json($departments);
    }
}