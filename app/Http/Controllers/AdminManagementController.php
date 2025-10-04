<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminManagementController extends Controller
{
    /**
     * Show admin management dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Only TC Admin (role 1) and TC Head (role 2) can access this
        if (!in_array($user->user_role, [1, 2])) {
            abort(403, 'Unauthorized access');
        }

        // Show admins from the same TC as the logged-in admin
        $admins = User::where('user_role', '!=', 1) // Exclude TC Admin from list
            ->where('from_tc', $user->from_tc) // Only show admins from same TC
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.management.index', compact('admins', 'user'));
    }

    /**
     * Show create admin form
     */
    public function create()
    {
        $user = Auth::user();
        
        // Only TC Admin (role 1) can access this
        if ($user->user_role !== 1) {
            abort(403, 'Unauthorized access');
        }

        return view('admin.management.create', compact('user'));
    }

    /**
     * Store new admin
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Only TC Admin (role 1) and TC Head (role 2) can access this
            if (!in_array($user->user_role, [1, 2])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Define allowed roles based on current user's role
            $allowedRoles = [];
            if ($user->user_role === 1) { // TC Admin
                $allowedRoles = [2, 3, 5]; // TC Head, Exam Cell, TC Faculty (removed Assessment Agency)
            } elseif ($user->user_role === 2) { // TC Head
                $allowedRoles = [3, 5]; // Exam Cell, TC Faculty only
            }

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'user_role' => 'required|in:' . implode(',', $allowedRoles),
            ]);

            // Check if trying to create TC Head and one already exists
            if ($request->user_role == 2) { // TC Head role
                $existingTcHead = User::where('user_role', 2)
                    ->where('from_tc', $user->from_tc)
                    ->first();
                
                if ($existingTcHead) {
                    return response()->json([
                        'success' => false,
                        'message' => 'A TC Head already exists for this training center. Only one TC Head is allowed per TC.'
                    ], 422);
                }
            }

            // Generate a random password
            $password = \Str::random(12);

            $admin = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($password),
                'user_role' => $request->user_role,
                'from_tc' => $user->from_tc, // Automatically set from logged-in admin's TC
            ]);

            \Log::info('Admin created', [
                'created_by' => $user->email,
                'created_by_role' => $user->user_role,
                'new_admin' => $admin->email,
                'new_admin_role' => $admin->user_role,
                'tc_code' => $user->from_tc
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Admin user created successfully! Password: ' . $password,
                'redirect_url' => route('admin.management.index')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Admin creation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create admin user. Please try again.'
            ], 500);
        }
    }

    /**
     * Show edit admin form
     */
    public function edit($id)
    {
        $user = Auth::user();
        
        // Only TC Admin (role 1) and TC Head (role 2) can access this
        if (!in_array($user->user_role, [1, 2])) {
            abort(403, 'Unauthorized access');
        }

        $admin = User::where('id', $id)
            ->where('from_tc', $user->from_tc) // Only allow editing admins from same TC
            ->first();
        
        if (!$admin) {
            abort(404, 'Admin not found or access denied');
        }
        
        // Cannot edit TC Admin users
        if ($admin->user_role === 1) {
            abort(403, 'Cannot edit TC Admin users');
        }

        return view('admin.management.edit', compact('admin', 'user'));
    }

    /**
     * Update admin
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            
            // Debug: Log received data
            \Log::info('Update request received', [
                'id' => $id,
                'request_data' => $request->all(),
                'user' => $user->email
            ]);
            
            // Only TC Admin (role 1) and TC Head (role 2) can access this
            if (!in_array($user->user_role, [1, 2])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $admin = User::where('id', $id)
                ->where('from_tc', $user->from_tc) // Only allow updating admins from same TC
                ->first();
            
            if (!$admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin not found or access denied'
                ], 404);
            }
            
            // Cannot edit TC Admin users
            if ($admin->user_role === 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot edit TC Admin users'
                ], 403);
            }

            // Define allowed roles based on current user's role
            $allowedRoles = [];
            if ($user->user_role === 1) { // TC Admin
                $allowedRoles = [2, 3, 5]; // TC Head, Exam Cell, TC Faculty (removed Assessment Agency)
            } elseif ($user->user_role === 2) { // TC Head
                $allowedRoles = [3, 5]; // Exam Cell, TC Faculty only
            }

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => ['required', 'email', Rule::unique('users')->ignore($admin->id)],
                'user_role' => 'required|in:' . implode(',', $allowedRoles),
            ]);

            // Check if trying to change role to TC Head and one already exists
            if ($request->user_role == 2 && $admin->user_role != 2) { // Changing to TC Head
                $existingTcHead = User::where('user_role', 2)
                    ->where('from_tc', $user->from_tc)
                    ->where('id', '!=', $admin->id) // Exclude current admin being updated
                    ->first();
                
                if ($existingTcHead) {
                    return response()->json([
                        'success' => false,
                        'message' => 'A TC Head already exists for this training center. Only one TC Head is allowed per TC.'
                    ], 422);
                }
            }

            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'user_role' => $request->user_role,
                'from_tc' => $user->from_tc, // Keep the same TC code
            ];

            $admin->update($updateData);

            \Log::info('Admin updated', [
                'updated_by' => $user->email,
                'updated_by_role' => $user->user_role,
                'admin' => $admin->email,
                'admin_role' => $admin->user_role,
                'tc_code' => $user->from_tc
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Admin user updated successfully!',
                'redirect_url' => route('admin.management.index')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Admin update error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update admin user. Please try again.'
            ], 500);
        }
    }

    /**
     * Delete admin
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            
            // Only TC Admin (role 1) and TC Head (role 2) can access this
            if (!in_array($user->user_role, [1, 2])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $admin = User::where('id', $id)
                ->where('from_tc', $user->from_tc) // Only allow deleting admins from same TC
                ->first();
            
            if (!$admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin not found or access denied'
                ], 404);
            }
            
            // Cannot delete TC Admin users
            if ($admin->user_role === 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete TC Admin users'
                ], 403);
            }

            // Cannot delete self
            if ($admin->id === $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete your own account'
                ], 403);
            }

            $adminEmail = $admin->email;
            $admin->delete();

            \Log::info('Admin deleted', [
                'deleted_by' => $user->email,
                'deleted_by_role' => $user->user_role,
                'deleted_admin' => $adminEmail,
                'tc_code' => $user->from_tc
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Admin user deleted successfully!'
            ]);

        } catch (\Exception $e) {
            \Log::error('Admin deletion error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete admin user. Please try again.'
            ], 500);
        }
    }

    /**
     * Get role names
     */
    public static function getRoleNames()
    {
        return [
            1 => 'TC Admin',
            2 => 'TC Head',
            3 => 'Exam Cell',
            4 => 'Assessment Agency',
            5 => 'TC Faculty'
        ];
    }
} 