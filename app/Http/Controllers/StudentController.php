<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StudentLogin;
use Illuminate\Validation\ValidationException;
use App\Traits\SessionManager;
use App\Services\LoginSecurityService;

class StudentController extends Controller
{
    use SessionManager;

    public function showLoginForm()
    {
        return view('student.login');
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            if (Auth::guard('student')->attempt($credentials)) {
                $student = Auth::guard('student')->user();
                
                // Record successful login attempt
                LoginSecurityService::recordAttempt($request, $request->email, true, 'student');
                
                // Invalidate all other sessions for this student
                $this->invalidateOtherSessions($student->id, 'student');
                
                $request->session()->regenerate();
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Login successful. All other sessions have been terminated.',
                        'redirect_url' => route('student.dashboard')
                    ]);
                }

                return redirect()->route('student.dashboard')->with('success', 'Login successful. All other sessions have been terminated.');
            }

            // Record failed login attempt
            LoginSecurityService::recordAttempt($request, $request->email, false, 'student');

            $errorMessage = 'The provided credentials do not match our records.';

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 422);
            }

            return back()->withErrors([
                'email' => $errorMessage,
            ])->onlyInput('email');

        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }

            throw $e;
        } catch (\Exception $e) {
            \Log::error('Student login error: ' . $e->getMessage());
            \Log::error('Student login error stack trace: ' . $e->getTraceAsString());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred during login: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors([
                'email' => 'An error occurred during login: ' . $e->getMessage(),
            ])->onlyInput('email');
        }
    }

    public function dashboard()
    {
        try {
            $student = Auth::guard('student')->user();
            
            return view('student.dashboard', compact('student'));
        } catch (\Exception $e) {
            \Log::error('Student Dashboard error: ' . $e->getMessage());
            abort(500, 'Internal server error');
        }
    }

    public function logout(Request $request)
    {
        try {
            $student = Auth::guard('student')->user();
            if ($student) {
                \Log::info('Student logout: ' . $student->email);
            }
            
            Auth::guard('student')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // For AJAX requests, return JSON response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Logout successful',
                    'redirect_url' => route('student.login')
                ]);
            }

            // For GET requests, redirect to login page
            return redirect()->route('student.login')->with('message', 'You have been successfully logged out.');
        } catch (\Exception $e) {
            \Log::error('Student logout error: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Logout failed. Please try again.'
                ], 500);
            }
            
            return redirect()->route('student.login')->with('message', 'Logout completed.');
        }
    }
}
