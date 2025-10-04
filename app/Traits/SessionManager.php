<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

trait SessionManager
{
    /**
     * Invalidate all other sessions for the given user
     * 
     * @param int $userId
     * @param string $guard
     * @return void
     */
    protected function invalidateOtherSessions($userId, $guard = 'web')
    {
        try {
            // Get current session ID
            $currentSessionId = Session::getId();
            
            // Get the sessions table name
            $sessionsTable = config('session.table');
            
            // Check if sessions table has user_id column
            $hasUserIdColumn = DB::getSchemaBuilder()->hasColumn($sessionsTable, 'user_id');
            
            if ($hasUserIdColumn) {
                // Only delete sessions that are older than 1 hour to prevent conflicts
                $oneHourAgo = now()->subHour();
                
                // Delete old sessions for this user except the current one
                DB::table($sessionsTable)
                    ->where('user_id', $userId)
                    ->where('id', '!=', $currentSessionId)
                    ->where('last_activity', '<', $oneHourAgo->timestamp)
                    ->delete();
                
                // Log the session invalidation
                \Log::info("Invalidated old sessions for user ID: {$userId}, Guard: {$guard}, Current Session: {$currentSessionId}");
            } else {
                // If no user_id column, we can't track user sessions properly
                \Log::warning("Sessions table does not have user_id column. Cannot invalidate other sessions for user {$userId}");
            }
            
        } catch (\Exception $e) {
            \Log::error("Error invalidating sessions for user {$userId}: " . $e->getMessage());
        }
    }

    /**
     * Invalidate sessions from other guards (admin/student)
     * 
     * @param int $userId
     * @param string $currentGuard
     * @return void
     */
    protected function invalidateCrossGuardSessions($userId, $currentGuard)
    {
        // Temporarily disabled to prevent authentication conflicts
        // This feature was causing issues with password authentication
        \Log::info("Cross-guard session invalidation disabled for user {$userId} to prevent authentication conflicts");
    }

    /**
     * Invalidate all sessions for the given user (including current)
     * 
     * @param int $userId
     * @param string $guard
     * @return void
     */
    protected function invalidateAllSessions($userId, $guard = 'web')
    {
        try {
            // Get the sessions table name
            $sessionsTable = config('session.table');
            
            // Check if sessions table has user_id column
            $hasUserIdColumn = DB::getSchemaBuilder()->hasColumn($sessionsTable, 'user_id');
            
            if ($hasUserIdColumn) {
                // Delete all sessions for this user
                DB::table($sessionsTable)
                    ->where('user_id', $userId)
                    ->delete();
                
                // Log the session invalidation
                \Log::info("Invalidated all sessions for user ID: {$userId}, Guard: {$guard}");
            } else {
                // If no user_id column, we can't track user sessions properly
                \Log::warning("Sessions table does not have user_id column. Cannot invalidate all sessions for user {$userId}");
            }
            
        } catch (\Exception $e) {
            \Log::error("Error invalidating all sessions for user {$userId}: " . $e->getMessage());
        }
    }
} 