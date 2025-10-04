<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Mail\OtpMail;
use Carbon\Carbon;
use App\Traits\SessionManager;

class OtpController extends Controller
{
    use SessionManager;
    public function showOtpForm()
    {
        // Check if user_id is stored in session (from login step)
        if (!session()->has('temp_user_id')) {
            return redirect()->route('admin.login')->with('error', 'Please login first to receive OTP.');
        }

        return view('admin.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        try {
            // Debug logging for test mode
            \Log::info('OTP Verification - Environment: ' . env('APP_ENV') . ', Test Mode: ' . env('OTP_TEST_MODE') . ', Test Code: ' . env('OTP_TEST_CODE'));
            \Log::info('OTP Verification - Request OTP: ' . $request->otp);
            
            $request->validate([
                'otp' => 'required|string|size:6',
            ]);

            // Get user from session
            $userId = session('temp_user_id');
            if (!$userId) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Session expired. Please login again.'
                    ], 401);
                }
                return redirect()->route('admin.login')->with('error', 'Session expired. Please login again.');
            }

            $user = User::find($userId);
            if (!$user) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User not found.'
                    ], 404);
                }
                return redirect()->route('admin.login')->with('error', 'User not found.');
            }

            // Check if OTP exists and is not expired
            if (!$user->user_otp || !$user->otp_expires_at) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No OTP found. Please request a new one.'
                    ], 400);
                }
                return redirect()->route('admin.login')->with('error', 'No OTP found. Please request a new one.');
            }

            // Check if OTP is expired
            if (Carbon::now()->isAfter($user->otp_expires_at)) {
                // Clear expired OTP
                $user->update([
                    'user_otp' => null,
                    'otp_expires_at' => null
                ]);

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'OTP has expired. Please request a new one.'
                    ], 400);
                }
                return redirect()->route('admin.login')->with('error', 'OTP has expired. Please request a new one.');
            }

            // Test mode bypass for automated testing
            if ((env('APP_ENV') === 'testing' || env('APP_ENV') === 'local') && (env('OTP_TEST_MODE') === 'true' || env('OTP_TEST_MODE') === '1')) {
                $testCode = env('OTP_TEST_CODE', '123456');
                if ($request->otp === $testCode) {
                    // Test mode OTP accepted
                    \Log::info('Test mode OTP accepted for user: ' . $user->email);
                } else {
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid test OTP. Please use: ' . $testCode
                        ], 400);
                    }
                    return back()->withErrors(['otp' => 'Invalid test OTP. Please use: ' . $testCode]);
                }
            } else {
                // Normal OTP verification
                if ($user->user_otp !== $request->otp) {
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid OTP. Please try again.'
                        ], 400);
                    }
                    return back()->withErrors(['otp' => 'Invalid OTP. Please try again.']);
                }
            }

            // OTP is valid - log in the user
            Auth::login($user);
            
            // Clear OTP fields first
            $user->update([
                'user_otp' => null,
                'otp_expires_at' => null
            ]);
            
            // Invalidate old sessions for this admin user (with safety delay)
            try {
                $this->invalidateOtherSessions($user->id, 'web');
            } catch (\Exception $e) {
                \Log::error("Session invalidation failed but login continued: " . $e->getMessage());
            }

            // Clear session
            session()->forget('temp_user_id');

            // Determine redirect URL based on user role
            $redirectUrl = $this->getRedirectUrl($user->user_role);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'OTP verified successfully! All other sessions have been terminated.',
                    'redirect_url' => $redirectUrl
                ]);
            }

            return redirect($redirectUrl)->with('success', 'Login successful. All other sessions have been terminated.');

        } catch (\Exception $e) {
            \Log::error('OTP verification error: ' . $e->getMessage());
            \Log::error('OTP verification error stack trace: ' . $e->getTraceAsString());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred during OTP verification: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['otp' => 'An error occurred during OTP verification: ' . $e->getMessage()]);
        }
    }

    public function resendOtp(Request $request)
    {
        try {
            $userId = session('temp_user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session expired. Please login again.'
                ], 401);
            }

            $user = User::find($userId);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.'
                ], 404);
            }

            // Generate new OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $expiresAt = Carbon::now()->addMinutes(5);

            // Save OTP to database
            $user->update([
                'user_otp' => $otp,
                'otp_expires_at' => $expiresAt
            ]);

            // Send OTP email
            Mail::to($user->email)->send(new OtpMail($otp, $user->name));

            return response()->json([
                'success' => true,
                'message' => 'New OTP has been sent to your email.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Resend OTP error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.'
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
