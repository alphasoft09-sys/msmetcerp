<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        \Log::info('=== ROLE MIDDLEWARE DEBUG START ===');
        \Log::info('Request URL: ' . $request->fullUrl());
        \Log::info('Request Method: ' . $request->method());
        \Log::info('Is AJAX: ' . ($request->ajax() ? 'true' : 'false'));
        \Log::info('Required Roles: ' . implode(',', $roles));
        \Log::info('Auth Check: ' . (Auth::check() ? 'true' : 'false'));
        
        if (!Auth::check()) {
            \Log::warning('User not authenticated for URL: ' . $request->fullUrl());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('admin.login');
        }

        $user = Auth::user();
        $userRole = $user->user_role;
        \Log::info('User: ' . $user->email . ', Role: ' . $userRole);

        // Check if user has any of the required roles
        if (!in_array($userRole, $roles)) {
            \Log::warning('Unauthorized access attempt by user: ' . $user->email . ' with role: ' . $userRole . ' for required roles: ' . implode(',', $roles));
            \Log::info('=== ROLE MIDDLEWARE DEBUG END (BLOCKED) ===');
            abort(403, 'Unauthorized access');
        }

        \Log::info('Role check passed for user: ' . $user->email);
        \Log::info('=== ROLE MIDDLEWARE DEBUG END (PASSED) ===');
        return $next($request);
    }
}
