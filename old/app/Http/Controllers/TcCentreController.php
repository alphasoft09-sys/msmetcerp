<?php

namespace App\Http\Controllers;

use App\Models\TcCentre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TcCentreController extends Controller
{
    /**
     * Display a listing of centres for the user's TC
     */
    public function index()
    {
        $user = Auth::user();
        
        // Check if user has permission to access centres
        if (!in_array($user->user_role, [1, 2])) {
            abort(403, 'Unauthorized access');
        }

        $centres = TcCentre::where('tc_code', $user->from_tc)
            ->orderBy('centre_name')
            ->get();

        return view('admin.centres.index', compact('centres', 'user'));
    }

    /**
     * Show the form for creating a new centre
     */
    public function create()
    {
        $user = Auth::user();
        
        // Check if user has permission to access centres
        if (!in_array($user->user_role, [1, 2])) {
            abort(403, 'Unauthorized access');
        }

        return view('admin.centres.create', compact('user'));
    }

    /**
     * Store a newly created centre
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Check if user has permission to access centres
            if (!in_array($user->user_role, [1, 2])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $request->validate([
                'centre_name' => [
                    'required',
                    'string',
                    'max:255',
                    // Prevent duplicate centre_name for the same tc_code
                    \Illuminate\Validation\Rule::unique('tc_centres')->where(function ($query) use ($user) {
                        return $query->where('tc_code', $user->from_tc);
                    }),
                ],
                'address' => 'required|string|max:1000',
            ]);

            $centre = TcCentre::create([
                'tc_code' => $user->from_tc,
                'centre_name' => $request->centre_name,
                'address' => $request->address,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Centre added successfully',
                    'centre' => $centre
                ]);
            }

            return redirect()->route('admin.centres.index')->with('success', 'Centre added successfully');

        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Centre creation error: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add centre. Please try again.'
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to add centre. Please try again.']);
        }
    }

    /**
     * Show the form for editing the specified centre
     */
    public function edit($id)
    {
        $user = Auth::user();
        
        // Check if user has permission to access centres
        if (!in_array($user->user_role, [1, 2])) {
            abort(403, 'Unauthorized access');
        }

        $centre = TcCentre::where('id', $id)
            ->where('tc_code', $user->from_tc)
            ->firstOrFail();

        return view('admin.centres.edit', compact('centre', 'user'));
    }

    /**
     * Update the specified centre
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            
            // Check if user has permission to access centres
            if (!in_array($user->user_role, [1, 2])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $centre = TcCentre::where('id', $id)
                ->where('tc_code', $user->from_tc)
                ->firstOrFail();

            $request->validate([
                'centre_name' => 'required|string|max:255',
                'address' => 'required|string|max:1000',
            ]);

            $centre->update([
                'centre_name' => $request->centre_name,
                'address' => $request->address,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Centre updated successfully',
                    'centre' => $centre
                ]);
            }

            return redirect()->route('admin.centres.index')->with('success', 'Centre updated successfully');

        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Centre update error: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update centre. Please try again.'
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to update centre. Please try again.']);
        }
    }

    /**
     * Remove the specified centre
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            
            // Check if user has permission to access centres
            if (!in_array($user->user_role, [1, 2])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $centre = TcCentre::where('id', $id)
                ->where('tc_code', $user->from_tc)
                ->firstOrFail();

            // Check if centre is being used in any exam schedules
            $examSchedules = $centre->examSchedules()->get();
            if ($examSchedules->count() > 0) {
                $scheduleDetails = $examSchedules->take(5)->map(function($schedule) {
                    return [
                        'id' => $schedule->id,
                        'program_name' => $schedule->program_name,
                        'current_stage' => $schedule->current_stage,
                        'created_at' => $schedule->created_at->format('d/m/Y')
                    ];
                })->toArray();
                
                $message = "Cannot delete centre '{$centre->centre_name}'. It is being used in {$examSchedules->count()} exam schedule(s).";
                if ($examSchedules->count() > 5) {
                    $message .= " Showing first 5 schedules.";
                }
                
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'usage_details' => [
                        'total_schedules' => $examSchedules->count(),
                        'schedules' => $scheduleDetails
                    ]
                ], 400);
            }

            $centre->delete();

            return response()->json([
                'success' => true,
                'message' => 'Centre deleted successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Centre deletion error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete centre. Please try again.'
            ], 500);
        }
    }

    /**
     * Get centres for the user's TC (AJAX endpoint for exam schedule form)
     */
    public function getCentresForTc()
    {
        try {
            $user = Auth::user();
            
            \Log::info('GetCentresForTc called by user: ' . $user->email . ' with TC: ' . $user->from_tc);
            
            $centres = TcCentre::where('tc_code', $user->from_tc)
                ->orderBy('centre_name')
                ->get(['id', 'centre_name']);

            \Log::info('Found ' . $centres->count() . ' centres for TC: ' . $user->from_tc);

            return response()->json([
                'success' => true,
                'centres' => $centres,
                'debug' => [
                    'user_tc' => $user->from_tc,
                    'user_role' => $user->user_role,
                    'centres_count' => $centres->count()
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Get centres error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch centres.',
                'debug' => [
                    'error' => $e->getMessage(),
                    'user_tc' => Auth::user()->from_tc ?? 'null',
                    'user_role' => Auth::user()->user_role ?? 'null'
                ]
            ], 500);
        }
    }
}
