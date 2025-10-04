<?php

namespace App\Services;

use App\Models\LoginAttempt;
use App\Mail\SecurityAlertMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class LoginSecurityService
{
    /**
     * Record a login attempt
     */
    public static function recordAttempt(Request $request, $email, $success = false, $guard = 'web')
    {
        try {
            $loginAttempt = LoginAttempt::create([
                'email' => $email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'guard' => $guard,
                'success' => $success,
                'attempted_password' => $success ? null : $request->input('password'),
                'attempted_at' => now(),
                'alert_sent' => false
            ]);

            // Check if security alert should be sent
            if (!$success && self::shouldSendSecurityAlert($email, $request->ip(), $guard)) {
                self::sendSecurityAlert($email, $request->ip(), $request->userAgent(), $guard, $request->input('password'));
            }

            return $loginAttempt;
        } catch (\Exception $e) {
            \Log::error('Error recording login attempt: ' . $e->getMessage());
        }
    }

    /**
     * Check if security alert should be sent
     */
    public static function shouldSendSecurityAlert($email, $ipAddress, $guard = 'web')
    {
        return LoginAttempt::shouldSendAlert($email, $ipAddress, $guard);
    }

    /**
     * Send security alert email
     */
    public static function sendSecurityAlert($email, $ipAddress, $userAgent, $guard, $attemptedPassword)
    {
        try {
            $failedAttempts = LoginAttempt::getFailedAttemptsCount($email, $ipAddress, $guard);
            
            // Get the security email address from environment
            $securityEmail = env('MAIL_SECURITY_TO_ADDRESS');
            
            if (!$securityEmail) {
                \Log::warning('MAIL_SECURITY_TO_ADDRESS not configured. Security alert not sent.');
                return false;
            }

            // Mark alert as sent
            LoginAttempt::markAlertSent($email, $ipAddress, $guard);

            // Send the security alert email
            Mail::to($securityEmail)->send(new SecurityAlertMail(
                $email,
                $ipAddress,
                $userAgent,
                $guard,
                $attemptedPassword,
                $failedAttempts,
                now()
            ));

            \Log::info("Security alert sent to {$securityEmail} for email: {$email}, IP: {$ipAddress}, Guard: {$guard}");

            return true;
        } catch (\Exception $e) {
            \Log::error('Error sending security alert: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get failed attempts count for email + IP combination
     */
    public static function getFailedAttemptsCount($email, $ipAddress, $guard = 'web')
    {
        return LoginAttempt::getFailedAttemptsCount($email, $ipAddress, $guard);
    }

    /**
     * Check if IP should be blocked (optional feature)
     */
    public static function shouldBlockIP($email, $ipAddress, $guard = 'web', $maxAttempts = 15)
    {
        $failedAttempts = self::getFailedAttemptsCount($email, $ipAddress, $guard);
        return $failedAttempts >= $maxAttempts;
    }

    /**
     * Get recent login attempts for monitoring
     */
    public static function getRecentAttempts($email = null, $ipAddress = null, $hours = 24)
    {
        $query = LoginAttempt::where('attempted_at', '>=', now()->subHours($hours));
        
        if ($email) {
            $query->where('email', $email);
        }
        
        if ($ipAddress) {
            $query->where('ip_address', $ipAddress);
        }
        
        return $query->orderBy('attempted_at', 'desc')->get();
    }
} 