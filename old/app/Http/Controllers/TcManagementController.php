<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\TcShotCode;
use App\Services\DynamicTableService;
use App\Mail\TcWelcomeEmail;

class TcManagementController extends Controller
{
    /**
     * Display a listing of TC Admins
     */
    public function index()
    {
        $user = Auth::user();
        
        // Only Assessment Agency (role 4) can access this
        if ($user->user_role !== 4) {
            abort(403, 'Unauthorized access');
        }

        // Get all TC Admins (role 1) with their details and shot codes
        $tcAdmins = User::where('user_role', 1)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($tcAdmin) {
                // Get shot code for this TC
                $shotCode = TcShotCode::where('tc_code', $tcAdmin->from_tc)->first();
                $tcAdmin->shot_code = $shotCode ? $shotCode->shot_code : 'N/A';
                return $tcAdmin;
            });

        return view('admin.tc-management.index', compact('tcAdmins', 'user'));
    }

    /**
     * Show create TC Admin form
     */
    public function create()
    {
        $user = Auth::user();
        
        // Only Assessment Agency (role 4) can access this
        if ($user->user_role !== 4) {
            abort(403, 'Unauthorized access');
        }

        return view('admin.tc-management.create', compact('user'));
    }

    /**
     * Store a new TC Admin
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
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'tc_code' => 'required|string|max:50|unique:users,from_tc',
                'shot_code' => 'required|string|size:2|unique:tc_shot_code,shot_code',
                'tc_name' => 'required|string|max:255',
                'tc_address' => 'required|string|max:500',
                'tc_phone' => 'required|string|max:20',
            ]);

            // Generate a random password
            $password = \Str::random(12);

            $tcAdmin = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($password),
                'user_role' => 1, // TC Admin role
                'from_tc' => $request->tc_code,
                'tc_name' => $request->tc_name,
                'tc_address' => $request->tc_address,
                'tc_phone' => $request->tc_phone,
            ]);

            // Save TC code and shot code to tc_shot_code table
            TcShotCode::create([
                'tc_code' => $request->tc_code,
                'shot_code' => strtoupper($request->shot_code), // Ensure uppercase
            ]);

            // Create dynamic student table for this TC
            $tableResult = DynamicTableService::createTcStudentTable($request->tc_code);
            
            if (!$tableResult['success']) {
                \Log::warning('Failed to create student table for TC: ' . $request->tc_code, $tableResult);
            }

            // Send welcome email to the new TC Admin
            try {
                Mail::to($tcAdmin->email)->send(new TcWelcomeEmail($tcAdmin, $user, $password));
                \Log::info('Welcome email sent to new TC Admin', [
                    'tc_admin_email' => $tcAdmin->email,
                    'tc_code' => $tcAdmin->from_tc,
                    'tc_name' => $tcAdmin->tc_name
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to send welcome email to TC Admin: ' . $e->getMessage(), [
                    'tc_admin_email' => $tcAdmin->email,
                    'tc_code' => $tcAdmin->from_tc
                ]);
            }

            \Log::info('TC Admin created by Assessment Agency', [
                'created_by' => $user->email,
                'new_tc_admin' => $tcAdmin->email,
                'tc_code' => $tcAdmin->from_tc,
                'shot_code' => strtoupper($request->shot_code),
                'tc_name' => $tcAdmin->tc_name,
                'table_creation' => $tableResult
            ]);

            $message = 'TC Admin created successfully! Welcome email sent to: ' . $tcAdmin->email . ' Password: ' . $password;
            if ($tableResult['success']) {
                $message .= ' Student table created: ' . $tableResult['table_name'];
            } else {
                $message .= ' (Note: ' . $tableResult['message'] . ')';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect_url' => route('admin.tc-management.index')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('TC Admin creation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create TC Admin. Please try again.'
            ], 500);
        }
    }

    /**
     * Show edit TC Admin form
     */
    public function edit($id)
    {
        $user = Auth::user();
        
        // Only Assessment Agency (role 4) can access this
        if ($user->user_role !== 4) {
            abort(403, 'Unauthorized access');
        }

        $tcAdmin = User::where('id', $id)
            ->where('user_role', 1) // Only TC Admins
            ->first();
        
        if (!$tcAdmin) {
            abort(404, 'TC Admin not found');
        }

        return view('admin.tc-management.edit', compact('tcAdmin', 'user'));
    }

    /**
     * Update TC Admin
     */
    public function update(Request $request, $id)
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

            $tcAdmin = User::where('id', $id)
                ->where('user_role', 1) // Only TC Admins
                ->first();
            
            if (!$tcAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'TC Admin not found'
                ], 404);
            }

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => ['required', 'email', Rule::unique('users')->ignore($tcAdmin->id)],
                'tc_name' => 'required|string|max:255',
                'tc_address' => 'required|string|max:500',
                'tc_phone' => 'required|string|max:20',
            ]);

            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'tc_name' => $request->tc_name,
                'tc_address' => $request->tc_address,
                'tc_phone' => $request->tc_phone,
            ];

            $tcAdmin->update($updateData);

            \Log::info('TC Admin updated by Assessment Agency', [
                'updated_by' => $user->email,
                'tc_admin' => $tcAdmin->email,
                'tc_code' => $tcAdmin->from_tc,
                'tc_name' => $tcAdmin->tc_name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'TC Admin updated successfully!',
                'redirect_url' => route('admin.tc-management.index')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('TC Admin update error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update TC Admin. Please try again.'
            ], 500);
        }
    }

    /**
     * Delete TC Admin
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

            $tcAdmin = User::where('id', $id)
                ->where('user_role', 1) // Only TC Admins
                ->first();
            
            if (!$tcAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'TC Admin not found'
                ], 404);
            }

            // Cannot delete self
            if ($tcAdmin->id === $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete your own account'
                ], 403);
            }

            $tcAdminEmail = $tcAdmin->email;
            $tcCode = $tcAdmin->from_tc;
            
            // Drop the student table for this TC
            $tableResult = DynamicTableService::dropTcStudentTable($tcCode);
            
            // Delete the TC Admin user
            // Note: Shot code is NOT deleted - it remains in tc_shot_code table
            // to prevent reuse by other TCs in the future
            $tcAdmin->delete();

            \Log::info('TC Admin deleted by Assessment Agency', [
                'deleted_by' => $user->email,
                'deleted_tc_admin' => $tcAdminEmail,
                'tc_code' => $tcCode,
                'table_deletion' => $tableResult
            ]);

            $message = 'TC Admin deleted successfully!';
            if ($tableResult['success']) {
                $message .= ' Student table dropped: ' . DynamicTableService::getTableName($tcCode);
            } else {
                $message .= ' (Note: ' . $tableResult['message'] . ')';
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            \Log::error('TC Admin deletion error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete TC Admin. Please try again.'
            ], 500);
        }
    }
} 