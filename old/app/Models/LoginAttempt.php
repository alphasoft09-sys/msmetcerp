<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    protected $fillable = [
        'email',
        'ip_address',
        'user_agent',
        'guard',
        'success',
        'attempted_password',
        'attempted_at',
        'alert_sent'
    ];

    protected $casts = [
        'success' => 'boolean',
        'alert_sent' => 'boolean',
        'attempted_at' => 'datetime'
    ];

    /**
     * Get failed attempts count for email + IP combination
     */
    public static function getFailedAttemptsCount($email, $ipAddress, $guard = 'web')
    {
        return self::where('email', $email)
            ->where('ip_address', $ipAddress)
            ->where('guard', $guard)
            ->where('success', false)
            ->where('attempted_at', '>=', now()->subHours(24))
            ->count();
    }

    /**
     * Check if security alert should be sent
     */
    public static function shouldSendAlert($email, $ipAddress, $guard = 'web')
    {
        $failedAttempts = self::getFailedAttemptsCount($email, $ipAddress, $guard);
        $alertAlreadySent = self::where('email', $email)
            ->where('ip_address', $ipAddress)
            ->where('guard', $guard)
            ->where('alert_sent', true)
            ->where('attempted_at', '>=', now()->subHours(24))
            ->exists();

        return $failedAttempts >= 10 && !$alertAlreadySent;
    }

    /**
     * Mark alert as sent for email + IP combination
     */
    public static function markAlertSent($email, $ipAddress, $guard = 'web')
    {
        self::where('email', $email)
            ->where('ip_address', $ipAddress)
            ->where('guard', $guard)
            ->where('alert_sent', false)
            ->update(['alert_sent' => true]);
    }
}
