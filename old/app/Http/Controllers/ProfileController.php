<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Mail\OtpMail;
use Carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Show the change password/email form.
     */
    public function showChangeForm(Request $request)
    {
        // Determine if user is admin or student
        if (Auth::guard('student')->check()) {
            $user = Auth::guard('student')->user();
            return view('student.profile.change-password-email', compact('user'));
        } else {
            $user = Auth::user();
            return view('admin.profile.change-password-email', compact('user'));
        }
    }

    /**
     * Send OTP for email/password change.
     */
    public function sendChangeOtp(Request $request)
    {
        try {
            $request->validate([
                'new_email' => 'nullable|email|unique:users,email,' . Auth::id(),
                'new_password' => 'nullable|min:8|confirmed',
            ]);

            // Determine if user is admin or student
            if (Auth::guard('student')->check()) {
                $user = Auth::guard('student')->user();
                $uniqueRule = 'unique:student_logins,email,' . $user->id;
            } else {
                $user = Auth::user();
                $uniqueRule = 'unique:users,email,' . $user->id;
            }
            
            // Check if at least one field is being changed
            if (!$request->new_email && !$request->new_password) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please provide either a new email or password to change.'
                ], 400);
            }

            // Generate 6-digit OTP
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
                'message' => 'OTP sent to your email. Please check your inbox.'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Send change OTP error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.'
            ], 500);
        }
    }

    /**
     * Verify OTP and update email/password.
     */
    public function verifyAndUpdate(Request $request)
    {
        try {
            $request->validate([
                'otp' => 'required|string|size:6',
                'new_email' => 'nullable|email|unique:users,email,' . Auth::id(),
                'new_password' => 'nullable|min:8|confirmed',
            ]);

            // Determine if user is admin or student
            if (Auth::guard('student')->check()) {
                $user = Auth::guard('student')->user();
                $uniqueRule = 'unique:student_logins,email,' . $user->id;
            } else {
                $user = Auth::user();
                $uniqueRule = 'unique:users,email,' . $user->id;
            }

            // Check if OTP exists and is not expired
            if (!$user->user_otp || !$user->otp_expires_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'No OTP found. Please request a new one.'
                ], 400);
            }

            // Check if OTP is expired
            if (Carbon::now()->isAfter($user->otp_expires_at)) {
                // Clear expired OTP
                $user->update([
                    'user_otp' => null,
                    'otp_expires_at' => null
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'OTP has expired. Please request a new one.'
                ], 400);
            }

            // Verify OTP
            if ($user->user_otp !== $request->otp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP. Please try again.'
                ], 400);
            }

            // OTP is valid - update user data
            $updateData = [
                'user_otp' => null,
                'otp_expires_at' => null
            ];

            if ($request->new_email) {
                $updateData['email'] = $request->new_email;
                if (isset($user->email_verified_at)) {
                    $updateData['email_verified_at'] = null; // Reset email verification
                }
            }

            if ($request->new_password) {
                $updateData['password'] = Hash::make($request->new_password);
            }

            $user->update($updateData);

            // Log the change
            \Log::info('User profile updated', [
                'user_id' => $user->id,
                'user_type' => Auth::guard('student')->check() ? 'student' : 'admin',
                'email_changed' => $request->new_email ? true : false,
                'password_changed' => $request->new_password ? true : false
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully! You will be logged out to re-login with your new credentials.',
                'logout_required' => true
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Profile update error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during profile update. Please try again.'
            ], 500);
        }
    }
}
