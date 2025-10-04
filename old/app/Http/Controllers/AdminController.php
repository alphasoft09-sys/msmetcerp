<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use App\Services\LoginSecurityService;

class AdminController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                
                // Record successful login attempt
                LoginSecurityService::recordAttempt($request, $request->email, true, 'web');
                
                // Test mode OTP handling
                if ((env('APP_ENV') === 'testing' || env('APP_ENV') === 'local') && (env('OTP_TEST_MODE') === 'true' || env('OTP_TEST_MODE') === '1')) {
                    $testOtp = env('OTP_TEST_CODE', '123456');
                    $expiresAt = \Carbon\Carbon::now()->addMinutes(30); // Longer expiry for testing
                    
                    // Save test OTP to database
                    $user->update([
                        'user_otp' => $testOtp,
                        'otp_expires_at' => $expiresAt
                    ]);
                    
                    \Log::info('Test mode OTP generated for user: ' . $user->email . ' - OTP: ' . $testOtp);
                } else {
                    // Generate 6-digit OTP for production
                    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                    $expiresAt = \Carbon\Carbon::now()->addMinutes(5);

                    // Save OTP to database
                    $user->update([
                        'user_otp' => $otp,
                        'otp_expires_at' => $expiresAt
                    ]);

                    // Send OTP email
                    \Mail::to($user->email)->send(new \App\Mail\OtpMail($otp, $user->name));
                }

                // Logout the user (we don't want them logged in yet)
                Auth::logout();

                // Store user ID in session for OTP verification
                session(['temp_user_id' => $user->id]);

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'OTP sent to your email. Please check your inbox.',
                        'redirect_url' => route('admin.verify-otp')
                    ]);
                }

                return redirect()->route('admin.verify-otp')->with('success', 'OTP sent to your email. Please check your inbox.');
            }

            // Record failed login attempt
            LoginSecurityService::recordAttempt($request, $request->email, false, 'web');

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
            \Log::error('Admin login error: ' . $e->getMessage());
            \Log::error('Admin login error stack trace: ' . $e->getTraceAsString());
            
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

    public function logout(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user) {
                \Log::info('Admin logout: ' . $user->email);
            }
            
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // For AJAX requests, return JSON response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Logout successful',
                    'redirect_url' => route('admin.login')
                ]);
            }

            // For GET requests, redirect to login page
            return redirect()->route('admin.login')->with('message', 'You have been successfully logged out.');
        } catch (\Exception $e) {
            \Log::error('Admin logout error: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Logout failed. Please try again.'
                ], 500);
            }
            
            return redirect()->route('admin.login')->with('message', 'Logout completed.');
        }
    }

    /**
     * Debug method to test authentication
     */
    public function debugAuth(Request $request)
    {
        try {
            $email = $request->input('email');
            $password = $request->input('password');
            
            if (!$email || !$password) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email and password required'
                ], 400);
            }
            
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }
            
            // Test password verification
            $passwordValid = \Hash::check($password, $user->password);
            
            return response()->json([
                'success' => true,
                'user_found' => true,
                'password_valid' => $passwordValid,
                'user_id' => $user->id,
                'email' => $user->email,
                'user_role' => $user->user_role,
                'has_otp' => !empty($user->user_otp),
                'otp_expires' => $user->otp_expires_at,
                'session_id' => session()->getId(),
                'is_authenticated' => Auth::check()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Debug auth error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Debug error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getRedirectUrl($userRole)
    {
        switch ($userRole) {
            case 1: // TC Admin
                return route('admin.tc-admin.dashboard');
            case 2: // TC Head
                return route('admin.tc-head.dashboard');
            case 3: // TC Exam Cell
                return route('admin.exam-cell.dashboard');
            case 4: // TC AA
                return route('admin.aa.dashboard');
            case 5: // TC Faculty
                return route('admin.tc-faculty.dashboard');
            default:
                return route('admin.tc-admin.dashboard');
        }
    }
}
