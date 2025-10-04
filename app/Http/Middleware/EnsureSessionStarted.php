<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class EnsureSessionStarted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Ensure session is started
            if (!Session::isStarted()) {
                Session::start();
            }
            
            // Always regenerate CSRF token to ensure it exists
            Session::regenerateToken();
        } catch (\Exception $e) {
            // If session fails (e.g., database not available), continue without session
            // This allows the application to work even when database is not accessible
        }
        
        return $next($request);
    }
}
