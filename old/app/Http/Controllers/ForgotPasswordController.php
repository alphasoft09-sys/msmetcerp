<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\StudentLogin;
use App\Mail\PasswordResetMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ForgotPasswordController extends Controller
{
    /**
     * Show forgot password form for admin
     */
    public function showAdminForgotForm()
    {
        return view('admin.forgot-password');
    }

    /**
     * Show forgot password form for student
     */
    public function showStudentForgotForm()
    {
        return view('student.forgot-password');
    }

    /**
     * Send password reset link for admin
     */
    public function sendAdminResetLink(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No account found with this email address.'
                ], 404);
            }

            // Generate reset token
            $token = Str::random(64);
            $expiresAt = Carbon::now()->addMinutes(60);

            // Store reset token in database
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                [
                    'email' => $request->email,
                    'token' => $token,
                    'created_at' => now(),
                    'user_type' => 'admin'
                ]
            );

            // Send email
            Mail::to($user->email)->send(new PasswordResetMail($token, $user->name, 'admin'));

            return response()->json([
                'success' => true,
                'message' => 'Password reset link has been sent to your email address.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Admin password reset error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reset link. Please try again.'
            ], 500);
        }
    }

    /**
     * Send password reset link for student
     */
    public function sendStudentResetLink(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);

            $student = StudentLogin::where('email', $request->email)->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'No account found with this email address.'
                ], 404);
            }

            // Generate reset token
            $token = Str::random(64);
            $expiresAt = Carbon::now()->addMinutes(60);

            // Store reset token in database
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                [
                    'email' => $request->email,
                    'token' => $token,
                    'created_at' => now(),
                    'user_type' => 'student'
                ]
            );

            // Send email
            Mail::to($student->email)->send(new PasswordResetMail($token, $student->name, 'student'));

            return response()->json([
                'success' => true,
                'message' => 'Password reset link has been sent to your email address.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Student password reset error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reset link. Please try again.'
            ], 500);
        }
    }

    /**
     * Show reset password form for admin
     */
    public function showAdminResetForm(Request $request)
    {
        $token = $request->query('token');
        
        if (!$token) {
            return redirect()->route('admin.login')->with('error', 'Invalid reset link.');
        }

        $resetToken = DB::table('password_reset_tokens')
            ->where('token', $token)
            ->where('user_type', 'admin')
            ->where('created_at', '>', Carbon::now()->subMinutes(60))
            ->first();

        if (!$resetToken) {
            return redirect()->route('admin.login')->with('error', 'Invalid or expired reset link.');
        }

        return view('admin.reset-password', compact('token'));
    }

    /**
     * Show reset password form for student
     */
    public function showStudentResetForm(Request $request)
    {
        $token = $request->query('token');
        
        if (!$token) {
            return redirect()->route('student.login')->with('error', 'Invalid reset link.');
        }

        $resetToken = DB::table('password_reset_tokens')
            ->where('token', $token)
            ->where('user_type', 'student')
            ->where('created_at', '>', Carbon::now()->subMinutes(60))
            ->first();

        if (!$resetToken) {
            return redirect()->route('student.login')->with('error', 'Invalid or expired reset link.');
        }

        return view('student.reset-password', compact('token'));
    }

    /**
     * Reset password for admin
     */
    public function resetAdminPassword(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:8|confirmed',
            ]);

            $resetToken = DB::table('password_reset_tokens')
                ->where('token', $request->token)
                ->where('email', $request->email)
                ->where('user_type', 'admin')
                ->where('created_at', '>', Carbon::now()->subMinutes(60))
                ->first();

            if (!$resetToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired reset link.'
                ], 400);
            }

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.'
                ], 404);
            }

            // Update password
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            // Delete reset token
            DB::table('password_reset_tokens')
                ->where('token', $request->token)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Password has been reset successfully. You can now login with your new password.',
                'redirect_url' => route('admin.login')
            ]);

        } catch (\Exception $e) {
            \Log::error('Admin password reset error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password. Please try again.'
            ], 500);
        }
    }

    /**
     * Reset password for student
     */
    public function resetStudentPassword(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:8|confirmed',
            ]);

            $resetToken = DB::table('password_reset_tokens')
                ->where('token', $request->token)
                ->where('email', $request->email)
                ->where('user_type', 'student')
                ->where('created_at', '>', Carbon::now()->subMinutes(60))
                ->first();

            if (!$resetToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired reset link.'
                ], 400);
            }

            $student = StudentLogin::where('email', $request->email)->first();
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found.'
                ], 404);
            }

            // Update password
            $student->update([
                'password' => Hash::make($request->password)
            ]);

            // Delete reset token
            DB::table('password_reset_tokens')
                ->where('token', $request->token)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Password has been reset successfully. You can now login with your new password.',
                'redirect_url' => route('student.login')
            ]);

        } catch (\Exception $e) {
            \Log::error('Student password reset error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password. Please try again.'
            ], 500);
        }
    }
} 