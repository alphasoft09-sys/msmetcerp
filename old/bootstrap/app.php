<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckUserRole::class,
            'admin.auth' => \App\Http\Middleware\AdminAuth::class,
            'student.auth' => \App\Http\Middleware\StudentAuth::class,
            'captcha' => \App\Http\Middleware\VerifyCaptcha::class,
        ]);
        
        // Add security headers middleware globally
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        
        // Add session middleware globally to ensure sessions are started
        $middleware->append(\App\Http\Middleware\EnsureSessionStarted::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle CSRF token mismatch exceptions
        $exceptions->renderable(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'CSRF token mismatch. Please refresh the page and try again.',
                    'error' => 'csrf_token_mismatch'
                ], 419);
            }

            return back()->withErrors([
                'csrf' => 'Session expired. Please refresh the page and try again.'
            ]);
        });

        // Handle 404 errors by redirecting to login page
        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, \Illuminate\Http\Request $request) {
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
        });
    })->create();
