<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handle404Errors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (NotFoundHttpException $e) {
            // Check if the request is for an API route
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Page not found',
                    'message' => 'The requested resource was not found'
                ], 404);
            }

            // Check if user is already authenticated
            if (auth()->check()) {
                // If authenticated, redirect to dashboard based on user role
                $user = auth()->user();
                switch ($user->user_role) {
                    case 1: // TC Admin
                        return redirect()->route('admin.tc-admin.dashboard');
                    case 2: // TC Head
                        return redirect()->route('admin.tc-head.dashboard');
                    case 3: // Exam Cell
                        return redirect()->route('admin.exam-cell.dashboard');
                    case 4: // Assessment Agency
                        return redirect()->route('admin.aa.dashboard');
                    case 5: // Faculty
                        return redirect()->route('admin.tc-faculty.dashboard');
                    default:
                        return redirect()->route('admin.login');
                }
            }

            // If not authenticated, redirect to login page
            return redirect()->route('admin.login')->with('error', 'Page not found. Please login to continue.');
        }
    }
} 